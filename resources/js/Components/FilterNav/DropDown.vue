<script setup>
import {computed, ref} from "vue";

import {onClickOutside} from "@vueuse/core";
import {HiSolidChevronDown} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
import {usePopper} from "@/hooks/usePopper";

const {showPopper, popperRef, popperRoot} = usePopper({
	placement: "bottom-end",
	offset: 4,
});
const props = defineProps({
	modelValue: String,
	left: Boolean,
	list: {
		type: Array,
		default: () => [
			{name: "All Rewards", value: "all-rewards"},
			{name: "Token", value: "token"},
			{name: "NFT", value: "nft"},
			{name: "Whitelist", value: "whitelist"},
		],
	},
});
const emit = defineEmits(["update:modelValue"]);
const outside = ref(null);
onClickOutside(outside, () => (showPopper.value = false));
const updateModelValue = (modelValue) => {
	emit("update:modelValue", modelValue);
	showPopper.value = false;
};
const selected = computed(() =>
	// eslint-disable-next-line eqeqeq
	props.list.find((l) => l.value == props.modelValue)
);
</script>
<template>
	<div
		ref="outside"
		class="relative w-full"
	>
		<button
			ref="popperRef"
			@click="showPopper = !showPopper"
			v-bind="$attrs"
			class="w-full cursor-pointer"
		>
			<div class="relative shadow-sm">
				<div
					type="text"
					class="border flex w-full focus:outline-none focus:ring-1 appearance-none transition-colors duration-300 bg-white border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white py-1 pr-8 text-sm !pl-3 !cursor-pointer"
					disabled=""
				>
					{{ selected?.name ?? "Select" }}
				</div>
				<div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
					<VueIcon
						:icon="HiSolidChevronDown"
						:class="
							showPopper
								? 'rotate-180 text-emerald-600 dark:text-emerald-400'
								: 'text-gray-300 dark:text-gray-600'
						"
						class="ml-2 h-5 w-5 transition duration-300 ease-in-out group-hover:text-opacity-80"
						aria-hidden="true"
					/>
				</div>
			</div>
		</button>
		<transition
			enter-active-class="transition duration-200 ease-out"
			enter-from-class="translate-y-1 opacity-0"
			enter-to-class="translate-y-0 opacity-100"
			leave-active-class="transition duration-150 ease-in"
			leave-from-class="translate-y-0 opacity-100"
			leave-to-class="translate-y-1 opacity-0"
		>
			<div
				ref="popperRoot"
				:class="left ? 'left-0' : 'right-0'"
				class="absolute top-[60px] shadow-md w-full min-w-[140px] z-10 py-5 bg-white dark:bg-gray-800 rounded-sm border border-gray-300 dark:border-gray-600"
				v-show="showPopper"
			>
				<div class="overflow-x-hidden overflow-y-scroll lg:max-h-[17rem]">
					<ul>
						<li
							v-for="item in list"
							:key="item.value"
						>
							<a
								href="#"
								@click.prevent="updateModelValue(item.value)"
							>
								<slot
									:item="item"
									:active="item.value === modelValue"
								>
									<span
										:class="
											item.value === modelValue
												? 'text-emerald-600 dark:text-emerald-400'
												: ''
										"
										class="flex h-8 cursor-pointer items-center px-3 pr-8 font-medium tracking-wide outline-none transition-all hover:bg-emerald-400/20 hover:text-emerald-600 focus:bg-gray-100 focus:text-gray-800 dark:hover:bg-gray-600 dark:hover:text-gray-100 dark:focus:bg-gray-600 dark:focus:text-gray-100"
										>{{ item.name }}</span
									>
								</slot>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</transition>
	</div>
</template>
<script>
export default {
	inheritAttrs: false,
};
</script>
