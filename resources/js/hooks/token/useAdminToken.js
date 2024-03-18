import { computed, reactive, ref } from "vue";

import { useForm } from "@inertiajs/vue3";
import { BigNumber, ethers } from "ethers";
import { formatUnits, id, parseUnits } from "ethers/lib/utils";
import { DateTime } from "luxon";
import { useI18n } from "vue-i18n";

import antiBotAbi from "@/constants/abi/AntiBot.json";
import { MultiCall } from "@/multicall";
import { addTransaction, useTxLink } from "@/Web3Modal";
import { MULTICALL_NETWORKS } from "@/Web3Modal/constants/multicall";
import { checkTransaction, useActiveWeb3Vue } from "@/Web3Modal/hooks";
import { useContract } from "@/Web3Modal/hooks/useContract";
import { calculateGasMargin, isAddress } from "@/Web3Modal/utils";

const { account, web3, chainId } = useActiveWeb3Vue();

export const adminRole = ethers.constants.HashZero;
export const minterRole = id('MINTER_ROLE');
export const pauserRole = id('PAUSER_ROLE');
export const roles = [
    { name: 'Admin Role', value: adminRole },
    { name: 'Pauser Role', value: minterRole },
    { name: 'Minter Role', value: pauserRole },
];

export const antibotAddress = ref();
export const user = ref(null);
export const delay = ref();
export const stopTime = ref(parseInt(DateTime.now().plus({ days: 3 }).toJSDate()));
export const stopInDays = ref(0);
export const increment = ref(null);
export const maxPerTrade = ref(null);
export const whitelist = ref([]);

