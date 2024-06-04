<script setup>
import {Link} from "@inertiajs/vue3";
import {GiDiamonds, HiSolidStar} from "oh-vue-icons/icons";
import TextClamp from "vue3-text-clamp";

import VueIcon from "@/Components/VueIcon.vue";
import {useBillions} from "@/hooks/useBillions";
import {shortenAddress} from "@/Wagmi/utils/utils";

defineProps({
	service: Object,
});
defineEmits(["destroy"]);
</script>

<template>
	<Link :href="route('services.show', {ad: service.slug})">
		<div
			class="content-contain border grid grid-cols-1 lg:grid-cols-3 border-gray-200 dark:border-gray-700 items-center rounded-[2px] min-h-[60px] contain-intrinsic-70 py-1 px-3 bg-white dark:bg-gray-800 relative transition-colors duration-300 box-border flex-wrap m-0"
		>
			<div class="lg:col-span-2">
				<div class="flex justify-between items-center">
					<div class="flex flex-row items-center space-x-2">
						<img
							:src="service.user.profile_photo_url"
							class="w-6 h-6 rounded-full"
						/>
						<h3
							:class="
								service.isOwner
									? ' text-emerald-500 dark:text-emerald-400'
									: ' text-gray-700 dark:text-gray-200'
							"
							class="text-sm max-w-sm truncate text-ellipsis font-semibold"
						>
							{{ service.title }}
							<span
								v-if="service.isOwner"
								class="text-white px-2 uppercase rounded-[4px] py-0.5 text-xs bg-emerald-500"
								>Owner</span
							>
						</h3>
					</div>
					<div class="flex-shrink-0">
						<div
							v-if="service.isPartner"
							class="flex items-center gap-1 text-xs font-semibold text-white bg-blue-800 py-0.5 px-2 rounded-md"
						>
							<div>Verified</div>
							<div class="text-green-500">Partner</div>
						</div>
						<div
							v-else-if="service.isTopRated"
							class="flex items-center gap-1 text-xs font-semibold text-amber-900 bg-amber-400/50 py-0.5 px-2 rounded-md"
						>
							<div>Top Rated</div>
							<div class="flex items-center">
								<VueIcon
									class="w-4 h-4"
									:icon="GiDiamonds"
								/><VueIcon
									class="w-4 h-4"
									:icon="GiDiamonds"
								/><VueIcon
									class="w-4 h-4"
									:icon="GiDiamonds"
								/>
							</div>
						</div>
					</div>
				</div>
				<div class="mt-2">
					<p class="text-sm text-gray-700 dark:text-gray-300">
						<TextClamp
							:text="service.description"
							:max-lines="2"
						/>
					</p>
				</div>
			</div>
			<div class="flex sm:mt-0 flex-col sm:items-end">
				<div class="flex items-center text-base">
					<span class="text-xs inline-flex"
						>[ {{ shortenAddress(service.user.account.address) }} ]</span
					>
					<VueIcon
						class="w-5 h-5"
						:icon="HiSolidStar"
					/>
					<strong class="text-semibold">{{ service.rating }}</strong>
					<span>({{ useBillions(service.comments) }})</span>
				</div>
				<h3 class="text-base mt-1">From ${{ service.price * 1 }}</h3>
			</div>
			<div
				class="lg:col-span-3 z-2 flex justify-end space-x-2"
				v-if="service.isOwner"
			>
				<Link
					class="text-xs font-semibold hover:underline"
					:href="route('services.edit', {ad: service.uuid})"
				>
					[ Edit ]</Link
				>
				<a
					class="text-xs text-red-500 z-2 font-semibold hover:underline"
					href="#"
					@click.prevent="$emit('destroy')"
					>[ Delete ]</a
				>
			</div>
		</div>
	</Link>
</template>
