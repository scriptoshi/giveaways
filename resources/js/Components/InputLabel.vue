<script setup>
defineProps({
	value: String,
});
</script>

<template>
	<label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
		<template v-if="value">{{ value }}</template>
		<template v-else><slot /></template>
	</label>
</template>
