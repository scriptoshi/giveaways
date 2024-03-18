<script setup>
import {ref} from "vue";

import {ChevronDownIcon} from "@heroicons/vue/24/outline";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import {useToggle} from "@/hooks/useToggle";
defineProps({
	items: Array,
	modelValue: Object,
});
const emit = defineEmits(["update:modelValue"]);
const isOpen = ref(false);
const {open, close} = useToggle(isOpen);
const select = (value) => {
	close();
	emit("update:modelValue", value);
};
</script>
<template>
	<div
		class="p-2 pb-3 mb-3 block md:hidden space-y-1 rounded border border-gray-300 dark:border-gray-600"
	>
		<CollapseTransition>
			<a
				href="#"
				@click.prevent="open"
				v-show="!isOpen"
				class="bg-emerald-50 dark:bg-transparent border-emerald-500 text-emerald-700 dark:text-emerald-300 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
				aria-current="page"
				>{{ modelValue.name }} <ChevronDownIcon class="w-6 h-6 float-right"
			/></a>
		</CollapseTransition>
		<CollapseTransition>
			<div v-show="isOpen">
				<a
					href="#"
					v-for="item in items"
					:key="item.slug"
					@click.prevent="select(item)"
					:class="
						item.slug === modelValue.slug
							? 'bg-emerald-50 dark:bg-transparent border-emerald-500 text-emerald-700 dark:text-emerald-300 block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
							: 'border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-800 dark:hover:text-gray-200 block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
					"
					class="active:bg-none"
					aria-current="page"
					>{{ item.name }} <span class="float-right">{{ item.count }}</span></a
				>
			</div></CollapseTransition
		>
	</div>
</template>
