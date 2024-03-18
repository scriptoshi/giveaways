<script setup>
import {computed} from "vue";

import {RadioGroup, RadioGroupLabel, RadioGroupOption} from "@headlessui/vue";
const props = defineProps({
	list: Array,
	modelValue: [Number, String, Boolean],
	cols: Number,
});
const classes = computed(() => {
	const colsLength = props.cols ?? props.list.length;
	if (colsLength <= 2) return "sm:grid-cols-2 gap-x-8";
	if (colsLength === 3) return "sm:grid-cols-3 gap-x-6";
	if (colsLength === 4) return "sm:grid-cols-4 gap-x-4";
	if (colsLength === 5) return "sm:grid-cols-5 gap-x-3";
	if (colsLength === 6) return "sm:grid-cols-6 gap-x-3";
	return "sm:grid-cols-4";
});
const emit = defineEmits(["update:modelValue"]);
const updateModelValue = (mv) => emit("update:modelValue", mv);
</script>
<template>
	<RadioGroup
		@update:model-value="updateModelValue"
		:model-value="modelValue"
		class="mt-2"
	>
		<div
			:class="classes"
			class="grid grid-cols-2 gap-y-4"
		>
			<RadioGroupOption
				as="template"
				v-for="item in list"
				:key="item.id ?? item.value"
				:value="item.bg ?? item.value"
				v-slot="{active, checked}"
			>
				<div
					:class="[
						'cursor-pointer focus:outline-none',
						active
							? 'ring-2 dark:ring-0 ring-offset-2 dark:ring-offset-0 ring-transparent dark:ring-transparent'
							: '',
						checked
							? 'bg-emerald-300/30 dark:bg-emerald-700/30 border-transparent text-emerald-900 dark:text-emerald-100'
							: 'bg-gray-50 border dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 hover:bg-gray-200 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 ',
						'border rounded-md p-2 flex items-center justify-center text-base font-semibold  sm:flex-1 transition-colors duration-200',
					]"
					:style="item.bg ? {backgroundImage: `url(${item.bg})`} : {}"
				>
					<RadioGroupLabel
						class="flex flex-row align-middle items-center"
						as="div"
					>
						<slot
							name="label"
							:item="item"
							:checked="checked"
						>
							<span
								class="transition-colors duration-300"
								:class="
									checked
										? 'text-emerald-800 dark:text-emerald-300'
										: 'text-gray-700 dark:text-gray-400'
								"
							>
								{{ item.label }}</span
							>
						</slot>
					</RadioGroupLabel>
				</div>
			</RadioGroupOption>
		</div>
	</RadioGroup>
</template>
