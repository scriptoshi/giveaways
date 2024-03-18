import { get } from '@vueuse/core';
import omit from "lodash/omit";
import toPairs from "lodash/toPairs";
import zip from "lodash/zip";
import { usePublicClient } from "use-wagmi";
export const DataTypes = Object.freeze({
    originAddress: "originAddress",
});
const createIndexSet = (data) => {
    return data.reduce((acc, item) => ({
        lastIndex: acc.lastIndex + item.length,
        built: acc.built.concat([[acc.lastIndex, acc.lastIndex + item.length]]),
    }),
        { lastIndex: 0, built: [] }
    ).built;
};



const mergeFromIndexSet = (arr, indexes) =>
    indexes.map(([before, after]) => arr.slice(before, after));

const stripLabels = (groupsOfShapes) => {
    return groupsOfShapes.map((group) =>
        group.map((relay) => {
            const pairs = toPairs(relay);
            const keysToRemove = pairs
                .filter(([key, value]) => {
                    return (typeof value !== "object") && !value?.functionName && !value?.abi;
                })
                .map(([key]) => key);
            return omit(relay, keysToRemove);
        })
    );
};
const recoverLabels = (original, withData) => {

    const nameRecall = zip(original, withData);
    const toReturn = nameRecall.map(([plainShape, withOrigin]) => {
        const zipped = zip(plainShape, withOrigin);
        return zipped.map(([plain, origin]) => {
            const keysToAdd = toPairs(plain)
                .filter(([key, value]) => typeof value !== "object")
                .map(([key, value]) => [
                    key,
                    value,
                ]);
            const keysAdded = keysToAdd.reduce(
                (acc, [key, value]) => ({
                    ...acc,
                    [key]: value,
                }),
                origin
            );
            return keysAdded;
        });
    });
    return toReturn;
};
const encodeAbi = (groupsOfShapes) => {
    return groupsOfShapes.map((group) =>
        group.map((shape) => {
            return Object.keys(shape).reduce((memo, key) => {
                if (typeof shape[key] === 'object' && shape[key].functionName !== undefined && isContractFunction(shape.contract.abi, shape[key].functionName)) {
                    return {
                        ...memo,
                        [key]: {
                            ...shape.contract,
                            ...shape[key]
                        }
                    };
                }
                if ((!shape[key] || Array.isArray(shape[key])) && isContractFunction(shape.contract.abi, key)) {
                    return {
                        ...memo,
                        [key]: {
                            ...shape.contract,
                            ...shape[key] ? { args: shape[key] } : {},
                            functionName: key,
                        }
                    };
                }
                if (key === 'contract') {
                    return {
                        ...memo,
                        [key]: shape.contract.address,
                    };
                }
                return {
                    ...memo,
                    [key]: shape[key],
                };

            }, {});
        })
    );
};
const multiCallGroups = async (calls, options = {}) => {
    const viem = options.client ?? usePublicClient(options.chainId ? { chainId: options.chainId } : {});
    if (options.client) delete options.client;
    if (calls.length === 0) return [];
    const indexes = createIndexSet(calls);
    const flatCalls = calls.flat(1);
    const res = await get(viem).multicall({
        ...options,
        contracts: flatCalls
    });
    return mergeFromIndexSet(res, indexes);
};

const isContractFunction = (abi = [], func) => {
    return abi.filter(a => a.type === 'function').map(a => a.name).includes(func);
};

export const multicall = async (groupsOfShapes = [], options = {}) => {
    /** const groups = [[{
        contract: { abi: ERC20_ABI, address: '0x8290333ceF9e6D528dD5618Fb97a76f268f3EDD4' },
        id: 7636,
        decimals: [],
        symbol: [],
        name: [],
        balanceOf: ['0x91F708a8D27F2BCcCe8c00A5f812e59B1A5e48E6']
    }, {
        contract: { abi: ERC20_ABI, address: '0xB8c77482e45F1F44dE1745F52C74426C631bDD52' },
        id: 334,
        decimals: [],
        symbol: [],
        name: [],
        balanceOf: ['0x91F708a8D27F2BCcCe8c00A5f812e59B1A5e48E6']
    }]];**/
    const callables = encodeAbi(groupsOfShapes);
    const plainShapes = stripLabels(callables);
    const groupsIndexSet = createIndexSet(callables);
    const multiCalls = plainShapes.flatMap((encodedGroup) =>
        encodedGroup.map((group) => Object.values(group)
        )
    );
    const res = await multiCallGroups(multiCalls, options);
    const rebuiltRes = mergeFromIndexSet(res, groupsIndexSet);
    const answer = zip(plainShapes, rebuiltRes);
    const better = answer.map(([abi, res]) => zip(abi, res));
    const rawMatch = better.map((group) =>
        group.map(([shape, resultsArr]) => zip(toPairs(shape), resultsArr))
    );
    const withOrigin = rawMatch.map((group) =>
        group.map((keys) => {
            return keys.reduce(
                // changes this
                (acc, [[key, value], data]) => {
                    return {
                        ...acc,
                        [key]: data.error ?? data.result,
                    };
                },
                {}
            );
        })
    );
    const renamed = recoverLabels(callables, withOrigin);
    return renamed;
};
