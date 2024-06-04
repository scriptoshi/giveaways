<script setup>
import {HiSolidStar} from "oh-vue-icons/icons";
import TextClamp from "vue3-text-clamp";

import VueIcon from "@/Components/VueIcon.vue";
import {useBillions} from "@/hooks/useBillions";
defineProps({
	services: Array,
});
</script>
<template>
	<div class="p-1 mt-4 bg-white dark:bg-gray-800 rounded-md shadow-sm">
		<ul>
			<a
				v-for="service in services"
				:key="service.id"
				:href="route('services.show', {ad: service.slug})"
				class="flex items-start bg-white content-contain dark:bg-gray-800 border-t hover:border-t-0 first:border-t-0 border-gray-200 dark:border-gray-700 p-2 transition-colors duration-300 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-700 no-underline"
			>
				<img
					:src="service.user?.profile_photo_url"
					class="w-7 h-7 mt-1 mr-2 rounded-full p-0.5 border bg-white dark:border-gray-600 dark:bg-gray-900"
				/>
				<div class="flex-1 block">
					<div class="flex items-center justify-between space-x-3">
						<div
							class="text-sm font-bold max-w-[150px] truncate text-ellipsis text-gray-800 dark:text-gray-100"
						>
							{{ service.title }}
						</div>
						<h3 class="text-sm py-0.5 px-2 border dark:border-gray-600 rounded-sm">
							From ${{ service.price * 1 }}
						</h3>
					</div>
					<div>
						<TextClamp
							:text="service.description"
							:max-lines="2"
						/>
					</div>
					<div
						class="leading-[18px] h-4.5 whitespace-nowrap flex items-center space-x-4 justify-start mt-1"
					>
						<div
							class="text-[10px] font-bold uppercase inline-block leading-[1em] tracking-[0.7px]"
						>
							<div
								v-if="service.user.isOnline"
								class="flex items-center"
							>
								<div
									class="w-3.5 h-3.5 mr-1 relative rounded-full border-2 border-white bg-success dark:border-navy-700"
								>
									<span
										class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success opacity-80"
									></span>
								</div>
								<span>{{ $t("Online") }}</span>
							</div>
							<div
								v-else
								class="flex items-center"
							>
								<div
									class="w-3.5 h-3.5 mr-1 relative rounded-full border-2 border-white bg-gray-400 dark:bg-gray-700 dark:border-navy-700"
								></div>
								<span>{{ $t("Offline") }}</span>
							</div>
						</div>
						<div
							class="flex items-center leading-[1em] text-[11px] font-bold tracking-[0.7px] uppercase"
						>
							<VueIcon
								class="w-4 h-4"
								:icon="HiSolidStar"
							/>
							<strong class="text-semibold">{{ service.rating }}</strong>
							<span>({{ useBillions(service.comments) }})</span>
						</div>
						<div
							class="text-gray-500 inline-block leading-[1em] text-[10px] font-bold tracking-[0.7px] uppercase"
						>
							{{ $t("Orders:") }}
							<span
								class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap"
								>{{ useBillions(service.totalOrders) }}</span
							>
						</div>
					</div>
				</div>
			</a>
		</ul>
	</div>
</template>
