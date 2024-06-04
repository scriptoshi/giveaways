<script setup>
import {computed} from "vue";

import {usePage} from "@inertiajs/vue3";

import chains from "@/Pages/Projects/Create/ChainSelect/chains.json";
const props = defineProps({
	chainId: String,
});
const chainIds = computed(() => usePage().props.chainIds);
const chain = computed(() =>
	chains.find((c) => {
		const indx = chainIds.value[parseInt(props.chainId)];
		if (indx) return c.id === indx.toLowerCase();
		return c.id === props.chainId.toLowerCase();
	}),
);
</script>
<template>
	<slot :chain="chain" />
</template>
