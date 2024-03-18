
import { reactive, ref, watch } from "vue";

import { formatUnits } from "@ethersproject/units";
import { get } from "@vueuse/core";
import { useI18n } from "vue-i18n";

import { MultiCall } from "@/multicall";
import { useTxLink } from "@/Web3Modal";
import { MULTICALL_NETWORKS } from "@/Web3Modal/constants/multicall";
import { addTransaction, checkTransaction, useActiveWeb3Vue } from "@/Web3Modal/hooks";
import { useContract } from "@/Web3Modal/hooks/useContract";
import { calculateGasMargin } from "@/Web3Modal/utils";


const { account, chainId, web3 } = useActiveWeb3Vue();
export const useTaxState = (tokenInfo) => {
    const decimals = ref(0);
    const symbol = ref('');
    const totalSupply = ref(0);
    const setSwapAndLiquifyEnabled = ref(false);
    const setLiquidityFeePercent = ref(0);
    const setTaxFeePercent = ref(0);
    const setMaxTxPercent = ref(0);
    const load = async () => {
        const tax = useContract(tokenInfo?.contract, tokenInfo?.factory?.abi);
        if (!account.value) return;
        if (!tax.value?.methods) return;
        const multiCallAddress = MULTICALL_NETWORKS[chainId.value];
        const multicall = new MultiCall(web3.value, multiCallAddress);
        const calls = [
            {
                setSwapAndLiquifyEnabled: tax.value.methods.swapAndLiquifyEnabled(),
                setLiquidityFeePercent: tax.value.methods._liquidityFee(),
                setTaxFeePercent: tax.value.methods._taxFee(),
                setMaxTxPercent: tax.value.methods._maxTxAmount(),
                decimals: tax.value.methods.decimals(),
                symbol: tax.value.methods.symbol(),
                totalSupply: tax.value.methods.totalSupply(),

            }
        ];
        const [results] = await multicall.all([calls]);
        decimals.value = results[0].decimals;
        symbol.value = results[0].symbol;
        totalSupply.value = formatUnits(results[0].totalSupply, decimals.value);
        setTaxFeePercent.value = results[0].setTaxFeePercent;
        setLiquidityFeePercent.value = results[0].setLiquidityFeePercent;

        setMaxTxPercent.value = (parseFloat(formatUnits(results[0].setMaxTxPercent, decimals.value)) * 100) / parseFloat(totalSupply.value);
        setSwapAndLiquifyEnabled.value = results[0].setSwapAndLiquifyEnabled;
    };
    const validateType = {
        setSwapAndLiquifyEnabled: 'boolean',
        setLiquidityFeePercent: 'percent',
        setMaxTxPercent: 'bips',
        setTaxFeePercent: 'percent',
        excludeFromReward: "address",
        includeInReward: "address",
        excludeFromFee: "address",
        includeInFee: "address",
    };
    const settings = reactive({
        load,
        decimals,
        symbol,
        totalSupply,
        validateType,
        setSwapAndLiquifyEnabled,
        setLiquidityFeePercent,
        setMaxTxPercent,
        setTaxFeePercent,
        excludeFromReward: "",
        includeInReward: "",
        excludeFromFee: "",
        includeInFee: "",
    });
    return settings;
};


export const useExcludeFromTaxFees = (tokenInfo, contract) => {
    const tax = useContract(tokenInfo?.contract, tokenInfo?.factory?.abi);
    const { t } = useI18n();
    const busy = ref(false);
    const error = ref(null);
    const status = ref(null);
    const txhash = ref(null);
    const isExcludedFromFee = ref(false);
    const [tx, txlink] = useTxLink(txhash, undefined, 14);
    const update = async () => {
        isExcludedFromFee.value = await tax.value.methods.isExcludedFromFee(get(contract)).call();
    };
    const excludeFromFee = async () => {
        const status = ref(t('Exclude from Fees'));
        error.value = null;
        busy.value = true;
        const estimatedGas = await tax.value.methods
            .excludeFromFee(get(contract))
            .estimateGas({
                from: account.value,
            }).catch((e) => {
                busy.value = false;
                const curlyBracesInclusive = /\{(.*)/s;
                const result = e.message.toString().match(curlyBracesInclusive);
                try {
                    const err = JSON.parse(result[0]);
                    if (err?.originalError?.message)
                        return error.value = err?.originalError?.message;
                    if (err?.message)
                        return error.value = err?.message;
                } catch (error) {
                    return (error.value = e.message ?? e.data?.message ?? e);
                }
            });
        if (!estimatedGas) return;
        const gasLimit = calculateGasMargin(estimatedGas);
        await tax.value.methods
            .excludeFromFee(get(contract))
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
                    summary: t('Add whitelist'),
                    onSuccess: async (receipt) => {
                        status.value = t('Successful');
                        setTimeout(() => {
                            busy.value = false;
                            txhash.value = null;
                        }, 3000);
                        update();
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
    watch([contract, () => tokenInfo.contract], () => update(), { immediate: true });
    return reactive({
        busy,
        error,
        tx,
        txlink,
        status,
        excludeFromFee,
        isExcludedFromFee
    });
};
