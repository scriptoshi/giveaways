<script setup>
import {useBillions} from "@/hooks/useBillions";
import UsdtLogo from "@/images/usdt.svg";
defineProps({
	projects: Array,
});
</script>
<template>
	<div class="p-1 mt-4 bg-white dark:bg-gray-800 rounded-md shadow-sm">
		<ul>
			<a
				v-for="project in projects"
				:key="project.id"
				:href="route('projects.show', {project: project.slug})"
				class="flex items-start bg-white content-contain dark:bg-gray-800 border-t hover:border-t-0 first:border-t-0 border-gray-200 dark:border-gray-700 p-2 transition-colors duration-300 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-700 no-underline"
			>
				<img
					:src="project?.logo?.url"
					class="w-7 h-7 mt-1 mr-2 rounded-full p-0.5 border bg-white dark:border-gray-600 dark:bg-gray-900"
				/>
				<div class="flex-1 block">
					<div class="flex items-center space-x-3">
						<div class="text-sm font-bold text-gray-800 dark:text-gray-100">
							@{{ project.slug }}
						</div>
						<div class="max-w-[150px] truncate text-ellipsis">
							{{ project.description }}
						</div>
					</div>
					<div
						class="leading-[18px] h-4.5 whitespace-nowrap flex items-center gap-3 justify-start mt-1"
					>
						<div
							:class="[
								{'text-sky-500': project.hasStarted && !project.hasEnded},
								{'text-gray-700 dark:text-gray-300': !project.hasStarted},
								{'text-red-500': project.hasEnded},
							]"
							class="text-[10px] font-bold uppercase inline-block leading-[1em] tracking-[0.7px]"
						>
							<div class="flex items-center">
								<div
									class="w-3.5 h-3.5 mr-1 relative rounded-full border-2 border-white bg-success dark:border-navy-700"
								>
									<span
										class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-80"
									></span>
								</div>
								<span>{{ project.activeGiveaways }} {{ $t("Live") }}</span>
							</div>
						</div>
						<div
							class="text-gray-900 flex items-center leading-[1em] text-[10px] font-bold tracking-[0.7px] uppercase"
						>
							<UsdtLogo class="inline-flex w-3.5 h-3.5 mr-1" />
							{{ project.totalPrize }}
						</div>
						<div
							class="text-gray-500 inline-block leading-[1em] text-[10px] font-bold tracking-[0.7px] uppercase"
						>
							{{ $t("GAS:") }}
							<span
								class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap"
								>{{ useBillions(project.sleep) }}</span
							>
						</div>
						<div
							class="text-gray-500 inline-block leading-[1em] text-[10px] font-bold tracking-[0.7px] uppercase"
						>
							{{ $t("Joined:") }}
							<span
								class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap"
								>{{ useBillions(project.followers) }}</span
							>
						</div>
					</div>
				</div>
			</a>
		</ul>
	</div>
</template>
