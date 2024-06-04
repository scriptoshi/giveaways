<script setup>
import {computed} from "vue";

import {NetworkIcon} from "@/Wagmi/components/Icons";

const props = defineProps({
	src: {
		type: String,
		default:
			"https://s3.ap-northeast-1.amazonaws.com/quest3.xyz/community/714666054149394512.jpeg",
	},
	chainId: {type: [Number, String]},
	sm: Boolean,
});
const size = computed(() =>
	props.sm ? "!w-[40px] !h-[40px] !rounded-sm" : "!w-[72px] !h-[72px] !rounded-sm",
);
const icon = computed(() => (props.sm ? "!w-5 !h-5 -right-1 " : "w-7 h-7 -right-3"));
</script>
<template>
	<div
		:class="size"
		class="relative rounded-2xl"
	>
		<div
			class="rounded-full border border-emerald-500 overflow-hidden flex justify-center items-center"
		>
			<img
				class="block object-cover bg-white"
				:class="size"
				:src="src"
			/>
		</div>
		<NetworkIcon
			v-if="chainId"
			:chainId="chainId"
			:class="icon"
			class="absolute w-7 h-7 bg-white border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-full bottom-0"
		/>
	</div>
</template>
