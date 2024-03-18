<script setup>
import {useAddress} from "@/Wagmi/hooks/useTxHash";

const props = defineProps({
	address: String,
	chainId: Number,
});
const [shortAddress, etherScanLink] = useAddress(props.address, props.chainId);
</script>
<template>
	<a
		:href="etherScanLink"
		target="_blank"
		class="cursor-pointer select-none font-semibold hover:text-blue-600"
	>
		<slot>{{ shortAddress }}</slot>
	</a>
</template>