export const setSwapAndLiquifyEnabled = ref(false);
export const setLiquidityFeePercent = ref(0);
export const setTaxFeePercent = ref(0);
export const setMaxTxPercent = ref(0);
export const decimals = ref(18);
// standard token state
const symbol = ref('');
const totalSupply = ref(0);
const paused = ref(0);
const antiBot = ref(0);
const hasAdminRole = ref(0);
const hasMinterRole = ref(0);
const hasPauserRole = ref(0);
const cap = ref(0);
export const useTokenState = (dbtoken) => {
    whitelist.value = dbtoken?.whitelist?.map?.(d => d.address) ?? [];
    const tokenAddress = dbtoken?.contract;
    const tokenName = dbtoken?.contract_name;
    const hasMint = tokenName?.toLowerCase?.().includes?.("capped") ?? false;
    const hasAntibot = tokenName?.toLowerCase?.().includes?.("antibot") ?? false;
    const hasPause = tokenName?.toLowerCase?.().includes?.("pausable") ?? false;
    const isTax = tokenName?.toLowerCase?.().includes?.("tax") ?? false;
    const load = async () => {
        const token = useContract(tokenAddress, dbtoken?.factory?.abi);
        if (hasAntibot)
            antibotAddress.value = await token.value.antiBot();
        if (!account.value) return;
        if (!token.value?.methods) return;
        const antibot = useContract(antibotAddress, antiBotAbi);
        if (hasAntibot && !antibot.value?.methods) return;
        const multiCallAddress = MULTICALL_NETWORKS[chainId.value];
        const multicall = new MultiCall(web3.value, multiCallAddress);
        const wl = dbtoken?.whitelist?.map(w => w.address)?.reduce?.((memo, white) => {
            memo[white] = antibot.value.methods.tokenWhitelist(tokenAddress, white);
            return memo;
        }, {});
        const calls = [
            {
                ...(hasMint || hasPause) && { hasAdminRole: token.value.methods.hasRole(adminRole, account.value) },
                ...hasMint && { hasMinterRole: token.value.methods.hasRole(minterRole, account.value) },
                ...hasPause && { hasPauserRole: token.value.methods.hasRole(pauserRole, account.value) },
                ...hasPause && { paused: token.value.methods.paused() },
                ...hasMint && { cap: token.value.methods.cap() },
                ...hasAntibot && { antiBot: token.value.methods.antiBot() },
                decimals: token.value.methods.decimals(),
                symbol: token.value.methods.symbol(),
                totalSupply: token.value.methods.totalSupply(),

            },
            hasAntibot && {
                admins: antibot.value.methods.admins(tokenAddress),
                delay: antibot.value.methods.delay(tokenAddress),
                stopTime: antibot.value.methods.stopTime(tokenAddress),
                maxPerTrade: antibot.value.methods.maxPerTrade(tokenAddress),
                increment: antibot.value.methods.increment(tokenAddress),
            },
            hasAntibot && wl,
            isTax && {
                setSwapAndLiquifyEnabled: token.value.methods.swapAndLiquifyEnabled(),
                setLiquidityFeePercent: token.value.methods._liquidityFee(),
                setTaxFeePercent: token.value.methods._taxFee(),
                setMaxTxPercent: token.value.methods._maxTxAmount(),
            }
        ];

        const [results] = await multicall.all([calls.filter(c => !!c)]);
        decimals.value = results[0].decimals;
        symbol.value = results[0].symbol;
        totalSupply.value = results[0].totalSupply ? formatUnits(results[0].totalSupply.toString(), decimals.value) : null;
        if (hasPause) {
            paused.value = results[0].paused;
            hasPauserRole.value = results[0].hasPauserRole;
            hasAdminRole.value = results[0].hasAdminRole;
        }
        if (hasMint) {
            cap.value = results[0].cap;
            hasMinterRole.value = results[0].hasMinterRole;
            hasAdminRole.value = results[0].hasAdminRole;
        }
        if (hasAntibot) {
            antiBot.value = results[0].antiBot;
            delay.value = results[1]?.delay;
            stopTime.value = parseInt(results[1]?.stopTime) > 0 ? parseInt(results[1]?.stopTime) : parseInt(DateTime.now().plus({ days: 3 }).toSeconds());
            stopInDays.value = (DateTime.fromSeconds(stopTime.value).diffNow('days').days).toFixed(4) * 1;
            maxPerTrade.value = formatUnits(results[1]?.maxPerTrade ?? '0', decimals.value);
            increment.value = formatUnits(results[1]?.increment ?? '0', decimals.value);
            const wls = results[2];
            whitelist.value = Object.keys(wls).filter(w => parseInt(wls[w]) === 1);
        }
        if (isTax) {
            setSwapAndLiquifyEnabled.value = results[1].setSwapAndLiquifyEnabled;
            setLiquidityFeePercent.value = results[1].setLiquidityFeePercent;
            setTaxFeePercent.value = results[1].setTaxFeePercent;
            setMaxTxPercent.value = results[1].setMaxTxPercent;
        }
    };
    const settings = reactive({
        hasAdminRole,
        hasPauserRole,
        hasMinterRole,
        adminRole,
        minterRole,
        pauserRole,
        whitelist,
        load,
        decimals,
        symbol,
        totalSupply,
        paused,
        cap,
        antiBot,
        setSwapAndLiquifyEnabled,
        setLiquidityFeePercent,
        setTaxFeePercent,
        setMaxTxPercent,
    });
    return settings;
};

