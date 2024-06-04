<script setup>
import StepItem from "./StepItem.vue";
defineProps({
	vertical: Boolean,
	current: Number,
	errors: Object,
	items: Array,
});
</script>
<template>
	<div
		:class="[
			'flex',
			'justify-between',
			'flex-col',
			'space-y-2',
			'md:space-y-0',
			'justify-between',
			vertical ? 'items-start flex-col' : 'md:items-center',
		]"
	>
		<StepItem
			v-for="(item, index) in items"
			:index="index"
			:key="index"
			:total="items.length"
			:error="errors[index] ?? null"
			:current="current"
			:vertical="vertical"
			:title="item.title"
			:description="item.description"
		>
		</StepItem>
	</div>
</template>
