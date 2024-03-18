
import { reactive, ref, watch } from "vue";

import {
    getPublicClient,
} from '@wagmi/core';
import { useConfig } from "use-wagmi";
import { formatUnits, zeroAddress } from "viem";

import ERC20_ABI from "@/Wagmi/constants/erc20.json";
import { multicall } from "@/Wagmi/hooks/multicall";


export const useManageFactory = (factory) => {
    const config = useConfig();
    const loading = ref(false);
    const data = reactive({
        owner: null,
        feeTo: null,
        fees: null,
        feeToken: null,
        feePercent: null,
        refPercent: null,
        isEther: false,
        useTokenForFees: false,
        update: null,
        loading,
    });
    data.update = async () => {
        const publicClient = getPublicClient(config, { chainId: parseInt(factory.chainId) });
        const hasFeePercent = !!factory.factory_abi.find(a => a.name === 'feePercent' && a.type === 'function');
        const hasRefPercent = !!factory.factory_abi.find(a => a.name === 'refPercent' && a.type === 'function');
        const hasFee = !!factory.factory_abi.find(a => a.name === 'fee' && a.type === 'function');
        loading.value = true;
        const calls = [
            {
                contract: { abi: factory.factory_abi, address: factory.contract },
                id: factory.id,
                manager: [],
                feeToken: [],
                feeTo: [],
                ...hasFeePercent ? { feePercent: [] } : {},
                ...hasRefPercent ? { refPercent: [] } : {},
                ...hasFee ? { fee: [] } : { fees: [] }
            },
        ];
        const [results] = await multicall([calls], { chainId: parseInt(factory.chainId), client: publicClient }).catch(e => {
            console.log(e);
            loading.value = false;
            return [];
        });
        let decimals = 18;
        if (results[0]?.feeToken !== zeroAddress) {
            decimals = await publicClient.readContract({
                abi: ERC20_ABI,
                address: results[0]?.feeToken,
                functionName: 'decimals',
            });
        }

        data.owner = results[0]?.manager;
        data.feeTo = results[0]?.feeTo;
        data.feeToken = results[0]?.feeToken;
        console.log(results[0]?.fees ?? results[0]?.fee, decimals);
        data.fees = formatUnits(results[0]?.fees ?? results[0]?.fee ?? '0', decimals);
        data.feePercent = formatUnits(results[0]?.feePercent ?? '0', 2);
        data.refPercent = formatUnits(results[0]?.refPercent ?? '0', 2);
        data.isEther = results[0]?.feeToken === zeroAddress;
        data.useTokenForFees =
            results[0]?.feeToken !== zeroAddress;
        loading.value = false;
    };
    watch(() => factory.chainId, chainId => {
        data.update();
    }, { immediate: true });

    return data;
};
