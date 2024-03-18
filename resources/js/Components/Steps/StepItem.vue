<script setup>
import {computed} from "vue";

import {HiX} from "oh-vue-icons/icons";

import VueIcon from "../VueIcon.vue";
const props = defineProps({
	index: Number,
	total: Number,
	current: Number,
	error: Boolean,
	vertical: Boolean,
	description: String,
	title: String,
});
const hasError = computed(() => props.error && props.index === props.current - 1);
const isLast = computed(() => props.index === props.total - 1);
const bg = computed(() => {
	if (hasError.value) return "bg-red-600 dark:bg-red-400";
	if (props.index < props.current) return "bg-emerald-600 dark:bg-emerald-400";
	if (props.index === props.current) return "border-emerald-600 dark:border-emerald-400 border-2";
	return "border-gray-300 dark:border-gray-600 border-2";
});

const line = computed(() => {
	if (hasError.value) return "bg-red-600 dark:bg-red-400";
	if (props.index < props.current) return "bg-emerald-600 dark:bg-emerald-400";
	return "bg-gray-300 dark:bg-gray-600";
});

const text = computed(() => {
	if (hasError.value) return "text-red-600 dark:text-red-400";
	if (props.index < props.current) return "text-white";
	if (props.index === props.current) return "text-emerald-600 dark:text-emerald-400";
	return "text-gray-300 dark:text-gray-600";
});
</script>
<template>
	<div
		:class="vertical ? 'items-start flex-col' : 'items-center'"
		class="flex"
	>
		<div class="items-center flex">
			<div
				:class="[bg, text]"
				class="rounded-full text-xl font-semibold h-9 justify-center leading-7 min-w-[2.25rem] flex items-center"
			>
				<span>
					<VueIcon
						:icon="HiX"
						v-if="hasError"
						class="text-red-500"
					/>
					<slot
						name="icon"
						v-else-if="current"
						>{{ index + 1 }}</slot
					>
					<template v-else>{{ index + 1 }}</template>
				</span>
			</div>
			<div class="ml-3 relative">
				<span
					:class="
						props.index === props.current
							? 'text-emerald-600 dark:text-emerald-400'
							: 'text-gray-700 dark:text-gray-300'
					"
					class="whitespace-nowrap font-bold block"
					>{{ title }}</span
				>
				<span v-if="vertical">{{ description }}</span>
			</div>
		</div>
		<div
			v-if="!isLast"
			:class="[line, vertical ? 'min-h-[3.5rem] w-0.5 ml-4' : 'w-full ml-2.5']"
			class="step-connect step-connect-vertical"
		></div>
	</div>
</template>
