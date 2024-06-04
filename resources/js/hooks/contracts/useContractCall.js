

import { reactive, ref } from "vue";


import { get } from "@vueuse/core";
import { useAccount, usePublicClient, useWalletClient } from "use-wagmi";
import { BaseError, ContractFunctionRevertedError } from 'viem';
import { useI18n } from "vue-i18n";

import { camelToTitle } from "@/hooks/useCamelToTitle";
import { addTransaction, txReceipt } from "@/Wagmi/hooks/txs";
import { useTxHash } from "@/Wagmi/hooks/useTxHash";

export const useContractCall = (
    abi,
    contract,
) => {
    const { address: account } = useAccount();
    const { t } = useI18n();
    const busy = ref(null);
    const error = ref(null);
    const simulation = ref(null);
    const called = ref(null);
    const status = ref('');
    const confirming = ref(false);
    const isConfirmed = ref(false);
    const receipt = ref(null);
    const txhash = ref(null);
    const [shortTx, etherScanLink] = useTxHash(txhash);
    // const client = useClient();
    const publicClient = usePublicClient();

    const { data: walletClient } = useWalletClient();
    const call = async (method, args, value, summary) => {
        called.value = method;
        isConfirmed.value = false;
        error.value = null;
        receipt.value = null;
        txhash.value = null;
        confirming.value = false;
        busy.value = method;
        simulation.value = method;
        status.value = t('Sending Transaction ...');
        const response = await publicClient.value.simulateContract({
            address: get(contract),
            abi: get(abi),
            functionName: method,
            account: account.value,
            args,
            value: value ?? 0,
        }).catch((err) => {
            console.log(err);
            status.value = null;
            if (err instanceof BaseError) {
                const revertError = err.walk(err => err instanceof ContractFunctionRevertedError);
                if (revertError instanceof ContractFunctionRevertedError) {
                    const errorName = revertError.data?.errorName ?? '';
                    error.value = camelToTitle(errorName);
                    return;
                }
                return error.value = err.shortMessage;
            }
        });
        simulation.value = null;
        if (error.value && !response?.request) {
            busy.value = null;
            return;
        }
        txhash.value = await walletClient.value.writeContract(response.request).catch((err) => {
            error.value = err.shortMessage ?? err.detials;
        });
        if (error.value) {
            status.value = null;
            busy.value = null;
            return;
        }
        addTransaction({
            hash: txhash.value,
            summary: summary ?? `Call ${method}`,
        });
        confirming.value = true;
        status.value = t('Confirming Transaction ...');
        receipt.value = await publicClient.value.waitForTransactionReceipt(
            { hash: txhash.value }
        );
        txReceipt(txhash.value, receipt.value);
        status.value = t('Tx Completed Successfully');
        confirming.value = false;
        isConfirmed.value = true;
        busy.value = null;
        setTimeout(() => {
            isConfirmed.value = false;
        }, 30000);
    };
    return {
        error,
        txhash,
        busy,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        status,
        etherScanLink,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        called,
        call
    };
};


export const useReactiveContractCall = (
    abi,
    contract,
) => {
    const { address: account } = useAccount();
    const { t } = useI18n();
    const busy = ref(null);
    const error = ref(null);
    const simulation = ref(null);
    const called = ref(null);
    const status = ref('');
    const confirming = ref(false);
    const isConfirmed = ref(false);
    const receipt = ref(null);
    const txhash = ref(null);
    const [shortTx, etherScanLink] = useTxHash(txhash, undefined, 6);
    // const client = useClient();
    const publicClient = usePublicClient();

    const { data: walletClient } = useWalletClient();
    const call = async (method, args, value, summary, txCallback) => {
        called.value = method;
        isConfirmed.value = false;
        error.value = null;
        receipt.value = null;
        txhash.value = null;
        confirming.value = false;
        busy.value = method;
        simulation.value = method;
        status.value = t('Sending Tx ...');
        console.log(get(contract), get(abi), method);
        const response = await publicClient.value.simulateContract({
            address: get(contract),
            abi: get(abi),
            functionName: method,
            account: account.value,
            args,
            value: value ?? 0,
        }).catch((err) => {
            console.log(err);
            status.value = null;
            if (err instanceof BaseError) {
                const revertError = err.walk(err => err instanceof ContractFunctionRevertedError);
                if (revertError instanceof ContractFunctionRevertedError) {
                    const errorName = revertError.data?.errorName ?? '';
                    error.value = camelToTitle(errorName);
                    return;
                }
                return error.value = err.shortMessage;
            }
        });
        console.log(response);
        simulation.value = null;
        if (error.value && !response?.request) {
            busy.value = null;
            return;
        }
        txhash.value = await walletClient.value.writeContract(response.request).catch((err) => {
            error.value = err.shortMessage ?? err.detials;
        });
        if (error.value) {
            status.value = null;
            busy.value = null;
            return;
        }
        if (typeof txCallback === 'function') {
            txCallback(txhash.value);
        }
        addTransaction({
            hash: txhash.value,
            summary: summary ?? `Call ${method}`,
        });
        confirming.value = true;
        status.value = t('Confirming Tx ...');
        receipt.value = await publicClient.value.waitForTransactionReceipt(
            { hash: txhash.value }
        );
        txReceipt(txhash.value, receipt.value);
        status.value = t('Tx Successful');
        confirming.value = false;
        isConfirmed.value = true;
        busy.value = null;
        setTimeout(() => {
            isConfirmed.value = false;
        }, 30000);
    };
    return reactive({
        error,
        txhash,
        busy,
        shortTx,
        tx: shortTx,
        txlink: etherScanLink,
        status,
        etherScanLink,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        called,
        call
    });
};
