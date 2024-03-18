import { computed, reactive, ref, watch } from "vue";


import { useAccount, usePublicClient } from "use-wagmi";
import { parseEther, parseEventLogs, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import { camelToTitle, isAddress } from "@/Wagmi/utils/utils";


export const useDeployFactory = (foundryInfo, form) => {
    const { address, chainId } = useAccount();
    const { t } = useI18n();
    const deployPrice = ref(0);
    const ready = ref(false);
    const foundryAddress = computed(() => foundryInfo.contracts[chainId.value]);
    const publicClient = usePublicClient();
    const {
        error,
        txhash,
        busy,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call
    } = useContractCall(foundryInfo.abi.foundry, foundryAddress);
    /**
     * save the factory if deployment was detected;
     */
    const saveFactory = async (factoryAddress) => {
        form.transform(data => ({
            ...data,
            type: foundryInfo.name,
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
    watch(confirming, (confirming) => {
        if (confirming) status.value = 'Confirming Transaction';
    });
    /**
     * deploy the factory
     */
    const deploy = async () => {
        form.clearErrors();
        if (form.fee <= 0) form.setError('fee', t("Invalid Fees amount"));
        const fees = parseEther(form.fee);
        const feeTo = isAddress(form.feeAddress);
        const _owner = isAddress(form.owner);
        if (!feeTo) form.setError('feeTo', t("Invalid Feeto address"));
        if (!_owner) form.setError('owner', t("Invalid Owner address"));
        if (_owner === zeroAddress)
            form.setError('owner', t("Invalid Owner address"));
        if (feeTo === zeroAddress)
            form.setError('feeTo', t("Invalid Feeto address"));
        if ((foundryInfo.name === "AirdropFoundry" || foundryInfo.name === "NftFoundry") && (!parseFloat(form.feePercent) || parseFloat(form.feePercent) <= 0)) {
            form.setError('feePercent', t("Invalid feePercent Value"));
        }
        if (form.hasErrors) return;
        busy.value = true;

        let params = [_owner, feeTo, fees.toString()];
        if (foundryInfo.name === "AirdropFoundry" || foundryInfo.name === "NftFoundry")
            params.push(parseUnits(form.feePercent, 2));
        if (foundryInfo.name === "MultiSenderFoundry") params.push(form.eip712);
        if (foundryInfo.name === "LockFoundry")
            params = [_owner, fees.toString(), feeTo];
        status.value = t('Deploying Factory ...');
        const isDeployFactory = !!foundryInfo.abi.foundry?.find(a => a.name === 'deployFactory' && a.type === 'function');
        const method = isDeployFactory ? 'deployFactory' : 'deploy';
        const summary = `Deploy ${camelToTitle(foundryInfo.name)}`;
        await call(method, params, deployPrice.value.toString(), summary);
        const logs = parseEventLogs({
            abi: foundryInfo.abi.foundry,
            logs: receipt.value.logs,
            eventName: ['FACTORY', 'LOCK', 'MULTISENDER']
        });
        status.value = t('Now Saving Factory ...');
        const destination = logs?.[0]?.args?.factoryContract;
        console.log(logs, destination);
        saveFactory(destination, receipt.value.transactionHash);
        status.value = t('Factory Deployed Successfully');
    };
    return reactive({
        deploy,
        ready,
        error,
        busy,
        status,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        txhash,
        receipt,
        deployPrice
    });
};
