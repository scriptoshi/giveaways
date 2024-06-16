<script setup>
import {computed} from "vue";

import {NetworkIcon} from "@/Web3Modal/components/Icons";
const props = defineProps({
	src0: {
		type: String,
		default:
			"https://s3.ap-northeast-1.amazonaws.com/quest3.xyz/community/714666054149394512.jpeg",
	},
	src0Text: {
		type: String,
		default: "GAS",
	},
	src1: {
		type: String,
		default:
			"https://s3.ap-northeast-1.amazonaws.com/quest3.xyz/community/714666054149394512.jpeg",
	},
	src1Text: {
		type: String,
		default: "DREAM",
	},
	chainId: {type: [Number, String]},
	sm: Boolean,
});
const size = computed(() =>
	props.sm ? "!w-[40px] !h-[40px] !rounded-sm" : "!w-[72px] !h-[72px] !rounded-sm",
);
const hgt = computed(() => (props.sm ? "!h-[40px]" : "!h-[72px]"));
const icon = computed(() => (props.sm ? "!w-5 !h-5 -right-1 " : "w-7 h-7 -right-3"));
</script>
<template>
	<div class="relative rounded-2xl">
		<div
			:class="hgt"
			class="flex flex-wrap -space-x-2"
		>
			<img
				v-if="src0"
				:src="src0"
				:class="size"
				alt="ALP"
			/>
			<div
				v-else
				:class="size"
				class="avatar hover:z-10"
			>
				<div
					class="is-initial rounded-full font-bold bg-success text-xs text-[10px] uppercase text-white ring ring-white dark:ring-navy-700"
				>
					{{ src0Text }}
				</div>
			</div>
			<img
				v-if="src1"
				:src="src1"
				:class="size"
				alt="ALP"
			/>
			<div
				v-else
				:class="size"
				class="avatar hover:z-10"
			>
				<div
					class="is-initial rounded-full font-bold bg-success text-xs text-[10px] uppercase text-white ring ring-white dark:ring-navy-700"
				>
					{{ src1Text }}
				</div>
			</div>
		</div>
		<NetworkIcon
			v-if="chainId"
			:chainId="chainId"
			:class="icon"
			class="absolute w-7 h-7 bg-white border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-full bottom-0"
		/>
	</div>
</template>
