<script setup>
import {computed} from "vue";

import {useChains} from "use-wagmi";

import {NetworkIcon} from "@/Wagmi/components/Icons";

const props = defineProps({
	chainId: [Number, String],
});
const chains = useChains();
const chain = computed(() =>
	chains.value.find((c) => c.id.toString() === props.chainId.toString()),
);
</script>
<template>
	<div
		v-if="chain?.id"
		class="flex flex-row align-middle items-center"
	>
		<NetworkIcon
			class="w-5 h-5 rounded-full inline-table"
			:chain-id="chain.id"
		/>
		<span class="text-emerald-600 ml-3 dark:text-emerald-400 font-bold text-xs hidden sm:flex"
			><slot>{{ chain.name }}</slot></span
		>
	</div>
</template>
