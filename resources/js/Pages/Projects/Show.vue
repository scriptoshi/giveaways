<script setup>
import {computed} from "vue";

import {router} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiSearch, HiSolidArrowUp, HiSolidCubeTransparent, HiSolidPlus} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import PaginationSquare from "@/Components/PaginationSquare.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useBillions} from "@/hooks/useBillions";
import Layout from "@/Layouts/AppLayout.vue";
import PopularEvents from "@/Pages/Giveaways/Index/PopularEvents.vue";
import BuyCard from "@/Pages/Projects/Show/BuyCard.vue";
import GiveAwayCard from "@/Pages/Projects/Show/GiveAwayCard.vue";
import LinkHeader from "@/Pages/Projects/Show/LinkHeader.vue";

const props = defineProps({
	project: Object,
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
		router.visit(
			window.route("projects.show", {search, by, order, project: props.project.slug}),
			{
				preserveScroll: true,
			},
		);
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
		<main
			v-if="!project"
			class="w-full relative container"
		>
			<div class="mx-auto mt-8 py-4 sm:py-6 md:py-12 container">
				<div class="flex-1 lg:flex-[2] w-full relative">
					<div
						class="flex shadow-sm items-center border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-md justify-center w-full h-64"
					>
						<div class="flex flex-col items-center">
							<VueIcon
								class="w-12 h-12 text-gray-400"
								:icon="HiSolidCubeTransparent"
							/>
							<div class="text-gray-400 text-2xl font-semibold">
								{{ $t("It seems you havent created a project yet") }}
							</div>
							<p class="mt-2">
								Create a giveaway
								<strong class="text-emerald-500">to host your project</strong>
							</p>
							<div class="mt-5">
								<PrimaryButton
									link
									:href="route('giveaways.create')"
									secondary
								>
									Create a giveaway
								</PrimaryButton>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<main
			v-else
			class="w-full relative container"
		>
			<div class="mx-auto mt-8 py-4 sm:py-6 md:py-12 container">
				<div class="flex-1 lg:flex-[2] w-full relative">
					<div class="flex items-center justify-between gap-2 ml-[20%] md:ml-[17%]">
						<h2
							class="text-[26px] font-semibold text-gray-600 dark:text-emerald-600 dark-text-emerald-400-text-dark capitalize leading-relaxed"
						>
							{{ project.title }}
						</h2>
						<div class="mb-2 p-2 flex justify-end space-x-2">
							<LinkHeader :project="project" />
						</div>
					</div>
					<div
						class="absolute -mt-10 sm:-mt-12 md:-mt-18 left-[10%] -translate-x-[50%] z-10"
					>
						<figure
							class="inline-block bg-white dark:bg-gray-900 relative shadow-xl rounded-full"
						>
							<img
								:src="project.logo?.url"
								alt=""
								class="rounded-full p-[2px] border border-gray-200 dark:border-gray-700 w-[80px] h-[80px] sm:w-[92px] sm:h-[92px] md:w-[128px] md:h-[128px]"
								loading="lazy"
							/>
						</figure>
					</div>
					<div
						class="shadow-sm dark:shadow-lg text-sm dark:text-gray-300 dark-text-emerald-400-text-dark p-4 py-6 relative overflow-hidden rounded-t-none rounded-b-md bg-white dark:bg-gray-800"
					>
						<div class="relative">
							<div class="flex items-center justify-end">
								<div class="font-bold mr-6 font-Walsheim-Bold text-2xl">
									{{ project.totalPrize }}$ In Rewards
								</div>
								<div class="flex items-center space-x-3">
									<PrimaryButton
										link
										v-if="project.isOwner"
										class="!py-1"
										primary
										:href="route('projects.edit', {project: project.uuid})"
										>Edit Project</PrimaryButton
									>
									<PrimaryButton
										link
										v-if="project.isOwner"
										class="!py-1"
										secondary
										:href="route('giveaways.create', {project: project.uuid})"
									>
										<VueIcon
											class="mr-2 -ml-2"
											:icon="HiSolidPlus"
										/>

										Create Giveaway</PrimaryButton
									>
								</div>
							</div>

							<div class="my-4 w-full overflow-hidden">
								<div class="flex flex-col lg:flex-row gap-6">
									<div class="text-sm">
										{{ project.description }}
									</div>
									<div class="w-full flex-1 flex gap-4 justify-end items-center">
										<ul
											class="inline-flex py-2 px-4 border border-gray-200 dark:border-gray-700 rounded-[4px] max-w-fit"
										>
											<li class="list-none inline-flex flex-col">
												<div
													class="inline-flex space-x-4 items-center justify-between text-gray-500 dark:text-gray-400 font-normal text-sm"
												>
													<span class="flex whitespace-nowrap"
														>Questers</span
													>
													<span
														class="inline-block text-xs leading-5 border border-gray-200 dark:border-gray-600 rounded-sm px-2"
														>#356</span
													>
												</div>
												<div
													class="font-bold font-Walsheim-Bold text-2xl mt-0.5"
												>
													{{ useBillions(project.followers) }}
												</div>
											</li>
											<div
												class="w-[1px] mx-4 sm:mx-8 bg-gray-200 dark:bg-gray-600"
											></div>
											<li class="list-none inline-flex flex-col">
												<div
													class="inline-flex space-x-4 items-center justify-between text-gray-500 dark:text-gray-400 font-normal text-sm"
												>
													<span class="flex whitespace-nowrap">GAS</span>
												</div>
												<div
													class="font-bold font-Walsheim-Bold text-2xl mt-0.5"
												>
													{{ useBillions(project.gas) }} +
												</div>
											</li>
											<div
												class="w-[1px] mx-4 sm:mx-8 bg-gray-200 dark:bg-gray-600"
											></div>
											<li class="list-none inline-flex flex-col">
												<div
													class="inline-flex space-x-4 items-center justify-between text-gray-500 dark:text-gray-400 font-normal text-sm"
												>
													<span class="flex whitespace-nowrap"
														>Give Aways</span
													>
												</div>
												<div
													class="font-bold font-Walsheim-Bold text-2xl mt-0.5"
												>
													<span class="text-emerald-500">{{
														project.activeGiveaways
													}}</span>
													/ {{ project.totalGiveaways }}
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div
				class="grid relative grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6"
			>
				<div class="lg:col-span-2 mt-6">
					<div class="grid sm:grid-cols-2 w-full mb-2 -mt-2">
						<FormInput
							class="max-w-xs"
							input-classes="!pl-7"
							placeholder="Giveaway hash"
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
							:project="project"
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
					<BuyCard
						v-if="project.launchpad"
						:launchpad="project.launchpad"
					/>
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
