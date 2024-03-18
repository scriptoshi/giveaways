<script setup>
import { useAddress } from "@/Web3Modal";
const props = defineProps({
    address: String,
    len: { type: Number, default: 12 },
});
const [shortAddress, etherScanLink] = useAddress(
    props.address,
    null,
    props.len
);
</script>
<template>
    <td role="cell">
        <a
            :href="etherScanLink"
            target="_blank"
            class="cursor-pointer select-none font-semibold hover:text-blue-600"
        >
            <slot>{{shortAddress}}</slot>
        </a>
    </td>
</template>