<script setup>
import {computed} from "vue";

import {XMarkIcon} from "@heroicons/vue/24/outline";
import {Link, usePage} from "@inertiajs/vue3";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {
	BiDropletHalf,
	CoMixer,
	FaBalanceScale,
	HiHome,
	HiPlusCircle,
	LaTwitterSquare,
	LaUsersSolid,
	MdGeneratingtokensSharp,
	MdLockpersonRound,
	MdRocketlaunchSharp,
	MdToken,
	RiContrastDrop2Fill,
	RiDiscordLine,
	RiFunctionLine,
	RiMacbookLine,
	RiMapPinUserFill,
	RiRocket2Line,
	RiTelegramLine,
} from "oh-vue-icons/icons";
import {uid} from "uid";
import {useChainId} from "use-wagmi";
import {useI18n} from "vue-i18n";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import SiteLogo from "@/Components/SiteLogo.vue";
import Lang from "@/Layouts/AppLayout/Lang.vue";
import MenuItem from "@/Layouts/AppLayout/MenuItem.vue";
import MiniMenuItem from "@/Layouts/AppLayout/MiniMenuItem.vue";

const config = computed(() => usePage().props.config);

const {t} = useI18n();
const AuthCheck = computed(() => usePage().props.AuthCheck);
const twitterUrl = computed(() => usePage().props?.config?.twitterUrl);
const telegramUrl = computed(() => usePage().props?.config?.telegramUrl);
const discordUrl = computed(() => usePage().props?.config?.discordUrl);
const chId = computed(() => usePage().props.chainId);
const chainId = useChainId();
const menus = computed(() => [
	...(AuthCheck.value
		? [
				{
					name: t("Home"),
					route: "home",
					active: window.route().current("home"),
					url: window.route("home"),
					icon: HiHome,
				},
		  ]
		: [
				{
					name: t("Home"),
					route: "home",
					active: window.route().current("home"),
					url: window.route("home"),
					icon: HiHome,
				},
		  ]),
	{
		name: t("Tokens"),
		route: ["projects.index"],
		active:
			window.route().current("projects.index") ||
			window.route().current("projects.create") ||
			window.route().current("projects.edit") ||
			window.route().current("projects.show"),
		url: window.route("projects.index"),
		icon: MdRocketlaunchSharp,
	},
	{
		name: t("Memes"),
		route: ["tokens.index"],
		active: window.route().current("tokens.index") || window.route().current("tokens.show"),
		url: window.route("tokens.index"),
		icon: RiFunctionLine,
	},

	{
		name: t("Drops"),
		route: ["airdrops.index"],
		active:
			window.route().current("airdrops.index") ||
			window.route().current("airdrops.show") ||
			window.route().current("airdrops.create"),
		url: window.route("airdrops.index"),
		icon: BiDropletHalf,
	},
	{
		name: t("Nfts"),
		route: ["nfts.index"],
		active:
			window.route().current("nfts.index") ||
			window.route().current("nfts.show") ||
			window.route().current("nfts.create"),
		url: window.route("nfts.index"),
		icon: MdToken,
	},
	{
		name: t("Influencers"),
		route: ["influencers.index"],
		active:
			window.route().current("influencers.index") ||
			window.route().current("influencers.show") ||
			window.route().current("influencers.create"),
		url: window.route("influencers.index"),
		icon: LaUsersSolid,
	},
	{
		name: "line",
		url: null,
		icon: null,
	},
	{
		name: t("Launchpad"),
		route: ["launchpads.*", "nfts.*"],
		active: window.route().current("launchpads.*") || window.route().current("nfts.*"),
		url: null,
		icon: RiRocket2Line,
		submenu: [
			{
				name: t("Token Sales"),
				route: ["launchpads.index"],
				active:
					window.route().current("launchpads.index") ||
					window.route().current("launchpads.show"),
				url: window.route("launchpads.index"),
				icon: MdRocketlaunchSharp,
			},
			{
				name: t("AirDrops"),
				route: ["airdrops.index"],
				active:
					window.route().current("airdrops.index") ||
					window.route().current("airdrops.show") ||
					window.route().current("airdrops.create"),
				url: window.route("airdrops.index"),
				icon: BiDropletHalf,
			},
			{
				name: t("Nft Sales"),
				route: ["nfts.index"],
				active:
					window.route().current("nfts.index") ||
					window.route().current("nfts.show") ||
					window.route().current("nfts.create"),
				url: window.route("nfts.index"),
				icon: MdToken,
			},
			{
				name: t("Token Locks"),
				route: ["nfts.index"],
				active:
					window.route().current("nfts.index") ||
					window.route().current("nfts.show") ||
					window.route().current("nfts.create"),
				url: window.route("nfts.index"),
				icon: MdToken,
			},
			{
				name: t("Multi Sender"),
				route: ["nfts.index"],
				active:
					window.route().current("nfts.index") ||
					window.route().current("nfts.show") ||
					window.route().current("nfts.create"),
				url: window.route("nfts.index"),
				icon: MdToken,
			},
		],
	},
	{
		name: t("Create"),
		route: ["launchpads.*", "nfts.*"],
		active: window.route().current("launchpads.*") || window.route().current("nfts.*"),
		url: null,
		icon: HiPlusCircle,
		submenu: [
			{
				id: uid(),
				name: "ERC20 Token",
				route: ["tokens.create"],
				url: window.route("tokens.create", {
					chainId: chainId.value ?? chId.value ?? 5,
					type: "standard",
				}),
				icon: MdGeneratingtokensSharp,
			},
			{
				id: uid(),
				name: "Nft Launchpad",
				route: ["nfts.create"],
				url: window.route("nfts.create", {
					chainId: chainId.value ?? chId.value ?? 5,
				}),
				icon: RiMapPinUserFill,
			},
			{
				id: uid(),
				name: "Token Launch",
				route: ["launchpads.create"],
				url: window.route("launchpads.create", {
					launchpad: "fairlaunch",
					chainId: chainId.value ?? chId.value ?? 5,
				}),
				icon: FaBalanceScale,
			},
			{
				id: uid(),
				name: "Token Airdrop",
				route: ["airdrops.create"],
				url: window.route("airdrops.create", {
					chainId: chainId.value ?? chId.value ?? 5,
				}),
				icon: RiContrastDrop2Fill,
			},
			{
				id: uid(),
				name: "Token Lock",
				route: ["locks.create"],
				url: window.route("airdrops.create", {
					chainId: chainId.value ?? chId.value ?? 5,
				}),
				/* url: window.route("locks.create", {
            chainId: chainId.value ?? chId.value??5,
        }), */
				href: "#",
				icon: MdLockpersonRound,
			},
			{
				id: uid(),
				route: ["sender.tokens"],
				name: "Multisend Tx",
				url: window.route("airdrops.create", {
					chainId: chainId.value ?? chId.value ?? 5,
				}),
				/* url: window.route("sender.tokens", {
            chainId: chainId.value ?? chId.value??5,
        }), */
				href: "#",
				icon: CoMixer,
			},
		],
	},
	{
		name: "line",
		url: null,
		icon: null,
	},
	{
		name: "Docs",
		url: "https://docs.sleep.finance",
		icon: RiMacbookLine,
	},
	{
		name: t("Telegram"),
		url: telegramUrl.value,
		icon: RiTelegramLine,
	},
	{
		name: t("Twitter"),
		url: twitterUrl.value,
		icon: LaTwitterSquare,
	},
	{
		name: t("Discord"),
		url: discordUrl.value,
		icon: RiDiscordLine,
	},
]);
const breakpoints = useBreakpoints(breakpointsTailwind);
const isLg = breakpoints.greaterOrEqual("sm");
defineProps({
	expanded: Boolean,
});
const emit = defineEmits(["draw"]);
const draw = (val) => emit("draw", val);

