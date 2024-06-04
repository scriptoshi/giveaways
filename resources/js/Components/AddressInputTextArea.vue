<script setup>
import {computed} from "vue";

import {HiArrowDown, HiX} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {isAddress} from "@/Wagmi/utils/utils";
const props = defineProps({
	modelValue: Array,
	label: String,
	error: String,
	disabled: Boolean,
	help: {type: String, default: "Paste address list seperated by a space, a comma or a new line"},
});
const emit = defineEmits(["update:modelValue"]);
const addresses = computed({
	set(val) {
		const items = val.replace(/['"]+/g, "").split(/[\n,\s+]+/);
		const addrs = items.filter((a) => isAddress(a)).map((a) => isAddress(a));
		emit("update:modelValue", addrs);
	},
	get() {
		return props.modelValue.join("\n") + "\n";
	},
});
const clear = () => (addresses.value = "");
const sort = () => {
	addresses.value = props.modelValue
		.slice()
		.sort((a, b) => a?.toString?.().localeCompare?.(b))
		.join("\n");
};
const validate = () => {
	const unique = [...new Set(props.modelValue)];
	addresses.value = unique.join("\n");
	sort();
};
</script>
<template>
	<div>
		<slot>
			<InputLabel
				v-if="label"
				class="mb-1"
				>{{ label }}</InputLabel
			>
		</slot>

		<div
			class="mx-0.5 rounded-md rounded-tl-none border border-gray-300 transition-colors duration-200 focus-within:!border-emerald-500 focus-within:ring-1 focus-within:outline-none focus-within:!ring-emerald-500 hover:border-gray-400 dark:border-gray-600 dark:focus-within:!border-emerald-500 dark:hover:border-gray-500 dark:bg-gray-900"
		>
			<label class="block">
				<textarea
					rows="8"
					v-model="addresses"
					:disabled="disabled"
					placeholder="Paste address list seperated by a space, a comma or a new line"
					class="form-textarea w-full resize-none bg-transparent p-3 pb-0 placeholder:text-gray-400/70"
				></textarea>
			</label>
			<div class="flex justify-between items-end p-2.5">
				<div class="flex items-center space-x-1">
					<button
						@click.prevent="sort"
						class="btn -ml-1 h-8 w-8 rounded-full p-0 hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
					>
						<VueIcon
							:icon="HiArrowDown"
							class="h-5 w-5"
						/>
					</button>
					<button
						@click.prevent="clear"
						class="btn h-8 w-8 rounded-full p-0 hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
					>
						<VueIcon
							:icon="HiX"
							class="h-5 w-5"
						/>
					</button>
					<span
						class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-700 dark:text-gray-400 border border-gray-500"
					>
						<svg
							class="w-2.5 h-2.5 me-1.5"
							aria-hidden="true"
							xmlns="http://www.w3.org/2000/svg"
							fill="currentColor"
							viewBox="0 0 20 20"
						>
							<path
								d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"
							/>
						</svg>
						{{ modelValue.length }} items
					</span>
				</div>
				<div class="flex justify-end">
					<PrimaryButton
						secondary=""
						class="!py-1 !px-2.5 !text-sm"
						@click.prevent="validate()"
					>
						{{ $t("Validate") }}
					</PrimaryButton>

					<slot name="actions" />
				</div>
			</div>
		</div>

		<p
			v-if="error"
			class="mt-1 text-xs text-red-600"
		>
			{{ error }}
		</p>
		<slot
			v-else-if="help"
			name="status"
		>
			<p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
				{{ help }}
			</p>
		</slot>
	</div>
</template>
