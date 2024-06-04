import { computed } from "vue";

import { usePage } from "@inertiajs/vue3";
import { useChainId as usStateChainId, useAccount } from "use-wagmi";
export const useChainId = () => {
    const AuthCheck = computed(() => usePage().props.AuthCheck);
    const stateChainId = usStateChainId();
    const { chainId: connectedChainId } = useAccount();
    const chainId = computed(() => AuthCheck.value ? connectedChainId.value : stateChainId.value);
    return { AuthCheck, chainId };
};