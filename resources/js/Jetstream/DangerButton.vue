<script setup>
import {Link} from "@inertiajs/vue3";
defineProps({
	type: {
		type: String,
		default: "button",
	},
	link: Boolean,
});
</script>
<template>
	<Link
		v-if="link"
		:type="type"
		class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-sm font-semibold text-md text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition"
	>
		<slot />
	</Link>
	<button
		v-else
		:type="type"
		class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-sm font-semibold text-md text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition"
	>
		<slot />
	</button>
</template>