function currentRoute(route = []) {
	if (!route) return false;
	if (typeof route === "string") return window.route().current(route);
	return route.filter((rt) => window.route().current(rt)).length > 0;
}
</script>
<template>
	<div
		v-if="isLg"
		:class="{'min-w-[200px]': expanded}"
		class="h-screen sticky z-1 top-0 flex-auto flex-col bg-white dark:bg-gray-900 flex-shrink-0 transition-all duration-300 ease-in-out"
	>
		<Link
			href="/"
			:class="expanded ? 'px-6' : 'pl-6'"
			class="py-4 w-auto flex items-center"
		>
			<SiteLogo class="w-7 h-auto mr-3" />
			<h3
				class="font-extralight"
				v-if="expanded"
			>
				{{ $page.props.config.appName }}
			</h3>
		</Link>
		<CollapseTransition dimension="width">
			<div
				v-show="expanded"
				class="h-[calc(100vh-4rem)] group max-w-[202px] scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-800 scrollbar-thumb-rounded-lg"
			>
				<nav class="pb-8">
					<div>
						<ul>
							<MenuItem
								v-for="menu in menus"
								:key="menu.id"
								:menu="menu"
								:active="menu.route ? currentRoute(menu.route) : false"
							/>
						</ul>
					</div>
				</nav>
			</div>
		</CollapseTransition>
		<nav
			v-show="!expanded"
			class="pb-8 relative"
		>
			<div class="flex relative flex-col">
				<MiniMenuItem
					v-for="menu in menus"
					:key="menu.id"
					:menu="menu"
				/>
			</div>
		</nav>
		<Lang :expanded="expanded" />
	</div>

	<!-- drawer component -->
	<template v-else>
		<div
			:class="expanded ? 'transform-none' : '-translate-x-full'"
			class="fixed z-40 h-screen overflow-y-auto bg-white w-80 dark:bg-gray-800 transition-transform left-0 top-0"
			tabindex="-1"
		>
			<div
				class="flex items-center justify-between p-4 border-gray-300 dark:border-gray-600 border-b"
			>
				<div class="flex items-center">
					<SiteLogo class="w-10 h-auto mr-3" />
					<h5 class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400">
						{{ config.appName }}
					</h5>
				</div>
				<button
					type="button"
					@click="draw(false)"
					class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
				>
					<XMarkIcon class="w-5 h-5" />
				</button>
			</div>
			<div class="py-8">
				<ul class="space-y-2">
					<MenuItem
						v-for="menu in menus"
						:key="menu.id"
						:menu="menu"
					/>
				</ul>
			</div>
			<Lang :expanded="expanded" />
		</div>
		<div
			v-if="expanded"
			class="bg-gray-900/70 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-30"
		></div>
	</template>
</template>
