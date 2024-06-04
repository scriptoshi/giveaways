<script setup>
import {reactive} from "vue";

import {Link} from "@inertiajs/vue3";
import {uid} from "uid";

const menus = reactive([
	{uid: uid(), name: "Docs", url: "https://docs.sleep.finance", route: null},
	{uid: uid(), name: "Give aways", url: null, route: "giveaways.index", current: "giveaways.*"},
	// {uid: uid(), name: "Services", url: null, route: "services.index"},
	{uid: uid(), name: "Projects", url: null, route: "projects.index", current: "projects.*"},
]);
</script>
<template>
	<nav class="text-sm leading-6 font-semibold text-slate-700 dark:text-slate-200">
		<ul class="flex space-x-8">
			<li
				v-for="menu in menus"
				:key="menu.uid"
			>
				<a
					v-if="menu.url"
					target="_blank"
					class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors duration-300"
					:href="menu.url"
					>{{ menu.name }}</a
				>
				<Link
					v-else
					:href="route(menu.route)"
					:class="{'text-emerald-500': route().current(menu.current)}"
					class="hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors duration-300"
					>{{ menu.name }}</Link
				>
			</li>
		</ul>
	</nav>
</template>
