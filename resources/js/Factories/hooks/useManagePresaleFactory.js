
import { reactive, watch } from "vue";

import {
    getPublicClient,
} from '@wagmi/core';
import { useConfig } from "use-wagmi";
import { formatUnits, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import ERC20_ABI from "@/Wagmi/constants/erc20.json";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";

export const useLaunchPadData = (factoryInfo) => {
    const config = useConfig();
    const info = reactive({
        owner: '',
        feeToken: '',
        useTokenForFees: '',
        timeLock: '',
        feeAddress: '',
        baseOnlyFee: '',
        baseFee: '',
        tokenFee: '',
        referralFee: '',
        ethCreationFee: '',
        maxPresalePeriod: '',
        minLockPeriod: '',
        minLiqiudityPercent: '',
        maxLockCycle: '',
        minCyclePercent: '',
        maxLockPeriod: '',
        update: () => null
    });

    info.update = async () => {
        const publicClient = getPublicClient(config, { chainId: parseInt(factoryInfo.chainId) });
        const calls = [
            {
                contract: { abi: factoryInfo.factory_abi, address: factoryInfo.contract },
                id: factoryInfo.id,
                feeToken: [],
                getSettings: [],
                getLpSettings: [],
                manager: [],
            },
        ];
        console.log(calls);
        const [results] = await multicall([calls], { client: publicClient }).catch(e => {
            console.log(e);
            return [];
        });
        const response = results[0];
        info.owner = response.manager;
        const settings = response.getSettings;
        const lpSettings = response.getLpSettings;
        info.feeToken = response.feeToken;
        let decimals = 18;
        if (response.feeToken !== zeroAddress) {
            decimals = await publicClient.readContract({
                abi: ERC20_ABI,
                address: response.feeToken,
                functionName: 'decimals',
            });
        }
        info.useTokenForFees =
            response.feeToken !== zeroAddress;
        info.timeLock = settings.timeLock;
        info.feeAddress = settings.feeAddress;
        info.baseOnlyFee = formatUnits(settings.baseOnlyFee, 2);
        info.baseFee = formatUnits(settings.baseFee, 2);
        info.tokenFee = formatUnits(settings.tokenFee, 2);
        info.referralFee = formatUnits(settings.referralFee, 2);
        info.ethCreationFee = formatUnits(settings.ethCreationFee, decimals);
        info.maxPresalePeriod = settings.maxPresalePeriod;
        info.minLockPeriod = settings.minLockPeriod;
        info.minLiqiudityPercent = settings.minLiqiudityPercent;
        info.maxLockCycle = lpSettings?.maxLockCycle ?? 0;
        info.minCyclePercent = formatUnits(lpSettings?.minCyclePercent ?? '0', 2);
        info.maxLockPeriod = lpSettings?.maxLockPeriod ?? 0;
    };
    watch(() => factoryInfo.id, (factoryID) => {
        info.update();
    }, { immediate: true });
    return info;
};

export const useUpdateSettings = (factoryInfo, form) => {
    const { t } = useI18n();
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
    } = useContractCall(factoryInfo.factory_abi, factoryInfo.contract);

    const updateSettings = async (decimals) => {
        form.clearErrors();
        const params = {
            timeLock: isAddress(form.timeLock),
            feeAddress: isAddress(form.feeAddress),
            baseOnlyFee: parseUnits(form.baseOnlyFee, 2),
            baseFee: parseUnits(form.baseFee, 2),
            tokenFee: parseUnits(form.tokenFee, 2),
            referralFee: parseUnits(form.referralFee, 2),
            ethCreationFee: parseUnits(form.ethCreationFee, decimals),
            maxPresalePeriod: form.maxPresalePeriod,
            minLockPeriod: form.minLockPeriod,
            minLiqiudityPercent: form.minLiqiudityPercent,
        };
        busy.value = true;
        if (!params.timeLock) form.setError('timeLock', t("Invalid TimeLock Adress"));
        if (!params.feeAddress || params.feeAddress === zeroAddress)
            form.setError('feeAddress', t("Invalid Fee recipient"));
        if (form.hasErrors) return;
        const summary = `Update Factory Settings`;
        await call('updateSettings', [params], null, summary);
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
        updateSettings
    });
};

export const useUpdateLp = (factoryInfo, form) => {
    const { t } = useI18n();
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
    } = useContractCall(factoryInfo.factory_abi, factoryInfo.contract);
    const update = async () => {
        form.clearErrors();
        if (!form.maxLockCycle) form.setError('maxLockCycle', t("Invalid Max lock cycle"));
        if (!form.minCyclePercent) form.setError('minCyclePercent', t("Invalid Min cycle Percent"));
        if (!form.maxLockPeriod) form.setError('maxLockPeriod', t("Invalid Max lock Period"));
        if (form.hasErrors) return;
        const lpParams = {
            maxLockCycle: form.maxLockCycle,
            minCyclePercent: parseUnits(form.minCyclePercent, 2),
            maxLockPeriod: form.maxLockPeriod,
        };
        const summary = `Update factory LP Settings`;
        await call('updateLpSettings', [lpParams], null, summary);
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

