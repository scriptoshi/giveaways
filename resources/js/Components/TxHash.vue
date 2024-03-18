<script setup>
import {useTxHash} from "@/Wagmi/hooks/useTxHash";
const props = defineProps({
	txhash: String,
	len: {type: Number, default: 33},
});
const [shortTx, etherScanLink] = useTxHash(props.txhash, null, props.len);
</script>
<template>
	<td role="cell">
		<a
			:href="etherScanLink"
			target="_blank"
			class="cursor-pointer select-none font-semibold hover:text-blue-600"
		>
			<slot>{{ shortTx }}</slot>
		</a>
	</td>
</template>
