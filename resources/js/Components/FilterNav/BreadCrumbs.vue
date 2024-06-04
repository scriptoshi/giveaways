<script setup>
import {Link} from "@inertiajs/vue3";
import {FaChevronRight, HiHome} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
defineProps({
	crumbs: Array,
});
</script>
<template>
	<nav
		class="flex"
		aria-label="Breadcrumb"
	>
		<ol class="inline-flex items-center space-x-1 md:space-x-3">
			<li
				v-if="$page.props.AuthCheck"
				class="inline-flex items-center"
			>
				<Link
					href="/dashboard"
					class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-white transition-colors"
				>
					<VueIcon
						:icon="HiHome"
						class="w-4 h-4 mr-2.5"
					/>
					{{ $t("Dashboard") }}
				</Link>
			</li>
			<li
				v-else
				class="inline-flex items-center"
			>
				<Link
					href="/"
					class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-white transition-colors"
				>
					<VueIcon
						:icon="HiHome"
						class="w-4 h-4 mr-2.5"
					/>
					{{ $t("Home") }}
				</Link>
			</li>
			<li
				v-for="crumb in crumbs"
				:key="crumb.name"
			>
				<div class="flex items-center">
					<slot :crumb="crumb">
						<VueIcon
							class="w-4 h-4 text-gray-400 mx-1"
							:icon="FaChevronRight"
						/>
						<Link
							v-if="!!crumb?.route"
							:href="route(crumb.route, crumb?.params)"
							class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2 dark:text-gray-400 dark:hover:text-white transition-colors"
							>{{ crumb.name }}</Link
						>
						<span
							v-else
							class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400"
							>{{ crumb.name }}</span
						>
					</slot>
				</div>
			</li>
		</ol>
	</nav>
</template>
