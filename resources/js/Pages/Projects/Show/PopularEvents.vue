<script setup>
import {UseTimeAgo} from "@vueuse/components";
import {DateTime} from "luxon";
import {MdAccesstime, RiCalendarEventFill} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
import {useBillions} from "@/hooks/useBillions";
defineProps({
	giveaways: Array,
});
</script>
<template>
	<div class="p-1 mt-4 bg-white dark:bg-gray-800 rounded-md shadow-sm">
		<ul>
			<a
				v-for="giveaway in giveaways"
				:key="giveaway.id"
				:href="route('giveaways.show', {giveaway: giveaway.slug})"
				class="flex items-start bg-white content-contain dark:bg-gray-800 border-t hover:border-t-0 first:border-t-0 border-gray-200 dark:border-gray-700 p-2 transition-colors duration-300 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-700 no-underline"
			>
				<img
					:src="giveaway.project?.logo?.url"
					class="w-7 h-7 mt-1 mr-2 rounded-full p-0.5 border bg-white dark:border-gray-600 dark:bg-gray-900"
				/>
				<div class="flex-1 block">
					<div class="flex items-center space-x-3">
						<div class="text-sm font-bold text-gray-800 dark:text-gray-100">
							{{ giveaway.short_summary }}
						</div>
						<div class="max-w-[150px] truncate text-ellipsis">
							# {{ giveaway.brief }}
						</div>
					</div>
					<div
						class="leading-[18px] h-4.5 whitespace-nowrap flex items-center justify-start mt-1"
					>
						<div
							:class="[
								{'text-sky-500': giveaway.hasStarted && !giveaway.hasEnded},
								{'text-gray-700 dark:text-gray-300': !giveaway.hasStarted},
								{'text-red-500': giveaway.hasEnded},
							]"
							class="text-[10px] font-bold uppercase inline-block leading-[1em] tracking-[0.7px]"
						>
							<div
								class="flex items-center"
								v-if="giveaway.hasStarted && !giveaway.hasEnded"
							>
								<div
									class="w-3.5 h-3.5 mr-1 relative rounded-full border-2 border-white bg-success dark:border-navy-700"
								>
									<span
										class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-80"
									></span>
								</div>
								<span>{{ $t("Live") }}</span>
							</div>
							<span v-else-if="giveaway.hasEnded">
								{{ $t("Ended") }}
							</span>
							<div
								v-else
								class="flex items-center"
							>
								<VueIcon
									:icon="MdAccesstime"
									v-if="giveaway.isToday"
									class="w-4 h-4 mr-0.5 align-middle"
								/>
								<VueIcon
									:icon="RiCalendarEventFill"
									v-else
									class="w-4 h-4 mr-0.5 align-middle"
								/>
								<UseTimeAgo
									:time="DateTime.fromSeconds(giveaway.startTimeTs).toJSDate()"
									v-slot="{timeAgo}"
								>
									<div>{{ timeAgo }}</div>
								</UseTimeAgo>
							</div>
						</div>
						<div
							class="text-gray-500 ml-5 inline-block leading-[1em] text-[10px] font-bold tracking-[0.7px] uppercase"
						>
							{{ $t("SLEEP TOKENS:") }}
							<span
								class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap"
								>{{ useBillions(giveaway.sleep) }}</span
							>
						</div>
						<div
							class="text-gray-500 ml-5 inline-block leading-[1em] text-[10px] font-bold tracking-[0.7px] uppercase"
						>
							{{ $t("Joined:") }}
							<span
								class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap"
								>{{ useBillions(giveaway.totalParticipants) }}</span
							>
						</div>
					</div>
				</div>
			</a>
		</ul>
	</div>
</template>
