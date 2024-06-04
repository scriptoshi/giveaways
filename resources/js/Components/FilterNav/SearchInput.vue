<script setup>
import {onMounted, ref} from "vue";

import {useElementHover} from "@vueuse/core";
import {HiSolidX, RiSearch2Line} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";

const el = ref();
const hover = useElementHover(el);
const emit = defineEmits(["update:modelValue", "reset"]);
defineProps({
	modelValue: String,
});
const input = ref(null);
onMounted(() => {
	if (input.value.hasAttribute("autofocus")) {
		input.value.focus();
	}
});
defineExpose({focus: () => input.value.focus()});
const reset = () => {
	emit("update:modelValue", null);
};
</script>
<template>
	<div
		class="w-full"
		ref="el"
	>
		<div class="relative rounded-sm shadow-sm">
			<input
				ref="input"
				type="text"
				name="price"
				class="bg-white peer px-10 border border-emerald-600 text-gray-900 rounded-sm focus:ring-4 focus:ring-emerald-600/40 focus:border-emerald-600 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white py-2 text-xl block w-full focus:outline-none appearance-none transition-colors duration-200"
				placeholder="Search contract address / project name"
				aria-describedby="price-currency"
				:value="modelValue"
				@input="
					$emit(
						'update:modelValue',
						$event.target.value == '' ? null : $event.target.value,
					)
				"
			/>
			<div
				class="absolute inset-y-0 left-0 pl-3 text-gray-400 peer-focus:text-emerald-600 flex items-center pointer-events-none transition-colors duration-200"
			>
				<VueIcon
					:icon="RiSearch2Line"
					class="w-5 h-5"
				/>
			</div>
			<a
				href="#"
				:class="hover ? 'opacity-100' : 'opacity-0'"
				class="absolute inset-y-0 transition-opacity duration-500 right-0 pr-3 flex items-center"
				@click="$emit('update:modelValue', null)"
			>
				<VueIcon
					:icon="HiSolidX"
					class="text-gray-400 hover:text-red-500 w-4 h-4"
				/>
			</a>
		</div>
	</div>
</template>
