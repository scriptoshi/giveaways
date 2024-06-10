<script setup>
import {computed, ref} from "vue";

import {router} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiSearch, HiSolidArrowUp, HiSolidCubeTransparent} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import PaginationSquare from "@/Components/PaginationSquare.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DialogModal from "@/Jetstream/DialogModal.vue";
import Layout from "@/Layouts/AppLayout.vue";
import GiveAwayCard from "@/Pages/Giveaways/Index/GiveAwayCard.vue";
import PopularEvents from "@/Pages/Projects/Index/PopularEvents.vue";
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
const showHowItWorks = ref(false);
</script>

<template>
	<Layout>
		<main class="w-full relative container">
			<div
				class="grid relative grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6"
			>
				<div class="lg:col-span-3 mt-4">
					<h1 class="text-base">Sleep Finance Giveaways</h1>
					<p>
						No nonsense giveaways. Only 3 - 5 simple tasks like retweet, follow and you
						done. Prize giveaway is decentralized and instant!. If you dont win, no
						worries, we dont give XPS, we give you sleep tokens. withdraw your sleep
						tokens and trade them for USDT.
					</p>
				</div>
				<div class="lg:col-span-2 mt-4">
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
								@click.prevent="showHowItWorks = !showHowItWorks"
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
						{{ $t("Top Projects to follow") }}
					</div>
					<PopularEvents :projects="popular" />
				</div>
			</div>
			<div>
				<h3 class="text-sm">What is a crypto giveaway</h3>
				<p>
					A Crypto giveaway or Crypto quest is when projects provide crypto rewards and
					prizes to users who complete simple social tasks. These tasks can include
					following accounts, sharing posts, or joining groups. Giveaways provide a risk
					free way for users to earn some crypto without investing or spending.
				</p>
				<h3 class="text-sm">How can you join a giveaway?</h3>
				<p>
					Basically, joining a crypto giveway will require you to have an account on
					twitter, telegram and discord. Find the giveway on sleepfinance, and enroll by
					following the project. After the giveaway ends and you are selected , you will
					claim your prize.
				</p>
				<h3 class="text-sm">How can I avoid giveaway/ quest scams?</h3>
				<ul>
					<li>Dont try to claim giveaways from unknown projects.</li>
					<li>Ensure the claim contract is verified on etherscan.</li>
					<li>Never accept to approve any action in your wallet during claims</li>
					<li>Ensure the address claiming giveaways has no valuable assets</li>
					<li>
						Only participate in giveways on sleep finance. Sleep finance only hosts USDT
						giveaways
					</li>
				</ul>
				<h3 class="text-sm">
					How long does it take to complete giveaways quests or tasks?
				</h3>
				<ul>
					<li>It should take you under 15 minutes to complete most giveaways</li>
					<li>
						The reward determination will take from 5 hours to 12 days depending on the
						giveaway settings.
					</li>
				</ul>
			</div>
		</main>
		<DialogModal
			:show="showHowItWorks"
			@close="showHowItWorks = false"
			closeable
		>
			<template #title> How it works </template>
			<template #content>
				<ul class="list-disc list-inside">
					<li>{{ $t("You will perform a few tasks in order to join the giveaway") }}</li>
					<li>
						{{
							$t(
								"The tasks include following the project on social media or contributing to the project.",
							)
						}}
					</li>
					<li>
						{{
							$t(
								"After the giveaway ends, sleep finance will select a winner based on the giveaway criterion",
							)
						}}
					</li>
					<li>
						{{
							$t(
								"Winners will have to claim the prize from the prize contract. You will need gas to do so.",
							)
						}}
					</li>
					<li>
						{{
							$t(
								"If you dont win, you can still claim the sleep tokens you earned from participation.",
							)
						}}
					</li>
					<li>
						{{
							$t(
								"Sleep tokens will be available to withdraw from all your quests, but after withdraw, you cannot pump giveaway again.",
							)
						}}
					</li>
					<li>
						{{
							"Sleep finance does not endorse any of the projects listed here, DYOR before investing or paying for anything."
						}}.
					</li>
				</ul>
			</template>
		</DialogModal>
	</Layout>
</template>
