<script setup>
import {computed, onMounted, ref, useSlots} from "vue";

import {ExclamationCircleIcon} from "@heroicons/vue/24/outline";
import {useElementHover} from "@vueuse/core";
const el = ref();
const hover = useElementHover(el);
const input = ref();
const props = defineProps({
	modelValue: [String, Number],
	placeholder: [String, Number],
	inputClasses: String,
	hide: Boolean,
	disabled: Boolean,
	autofocus: Boolean,
	label: String,
	type: String,
	error: String,
	help: String,
	size: {
		type: String,
		default: "sm",
	},
});
defineEmits(["update:modelValue"]);
defineExpose({focus: () => input.value.focus()});
onMounted(() => {
	if (props.autofocus) {
		input.value.focus();
	}
});
const slots = useSlots();
const lg = computed(() => props.size === "lg");
const sm = computed(() => props.size === "sm");
const xs = computed(() => props.size === "xs");
const xm = computed(() => props.size === "xm");
const md = computed(() => props.size === "md");
const xl = computed(() => props.size === "xl");
const xl2 = computed(() => props.size === "2xl");
const trail = computed(() => !!slots.trail);
const lead = computed(() => !!slots.lead);
const classes = computed(() => {
	if (xs.value)
		return `py-1 rounded-sm text-xs ${lead.value ? "pl-12" : "pl-1"} ${
			trail.value ? "pr-12" : "pr-1"
		}`;
	if (xm.value)
		return `py-1.5 text-sm ${lead.value ? "pl-6" : "pl-1"} ${trail.value ? "pr-6" : "pr-1"}`;
	if (sm.value)
		return `py-2 text-sm ${lead.value ? "pl-8" : "pl-2"} ${trail.value ? "pr-12" : "pr-2"}`;
	if (md.value)
		return `py-3 text-base ${lead.value ? "pl-12" : "pl-3"} ${trail.value ? "pr-12" : "pr-3"}`;
	if (lg.value)
		return `py-4 text-lg ${lead.value ? "pl-12" : "pl-4"} ${trail.value ? "pr-12" : "pr-4"}`;
	if (xl.value)
		return `py-5 text-xl ${lead.value ? "pl-6" : "pl-5"} ${trail.value ? "pr-6" : "pr-5"}`;
	if (xl2.value)
		return `py-6 text-2xl ${lead.value ? "pl-12" : "pl-6"} ${trail.value ? "pr-12" : "pr-6"}`;
	return "p-2";
});
</script>
<template>
	<div>
		<label
			for="name"
			v-if="label || $slots.label"
			:class="xs ? 'text-xs' : 'text-sm'"
			class="block mb-1 font-medium text-gray-900 dark:text-gray-300"
			><slot
				name="label"
				:hover="hover"
				>{{ label }}</slot
			></label
		>
		<div
			v-if="$slots.trail || $slots.lead"
			class="relative rounded-sm shadow-sm"
			ref="el"
		>
			<div
				v-if="$slots.lead"
				class="absolute inset-y-0 left-0 pl-1.5 flex items-center pointer-events-none"
			>
				<slot
					name="lead"
					:hover="hover"
				/>
			</div>
			<input
				:value="modelValue"
				:disabled="disabled"
				ref="input"
				@input="$emit('update:modelValue', $event.target.value)"
				:type="type ? type : hide ? 'password' : 'text'"
				class="border block w-full focus:outline-none focus:ring-1 appearance-none transition-colors duration-300"
				:class="[
					error
						? 'bg-red-50 border-red-500  text-red-900 placeholder-red-700 rounded-sm focus:ring-red-500 focus:border-red-500  dark:bg-red-100 dark:border-red-400'
						: 'bg-white border-gray-300 text-gray-900  rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white',
					classes,
					inputClasses,
				]"
				:placeholder="placeholder"
			/>
			<div
				v-if="error"
				class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
			>
				<ExclamationCircleIcon
					class="h-5 w-5 text-red-500"
					aria-hidden="true"
				/>
			</div>
			<div
				v-else-if="$slots.trail"
				class="absolute inset-y-0 right-0 pr-3 flex items-center"
			>
				<slot
					name="trail"
					:hover="hover"
				/>
			</div>
		</div>
		<input
			v-else
			name="code"
			:disabled="disabled"
			:value="modelValue"
			@input="$emit('update:modelValue', $event.target.value)"
			class="appearance-none"
			:class="[
				error
					? 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 rounded-sm focus:ring-red-500 focus:border-red-500  dark:bg-red-100 dark:border-red-400'
					: 'bg-white border-gray-300 text-gray-900  rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white',
				'border block w-full focus:outline-none  focus:ring-1 appearance-none',
				classes,
				inputClasses,
			]"
			:type="type ? type : hide ? 'password' : 'text'"
			:placeholder="placeholder"
		/>
		<p
			v-if="error"
			class="mt-1 text-xs text-red-600"
		>
			{{ error }}
		</p>
		<p
			v-else-if="help"
			class="mt-1 text-xs text-gray-600 dark:text-gray-300"
			id="email-error"
		>
			{{ help }}
		</p>
		<slot name="help" />
	</div>
</template>
