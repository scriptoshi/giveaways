import { reactive, ref } from "vue";

import { parseUnits } from "@ethersproject/units";
import { useI18n } from "vue-i18n";

import { addTransaction, useActiveWeb3Vue, useTxLink } from "@/Web3Modal";
import { checkTransaction } from "@/Web3Modal/hooks";
import { useTokenContract } from "@/Web3Modal/hooks/useContract";
import { calculateGasMargin, isAddress } from "@/Web3Modal/utils";


export const useTokenTransfer = (tokenAddress) => {
    const { account } = useActiveWeb3Vue();
    const token = useTokenContract(tokenAddress);
    const busy = ref(false);
    const working = ref(false);
    const error = ref(null);
    const txhash = ref(null);
    const [tx, txlink] = useTxLink(txhash, undefined, 10);
    const { t } = useI18n();
    const status = ref(t('Topup Airdrop'));
    const send = async (to, amount, onSuccess) => {
        error.value = null;
        busy.value = true;
        working.value = true;
        const decimals = await token.value.methods.decimals().call();
        const destination = isAddress(to);
        const sendAmount = parseUnits(amount.toString(), decimals);
        const estimatedGas = await token.value.methods
            .transfer(destination, sendAmount)
            .estimateGas({
                from: account.value,
            }).catch((e) => {
                busy.value = false;
                const curlyBracesInclusive = /\{(.*)/s;
                const result = e.message.toString().match(curlyBracesInclusive);
                try {
                    const err = JSON.parse(result[0]);
                    if (err?.message)
                        return error.value = err?.message;
                    if (err?.originalError?.message)
                        return error.value = err?.originalError?.message;
                } catch (error) {
                    return (error.value = e.message ?? e.data?.message ?? e);
                }
            });
        if (!estimatedGas) return;
        const gasLimit = calculateGasMargin(estimatedGas);
        await token.value.methods
            .transfer(destination, sendAmount)
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
                    summary: t('Set Vesting'),
                    onSuccess: async (receipt) => {
                        working.value = false;
                        status.value = t('Set Vesting: successful');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                        onSuccess?.();
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
        error,
        tx,
        txlink,
        status,
        token,
        send,
        name,
    });
    return response;
};

