<script setup>
import {computed} from "vue";

import {useCountDown} from "@/hooks/useCountDown";
const props = defineProps({
	timestamp: Number,
	simple: Boolean,
});
const time = computed(() => parseInt(props.timestamp) * 1000);
const {days, hours, minutes, seconds} = useCountDown(time);
</script>
<template>
	<div class="grid grid-cols-4 gap-5 text-center">
		<div
			class="flex flex-col border border-gray-400 dark:border-gray-600 bg-white/60 dark:bg-gray-700/30 rounded-md p-1.5"
		>
			<span
				:class="parseInt(days) > 100 ? '' : 'countdown'"
				class="font-mono font-semibold text-gray-600 dark:text-gray-200 text-sm md:text-xl flex justify-center"
			>
				<span :style="{'--value': days ?? '00'}">{{
					parseInt(days) > 100 ? days : ""
				}}</span>
			</span>
			<span
				class="text-xs"
				v-if="simple"
				>{{ $t("days") }}</span
			>
			<span v-else>{{ $t("days") }}</span>
		</div>
		<div
			class="flex flex-col border border-gray-400 dark:border-gray-600 bg-white/60 dark:bg-gray-700/30 rounded-md p-2"
		>
			<span
				class="countdown font-mono font-semibold text-gray-600 dark:text-gray-200 text-sm md:text-xl flex justify-center"
			>
				<span :style="{'--value': hours ?? '00'}"></span>
			</span>
			<span
				class="text-xs"
				v-if="simple"
				>{{ $t("hours") }}</span
			>
			<span v-else>{{ $t("hours") }}</span>
		</div>
		<div
			class="flex flex-col border border-gray-400 dark:border-gray-600 bg-white/60 dark:bg-gray-700/30 rounded-md p-2"
		>
			<span
				class="countdown font-mono font-semibold text-gray-600 dark:text-gray-200 text-sm md:text-xl flex justify-center"
			>
				<span :style="{'--value': minutes ?? '00'}"></span>
			</span>
			<span
				class="text-xs"
				v-if="simple"
				>{{ $t("min") }}</span
			>
			<span v-else>{{ $t("min") }}</span>
		</div>
		<div
			class="flex flex-col border border-gray-400 dark:border-gray-600 bg-white/60 dark:bg-gray-700/30 rounded-md p-2"
		>
			<span
				class="countdown font-mono font-semibold text-gray-600 dark:text-gray-200 text-sm md:text-xl flex justify-center"
			>
				<span :style="{'--value': seconds ?? '00'}"></span>
			</span>
			<span
				class="text-xs"
				v-if="simple"
				>{{ $t("sec") }}</span
			>
			<span v-else>{{ $t("sec") }}</span>
		</div>
	</div>
</template>
<style>
.countdown {
	line-height: 1em;
}

.countdown {
	display: inline-flex;
}

.countdown > * {
	height: 1em;
	display: inline-block;
	overflow-y: hidden;
}

.countdown > *:before {
	position: relative;
	content: "00\a 01\a 02\a 03\a 04\a 05\a 06\a 07\a 08\a 09\a 10\a 11\a 12\a 13\a 14\a 15\a 16\a 17\a 18\a 19\a 20\a 21\a 22\a 23\a 24\a 25\a 26\a 27\a 28\a 29\a 30\a 31\a 32\a 33\a 34\a 35\a 36\a 37\a 38\a 39\a 40\a 41\a 42\a 43\a 44\a 45\a 46\a 47\a 48\a 49\a 50\a 51\a 52\a 53\a 54\a 55\a 56\a 57\a 58\a 59\a 60\a 61\a 62\a 63\a 64\a 65\a 66\a 67\a 68\a 69\a 70\a 71\a 72\a 73\a 74\a 75\a 76\a 77\a 78\a 79\a 80\a 81\a 82\a 83\a 84\a 85\a 86\a 87\a 88\a 89\a 90\a 91\a 92\a 93\a 94\a 95\a 96\a 97\a 98\a 99\a";
	white-space: pre;
	top: calc(var(--value) * -1em);
	text-align: center;
	transition: all 1s cubic-bezier(1, 0, 0, 1);
}
</style>
