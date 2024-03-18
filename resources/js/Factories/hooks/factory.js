import { reactive, ref } from "vue";
import { get } from "@vueuse/core";
import {
    getPublicClient,
} from '@wagmi/core';
import { useConfig } from "use-wagmi";
import { formatEther, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";

export const useFactory = (factory) => {
    const owner = ref(null);
    const feeTo = ref(null);
    const fees = ref(null);
    const config = useConfig();
    const loadData = async () => {
        const publicClient = getPublicClient(config, { chainId: parseInt(factory.chainId) });
        const calls = [
            {
                contract: { abi: factory.factory_abi, address: factory.contract },
                owner: [],
                feeTo: [],
                fees: [],
            },
        ];
        const [results] = await multicall([calls], { client: publicClient }).catch(e => {
            console.log(e);
            return [];
        });
        owner.value = results[0]?.owner;
        feeTo.value = results[0]?.feeTo;
        fees.value = formatEther(results[0]?.fees ?? 0);
    };
    loadData();
    return {
        data: reactive({
            owner,
            feeTo,
            fees,
        }),
        loadData
    };
};


export const useUpdateFeeToken = (factory, token) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async (toEther = false) => {
        status.value = t("Fee token will be changed");
        const addr = toEther ? zeroAddress : isAddress(token.value);
        if (!addr) return (error.value = t("Invalid Fee Token address"));
        const usesUpdateFeeToken = !!factory.factory_abi.find(a => a.name === 'updateFeeToken' && a.type === 'function');
        // launchpads tiniiy glitch
        const method = usesUpdateFeeToken ? 'updateFeeToken' : 'updateFeeToKen';
        const summary = t('Update Fee Token');
        await call(method, [addr], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });
};

export const useUpdateFees = (factory, fees) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async () => {
        status.value = t("Fee token will be changed");
        if (parseFloat(get(fees)) <= 0) return (error.value = t("Invalid Fees amount"));
        const usesUpdateFees = !!factory.factory_abi.find(a => a.name === 'updateFees' && a.type === 'function');
        const method = usesUpdateFees ? 'updateFees' : 'updateFee';
        const summary = t('Update Deploy Fee');
        await call(method, [get(fees)], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });


};


export const useUpdateFeePercent = (factory, feePercent) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async () => {
        status.value = t("Fee token will be changed");
        if (parseFloat(feePercent.value) <= 0) return (error.value = t("Invalid Fees amount"));
        const summary = t('Update Deploy Fee Percent');
        const formatedFees = parseUnits(feePercent.value, 2).toString();
        await call('updateFeePercent', [formatedFees], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });
};


export const useUpdateFeeTo = (factory, feeTo) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async () => {
        const addr = isAddress(feeTo.value);
        if (!addr) return (error.value = t("Invalid fee recipient address"));
        const summary = t('Update Fee Recipient');
        await call('updateFeeTo', [addr], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });

};


export const useUpdateOwner = (factory, owner) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async () => {
        const addr = isAddress(owner.value);
        if (!addr) return (error.value = t("Invalid New owner address"));
        const summary = t('Transfer Ownership');
        await call('switchRole', [addr], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });
};





export const useUpdateImplementation = (factory, impl) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async () => {
        const addr = isAddress(impl.value);
        if (!addr) return (error.value = t("Invalid New Implementation address"));
        const summary = t('Update Clone Implementor');
        await call('switchRole', [addr], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });
};



export const useUpdateRefPercent = (factory, feePercent) => {
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
    } = useContractCall(factory.factory_abi, factory.contract);
    const { t } = useI18n();
    const update = async () => {
        if (parseFloat(feePercent.value) <= 0) return (error.value = t("Invalid  Min Ref %"));
        const summary = t('Update Clone Implementor');
        const formatedFees = parseUnits(feePercent.value, 2).toString();
        await call('updateRefPercent', [formatedFees], null, summary);
    };
    return reactive({
        busy,
        error,
        txhash,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        receipt,
        status,
        call,
        update
    });
};

