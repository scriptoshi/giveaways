
import { computed, reactive, ref, watch } from "vue";


import { get } from "@vueuse/core";
import { useAccount, useClient, useWalletClient } from "use-wagmi";
import { BaseError, ContractFunctionRevertedError, formatEther } from 'viem';

import { camelToTitle } from "@/hooks/useCamelToTitle";
import tokenAbi from "@/Wagmi/constants/erc20.json";
export const useTokenApproval = ({
    contract,
    spender,
    amount,
}) => {
    const busy = ref(false);
    const loading = ref(false);
    const allowance = ref(0);
    const allowanceFormated = ref(0);
    const error = ref(null);
    const receipt = ref(null);
    const txhash = ref(null);
    const client = useClient();
    const walletClient = useWalletClient();
    const { address } = useAccount();
    const load = async () => {
        loading.value = true;
        allowance.value = await client.readContract({
            address: get(contract),
            abi: tokenAbi,
            functionName: "allowance",
            args: [get(spender)],
        });
        allowanceFormated.value = parseFloat(formatEther(allowance.value));
        loading.value = false;
    };
    watch([() => get(contract), () => get(spender)], ([newContract, newSpender]) => {
        if (newContract && newSpender) load();
    }, { immediate: true });
    const requiresApproval = computed(() => parseFloat(allowanceFormated.value) < parseFloat(amount));
    const approve = async () => {
        busy.value = true;
        const { request } = await client.simulateContract({
            address: get(contract),
            abi: tokenAbi,
            functionName: 'approve',
            account: address.value,
            args: [get(spender), get(amount)],
        }).catch((err) => {
            if (err instanceof BaseError) {
                const revertError = err.walk(err => err instanceof ContractFunctionRevertedError);
                if (revertError instanceof ContractFunctionRevertedError) {
                    const errorName = revertError.data?.errorName ?? '';
                    error.value = camelToTitle(errorName);
                }
                return error.value = err.shortMessage;
            }
        });
        if (!request) return;
        txhash.value = await walletClient.writeContract(request);
        receipt.value = await client.waitForTransactionReceipt(
            { hash: txhash.value }
        );
        busy.value = false;
        load();
    };
    return reactive({
        busy,
        loading,
        allowance,
        allowanceFormated,
        error,
        txhash,
        requiresApproval,
        load,
        approve
    });
};
