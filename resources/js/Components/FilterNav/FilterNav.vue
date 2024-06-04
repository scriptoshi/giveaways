<script setup>
import {watch} from "vue";

import {router as Inertia} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {BiClock} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {useI18n} from "vue-i18n";

import DropDown from "@/Components/FilterNav/DropDown.vue";
import MenuButton from "@/Components/FilterNav/MenuButton.vue";
import MultiTabButton from "@/Components/FilterNav/MultiTabButton.vue";
import SearchInput from "@/Components/FilterNav/SearchInput.vue";
import Flame from "@/Components/FilterNav/svg/flame.svg";
import VueIcon from "@/Components/VueIcon.vue";
const params = useUrlSearchParams("history");
const {t} = useI18n();
const {chain} = useAccount();
const props = defineProps({
	item: String,
	presale: Boolean,
	nft: Boolean,
	airdrop: Boolean,
	staking: Boolean,
	farming: Boolean,
	perPage: {type: Number, default: 6},
	routed: {type: String, default: "home"},
});
const typeMenu = [
	{
		name: props.airdrop || props.staking ? t("Admin") : t("Mine"),
		value: "mine",
	},
	{
		name: props.staking ? "Staked" : props.airdrop ? t("Received") : t("Joined"),
		value: "joined",
	},
	{name: props.airdrop ? t("Quested") : t("Liked"), value: "liked"},
];
const emit = defineEmits(["reset"]);
const reset = () => emit("reset");
const statusMenu = [
	{name: t("Any Status"), value: null},
	{name: t("Pending"), value: "pending"},
	{name: t("Live"), value: "live"},
	{name: t("Failed"), value: "failed"},
	{name: t("Ended"), value: "ended"},
	{name: t("Cancelled"), value: "cancelled"},
];
const categoryMenu = [
	{value: null, name: t("Any Type")},
	...(props.presale
		? [
				{value: "launchpad", name: t("Presales")},
				{value: "fairlaunch", name: t("Fair Launch")},
				{value: "fairliquidity", name: t("Fair Liquidity")},
				{value: "privatesale", name: t("Private Sale")},
		  ]
		: []),
	...(props.nft
		? [
				{value: "memberships", name: t("NFT Membership")},
				{value: "unique", name: t("NFT  Collections")},
				{value: "builder", name: t("NFT  Custom Build")},
		  ]
		: []),
	...(props.airdrop
		? [
				{value: "nft", name: t("NFT Airdrop")},
				{value: "token", name: t("Token  Airdrop")},
				{value: "quest", name: t("Quests Airdrop")},
		  ]
		: []),
	...(props.staking || props.farming
		? [
				{value: "farms", name: t("Liquidity Farms")},
				{value: "staking", name: t("Token Staking")},
		  ]
		: []),
];

debouncedWatch(
	[
		() => params.status,
		() => params.latest,
		() => params.type,
		() => params.allchains,
		() => params.query,
		() => params.category,
		// () => params.chainId,
		() => params.testnet,
	],
	([status, latest, itype, allchains, query, category, chainId]) => {
		reset();
		Inertia.visit(window.route(props.routed, params), {
			preserveScroll: true,
		});
	},
	{debounce: 800},
);
watch(
	() => props.perPage,
	(limit) => {
		Inertia.visit(window.route(props.routed, {...params, limit}), {
			preserveScroll: true,
		});
	},
);

