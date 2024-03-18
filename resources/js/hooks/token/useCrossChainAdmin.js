
import { reactive, ref, watch } from "vue";

import {
    getPublicClient,
} from '@wagmi/core';
import { useAccount, useChains, useConfig } from "use-wagmi";

import { endpoints } from "@/constants/lzEndpoints";
import { useContractCall } from "@/hooks/contracts/useContractCall";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";
export const useCrossChainAdmin = (token, form) => {
    const chains = useChains();
    const destinations = reactive({
        ...chains.value.reduce((memo, c) => {
            memo[c.id.toString()] = null;
            return memo;
        }, {}),
    });
    const config = useConfig();
    const { address } = useAccount();
    const loading = ref(false);
    const {
        error,
        busy,
        shortTx,
        txhash,
        status,
        etherScanLink,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        called,
        call,
    } = useContractCall(token.factory.abi, token.contract);
    const update = async () => {
        form.clearErrors();
        chains.value.forEach((chain) => {
            form[chain.id.toString()] = null;
        });
        loading.value = true;
        const client = getPublicClient(config, { chainId: parseInt(token.chainId) });
        const calls = [
            {
                contract: { abi: token.factory.abi, address: token.contract },
                ...chains.value.reduce((memo, c) => {
                    memo[c.id.toString()] = {
                        functionName: 'trustedRemoteLookup',
                        args: [endpoints[c.id.toString()].endpointId]
                    };
                    return memo;
                }, {}),
            }
        ];
        const [results] = await multicall([calls], { client }).catch(e => {
            console.log(e);
            loading.value = false;
            return [];
        });
        const response = results[0];
        console.log(results[0], calls);
        chains.value.forEach(chain => {
            form[chain.id.toString()] = isAddress(response[chain.id.toString()]);
            destinations[chain.id.toString()] = isAddress(response[chain.id.toString()]);
        });
        loading.value = false;
    };
    watch(address, (address) => {
        if (address) update();
    }, { immediate: true });

    const setTrustedRemotes = async () => {
        const srcChainIds = chains.value.map(c => parseInt(endpoints[c.id.toString()].endpointId));
        const remotes = chains.value.map(c => isAddress(form[c.id.toString()]) ?? '0x');
        await call('setTrustedRemotes', [srcChainIds, remotes], null, 'Set Crosschain Contracts');
        update();
    };
    return reactive({
        destinations,
        error,
        busy,
        shortTx,
        txhash,
        status,
        etherScanLink,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        called,
        loading,
        call,
        update,
        setTrustedRemotes
    });
};
