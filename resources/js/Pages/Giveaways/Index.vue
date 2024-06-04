<script setup>
import {computed} from "vue";

import {router} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiSearch, HiSolidArrowUp, HiSolidCubeTransparent} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import PaginationSquare from "@/Components/PaginationSquare.vue";
import VueIcon from "@/Components/VueIcon.vue";
import Layout from "@/Layouts/AppLayout.vue";
import GiveAwayCard from "@/Pages/Giveaways/Index/GiveAwayCard.vue";
import PopularEvents from "@/Pages/Giveaways/Index/PopularEvents.vue";
defineProps({
	giveaways: Object,
	popular: Array,
});
const params = useUrlSearchParams("history");
debouncedWatch(
	[() => params.search, () => params.by, () => params.order],
	([searchBy, orderBy, orderDir]) => {
		const search = searchBy === "" ? null : searchBy;
		const by = orderBy === "" ? null : orderBy;
		const order = orderDir === "" || orderDir === "latest" ? null : orderDir;
		router.visit(window.route("giveaways.index", {search, by, order}), {
			preserveScroll: true,
		});
	},
	{debounce: 500},
);
const setLatest = () => {
	params.by = !params.order ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = null;
};
const setPrize = () => {
	params.by = params.order === "prize" ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = "prize";
};
const setWinners = () => {
	params.by = params.order === "winners" ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = "winners";
};
const setJoined = () => {
	params.by = params.order === "joined" ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = "joined";
};
const order = computed(() => params.order);
</script>

<template>
	<Layout>
		<main class="w-full relative container">
			<div
				class="grid relative grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6"
			>
				<div class="lg:col-span-2 mt-8">
					<div class="grid sm:grid-cols-2 w-full mb-2 -mt-2">
						<FormInput
							class="max-w-xs"
							input-classes="!pl-7"
							placeholder="project name"
							v-model="params.search"
							size="xs"
						>
							<template #lead>
								<VueIcon
									:icon="HiSearch"
									class="w-4 h-4"
								/>
							</template>
						</FormInput>
						<div class="flex items-center justify-end space-x-2">
							<a
								class="text-xs font-semibold hover:underline"
								href="#"
								>[ How it Works ]</a
							><a
								class="text-xs font-semibold hover:underline"
								:class="{'text-emerald-500': !order}"
								href="#"
								@click.prevent="setLatest"
								>[ Latest
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && !order"
									class="w-3 h-3"
								/>
								]</a
							>
							<a
								class="text-xs font-semibold hover:underline"
								href="#"
								:class="{'text-emerald-500': order === 'prize'}"
								@click.prevent="setPrize"
								>[ Prize
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && order === 'prize'"
									class="w-3 h-3"
								/>]</a
							>
							<a
								class="text-xs font-semibold hover:underline"
								href="#"
								:class="{'text-emerald-500': order === 'winners'}"
								@click.prevent="setWinners"
								>[ Winners
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && order === 'winners'"
									class="w-3 h-3"
								/>
								]</a
							>
							<a
								class="text-xs font-semibold hover:underline"
								:class="{'text-emerald-500': order === 'joined'}"
								@click.prevent="setJoined"
								href="#"
								>[ Joined
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && order === 'joined'"
									class="w-3 h-3"
								/>
								]</a
							>
						</div>
					</div>
					<div
						v-if="giveaways.data?.length"
						class="grid gap-y-3 w-full"
					>
						<GiveAwayCard
							v-for="giveaway in giveaways.data"
							:key="giveaway.id"
							:giveaway="giveaway"
						/>
						<PaginationSquare :meta="giveaways.meta" />
					</div>
					<div
						v-else
						class="flex shadow-sm items-center border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-md justify-center w-full h-64"
					>
						<div class="flex flex-col items-center">
							<VueIcon
								class="w-12 h-12 text-gray-400"
								:icon="HiSolidCubeTransparent"
							/>
							<div class="text-gray-400 text-2xl font-semibold">
								{{ $t("No Giveaways found") }}
							</div>
							<p class="mt-5">
								If you need
								<strong class="text-emerald-500">to host your giveaway</strong>
							</p>
							<p class="mt-1">
								Please contact our
								<a
									target="_blank"
									class="text-sky-600 dark:text-sky-500 hover:text-sky-800 dark:hover:text-sky-400"
									href="https://t.me/betnfinance"
									>Telegram</a
								>
								for more information
							</p>
						</div>
					</div>
				</div>
				<div class="dark:bg-gray-800 h-[calc(100vh-40px)] bg-white/90 sticky top-16">
					<div
						class="bg-gray-300/40 dark:bg-gray-700/40 text-gray-900 dark:text-white p-3 text-sm font-semibold"
					>
						{{ $t("Popular Giveaways") }}
					</div>
					<PopularEvents :giveaways="popular" />
				</div>
			</div>
		</main>
	</Layout>
</template>
