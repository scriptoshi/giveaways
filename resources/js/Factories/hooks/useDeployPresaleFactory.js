import { computed, reactive, ref, watch } from "vue";


import { useAccount, usePublicClient } from "use-wagmi";
import { parseEventLogs, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import { camelToTitle, isAddress } from "@/Wagmi/utils/utils";

export const useDeployPresaleFactory = (foundryInfo, form) => {
    const { address, chainId } = useAccount();
    const foundryAddress = computed(() => foundryInfo.contracts[chainId.value]);
    const ready = ref(false);
    const { t } = useI18n();

    const deployPrice = ref(0);
    const publicClient = usePublicClient();
    const {
        error,
        txhash,
        busy,
        status,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        call
    } = useContractCall(foundryInfo.abi.foundry, foundryAddress);
    /**
     * save the factory if deployment was detected;
     */
    const saveFactory = async (factoryAddress, hash = null) => {
        form.transform(data => ({
            ...data,
            type: foundryInfo.name,
            hash,
            chainId: chainId.value,
            from: address.value,
            contract: factoryAddress,
            foundry: foundryAddress.value
        })).post(window.route("admin.factories.store"));
    };
    const update = async () => {
        if (!chainId.value) return;
        const hasFeesMethod = !!foundryInfo.abi.foundry?.find(a => a.name === 'fees' && a.type === 'function');
        if (hasFeesMethod) {
            deployPrice.value = await publicClient.value.readContract({
                address: foundryInfo.contracts[chainId.value],
                abi: foundryInfo.abi.foundry,
                functionName: 'fees',
            });
            ready.value = true;
            return;
        }
        const fnd = await publicClient.value.readContract({
            address: foundryInfo.contracts[chainId.value],
            abi: foundryInfo.abi.foundry,
            functionName: 'foundry',
        });
        deployPrice.value = fnd.fees;
        ready.value = true;
    };
    watch(chainId, () => update(), { immediate: true });
    /**
     * deploy the factory
     */
    const deploy = async () => {
        const params = {
            timeLock: isAddress(form.timeLock),
            feeAddress: isAddress(form.feeAddress),
            baseOnlyFee: parseUnits(form.baseOnlyFee, 2),
            baseFee: parseUnits(form.baseFee, 2),
            tokenFee: parseUnits(form.tokenFee, 2),
            referralFee: parseUnits(form.referralFee, 2),
            ethCreationFee: parseUnits(form.ethCreationFee, 18),
            maxPresalePeriod: form.maxPresalePeriod,
            minLockPeriod: form.minLockPeriod,
            minLiqiudityPercent: form.minLiqiudityPercent,
        };
        busy.value = true;
        console.log(params);
        if (!params.timeLock) form.setError('timeLock', t("Invalid TimeLock Adress"));
        if (!params.feeAddress || params.feeAddress === zeroAddress)
            form.setError('feeAddress', t("Invalid Fee recipient"));
        const _owner = isAddress(form.owner);
        if (!_owner || _owner === zeroAddress)
            form.setError('owner', t("Invalid Owner address"));
        if (form.hasErrors) return;
        const isDeployFactory = !!foundryInfo.abi.foundry?.find(a => a.name === 'deployFactory' && a.type === 'function');
        const method = isDeployFactory ? 'deployFactory' : 'deploy';
        status.value = t('Deploying Factory ...');
        const summary = `Deploy ${camelToTitle(foundryInfo.name)}`;
        await call(method, [params, _owner], deployPrice.value.toString(), summary);
        const logs = parseEventLogs({
            abi: foundryInfo.abi.foundry,
            logs: receipt.value.logs,
            eventName: ['FACTORY', 'LOCK', 'MULTISENDER']
        });
        status.value = t('Now Saving Factory ...');
        const destination = logs?.[0]?.args?.factoryContract;
        saveFactory(destination, receipt.value.transactionHash);
        status.value = t('Factory Deployed Successfully');
    };
    return reactive({
        deploy,
        ready,
        error,
        busy,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        call,
        txhash,
        status,
        deployPrice
    });
};
