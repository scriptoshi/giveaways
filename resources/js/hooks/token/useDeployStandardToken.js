import { computed, reactive, ref, watch } from "vue";

import { useForm } from "@inertiajs/vue3";
import camelCase from "lodash/camelCase";
import upperFirst from "lodash/upperFirst";
import { useAccount, usePublicClient } from "use-wagmi";
import { encodePacked, formatUnits, keccak256, parseEventLogs, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import tokenAbi from "@/Wagmi/constants/erc20.json";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";

export const useDeployStandardToken = (factory, form) => {
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
    const plugins = ref(['pausable', 'burnable']);
    const extensions = ['antibot', 'burnable', 'pausable', 'capped', 'votes', 'permit'];
    const contractName = computed(() => {
        let list = plugins.value;
        if (list.includes('votes') && list.includes('permit')) {
            list = list.filter(l => l !== 'permit');
        }
        const name = list.length > 0
            ? camelCase(list.sort().join('_')) + 'Token'
            : camelCase(['pausable', 'capped'].sort().join('_')) + 'Token';
        return upperFirst(name);
    });
    const bytes32 = computed(() => keccak256(encodePacked(['string'], [contractName.value])));
    const deployed = ref(null);
    const fees = reactive({
        price: 0n,
        priceFormatted: '0',
        feeToken: zeroAddress,
        name: chain?.value?.nativeCurrency?.name,
        symbol: chain?.value?.nativeCurrency?.symbol,
        decimals: chain?.value?.nativeCurrency?.decimals
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
        console.log('results', results);
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
            console.log('response', response);
            fees.decimals = response[0].decimals;
            fees.symbol = response[0].symbol;
            fees.symbol = response[0].symbol;
        }
        fees.feeToken = results[0]?.feeToken;
        fees.price = results[0]?.fees;
        console.log(results[0]?.fees, fees.decimals);
        fees.priceFormatted = formatUnits(results[0]?.fees, fees.decimals);
    };
    const noMinter = computed(() => {
        const all = plugins.value ?? [];
        return all.includes('capped');
    });
    const method = () => {
        if (plugins.value.includes('capped') && plugins.value.includes('antibot'))
            return 'createCappedAntibotToken';
        if (plugins.value.includes('capped'))
            return 'createCappedToken';
        if (plugins.value.includes('antibot'))
            return 'createAntiBotToken';
        return 'createToken';
    };
    const tokens = ref(null);
    watch([() => factory?.contract, chain], () => {
        updatePrice();
    }, { immediate: true });
    /**
     * Deploy a new contract
     * @returns void
     */
    const deploy = async () => {
        form.clearErrors();
        if (!form.logo_uri) form.setError('logo_uri', t('Please Upload a logo'));
        if (!form.name) form.setError('name', t("Please Enter token name"));
        if (!form.symbol) form.setError('symbol', t("Please Enter token symbol"));
        if (!form.decimals) form.setError('decimals', t("Please Enter token decimals"));
        if (!form.total_supply) form.setError('total_supply', t("Total supply is required"));
        if (form.total_supply <= 0) form.setError('total_supply', t("Total supply Must be greater than zero"));
        let addr;
        if (!noMinter.value) {
            addr = isAddress(form.minter);
            if (!addr) form.setError('minter', t("Provide a valid destination Address"));
        }
        if (form.hasErrors) return;
        await updatePrice();
        const value = fees.feeToken === zeroAddress
            ? fees.price
            : zeroAddress;
        const params = noMinter.value
            ? [
                bytes32.value,
                form.name,
                form.symbol,
                form.decimals,
                parseUnits(form.total_supply, form.decimals).toString(),
            ]
            : [
                bytes32.value,
                addr,
                form.name,
                form.symbol,
                form.decimals,
                parseUnits(form.total_supply, form.decimals).toString(),
            ];
        status.value = t('Deploying Token ...');
        const summary = `Deploy Standard Token`;
        await call(method(), params, value, summary);
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
            contract: contractName.value,
            token: destination,
            chainId: chainId.value,
            txhash: txhash.value,
            redirect: form.redirect?.(destination, chainId.value),
        }).post(window.route('tokens.store'), {
            preserveState: false,
            preserveScroll: false,
            onFinish: () => status.value = t('Factory Deployed Successfully')
        });
    };

    const ret = reactive({
        contractName,
        extensions,
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
        plugins,
        noMinter,
        deployed,
        fees,
        deploy,
    });
    return ret;
};


