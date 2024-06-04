<script setup>
defineProps({
	modelValue: String,
	list: {
		type: Array,
		default: () => [
			{name: "Mine", value: "mine"},
			{name: "Joined", value: "Joined"},
			{name: "Liked", value: "liked"},
		],
	},
});
const emit = defineEmits(["update:modelValue"]);
const updateModelValue = (val) => emit("update:modelValue", val);
</script>
<template>
	<div
		class="flex w-full border border-emerald-600 dark:border-emerald-500 rounded-sm overflow-hidden"
	>
		<div
			@click="updateModelValue(null)"
			:class="
				!modelValue
					? 'bg-emerald-600 dark:bg-emerald-500 text-white'
					: 'text-emerald-600 font-semibold dark:text-emerald-500'
			"
			class="text-sm py-1 px-2 w-full text-center cursor-pointer border-r border-emerald-600 dark:border-emerald-500 last:border-0"
		>
			{{ $t("List") }}
		</div>
		<div
			v-for="item in list"
			:key="item.name"
			@click="updateModelValue(item.value)"
			:class="
				item.value === modelValue
					? 'bg-emerald-600 dark:bg-emerald-500 text-white'
					: 'text-emerald-600 font-semibold dark:text-emerald-500'
			"
			class="text-sm py-1 px-2 w-full text-center cursor-pointer border-r border-emerald-600 dark:border-emerald-500 last:border-0"
		>
			{{ item.name }}
		</div>
	</div>
</template>
