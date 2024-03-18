<script setup>
import {computed, defineEmits} from "vue";
const props = defineProps({
	modelValue: {type: [Array, Boolean]},
	disabled: {type: Boolean, default: false},
	xs: Boolean,
	xl: Boolean,
});
const emit = defineEmits(["update:modelValue"]);
const model = computed({
	get() {
		return props.modelValue;
	},
	set(value) {
		emit("update:modelValue", value);
	},
});
</script>
<template>
	<label
		:disabled="disabled"
		class="relative inline-flex items-center cursor-pointer"
	>
		<input
			type="checkbox"
			v-model="model"
			class="sr-only peer"
			:disabled="disabled"
		/>
		<div
			:class="
				xs
					? 'w-9 h-5 after:h-4 after:w-4'
					: xl
					  ? 'w-14 h-7 after:h-6 after:w-6'
					  : 'w-11 h-6 after:h-5 after:w-5'
			"
			class="bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:transition-all dark:border-gray-600 peer-checked:bg-emerald-600"
		></div>
		<span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
			<slot />
		</span>
	</label>
</template>
