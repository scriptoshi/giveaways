<script setup>
import {computed, defineEmits} from "vue";
const props = defineProps({
	modelValue: {type: [Array, Boolean]},
	disabled: {type: Boolean, default: false},
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
		class="inline-flex items-center space-x-2"
	>
		<input
			class="form-switch h-5 w-10 rounded-full bg-gray-300 before:rounded-full before:bg-gray-50 checked:!bg-emerald-500 checked:before:bg-white dark:bg-gray-900 dark:before:bg-gray-300 dark:checked:before:bg-white"
			type="checkbox"
			v-model="model"
			:disabled="disabled"
		/>
		<span class="text-sm font-medium cursor-pointer text-gray-900 dark:text-gray-300"
			><slot
		/></span>
	</label>
</template>