export const useGrantRole = (tokenInfo) => {
    const grantBusy = ref(false);
    const revokeBusy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const role = ref(minterRole);
    const user = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const token = useContract(tokenInfo.contract, tokenInfo.factory.abi);
    const { t } = useI18n();
    const status = ref(t('Granting Roles'));
    const { load } = useTokenState();
    const grant = async () => {
        status.value = t("Ownership will be transfered!");
        const isAdmin = await token.value.methods.hasRole(adminRole, account.value);
        if (!isAdmin) return error.value = "Your Account is not Admin!";
        if (![adminRole, minterRole, pauserRole].includes(role.value)) return error.value = "Unknown Role Being Set";
        const addr = isAddress(user.value);
        if (!addr) return (error.value = t("Invalid Role User address"));
        const roleString = roles[role.value];
        error.value = null;
        grantBusy.value = true;
        let estimatedGas;
        try {
            estimatedGas = await token.value.methods
                .grantRole(role.value, user.value)
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            grantBusy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .grantRole(role.value, user.value)
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Granting {role} to User", { role: roleString });
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Grant Role'),
                    onSuccess: async (receipt) => {
                        status.value = t('Role granted successfully');
                        setTimeout(() => {
                            grantBusy.value = false;
                            txhash.value = null;
                        }, 3000);
                        await load();
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            grantBusy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    grantBusy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const revoke = async () => {
        status.value = t("Ownership will be transfered!");
        const isAdmin = await token.value.methods.hasRole(adminRole, account.value);
        if (!isAdmin) return error.value = "Your Account is not Admin!";
        if (![adminRole, minterRole, pauserRole].includes(role.value)) return error.value = "Unknown Role Being Set";
        const addr = isAddress(user.value);
        if (!addr) return (error.value = t("Invalid Role User address"));
        const roleString = roles[role.value];
        error.value = null;
        revokeBusy.value = true;
        let estimatedGas;
        try {
            estimatedGas = await token.value.methods
                .revokeRole(role.value, user.value)
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            revokeBusy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .revokeRole(role.value, user.value)
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Revoking {role} to User", { role: roleString });
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Revoke Role'),
                    onSuccess: async (receipt) => {
                        status.value = t('Role revoked successfully');
                        setTimeout(() => {
                            revokeBusy.value = false;
                            txhash.value = null;
                        }, 3000);
                        await load();
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            revokeBusy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    revokeBusy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const grantData = reactive({
        revokeBusy,
        grantBusy,
        grant,
        revoke,
        error,
        role,
        user,
        tx,
        txlink,
        status
    });
    return grantData;
};


export const renounceRole = (tokenInfo) => {
    const busy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const role = ref(minterRole);
    const user = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const token = useContract(tokenInfo.contract, tokenInfo.factory.abi);
    const { t } = useI18n();
    const status = ref(t('Granting Roles'));

    const renounce = async () => {
        status.value = t("You will loose access to thos Previlage!");
        const isOwner = await token.value.methods.hasRole(role, account.value);
        if (!isOwner) return error.value = "Your Account Doesnt have this role";
        if (![adminRole, minterRole, pauserRole].includes(role.value)) return error.value = "Unknown Role Being Set";
        const addr = isAddress(account.value);
        if (!addr) return (error.value = t("Looks like your Wallet not connected"));
        const roleString = roles[role.value];
        const { load } = useTokenState();
        error.value = null;
        busy.value = true;
        let estimatedGas;
        try {
            estimatedGas = await token.value.methods
                .renounceRole(role.value, addr)
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            busy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .renounceRole(role.value, addr)
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Renouncing {role} to User", { role: roleString });
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Renounce Role'),
                    onSuccess: async (receipt) => {
                        status.value = t('Role renounced successfully');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                        await load();
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const renounceData = reactive({
        busy,
        renounce,
        error,
        role,
        user,
        tx,
        txlink,
        status
    });
    return renounceData;
};



export const useMint = (tokenInfo) => {
    const busy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const user = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const token = useContract(tokenInfo.contract, tokenInfo.factory.abi);
    const amount = ref(0);
    const mintAmount = computed(() => parseUnits(amount.value.toString(), decimals.value));
    const { t } = useI18n();
    const status = ref(t('Mint Tokens'));
    const mint = async () => {
        status.value = t("Mint Tokens");
        const isMinter = await token.value.methods.hasRole(minterRole, account.value);
        if (!isMinter) return error.value = "Your Account Cannot Mint tokens";
        if (!mintAmount.value.gt(0)) return error.value = "Amount is Zero";
        const addr = isAddress(user.value);
        if (!addr) return (error.value = t("Invalid Mint destination address"));
        error.value = null;
        busy.value = true;
        let estimatedGas;
        try {
            estimatedGas = await token.value.methods
                .mint(user.value, mintAmount.value)
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            busy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .mint(user.value, mintAmount.value)
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Minting Tokens");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Minting Tokens'),
                    onSuccess: async (receipt) => {
                        status.value = t('Tokens Minted successfully');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const renounceData = reactive({
        busy,
        mint,
        error,
        amount,
        mintAmount,
        decimals,
        user,
        tx,
        txlink,
        status
    });
    return renounceData;
};


export const usePause = (tokenInfo) => {
    const token = useContract(tokenInfo.contract, tokenInfo.factory.abi);
    const busy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const { t } = useI18n();
    const status = ref(t('Unpause contract'));
    const pause = async () => {
        error.value = null;
        busy.value = true;
        const estimatedGas = await token.value.methods
            .pause()
            .estimateGas({
                from: account.value,
            }).catch((e) => {
                busy.value = false;
                if (e.message.toString().includes('Internal JSON-RPC error.')) {
                    const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                    return error.value = err?.message;
                }
                return (error.value = e.message ?? e.data?.message ?? e);
            });

        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .pause()
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Sendin Tx..");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Unpause contract'),
                    onSuccess: async (receipt) => {
                        status.value = t('Unpause contract: successful');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const unpause = async () => {
        error.value = null;
        busy.value = true;
        const estimatedGas = await token.value.methods
            .unpause()
            .estimateGas({
                from: account.value,
            }).catch((e) => {
                busy.value = false;
                if (e.message.toString().includes('Internal JSON-RPC error.')) {
                    const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                    return error.value = err?.message;
                }
                return (error.value = e.message ?? e.data?.message ?? e);
            });

        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .unpause()
            .send({
                from: account.value,
                gasLimit,
                gasPrice: null,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null,
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Sendin Tx..");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Pause'),
                    onSuccess: async (receipt) => {
                        status.value = t('Pause: successful');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const response = reactive({
        busy,
        pause,
        unpause,
        error,
        tx,
        txlink,
        status
    });
    return response;
};


// Antibot

export const useTransferAdmin = (tokenInfo) => {
    const busy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const antibot = useContract(antibotAddress, antiBotAbi);
    const { t } = useI18n();
    const status = ref(t('Granting Roles'));
    const loadAdmin = async () => {
        user.value = await antibot.value.admins(tokenInfo.contract);
    };
    const transfer = async () => {
        status.value = t("Ownership will be transfered!");
        const isAdmin = await antibot.value.methods.isAdmin(tokenInfo.contract, account.value);
        if (!isAdmin) return error.value = "Your Account is not Token Admin!";
        const addr = isAddress(user.value);
        if (!addr) return (error.value = t("Invalid Role User address"));
        error.value = null;
        busy.value = true;
        let estimatedGas;
        try {
            estimatedGas = await antibot.value.methods
                .transferAdmin(tokenInfo.contract, user.value)
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            busy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await antibot.value.methods
            .transferAdmin(tokenInfo.contract, user.value)
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Transfer Token Admin");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Transfer Token Admin'),
                    onSuccess: async (receipt) => {
                        status.value = t('Token Admin Transfer successful');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const grantData = reactive({
        busy,
        transfer,
        error,
        user,
        tx,
        txlink,
        status,
        loadAdmin
    });
    return grantData;
};

export const useUpdateSettings = (tokenInfo) => {
    const busy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const antibot = useContract(antibotAddress, antiBotAbi);
    const { t } = useI18n();
    const status = ref(t('Update Settings'));
    const { load } = useTokenState();
    const update = async () => {
        status.value = t("Settings will be updated!");
        const token = tokenInfo.contract;
        error.value = null;
        busy.value = true;
        let estimatedGas;
        const delayFormated = BigNumber.from(delay.value.toString());
        const stopTimeFormated = BigNumber.from(stopInDays.value.toString());
        const maxPerTradeFormated = parseUnits(delay.value.toString(), decimals.value);
        const incrementFormated = parseUnits(increment.value.toString(), decimals.value);
        try {
            estimatedGas = await antibot
                .value
                .methods
                .update(
                    token,
                    delayFormated,
                    stopTimeFormated,
                    incrementFormated,
                    maxPerTradeFormated
                )
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            busy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await antibot.value.methods
            .update(
                token,
                delayFormated,
                stopTimeFormated,
                incrementFormated,
                maxPerTradeFormated
            )
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Update Settings");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Update Settings'),
                    onSuccess: async (receipt) => {
                        status.value = t('Update Settings Complete');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                        await load();
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const grantData = reactive({
        busy,
        update,
        error,
        delay,
        stopTime,
        stopInDays,
        increment,
        maxPerTrade,
        decimals,
        tx,
        txlink,
        status
    });
    return grantData;
};


export const useWhiteList = (tokenInfo) => {
    const busy = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const [tx, txlink] = useTxLink(txhash);
    const antibot = useContract(antibotAddress, antiBotAbi);
    const { t } = useI18n();
    const status = ref(t('Update Whitelist'));
    const list = reactive([]);
    const { load } = useTokenState();
    const addWhiteList = async () => {
        status.value = t("Whitelist will be updated!");
        const token = tokenInfo.contract;
        error.value = null;
        busy.value = true;
        let estimatedGas;
        const whiteListed = list.map(l => isAddress(l)).filter(f => f !== null);
        try {
            estimatedGas = await antibot
                .value
                .methods
                .addTokenWhiteList(
                    token,
                    whiteListed
                )
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            busy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await antibot.value.methods
            .addTokenWhiteList(
                token,
                whiteListed
            )
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Update Whitelist");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Update Whitelist'),
                    onSuccess: async (receipt) => {
                        status.value = t('Update Whitelist Complete');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                        await load();
                        useForm({ list: whiteListed }).put(window.route('tokens.whitelist.add', token));
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };
    const removeWhiteList = async () => {
        status.value = t("Whitelist will be updated!");
        const token = tokenInfo.contract;
        error.value = null;
        busy.value = true;
        let estimatedGas;
        const whiteListed = whitelist.value.map(l => isAddress(l)).filter(f => f !== null);
        try {
            estimatedGas = await antibot
                .value
                .methods
                .unTokenWhiteList(
                    token,
                    whiteListed
                )
                .estimateGas({
                    from: account.value,
                });
        } catch (e) {
            busy.value = false;
            if (e.message.toString().includes('Internal JSON-RPC error.')) {
                const err = JSON.parse(e.message.toString().replace('Internal JSON-RPC error.', ''));
                return error.value = err?.message;
            }
            return (error.value = e.message ?? e.data?.message ?? e);
        }
        const gasLimit = calculateGasMargin(estimatedGas);
        await antibot.value.methods
            .unTokenWhiteList(
                token,
                whiteListed
            )
            .send({
                from: account.value,
                gasLimit,
                maxPriorityFeePerGas: null,
                maxFeePerGas: null
            })
            .once("transactionHash", async (hash) => {
                status.value = t("Update Whitelist");
                txhash.value = hash;
                addTransaction({
                    hash,
                    type: "tx",
                    summary: t('Update Whitelist'),
                    onSuccess: async (receipt) => {
                        status.value = t('Update Whitelist Complete');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                        await load();
                        useForm({ list: whiteListed }).put(window.route('tokens.whitelist.remove', token));
                    },
                    onError: (e) => {
                        error.value = e.message ?? e;
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                            error.value = null;
                        }, 3000);
                    },
                });
            })
            .once("confirmation", (confirmationNumber, receipt) => {
                checkTransaction(receipt.transactionHash);
            })
            .on("error", () => {
                checkTransaction(txhash.value);
            }).catch(e => {
                if (e.code === 4001) {
                    error.value = 'Signature Denied';
                    busy.value = false;
                    setTimeout(() => {
                        txhash.value = null;
                        error.value = null;
                    }, 3000);
                }
            });
    };

    const grantData = reactive({
        busy,
        addWhiteList,
        removeWhiteList,
        list,
        error,
        delay,
        stopTime,
        increment,
        maxPerTrade,
        decimals,
        tx,
        txlink,
        status
    });
    return grantData;
};













