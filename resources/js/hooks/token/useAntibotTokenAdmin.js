
import { reactive, ref, watch } from "vue";

import { useForm } from "@inertiajs/vue3";
import {
    getPublicClient,
} from '@wagmi/core';
import { DateTime } from "luxon";
import { useAccount, useConfig } from "use-wagmi";
import { formatUnits, parseUnits } from "viem";
import { useI18n } from "vue-i18n";

import antiBotAbi from "@/foundries/AntiBot.json";
import { useContractCall } from "@/hooks/contracts/useContractCall";
import { multicall } from "@/Wagmi/hooks/multicall";
export const useAntibotTokenAdmin = (token, antibotAddress, form) => {
    const delay = ref();
    const stopTime = ref(parseInt(DateTime.now().plus({ days: 3 }).toJSDate()));
    const stopInDays = ref(0);
    const increment = ref(null);
    const maxPerTrade = ref(null);
    const whitelist = ref([]);
    const antiBot = ref(0);
    const config = useConfig();
    const { address } = useAccount();
    const { t } = useI18n();
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
        called,
        call,
    } = useContractCall(antiBotAbi, antibotAddress);
    const update = async () => {
        const client = getPublicClient(config, { chainId: parseInt(token.chainId) });
        const wl = token?.whitelist?.map(w => w.address)?.reduce?.((memo, white) => {
            memo[white] = {
                functionName: 'tokenWhitelist',
                args: [token.contract, white]
            };
            return memo;
        }, { contract: { abi: antiBotAbi, address: antibotAddress } });
        const calls = [
            {
                contract: { abi: antiBotAbi, address: antibotAddress },
                admins: [token.contract],
                delay: [token.contract],
                stopTime: [token.contract],
                maxPerTrade: [token.contract],
                increment: [token.contract],
            },
            !!wl && wl,
        ];

        const [results] = await multicall([calls], { client }).catch(e => {
            console.log(e);
            return [];
        });
        console.log(results[0]);
        delay.value = results[0]?.delay;
        stopTime.value = parseInt(results[0]?.stopTime) > 0 ? parseInt(results[0]?.stopTime) : parseInt(DateTime.now().plus({ days: 3 }).toSeconds());
        stopInDays.value = (DateTime.fromSeconds(parseInt(stopTime.value)).diffNow('days').days).toFixed(4) * 1;
        maxPerTrade.value = formatUnits(results[0]?.maxPerTrade ?? '0', token.decimals);
        increment.value = formatUnits(results[0]?.increment ?? '0', token.decimals);
        console.log(stopTime.value);
        form.delay = delay.value;
        form.stopTime = stopTime.value;
        form.stopInDays = stopInDays.value;
        form.maxPerTrade = maxPerTrade.value;
        form.increment = increment.value;
        const wls = results[1];
        whitelist.value = Object.keys(wls).filter(w => parseInt(wls[w]) === 1) ?? [];
    };
    watch(address, (address) => {
        if (address) {
            update();
        }
    });


    const updateAntiBotSettings = async () => {
        form.clearErrors();
        if (parseInt(form.delay) > 600) form.setError("delay", "Delay must be less than 10 minutes");
        if (form.hasErrors) return;
        const delayFormated = parseInt(form.delay).toString();
        const stopTimeFormated = parseInt(form.stopInDays).toString() ?? '0';
        const maxPerTradeFormated = parseUnits(form.maxPerTrade.toString() ?? '0', token.decimals);
        const incrementFormated = parseUnits(form.increment.toString() ?? '0', token.decimals);
        await call(
            "update",
            [
                token.contract,
                delayFormated,
                stopTimeFormated,
                incrementFormated,
                maxPerTradeFormated,
            ],
            null,
            t("Update Antibot Settings"),
        );
        update();
    };

    const unTokenWhiteList = async () => {
        form.clearErrors();
        if (form.addressList.length === 0)
            form.setError("addressList", t("Please enter some valid addresses"));
        if (form.hasErrors) return;
        await call(
            "unTokenWhiteList",
            [token.contract, form.addressList],
            null,
            t("Remove Antibot Whitelist"),
        );
        update();
        if (!error.value)
            useForm({ list: form.addressList }).put(window.route('tokens.whitelist.remove', token));
    };


    const addTokenWhiteList = async () => {
        form.clearErrors();
        if (form.addressList.length === 0)
            form.setError("addressList", t("Please enter some valid addresses"));
        if (form.hasErrors) return;
        await call(
            "addTokenWhiteList",
            [token.contract, form.addressList],
            null,
            t("Add Antibot Whitelist"),
        );
        update();
        if (!error.value)
            useForm({ list: form.addressList }).put(window.route('tokens.whitelist.add', token));
    };
    return reactive({
        whitelist,
        antiBot,
        antibotAddress,
        delay,
        stopTime,
        stopInDays,
        increment,
        maxPerTrade,
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
        called,
        call,
        update,
        addTokenWhiteList,
        unTokenWhiteList,
        updateAntiBotSettings,
    });
};
