<script setup>
import { useTxLink } from "@/Web3Modal";
const props = defineProps({
    txhash: String,
    len: { type: Number, default: 33 },
});
const [shortTx, etherScanLink] = useTxLink(props.txhash, null, props.len);
</script>
<template>
    <td role="cell">
        <a
            :href="etherScanLink"
            target="_blank"
            class="cursor-pointer select-none font-semibold hover:text-blue-600"
        >
            <slot>{{shortTx}}</slot>
        </a>
    </td>
</template>