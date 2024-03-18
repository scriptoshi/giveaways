
import { reactive, ref } from "vue";


import {
    getPublicClient,
} from '@wagmi/core';
import { useConfig } from "use-wagmi";
import { formatUnits, parseUnits } from "viem";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";


export const useTaxTokenAdmin = (token, form) => {
    const decimals = ref(0);
    const symbol = ref('');
    const totalSupply = ref(0);
    const swapAndLiquifyEnabled = ref(false);
    const liquidityFee = ref(0);
    const taxFee = ref(0);
    const maxTxAmount = ref(0);
    const config = useConfig();
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
    } = useContractCall(token.factory.abi, token.contract);
    const update = async () => {
        const client = getPublicClient(config, { chainId: parseInt(token.chainId) });
        const calls = [
            {
                contract: { abi: token.factory.abi, address: token.contract },
                swapAndLiquifyEnabled: [],
                _liquidityFee: [],
                _taxFee: [],
                _maxTxAmount: [],
                decimals: [],
                symbol: [],
                totalSupply: [],
            }
        ];
        const [results] = await multicall([calls], { client }).catch(e => {
            console.log(e);
            return [];
        });
        decimals.value = results[0].decimals;
        symbol.value = results[0].symbol;
        totalSupply.value = formatUnits(results[0].totalSupply, decimals.value);
        taxFee.value = results[0]._taxFee;
        liquidityFee.value = results[0]._liquidityFee;
        swapAndLiquifyEnabled.value = results[0].swapAndLiquifyEnabled;
        maxTxAmount.value = (parseFloat(formatUnits(results[0]._maxTxAmount, decimals.value)) * 100) / parseFloat(totalSupply.value);
        form.swapAndLiquifyEnabled = swapAndLiquifyEnabled.value;
        form.liquidityFee = liquidityFee.value;
        form.taxFee = taxFee.value;
        form.maxTxAmount = maxTxAmount.value;
    };

    const excludeFromFee = async () => {
        form.clearErrors();
        if (!isAddress(form.address)) form.setError("address", "Address is Invalid");
        if (form.hasErrors) return;
        await call('excludeFromFee', [form.address], null, "Exclude from fees");
        update();
    };
    const includeInFee = async () => {
        form.clearErrors();
        if (!isAddress(form.address)) form.setError("address", "Address is Invalid");
        if (form.hasErrors) return;
        await call('includeInFee', [form.address], null, "Include in fees");
        update();
    };
    const includeInReward = async () => {
        form.clearErrors();
        if (!isAddress(form.address)) form.setError("address", "Address is Invalid");
        if (form.hasErrors) return;
        await call('includeInReward', [form.address], null, "Include from reward");
        update();
    };
    const excludeFromReward = async () => {
        form.clearErrors();
        if (!isAddress(form.address)) form.setError("address", "Address is Invalid");
        if (form.hasErrors) return;
        await call('excludeFromReward', [form.address], null, "Exclude from reward");
        update();
    };

    const setTaxFeePercent = async () => {
        form.clearErrors();
        if (parseInt(form.taxFee) < 0) form.setError("taxFee", "Tax fee cannot be negative!");
        if (form.hasErrors) return;
        await call('setTaxFeePercent', [form.taxFee], null, "Set Tax Fee");
        update();
    };

    const setLiquidityFeePercent = async () => {
        form.clearErrors();
        if (parseInt(form.liquidityFee) < 0) form.setError("liquidityFee", "Liquidity fee cannot be negative!");
        if (form.hasErrors) return;
        await call('setLiquidityFeePercent', [form.liquidityFee], null, "set Liquidity Fee Percent");
        update();
    };

    const setMaxTxAmount = async () => {
        form.clearErrors();
        if (parseInt(form.maxTxAmount) < 0) form.setError("liquidityFee", "Liquidity fee cannot be negative!");
        if (form.hasErrors) return;
        const percent = form.maxTxAmount === 0
            ? 0
            : ((form.maxTxAmount * 100) / totalSupply.value);
        console.log(percent, totalSupply.value, form.maxTxAmount);
        const maxTxPercent = parseUnits(percent.toString(), 2);
        await call('setMaxTxPercent', [maxTxPercent], null, "Set Max Tx Amount");
        update();
    };

    const setSwapAndLiquifyEnabled = async () => {
        await call('setSwapAndLiquifyEnabled', [form.swapAndLiquifyEnabled], null, form.swapAndLiquifyEnabled ? "Enable swap and Liquify" : "Disable swap and Liquify");
        update();
    };
    return reactive({
        /// shared
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
        /// local
        decimals,
        symbol,
        totalSupply,
        swapAndLiquifyEnabled,
        liquidityFee,
        taxFee,
        maxTxAmount,
        update,
        call,
        excludeFromFee,
        includeInReward,
        includeInFee,
        setLiquidityFeePercent,
        setMaxTxAmount,
        setTaxFeePercent,
        excludeFromReward,
        setSwapAndLiquifyEnabled
    });
};

