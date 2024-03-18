<script setup>
import {ref} from "vue";

import {ChevronDownIcon} from "@heroicons/vue/24/outline";
import {onClickOutside} from "@vueuse/core";

import ChainPicker from "@/Components/ChainSelect/ChainPicker.vue";
import {useToggle} from "@/hooks/useToggle";
import {NetworkIcon} from "@/Wagmi/components/Icons";

const {isOpen, toggle, close} = useToggle();
const outside = ref(null);
onClickOutside(outside, () => {
	if (!isOpen.value) return;
	close();
});
defineProps({
	modelValue: Object,
});
const emit = defineEmits(["update:modelValue"]);
const updateModelValue = (val) => emit("update:modelValue", val);
</script>
<template>
	<div
		ref="outside"
		class="w-full"
	>
		<button
			type="button"
			@click="toggle"
			:class="
				isOpen
					? 'bg-zinc-50 dark:bg-gray-700 hover:bg-zinc-100 dark:hover:bg-gray-700'
					: ' hover:bg-gray-50 bg-white dark:bg-gray-900'
			"
			class="border text-gray-600 dark:text-gray-300 flex items-center py-1.5 px-3 border-gray-300 dark:border-gray-600 rounded-md text-sm font-semibold leading-6 w-full text-center align-middle select-none normal-case transition-colors duration-200 ease-in-out"
		>
			<template v-if="modelValue?.name">
				<NetworkIcon
					class="mr-3 w-5"
					:chainId="modelValue?.id"
				/>
				<span class="block text-left w-full truncate font-mediuml mr-2">{{
					modelValue?.name
				}}</span>
			</template>
			<span
				v-else
				class="block text-left w-full truncate font-mediuml mr-2"
				><slot>{{ $t("Filter Networks") }}</slot></span
			>
			<ChevronDownIcon
				:class="isOpen ? 'rotate-180' : ''"
				class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 transition-transform ease-in-out duration-200"
			/>
		</button>
		<transition
			enter-active-class="transition duration-200 ease-out"
			enter-from-class="translate-y-1 opacity-0"
			enter-to-class="translate-y-0 opacity-100"
			leave-active-class="transition duration-150 ease-in"
			leave-from-class="translate-y-0 opacity-100"
			leave-to-class="translate-y-1 opacity-0"
		>
			<ChainPicker
				v-if="isOpen"
				@close="close"
				:modelValue="modelValue"
				@update:modelValue="updateModelValue"
			/>
		</transition>
	</div>
</template>
