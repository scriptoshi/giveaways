<script setup>
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/vue/24/outline";
import {Link} from "@inertiajs/vue3";
defineProps({
	meta: Object,
});
</script>
<template>
	<div
		v-if="meta?.links?.length > 3"
		class="flex flex-col justify-between space-y-4 px-4 py-4 sm:flex-row sm:items-center sm:space-y-0 sm:px-5"
	>
		<div class="text-xs font-bold">
			{{ meta.from }} - {{ meta.to }} of {{ meta.total }} entries
		</div>
		<ol class="pagination space-x-1.5">
			<template
				v-for="link in meta.links"
				:key="link.label"
			>
				<li v-if="link.label == 'Previous'">
					<Link
						v-if="link.url"
						:href="link.url"
						class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300/80 dark:bg-gray-500 dark:text-gray-200 dark:hover:bg-gray-450 dark:focus:bg-gray-450 dark:active:bg-gray-450/90"
					>
						<ChevronLeftIcon class="h-4 w-4" />
					</Link>
					<div
						v-else
						class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors dark:bg-gray-500 dark:text-gray-200"
					>
						<ChevronLeftIcon class="h-4 w-4" />
					</div>
				</li>
				<li v-else-if="link.label == 'Next'">
					<Link
						v-if="link.url"
						:href="link.url"
						class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300/80 dark:bg-gray-500 dark:text-gray-200 dark:hover:bg-gray-450 dark:focus:bg-gray-450 dark:active:bg-gray-450/90"
					>
						<ChevronRightIcon class="h-4 w-4" />
					</Link>
					<div
						v-else
						class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors dark:bg-gray-500 dark:text-gray-200"
					>
						<ChevronRightIcon class="h-4 w-4" />
					</div>
				</li>
				<li v-else>
					<Link
						v-if="link.active"
						:href="link.url"
						class="flex h-8 min-w-[2rem] items-center justify-center rounded-full bg-emerald-600 px-3 leading-tight text-white transition-colors hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-emerald-400 dark:hover:bg-emerald-300 dark:focus:bg-emerald-300 dark:active:bg-emerald-400/90"
						>{{ link.label }}</Link
					>
					<Link
						v-else
						:href="link.url"
						class="flex h-8 min-w-[2rem] items-center justify-center rounded-full bg-gray-150 px-3 leading-tight transition-colors hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300/80 dark:bg-gray-500 dark:hover:bg-gray-450 dark:focus:bg-gray-450 dark:active:bg-gray-450/90"
						>{{ link.label }}</Link
					>
				</li>
			</template>
		</ol>
	</div>
</template>
