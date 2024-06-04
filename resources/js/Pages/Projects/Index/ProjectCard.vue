<script setup>
import {Link} from "@inertiajs/vue3";

import UsdtLogo from "@/images/usdt.svg";
defineProps({
	project: Object,
});
</script>

<template>
	<Link :href="route('projects.show', {project: project.slug})">
		<div
			class="content-contain border grid grid-cols-1 lg:grid-cols-3 border-gray-200 dark:border-gray-700 items-center rounded-[2px] min-h-[60px] contain-intrinsic-70 py-1 px-3 bg-white dark:bg-gray-800 relative transition-colors duration-300 box-border flex-wrap m-0"
		>
			<div class="lg:col-span-2 flex items-center">
				<img
					:src="project?.logo?.url"
					class="w-8 h-8 mt-1 mr-2 rounded-full p-0.5 border bg-white dark:border-gray-600 dark:bg-gray-900"
				/>
				<div>
					<div class="flex flex-row items-center space-x-6 justify-between">
						<div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
							@{{ project.slug }}
						</div>
					</div>
					<div class="flex justify-between space-x-2">
						<div class="flex items-center flex-row space-x-1">
							<div
								class="text-xs max-w-[200px] sm:max-w-sm text-ellipsis truncate text-slate-400 dark:text-navy-300"
							>
								{{ project.description }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="flex mt-3 sm:mt-0 flex-col items-end gap-2">
				<div class="flex items-center w-full space-x-8 justify-end">
					<div class="text-base font-semibold">
						<span class="text-emerald-500">{{ project.activeGiveaways }} active</span>
					</div>
					<div
						class="px-2 flex items-center text-lg font-semibold rounded-sm border border-gray-200 dark:border-gray-600"
					>
						<UsdtLogo class="inline-flex w-4 h-4 mr-2" />
						{{ project.totalPrize }} USDT
					</div>
				</div>
			</div>
		</div>
	</Link>
</template>
