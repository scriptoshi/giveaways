import { computed, reactive, ref } from "vue";

import { router as Inertia } from "@inertiajs/vue3";
import {
    getPublicClient,
} from '@wagmi/core';
import { DateTime } from "luxon";
import { useChains, useConfig } from "use-wagmi";
import { formatEther, formatUnits, parseEther, parseEventLogs, parseUnits, zeroAddress } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import tokenAbi from "@/Wagmi/constants/erc20.json";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";

export const useDeployLaunchpad = (
    factory,
    form,
    isFairLiquidity,
    type
) => {
    console.log(factory);
    const {
        error,
        busy,
        txhash,
        status,
        shortTx,
        etherScanLink,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        called,
        call,
    } = useContractCall(factory?.factory_abi, factory?.contract);


    const { t } = useI18n();

    const chains = useChains();
    const factoryChain = computed(() => chains.value.find(c => c.id.toString() === factory.chainId.toString()));
    const baseToken = reactive({
        contract: zeroAddress,
        name: factoryChain.value?.nativeCurrency?.name,
        symbol: factoryChain.value?.nativeCurrency?.symbol,
        decimals: factoryChain.value?.nativeCurrency?.decimals,
    });
    const baseDecimals = ref(18);
    const feeToken = reactive({
        contract: zeroAddress,
        name: factoryChain.value?.nativeCurrency?.name,
        symbol: factoryChain.value?.nativeCurrency?.symbol,
        decimals: factoryChain.value?.nativeCurrency?.decimal,
    });
    const timeLock = ref(null);
    const feeAddress = ref(null);
    const baseOnlyFee = ref('0');
    const baseFee = ref('0');
    const tokenFee = ref('0');
    const referralFee = ref('0');
    const ethCreationFee = ref(null);
    const maxPresalePeriod = ref(null);
    const minLockPeriod = ref(null);
    const minLiqiudityPercent = ref(null);
    const maxLockCycle = ref(null);
    const minCyclePercent = ref(null);
    const maxLockPeriod = ref(null);
    const config = useConfig();
    const update = async () => {
        // reset
        baseToken.contract = zeroAddress;
        baseToken.name = factoryChain.value?.nativeCurrency?.name;
        baseToken.symbol = factoryChain.value?.nativeCurrency?.symbol;
        baseToken.decimals = factoryChain.value?.nativeCurrency?.decimals;
        feeToken.contract = zeroAddress;
        feeToken.name = factoryChain.value?.nativeCurrency?.name;
        feeToken.symbol = factoryChain.value?.nativeCurrency?.symbol;
        feeToken.decimals = factoryChain.value?.nativeCurrency?.decimals;
        // client should load contract even when wallect chain is not the factory chain
        const client = getPublicClient(config, { chainId: parseInt(factory.chainId) });
        const calls = [
            {
                contract: { abi: factory?.factory_abi, address: factory?.contract },
                feeToken: [],
                getSettings: [],
                ...isFairLiquidity ? { getLpSettings: [] } : {},
            },
            ...isAddress(form.baseToken) && form.baseToken !== zeroAddress
                ? [{
                    contract: { abi: tokenAbi, address: form.baseToken },
                    name: [],
                    symbol: [],
                    decimals: []
                }]
                : [],

        ];
        const [results] = await multicall([calls], { client }).catch(e => {
            console.log(e);
            return [];
        });
        // feeToken
        if (results[0]?.feeToken !== zeroAddress) {
            const tokencalls = [
                {
                    contract: { abi: tokenAbi, address: results[0]?.feeToken },
                    name: [],
                    symbol: [],
                    decimals: []
                }
            ];
            const [resp] = await multicall([tokencalls], { client }).catch(e => {
                console.log(e);
                return [];
            });
            feeToken.contract = results[0]?.feeToken;
            feeToken.name = resp[0]?.name;
            feeToken.symbol = resp[0]?.symbol;
            feeToken.decimals = resp[0]?.decimals;
        }
        // baseToken
        if (isAddress(form.baseToken) && form.baseToken !== zeroAddress) {
            baseToken.contract = isAddress(form.baseToken);
            baseToken.name = results[1]?.name;
            baseToken.symbol = results[1]?.symbol;
            baseToken.decimals = results[1]?.decimals;
        }
        const settings = results[0]?.getSettings;
        timeLock.value = settings?.timeLock;
        feeAddress.value = settings?.feeAddress;
        baseOnlyFee.value = settings?.baseOnlyFee;
        baseFee.value = settings?.baseFee;
        tokenFee.value = settings?.tokenFee;
        referralFee.value = settings?.referralFee;
        baseDecimals.value = results[1]?.baseDecimals;
        ethCreationFee.value = settings?.ethCreationFee;
        maxPresalePeriod.value = settings?.maxPresalePeriod;
        minLockPeriod.value = settings?.minLockPeriod;
        minLiqiudityPercent.value = settings.minLiqiudityPercent;
        if (isFairLiquidity) {
            const lpsettings = results[0]?.getLpSettings;
            maxLockCycle.value = lpsettings.maxLockCycle;
            minCyclePercent.value = formatUnits(lpsettings.minCyclePercent, 2);
            maxLockPeriod.value = lpsettings.maxLockPeriod;
        }
    };

    const loadPresale = async (clone) => {
        const client = getPublicClient(config, { chainId: parseInt(factory.chainId) });
        const calls = [
            {
                contract: { abi: factory.abi, address: clone },
                presaleAddress: clone,
                univ2factory: { functionName: 'uniFactory', args: [] },
                presaleInfo: [],
                status_code: { functionName: 'presaleStatus', args: [] },
                settings: [],
                lock: [],
                timeLock: [],
                presaleInNative: [],
                saleType: [],
            }
        ];
        const [results] = await multicall([calls], { client }).catch(e => {
            console.log(e);
            return [];
        });
        return results[0];
    };


    const deploy = async () => {
        status.value = t("Deploying Launchpad contract");
        error.value = null;
        form.clearErrors();
        const router = isAddress(form.router.router);
        if (!isAddress(form.presaleOwner)) form.setError('presaleOwner', t('Invalid Owner Address'));
        if (isAddress(form.presaleOwner) === zeroAddress) form.setError('presaleOwner', t('Invalid Owner Address'));
        if (!form.maxSpendPerBuyer) form.setError('maxSpendPerBuyer', t('Invalid Max Token Purchase'));
        if (!form.minSpendPerBuyer) form.setError('minSpendPerBuyer', t('Invalid Min Spending Amount'));
        if (!form.softcap) form.setError('softcap', t('Invalid Softcap'));
        if (form.isFairLaunch) {
            if (!form.total) form.setError('total', t('Invalid Token amount'));
            if (form.total <= 0) form.setError('total', t('Token amount is Low'));
        } else {
            if (!form.tokenPrice) form.setError('tokenPrice', t('Invalid token price'));
            if (!form.hardcap) form.setError('hardcap', t('Invalid Hardcap'));
            if (parseEther(form.listingRate ?? '0').gt(parseEther(form.tokenPrice ?? '0'))) form.setError('listingRate', t('Must be less than Token Price'));
            if (parseEther(form.softcap).gt(parseEther(form.hardcap))) form.setError('softcap', t('Must be less than hardcap'));
            if ((parseEther(form.softcap)).mul(2).lt(parseEther(form.hardcap))) form.setError('softcap', t('Must be more than 50% of hardcap'));
        }
        if (form.isLpLaunch) {
            if ((parseFloat(form.totalSupply) / 2) < parseFloat(form.total)) form.setError('total', t('Must be Greater than Half TotalSupply'));
            if (form.total <= 0) form.setError('total', t('Token amount is Low'));
        }
        if (form.isPrivateSale) {
            if (form.participants.map(p => !!p).length === 0) form.setError('participants', t('Please Add some participants'));
        }
        if (parseEther(form.minSpendPerBuyer).gt(parseEther(form.maxSpendPerBuyer))) form.setError('maxSpendPerBuyer', t('Must be More than Min Purchase Amount'));
        if (!form.liquidityPercent) form.setError('liquidityPercent', t('Liquidity Percent is required'));
        if (parseFloat(form.liquidityPercent) > 100) form.setError('liquidityPercent', t('Liquidity Percent  must be less than 100'));
        if (parseFloat(form.liquidityPercent) < 60) form.setError('liquidityPercent', t('Liquidity Percent must be greater than 60'));
        if (parseFloat(form.presalePeriod) > maxPresalePeriod.value) form.setError('presalePeriod', t('Max period of {max} days allowed', { max: maxPresalePeriod.value }));
        // if (parseFloat(form.lockPeriod) < minLockPeriod.value) form.setError('lockPeriod', t('Min {min} days allowed', { min: minLockPeriod.value }))
        if (!form.startTime) form.setError('startTime', t('Please provide a start date'));
        if (DateTime.fromJSDate(form.startTime) < DateTime.now().plus({ day: 1 })) form.setError('startTime', t('Must start after tommorow'));
        if (form.hasErrors) return error.value = t('Some errors were detected');
        busy.value = true;
        const presaleParams = {
            burn: form.burn,
            presaleOwner: isAddress(form.presaleOwner),
            presaleToken: isAddress(form.contract),
            baseToken: form.baseToken === zeroAddress ? zeroAddress : isAddress(form.baseToken),
            maxSpendPerBuyer: parseUnits(form.maxSpendPerBuyer, baseToken.decimals),
            minSpendPerBuyer: parseUnits(form.minSpendPerBuyer, baseToken.decimals),
            ...(form.isFairLaunch) ? {
                isLpLaunch: form.isLpLaunch ?? false,
                total: parseUnits(form.total.toString(), form.decimals),
            } : {
                ...form.isPrivateSale && {
                    merkleRoot: form.merkleRoot,
                },
                hardcap: parseUnits(form.hardcap ?? "0", baseToken.decimals),
                tokenPrice: parseUnits(form.tokenPrice ?? "0", form.decimals),
                listingRate: parseUnits(form.listingRate ?? "0", form.decimals),
            },
            softcap: parseUnits(form.softcap, baseToken.decimals),
            liquidityPercent: (parseFloat(form.liquidityPercent) * 100),
            startTime: parseInt(DateTime.fromJSDate(form.startTime).toSeconds()),
            presalePeriod: form.presalePeriod
        };
        const LockSettings = {
            timeLock: timeLock.value,
            lockPeriod: form.lockPeriod,
            initialPercent: form.useVesting ? (parseFloat(form.initialPercent) * 100).toFixed() : '0',
            cycle: form.useVesting ? form.cycle : '0',
            cyclePercent: form.useVesting ? (parseFloat(form.cyclePercent) * 100).toFixed() : '0'
        };
        await update();
        await call('createPresale', [
            router,
            presaleParams,
            LockSettings,
            form.feeInBase
        ],
            feeToken.contract === zeroAddress ? ethCreationFee.value : 0,
            t('Deploy Token Launchpad')
        );
        const logs = parseEventLogs({
            abi: factory?.factory_abi,
            logs: receipt.value.logs,
            eventName: 'CLONE'
        });
        const event = {
            creator: isAddress(logs[0].creator),
            owner: isAddress(logs[0].owner),
            token: logs[0].token,
            clone: isAddress(logs[0].clone),
            contract: isAddress(logs[0].clone),
            txhash: receipt.transactionHash,
            block: receipt.blockNumber,
            fee: formatEther(logs[0].fees ?? logs[0].fee ?? '0'),
            timestamp: DateTime.fromSeconds(
                parseInt(logs[0].timestamp)
            ).toSQLDate(),
        };
        status.value = t('Now Saving Token ...');
        const presaleInfo = await loadPresale(event.clone);
        Inertia.post(window.route('launchpads.store'), {
            ...presaleInfo,
            ...event,
            whitelist: form.participants,
            amm_id: form.router.id,
            factory_id: form.factory_id,
            token: form.token,
            token_name: form.token_name,
            token_symbol: form.token_symbol,
            token_decimals: form.token_decimals,
            token_supply: form.token_supply,
            logo_uri: form.logo_uri,
            upload_logo: form.upload_logo,
            type,
        }, {
            preserveScroll: false,
            preserveState: false,
        });
    };
    const result = reactive({
        timeLock,
        feeAddress,
        baseOnlyFee,
        baseFee,
        tokenFee,
        referralFee,
        ethCreationFee,
        maxPresalePeriod,
        minLockPeriod,
        minLiqiudityPercent,
        baseDecimals,
        maxLockCycle,
        minCyclePercent,
        maxLockPeriod,
        busy,
        error,
        status,
        feeToken,
        tx: shortTx,
        txlink: etherScanLink,
        shortTx,
        etherScanLink,
        txhash,
        confirming,
        isConfirmed,
        simulation,
        receipt,
        called,
        call,
        update,
        deploy,
    });
    return result;
};
