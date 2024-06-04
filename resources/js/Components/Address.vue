<script setup>
import {useAddress} from "@/Wagmi/hooks/useTxHash";
const props = defineProps({
	address: String,
	len: {type: Number, default: 8},
	chainId: Number,
});

const [shortAddress, etherScanLink] = useAddress(
	props.address,
	props.chainId ?? null,
	props.len ?? 33,
);
</script>
<template>
	<a
		:href="etherScanLink"
		target="_blank"
		rel="noopener noreferrer nofollow"
		class="cursor-pointer select-none font-semibold hover:text-blue-600"
	>
		<slot>{{ shortAddress }}</slot>
	</a>
</template>
