<script setup>
import {computed} from "vue";
const props = defineProps({
	name: String,
});

const src = computed(() => `https://icons.llamao.fi/icons/chains/rsz_${props.name}.jpg`);
</script>
<template>
	<img
		class="rounded-full"
		:src="src"
	/>
</template>
