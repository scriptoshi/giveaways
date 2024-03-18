import { reactive, ref, watch } from "vue";

import { useForm } from "@inertiajs/vue3";
import { useAccount, usePublicClient } from "use-wagmi";
import { formatUnits, parseEther, parseEventLogs, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import tokenAbi from "@/Wagmi/constants/erc20.json";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";

export const useDeployCrossChainToken = (factory, form) => {
    const { chainId, chain } = useAccount();
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
        call,
    } = useContractCall(factory?.factory_abi, factory?.contract);
    const { t } = useI18n();
    const contractName = 'LzToken';
    const fees = reactive({
        price: 0n,
        priceFormatted: '0',
        feeToken: zeroAddress,
        name: chain.value?.nativeCurrency?.name,
        symbol: chain.value?.nativeCurrency?.symbol,
        decimals: chain.value?.nativeCurrency?.decimals
    });
    const publicClient = usePublicClient();
    /**
     * update token factory information
     */
    const updatePrice = async () => {
        if (!chain.value) return;
        fees.feeToken = zeroAddress;
        fees.name = chain.value?.nativeCurrency?.name;
        fees.symbol = chain.value?.nativeCurrency?.symbol;
        fees.decimals = chain.value?.nativeCurrency?.decimals;
        const calls = [
            {
                contract: { abi: factory?.factory_abi, address: factory?.contract },
                fees: [],
                feeToken: []
            }
        ];
        const [results] = await multicall([calls], { client: publicClient.value }).catch(e => {
            console.log(e);
            return [];
        });
        if (isAddress(results[0]?.feeToken) && results[0]?.feeToken !== zeroAddress) {
            const tokenCalls = [
                {
                    contract: { abi: tokenAbi, address: results[0]?.feeToken },
                    symbol: [],
                    name: [],
                    decimals: []
                }
            ];
            const [response] = await multicall([tokenCalls], { client: publicClient.value }).catch(e => {
                console.log(e);
                return [];
            });
            fees.decimals = response[0].decimals;
            fees.symbol = response[0].symbol;
            fees.symbol = response[0].symbol;
        }
        fees.feeToken = results[0]?.feeToken;
        fees.price = results[0]?.fees;
        fees.priceFormatted = formatUnits(results[0]?.fees, fees.decimals);
    };
    const tokens = ref(null);
    watch([() => factory?.contract, chain], () => {
        updatePrice();
    }, { immediate: true });
    /**
    /**
     * Deploy a new contract
     * @returns void
     */
    const deploy = async () => {
        form.clearErrors();
        if (!form.logo_uri) form.setError('logo_uri', t('Please Upload a logo'));
        if (!form.name) form.setError('name', t("Please Enter token name"));
        if (!form.symbol) form.setError('symbol', t("Please Enter token symbol"));
        if (!form.owner) form.setError('owner', t("Please Enter owner Address"));
        if (!isAddress(form.owner)) form.setError('owner', t("Invalid owner Address"));
        if (!form.total_supply) form.setError('total_supply', t("Total supply is required"));
        if (form.total_supply <= 0) form.setError('total_supply', t("Total supply Must be greater than zero"));
        if (form.hasErrors) return;
        await updatePrice();
        const value = fees.feeToken === zeroAddress
            ? fees.price
            : zeroAddress;
        const params = [
            form.owner,
            form.name,
            form.symbol,
            parseEther(form.total_supply)
        ];
        status.value = t('Sending Tx ...');
        const summary = `Deploy CrossChain Token`;
        await call('createLzToken', params, value, summary);
        const logs = parseEventLogs({
            abi: factory?.factory_abi,
            logs: receipt.value.logs,
            eventName: 'CLONE'
        });
        status.value = t('Now Saving Token ...');
        const destination = logs?.[0]?.args?.clone;
        useForm({
            ...form,
            factory_id: factory.id,
            contract: contractName,
            token: destination,
            chainId: chainId.value,
            txhash: txhash.value,
            redirect: form.redirect?.(destination, chainId.value),
        }).post(window.route('tokens.store'), {
            preserveState: false,
            preserveScroll: false,
            onFinish: () => status.value = t('Token Deployed Successfully')
        });
    };
    const ret = reactive({
        contractName,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        busy,
        error,
        status,
        tx: shortTx,
        txlink: etherScanLink,
        tokens,
        fees,
        deploy,
    });
    return ret;
};


