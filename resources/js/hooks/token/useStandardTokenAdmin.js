
import { reactive, ref, watch } from "vue";

import {
    getPublicClient,
} from '@wagmi/core';
import { useAccount, useConfig } from "use-wagmi";
import { formatUnits, keccak256, parseUnits, toHex, zeroHash } from "viem";
import { useI18n } from "vue-i18n";

import { useContractCall } from "@/hooks/contracts/useContractCall";
import { multicall } from "@/Wagmi/hooks/multicall";
import { isAddress } from "@/Wagmi/utils/utils";
export const useStandardTokenAdmin = (token, form) => {
    const hasMint = token.contract_name?.toLowerCase?.().includes?.("capped") ?? false;
    const hasAntibot = token.contract_name?.toLowerCase?.().includes?.("antibot") ?? false;
    const hasPause = token.contract_name?.toLowerCase?.().includes?.("pausable") ?? false;
    const adminRole = zeroHash;
    const minterRole = keccak256(toHex('MINTER_ROLE'));
    const pauserRole = keccak256(toHex('PAUSER_ROLE'));
    const roles = [
        { name: 'Admin Role', value: adminRole },
        { name: 'Pauser Role', value: minterRole },
        { name: 'Minter Role', value: pauserRole },
    ];
    const antibotAddress = ref();
    const user = ref(null);
    const decimals = ref(18);
    const symbol = ref('');
    const totalSupply = ref(0);
    const paused = ref(0);
    const hasAdminRole = ref(0);
    const hasMinterRole = ref(0);
    const hasPauserRole = ref(0);
    const cap = ref(0);
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
    } = useContractCall(token.factory.abi, token.contract);
    const update = async () => {
        const client = getPublicClient(config, { chainId: parseInt(token.chainId) });
        const calls = [
            {
                contract: { abi: token.factory.abi, address: token.contract },
                ...(hasMint || hasPause) && { hasAdminRole: { functionName: 'hasRole', args: [adminRole, address.value] } },
                ...hasMint && { hasMinterRole: { functionName: 'hasRole', args: [minterRole, address.value] } },
                ...hasPause && { hasPauserRole: { functionName: 'hasRole', args: [pauserRole, address.value] } },
                ...hasPause && { paused: [] },
                ...hasMint && { cap: [] },
                ...hasAntibot && { antiBot: [] },
                decimals: [],
                symbol: [],
                totalSupply: [],
                antiBot: [],
            },
        ];
        const [results] = await multicall([calls], { client }).catch(e => {
            console.log(e);
            return [];
        });
        decimals.value = results[0].decimals;
        symbol.value = results[0].symbol;
        totalSupply.value = results[0].totalSupply ? formatUnits(results[0].totalSupply.toString(), decimals.value) : null;
        if (hasPause) {
            paused.value = results[0].paused;
            hasPauserRole.value = results[0].hasPauserRole;
            hasAdminRole.value = results[0].hasAdminRole;
        }
        if (hasMint) {
            cap.value = results[0].cap;
            hasMinterRole.value = results[0].hasMinterRole;
            hasAdminRole.value = results[0].hasAdminRole;
        }
        if (hasAntibot)
            antibotAddress.value = results[0].antiBot;
    };
    watch(address, (address) => {
        if (address) {
            update();
        }
    });

    const grantRole = async () => {
        form.clearErrors();
        if (!isAddress(user.value)) form.setError("user", t("Invalid Grant address"));
        if (!form.role) form.setError("role", t("Please Select  A role"));
        if (form.hasErrors) return;
        await call("grantRole", [form.role, user.value], null, t("Grant Role"));
        update();
    };
    const revokeRole = async () => {
        form.clearErrors();
        if (!isAddress(user.value)) form.setError("user", t("Invalid Revoke address"));
        if (!form.role) form.setError("role", t("Please Select  A role"));
        if (form.hasErrors) return;
        await call("revokeRole", [form.role, user.value], null, t("Revoke Role"));
        update();
    };
    const renounceRole = async () => {
        await call("renounceRole", [form.role, address.value], null, t("Renounce Role"));
        update();
    };

    const pause = async () => {
        await call("pause", [], null, t("Pause Contract"));
        update();
    };

    const unpause = async () => {
        await call("unpause", [], null, t("Unpause Contract"));
        update();
    };
    const mint = async () => {
        form.clearErrors();
        if (!isAddress(form.minto)) form.setError("minto", t("Invalid mint to address"));
        if (form.amount <= 0) form.setError("amount", t("Invalid mint amount"));
        if (form.hasErrors) return;
        const amt = parseUnits(form.amount, decimals);
        await call("mint", [form.minto, amt], null, t("Mint Tokens"));
        update();
    };

    return reactive({
        hasAdminRole,
        hasPauserRole,
        hasMinterRole,
        adminRole,
        minterRole,
        pauserRole,
        decimals,
        symbol,
        totalSupply,
        paused,
        cap,
        antibotAddress,
        user,
        roles,
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
        grantRole,
        mint,
        revokeRole,
        renounceRole,
        pause,
        unpause
    });
};
