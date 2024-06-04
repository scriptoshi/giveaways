import { computed } from "vue";

import { get } from "@vueuse/core";
import { useAccount, useChains } from "use-wagmi";
import { sepolia } from "viem/chains";

import { useChain } from "@/Wagmi/hooks/useChain";
import { isAddress, shortenAddress, truncateTx } from "@/Wagmi/utils/utils";
export function useTxHash(txhash, chId = null, chars = 18) {
    console.log(txhash, chId);
    const { chain } = useAccount();
    const chains = useChains();
    const activeChain = computed(() => chains.value.find(c => c.id.toString() === chId?.toString?.()));
    const shortTx = computed(() => {
        if (!get(txhash) || get(txhash).length < 30) return "";
        return truncateTx(get(txhash), chars);
    });

    const etherScanLink = computed(() => {
        if (!get(txhash) || get(txhash).length < 30) return "";
        return useChain(activeChain.value ?? chain.value ?? sepolia).getTxUrl(get(txhash));
    });
    return [shortTx, etherScanLink];
}

export function useAddress(txAddress, chId = null, chars = 4) {
    const { chainId } = useAccount();
    const chains = useChains();
    const activeChain = computed(() => {
        if (get(chId))
            return chains.value.find(c => c.id.toString() === get(chId)?.toString?.());
        return chains.value.find(c => c.id.toString() === chainId.value?.toString?.());
    });
    const shortTx = computed(() => {
        const address = isAddress(get(txAddress));
        if (!address) return "";
        return shortenAddress(address, chars);
    });
    const etherScanLink = computed(() => {
        const address = isAddress(get(txAddress));
        if (!address || (!activeChain.value)) return "#";
        return useChain(activeChain.value).getAccountUrl(get(txAddress));

    });
    return [shortTx, etherScanLink];
}

