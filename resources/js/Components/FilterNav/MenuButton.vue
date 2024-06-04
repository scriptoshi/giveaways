<script setup>
import {Link} from "@inertiajs/vue3";
defineProps({
	active: Boolean,
	href: String,
	as: {
		type: String,
		default: "button",
	},
});
</script>
<template>
	<Link
		v-if="as == 'link'"
		:href="href"
		:preserve-state="false"
		:preserve-scroll="false"
		:class="
			active
				? 'text-white bg-emerald-800 hover:text-white hover:bg-emerald-700'
				: 'text-emerald-700 dark:text-emerald-400 '
		"
		class="flex items-center border border-transparent hover:border-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-2 py-1 text-center dark:hover:border-emerald-400 dark:focus:ring-emerald-900"
	>
		<slot />
	</Link>
	<component
		v-else
		:is="as"
		:href="href"
		type="button"
		:class="
			active
				? 'text-white bg-emerald-800 hover:text-white hover:bg-emerald-700'
				: 'text-emerald-700 dark:text-emerald-400 '
		"
		class="flex items-center border border-transparent hover:border-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-2 py-1 text-center dark:hover:border-emerald-400 dark:focus:ring-emerald-900"
	>
		<slot />
	</component>
</template>
