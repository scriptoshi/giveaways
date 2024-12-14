<script setup>
import {computed} from "vue";

import {Link} from "@inertiajs/vue3";
import {UseTimeAgo} from "@vueuse/components";
import {DateTime} from "luxon";

import {useBillions} from "@/hooks/useBillions";
import UsdtLogo from "@/images/usdt.svg";
const props = defineProps({
	giveaway: Object,
	project: Object,
});
const totalParticipants = computed(() => useBillions(props.giveaway.totalParticipants));
</script>

<template>
	<Link :href="route('giveaways.show', {giveaway: giveaway.slug})">
		<div
			class="content-contain border grid grid-cols-1 lg:grid-cols-3 border-gray-200 dark:border-gray-700 items-center rounded-[2px] min-h-[60px] contain-intrinsic-70 py-1 px-3 bg-white dark:bg-gray-800 relative transition-colors duration-300 box-border flex-wrap m-0"
		>
			<div class="lg:col-span-2">
				<div class="flex flex-row items-center space-x-6 justify-between">
					<div class="text-sm font-semibold uppercase text-gray-700 dark:text-gray-200">
						{{ giveaway.summary }}
					</div>
				</div>
				<div class="flex justify-between space-x-2">
					<div class="flex items-center flex-row space-x-1">
						<img
							:src="project.logo.url"
							class="w-3 h-3"
						/>
						<p class="text-xs font-semibold text-slate-700 dark:text-gray-100">
							{{ project.name }}
						</p>
						<p
							class="text-xs max-w-sm text-ellipsis truncate text-slate-400 dark:text-gray-300"
						>
							# {{ giveaway.brief }}
						</p>
					</div>
				</div>
			</div>
			<div class="flex mt-3 sm:mt-0 flex-col items-end gap-2">
				<div class="flex items-center w-full space-x-8 justify-end">
					<template v-if="giveaway.hasStarted">
						<h3
							v-if="giveaway.hasEnded"
							v-tippy="$t('Live')"
							class="text-red-500 flex items-center text-xs"
						>
							ENDED
							<div
								v-if="giveaway.totalParticipants > 0"
								class="bg-success ml-2 font-Walsheim-Bold text-white px-2 rounded-sm"
							>
								{{ totalParticipants }}
							</div>
						</h3>
						<h3
							v-else
							v-tippy="$t('Live')"
							class="text-success text-xs flex items-center"
						>
							LIVE
							<div
								v-if="giveaway.totalParticipants > 0"
								class="bg-success ml-2 font-Walsheim-Bold text-white px-2 rounded-sm"
							>
								{{ totalParticipants }}
							</div>
						</h3>
					</template>
					<h3
						v-else
						class="text-gray-300 text-xs dark:text-gray-600"
					>
						PENDING
					</h3>
					<div
						v-if="giveaway.hasEnded"
						class="px-3 py-2 rounded-sm border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-900"
					>
						<UseTimeAgo
							v-slot="{timeAgo}"
							:time="DateTime.fromSeconds(giveaway.endTimeTs).toJSDate()"
						>
							<div
								class="countdown text-gray-400 dark:text-gray-400 text-base font-mono font-semibold flex justify-center"
							>
								{{ timeAgo }}
							</div>
						</UseTimeAgo>
					</div>
					<div
						v-else
						class="px-2 flex items-center text-lg font-semibold rounded-sm border border-gray-200 dark:border-gray-600"
					>
						<UsdtLogo class="inline-flex w-4 h-4 mr-2" />
						{{ giveaway.totalPrize }} USDT
					</div>
				</div>
			</div>
		</div>
	</Link>
</template>
