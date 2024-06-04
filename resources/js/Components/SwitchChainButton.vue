<script setup>
import {computed} from "vue";

import {useAccount, useChains, useSwitchChain} from "use-wagmi";

import {NetworkIcon} from "@/Wagmi/components/Icons/index";

const props = defineProps({
	toChain: [Number, String],
});
const emit = defineEmits(["switched"]);
const {chainId} = useAccount();
const chains = useChains();
const chain = chains.value.find((c) => c.id.toString() === props.toChain.toString());
const {switchChainAsync} = useSwitchChain();
const switchChain = async (chainId) => {
	await switchChainAsync({chainId});
	emit("switched");
};
const shouldSWitch = computed(() => parseInt(chainId.value) !== parseInt(props.toChain));
</script>
<template>
	<button
		type="button"
		@click="switchChain(chain.id)"
		v-if="shouldSWitch"
		:key="chainId"
		class="btn disabled:pointer-events-none disabled:bg-emerald-50/50 disabled:dark:bg-gray-800/50 bg-white dark:bg-gray-800 hover:bg-emerald-300/20 hover:dark:bg-emerald-900/20 rounded-lg py-3 border border-emerald-200 dark:border-emerald-500 transition duration-200 text-emerald-700 dark:text-emerald-400 font-semibold"
	>
		<slot name="switch">
			<NetworkIcon
				:chain-id="toChain"
				class="w-5 h-5 -ml-1 mr-2 inline-block"
			/>{{ $t("Switch to {chain}", {chain: chain?.name}) }}
		</slot>
	</button>
	<slot v-else></slot>
</template>
