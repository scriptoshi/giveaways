
import { reactive, ref, watch } from "vue";

import {
    getPublicClient,
} from '@wagmi/core';
import { useAccount, useChains, useConfig } from "use-wagmi";
import { formatUnits, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { endpoints } from "@/constants/lzEndpoints";
import { useContractCall } from "@/hooks/contracts/useContractCall";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";
export const useCrossChain = (token, form) => {
    const chains = useChains();
    const destinations = reactive({
        ...chains.value.reduce((memo, c) => {
            memo[c.id.toString()] = null;
            return memo;
        }, {}),
    });
    const sendFee = ref(null);
    const balance = ref(0);
    const decimals = ref(null);
    const config = useConfig();
    const { address } = useAccount();
    const loading = ref(false);
    const { t } = useI18n();
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
                balanceOf: [address.value],
                decimals: [],
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
            destinations[chain.id.toString()] = isAddress(response[chain.id.toString()]);
        });
        balance.value = formatUnits(response.balanceOf, response.decimals);
        decimals.value = response.decimals;
        loading.value = false;
    };
    watch(address, (address) => {
        if (address) update();
    }, { immediate: true });

    const estimateSendFee = async () => {
        const client = getPublicClient(config, { chainId: parseInt(token.chainId) });
        const endPointId = endpoints[form.chain.id.toString()].endpointId;
        const amount = parseUnits(form.amount ?? '0', decimals.value);
        if (!endPointId) return;
        sendFee.value = client.readContract({
            abi: token.factory.abi,
            address: token.contract,
            functionName: 'estimateSendFee',
            args: [endPointId, address.value, amount, false, []]
        });
    };
    const sendFrom = async () => {
        form.clearErrors();
        if (parseFloat(form.amount??'0') <= 0) form.setError('amount', t('Invalid Amount specified'));
        if (!(form.chain?.id)) form.setError('chain', t('Please select a destination chain'));
        if (form.hasErrors) return;
        const amount = parseUnits(form.amount, decimals.value);
         await estimateSendFee();
        const endPointId = endpoints[form.chain?.id?.toString?.()]?.endpointId;
        const params = [
            address.value,
            endPointId, // destination chainId
            address.value, // destination address to send tokens to
            amount, // quantity of tokens to send (in units of wei)
            address.value, // LayerZero refund address (if too much fee is sent gets refunded)
            zeroAddress, // future parameter
            "0x",
        ];
        await call('sendFrom', params, null, 'Send Crosschain Token');
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
        sendFee,
        balance,
        decimals,
        call,
        update,
        estimateSendFee,
        sendFrom
    });
};
