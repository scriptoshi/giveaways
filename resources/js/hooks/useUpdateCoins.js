import { computed, reactive, ref } from 'vue';

import { debouncedWatch, get } from '@vueuse/core';
import {
    getPublicClient,
} from '@wagmi/core';
import axios from "axios";
import { useAccount, useChains, useConfig } from "use-wagmi";
import { BaseError, erc721Abi, formatUnits, zeroAddress } from "viem";
import { useI18n } from 'vue-i18n';

import ERC20_ABI from "@/Wagmi/constants/erc20.json";
import { multicall } from "@/Wagmi/hooks/multicall";
import { getError, isAddress } from '@/Wagmi/utils/utils';
export const useUpdateCoins = (chains) => {
    const config = useConfig();
    const chainIds = Object.keys(chains);
    return async () => {
        for (const chainId of chainIds) {
            const publicClient = getPublicClient(config, { chainId: parseInt(chainId) });
            const coins = chains[chainId] ?? [];
            if (coins.length === 0) continue;
            const calls = coins.filter(c => c.contract !== zeroAddress).map((coin) => {
                return {
                    contract: { abi: ERC20_ABI, address: coin.contract },
                    id: coin.id,
                    decimals: [],
                    symbol: [],
                    name: [],
                };
            });
            if (calls.length) {
                const [results] = await multicall([calls], { chainId: parseInt(chainId), client: publicClient }).catch(e => {
                    console.log(e);
                    return [];
                });
                if (results)
                    await axios.put(window.route('admin.coins.update.many'), { 'coins': results });
            }

        }
    };
};

export const useToken = (address, chainId, native = true) => {
    const config = useConfig();
    const chains = useChains();
    const { address: account } = useAccount();
    const chain = computed(() =>
        chains.value.find((c) => c.id.toString() === get(chainId)?.toString()),
    );
    const decimals = ref(18);
    const symbol = ref('');
    const supply = ref('0');
    const balance = ref('0');
    const totalSupply = ref(0n);
    const name = ref('');
    const error = ref(null);
    const loading = ref(false);
    const { t } = useI18n();
    const updateToken = async () => {
        loading.value = true;
        error.value = null;
        decimals.value = 18;
        totalSupply.value = 0n;
        supply.value = '0';
        balance.value = '0';
        name.value = '';
        symbol.value = '';
        const publicClient = getPublicClient(config, { chainId: parseInt(get(chainId)) });
        const calls = [
            {
                contract: { abi: ERC20_ABI, address: get(address) },
                decimals: [],
                symbol: [],
                name: [],
                totalSupply: [],
                ...account.value ? { balanceOf: [account.value] } : {}

            }
        ];
        const [results] = await multicall([calls], { chainId: parseInt(chainId), client: publicClient }).catch(e => {
            console.log(e);
            return [];
        });
        if (results[0]?.decimals instanceof BaseError) {
            const err = getError(results[0]?.decimals);
            error.value = err.includes('returned no data ') ? t('Invalid Contract Address. Check Address or Chain') : err;
        } else {
            decimals.value = results[0]?.decimals;
            name.value = results[0]?.name;
            symbol.value = results[0]?.symbol;
            supply.value = formatUnits(results[0]?.totalSupply, decimals.value);
            totalSupply.value = results[0]?.totalSupply;
            if (account.value)
                balance.value = formatUnits(results[0]?.balanceOf, decimals.value);
        }
        loading.value = false;
    };
    debouncedWatch([() => get(address), () => get(chainId)], ([addr, chId]) => {
        console.log(addr, chId);
        if (parseInt(chId) > 0 && native) {
            decimals.value = chain.value.nativeCurrency.decimals;
            name.value = chain.value.nativeCurrency.name;
            symbol.value = chain.value.nativeCurrency.symbol;
        }
        if (isAddress(addr) && addr !== zeroAddress && parseInt(chId) > 0)
            updateToken();
    }, { debounce: 300, immediate: true });
    const token = reactive({
        name,
        symbol,
        decimals,
        supply,
        totalSupply,
        balance
    });
    return {
        token,
        name,
        symbol,
        decimals,
        supply,
        balance,
        totalSupply,
        error,
        loading,
        updateToken
    };
};



export const useNft = (address, chainId) => {
    const config = useConfig();
    const symbol = ref('');
    const name = ref('');
    const error = ref(null);
    const loading = ref(false);
    const { t } = useI18n();
    const updateToken = async () => {
        loading.value = true;
        error.value = null;
        name.value = '';
        symbol.value = '';
        const publicClient = getPublicClient(config, { chainId: parseInt(get(chainId)) });
        const calls = [
            {
                contract: { abi: erc721Abi, address: get(address) },
                symbol: [],
                name: []
            }
        ];
        const [results] = await multicall([calls], { chainId: parseInt(chainId), client: publicClient }).catch(e => {
            console.log(e);
            return [];
        });
        console.log(results);
        if (results[0]?.decimals instanceof BaseError) {
            const err = getError(results[0]?.decimals);
            error.value = err.includes('returned no data ') ? t('Invalid Contract Address. Check Address or Chain') : err;
        } else {
            name.value = results[0]?.name;
            symbol.value = results[0]?.symbol;
        }
        loading.value = false;
    };
    debouncedWatch([() => get(address), () => get(chainId)], ([addr, chId]) => {
        console.log(addr, chId);
        if (isAddress(addr) && addr !== zeroAddress && parseInt(chId) > 0)
            updateToken();
    }, { debounce: 300, immediate: true });
    const token = reactive({
        name,
        symbol,
    });
    return {
        token,
        name,
        symbol,
        error,
        loading,
        updateToken
    };
};
