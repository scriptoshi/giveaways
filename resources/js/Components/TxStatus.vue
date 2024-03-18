<script setup>
defineProps({state: Object});
</script>
<template>
	<p
		v-if="state.error"
		class="col-span-3 mt-1 text-red text-xs font-semibold"
	>
		{{ state.error }}
	</p>
	<p
		v-else
		class="col-span-3 text-xs font-semibold"
	>
		<span :class="{'text-green-500': state.isConfirmed}">{{ state.status }}</span>
		<a
			:href="state.etherScanLink"
			class="ml-2 mt-1 text-emerald-500"
			target="_blank"
			v-if="state.txhash"
			>{{ state.shortTx }}</a
		>
	</p>
</template>