const clear = () => {
	params.status = null;
	params.latest = null;
	params.type = null;
	params.allchains = null;
	params.chainId = null;
	params.query = null;
	params.category = null;
	params.testnet = null;
};
watch(
	[() => chain.value?.testnet, chain],
	([testnet, chain]) => {
		console.log(!testnet, params.testnet, chain);
		if (!chain?.id) return;
		if (testnet && !params.testnet) {
			params.testnet = 1;
			return;
		}
		if (!testnet && params.testnet === "1") {
			params.testnet = null;
		}
	},
	{immediate: true},
);
defineExpose({
	clear,
});
</script>
<template>
	<div class="mt-4 mb-8">
		<slot>
			<h3 class="mb-6 font-Walsheim-Bold text-[28px]">
				{{ item ?? "Explore Launchpads" }}
			</h3>
		</slot>
		<div
			class="flex flex-col lg:flex-row lg:items-center space-y-5 lg:space-y-0 justify-between"
		>
			<div class="flex w-full items-center space-x-3">
				<MenuButton
					:active="!params.latest || params.latest === 'trending'"
					class="font-Walsheim-Bold flex items-center"
					@click="params.latest = params.latest === 'trending' ? null : 'trending'"
				>
					<Flame class="w-5 h-5 -ml-1 mr-2" />
					Trending
				</MenuButton>
				<MenuButton
					:active="!!params.latest && params.latest !== 'trending'"
					@click="params.latest = params.latest === 'newest' ? 'oldest' : 'newest'"
					class="font-Walsheim-Bold flex items-center"
				>
					<VueIcon
						:icon="BiClock"
						class="w-5 h-5 -ml-1 mr-2"
					/>
					{{ params.latest === "oldest" ? "Oldest" : "Newest" }}
				</MenuButton>
				<MenuButton
					:active="params.allchains == 1"
					@click="params.allchains = params.allchains == 1 ? null : 1"
					class="font-Walsheim-Bold flex items-center"
				>
					{{ $t("All Chains") }}
				</MenuButton>
			</div>
			<div class="flex items-center space-x-2 sm:space-x-3">
				<MenuButton
					:active="route().current('home')"
					:href="route('home')"
					@click="menu = null"
					as="link"
					class="font-Walsheim-Bold flex items-center"
				>
					{{ $t("All") }}
				</MenuButton>
				<MenuButton
					:active="route().current('launchpads.index', {...params})"
					:href="route('launchpads.index', {...params})"
					as="link"
					class="font-Walsheim-Bold"
					>{{ $t("IDOs") }}</MenuButton
				>
				<MenuButton
					:active="route().current('nfts.index', {...params})"
					:href="route('nfts.index', {...params})"
					as="link"
					class="font-Walsheim-Bold"
					>{{ $t("Nft") }}</MenuButton
				>
				<MenuButton
					:active="route().current('airdrops.index')"
					:href="route('airdrops.index')"
					as="link"
					class="font-Walsheim-Bold"
					>{{ $t("Airdrops") }}</MenuButton
				>
				<MenuButton
					:active="route().current('staking.pools')"
					href="route('staking.pools')"
					as="link"
					class="font-Walsheim-Bold"
					>{{ $t("Stakes") }}</MenuButton
				>
				<MenuButton
					:active="route().current('farming.pools')"
					href="route('farming.pools')"
					as="link"
					class="font-Walsheim-Bold"
					>{{ $t("Farms") }}</MenuButton
				>

				<MenuButton
					:active="route().current('locks.tokens', {...params})"
					href="route('locks.tokens', {...params})"
					as="link"
					class="font-Walsheim-Bold hidden sm:flex"
					>{{ $t("Locks") }}</MenuButton
				>
			</div>
		</div>
		<div class="pt-6 pb-3">
			<SearchInput
				@reset="clear"
				v-model="params.query"
			/>
		</div>
		<div class="hidden lg:flex items-center mt-4 justify-between">
			<div class="flex w-1/2 items-center space-x-3">
				<div class="min-w-[180px]">
					<DropDown
						left
						v-model="params.status"
						:list="statusMenu"
					/>
				</div>
				<MultiTabButton
					:list="typeMenu"
					v-model="params.type"
				/>
			</div>
			<div class="min-w-[300px] w-">
				<DropDown
					left
					v-model="params.category"
					:list="categoryMenu"
				/>
			</div>
		</div>
		<div class="grid gap-4 lg:hidden mt-4">
			<div class="flex items-center space-x-3">
				<DropDown
					left
					v-model="params.status"
					:list="statusMenu"
				/>
				<DropDown
					left
					v-model="params.category"
					:list="categoryMenu"
				/>
			</div>
			<MultiTabButton
				:list="typeMenu"
				v-model="params.type"
			/>
		</div>
	</div>
</template>
