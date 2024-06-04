import { reactive, ref } from "vue";

import { useAccount, usePublicClient, useWalletClient } from "use-wagmi";
import { parseEther } from "viem";
import { useI18n } from "vue-i18n";

import { addTransaction, txReceipt } from "@/Wagmi/hooks/txs";
import { useTxHash } from "@/Wagmi/hooks/useTxHash";
export const useEtherTx = (to, amount) => {
    const { data: walletClient } = useWalletClient();
    const publicClient = usePublicClient();
    const error = ref("");
    const busy = ref(false);
    const status = ref("");
    const receipt = ref("");
    const isConfirmed = ref(false);
    const txhash = ref("");
    const [shortTx, etherScanLink] = useTxHash(txhash);
    const { t } = useI18n();
    const { address } = useAccount();
    const transact = async () => {
        busy.value = true;
        error.value = "";
        status.value = t("Sending Transaction ...");
        const request = await walletClient.value.prepareTransactionRequest({
            account: address.value,
            to,
            value: parseEther(amount.toString()),
            type: 'eip1559'
        });
        txhash.value = await walletClient.value
            .sendTransaction(request)
            .catch((err) => {
                busy.value = false;
                error.value = err.shortMessage ?? err.detials;
            });
        if (error.value) return;
        addTransaction({
            hash: txhash.value,
            summary: t("Topup BETNC"),
        });
        status.value = t("Confirming Tx ...");
        receipt.value = await publicClient.value.waitForTransactionReceipt({ hash: txhash.value });
        txReceipt(txhash.value, receipt.value);
        status.value = t("Tx Complete");
        isConfirmed.value = true;
    };
    return reactive({
        shortTx,
        etherScanLink,
        tx: shortTx,
        txlink: etherScanLink,
        txhash,
        error,
        busy,
        status,
        isConfirmed,
        receipt,
        transact
    });
};
