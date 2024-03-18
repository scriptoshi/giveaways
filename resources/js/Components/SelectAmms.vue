<script setup>
import {computed} from "vue";

import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from "@headlessui/vue";
import {CheckIcon, ChevronDownIcon} from "@heroicons/vue/24/solid";
import {usePage} from "@inertiajs/vue3";
import {invoke, until} from "@vueuse/core";
import {useAccount} from "use-wagmi";
import {bsc} from "viem/chains";
const {chain} = useAccount();
const amms = computed(() => {
	const list = usePage().props.amms;
	return list[chain.id ?? bsc.id];
});
const props = defineProps({
	modelValue: Object,
	label: {type: String, default: "Select A DEX"},
});
const emit = defineEmits(["update:modelValue"]);
invoke(async () => {
	await until(chain.value).not.toBeNull();
	if (!props.modelValue?.router) emit("update:modelValue", amms.value?.[0]);
});

const selected = computed({
	set(value) {
		emit("update:modelValue", value);
	},
	get() {
		return props.modelValue;
	},
});
</script>

<!-- This example requires Tailwind CSS v2.0+ -->
<template>
	<Listbox
		as="div"
		v-model="selected"
	>
		<ListboxLabel class="block text-sm font-medium text-gray-700 dark:text-gray-300">
			{{ label ?? "Auto Liquidity Exchange" }}
		</ListboxLabel>
		<div class="mt-1 relative">
			<ListboxButton
				class="relative w-full py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-sm shadow-sm pl-3 pr-10 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
			>
				<span class="block truncate font-semibold">{{
					selected?.name ?? "Select An Exchange"
				}}</span>
				<span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
					<ChevronDownIcon
						class="h-5 w-5 text-gray-400 dark:text-gray-600"
						aria-hidden="true"
					/>
				</span>
			</ListboxButton>

			<transition
				leave-active-class="transition ease-in duration-100"
				leave-from-class="opacity-100"
				leave-to-class="opacity-0"
			>
				<ListboxOptions
					class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-sm py-1 text-base ring-1 ring-gray-300 dark:ring-gray-600 ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
				>
					<ListboxOption
						as="template"
						v-for="amm in amms"
						:key="amm.id"
						:value="amm"
						v-slot="{active, selected}"
					>
						<li
							:class="[
								active
									? 'text-white bg-emerald-600 dark:bg-emerald-400'
									: 'text-gray-900 dark:text-gray-200',
								'cursor-default select-none relative py-2 pl-8 pr-4',
							]"
						>
							<span
								:class="[
									selected ? 'font-semibold' : 'font-medium',
									'block truncate text-base',
								]"
							>
								{{ amm?.name }}
							</span>
							<span
								v-if="selected"
								:class="[
									active
										? 'text-white'
										: 'text-emerald-600 dark:text-emerald-400',
									'absolute inset-y-0 left-0 flex items-center pl-1.5',
								]"
							>
								<CheckIcon
									class="h-5 w-5"
									aria-hidden="true"
								/>
							</span>
						</li>
					</ListboxOption>
				</ListboxOptions>
			</transition>
		</div>
		<a
			:href="selected.url"
			target="_blank"
			v-if="selected"
			class="block text-sm mt-1 font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
		>
			{{ selected.url }}
		</a>
	</Listbox>
</template>
