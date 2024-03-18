import { computed } from "vue";

import { get } from "@vueuse/core";
import { Chain } from "sushi/chain";
import { useAccount } from "use-wagmi";

import { isAddress, shortenAddress, truncateTx } from "@/Wagmi/utils/utils";

export function useTxHash(txhash, chId = null, chars = 18) {
    const { chain } = useAccount();
    const shortTx = computed(() => {
        if (!get(txhash) || get(txhash).length < 30) return "";
        return truncateTx(get(txhash), chars);
    });

    const etherScanLink = computed(() => {
        if (!get(txhash) || get(txhash).length < 30) return "";
        return Chain.from(chId ?? chain?.value?.id ?? 5).getTxUrl(get(txhash));
    });
    return [shortTx, etherScanLink];
}

export function useAddress(txAddress, chId = null, chars = 4) {
    const { chain } = useAccount();
    const shortTx = computed(() => {
        const address = isAddress(get(txAddress));
        if (!address) return "";
        return shortenAddress(address, chars);
    });
    const etherScanLink = computed(() => {
        const address = isAddress(get(txAddress));
        if (!address) return "";
        return Chain.from(chId ?? chain?.value?.id ?? 5).getAccountUrl(get(txAddress));
    });
    return [shortTx, etherScanLink];
}

