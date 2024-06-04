<script setup>
import {Link} from "@inertiajs/vue3";
defineProps({
	link: Boolean,
	type: {
		type: String,
		default: "button",
	},
});
</script>

<template>
	<Link
		v-if="link"
		as="button"
		:type="type"
		class="inline-flex btn items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-sm font-semibold text-sm text-gray-700 dark:text-gray-400 uppercase tracking-widest shadow-sm hover:text-gray-500 dark:hover:text-gray-200 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-45 transition"
	>
		<slot />
	</Link>
	<button
		v-else
		:type="type"
		class="inline-flex btn items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-sm font-semibold text-sm text-gray-700 dark:text-gray-400 uppercase tracking-widest shadow-sm hover:text-gray-500 dark:hover:text-gray-200 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-45 transition"
	>
		<slot />
	</button>
</template>
