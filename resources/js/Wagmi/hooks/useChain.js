
import { base, boba, filecoin, optimism } from "viem/chains";
export const Standard = {
    Eip3091: 'EIP3091',
    None: 'none',
};
export const EIP3091_OVERRIDE = [
    optimism.id,
    boba.id,
    base.id,
    filecoin.id,
];
export const useChain = (chain) => {
    const getTxUrl = (txHash) => {
        if (!chain?.explorers) {
            if (chain?.blockExplorers?.default) {
                return `${chain?.blockExplorers?.default.url}/tx/${txHash}`;
            }
            return '';
        }
        for (const explorer of chain?.explorers) {
            if (
                explorer.standard === Standard.Eip3091 ||
                EIP3091_OVERRIDE.includes(chain?.chainId)
            ) {
                return `${explorer.url}/tx/${txHash}`;
            }
        }
        return '';
    };
    const getBlockUrl = (blockHashOrHeight) => {
        if (!chain?.explorers) {
            if (chain?.blockExplorers?.default) {
                return `${chain?.blockExplorers?.default.url}/block/${blockHashOrHeight}`;
            }
            return '';
        }
        for (const explorer of chain?.explorers) {
            if (explorer.standard === Standard.Eip3091) {
                return `${explorer.url}/block/${blockHashOrHeight}`;
            }
        }
        return '';
    };
    const getTokenUrl = (tokenAddress) => {
        if (!chain?.explorers) {
            if (chain?.blockExplorers?.default) {
                return `${chain?.blockExplorers?.default.url}/token/${tokenAddress}`;
            }
            return '';
        }
        for (const explorer of chain?.explorers) {
            if (
                explorer.standard === Standard.Eip3091 ||
                EIP3091_OVERRIDE.includes(chain?.chainId)
            ) {
                return `${explorer.url}/token/${tokenAddress}`;
            }
        }
        return '';
    };
    const getAccountUrl = (accountAddress) => {
        if (!chain?.explorers) {
            if (chain?.blockExplorers?.default) {
                return `${chain?.blockExplorers?.default.url}/address/${accountAddress}`;
            }
            return '';
        }
        for (const explorer of chain?.explorers) {
            if (
                explorer.standard === Standard.Eip3091 ||
                EIP3091_OVERRIDE.includes(chain?.chainId)
            ) {
                return `${explorer.url}/address/${accountAddress}`;
            }
        }
        return '';
    };

    return {
        getAccountUrl,
        getTokenUrl,
        getBlockUrl,
        getTxUrl,
        chain
    };
};