<script setup>
import {computed} from "vue";

import {Popover, PopoverButton, PopoverPanel} from "@headlessui/vue";
import {
	BiSpeedometer,
	CoBuzzfeed,
	CoMixer,
	FaBalanceScale,
	FaTruck,
	HiSolidChevronDown,
	IoWallet,
	MdGeneratingtokensSharp,
	MdLockpersonRound,
	OiRepoLocked,
	RiBarcodeFill,
	RiContrastDrop2Fill,
	RiExchangeBoxFill,
	RiExchangeLine,
	RiGovernmentFill,
	RiRocket2Fill,
} from "oh-vue-icons/icons";
import {uid} from "uid";

import VueIcon from "@/Components/VueIcon.vue";

const props = defineProps({
	counts: [Array, Object],
});
const types = Object.freeze({
	PRIVATESALEFACTORY: "PrivateSaleFactory",
	AIRDROPFACTORY: "AirdropFactory",
	BADGESFACTORY: "Badge",
	GOVERNORFACTORY: "GovernorFactory",
	MULTISENDERFACTORY: "Multisender",
	PRESALEFACTORY: "PresaleFactory",
	FAIRLAUNCHFACTORY: "FairLaunchFactory",
	TAXFACTORY: "TaxFactory",
	STAKINGFACTORY: "StakingFactory",
	STAKINGREWARDSFACTORY: "StakingRewardsFactory",
	STANDARDTOKENFACTORY: "StandardTokenFactory",
	LOCKFACTORY: "TokenLock",
	DEXFACTORY: "DexFactory",
	NFTFACTORY: "NftFactory",
	LZFACTORY: "LZFactory",
	MIGRATOR: "Migrator",
});
const menu = computed(() => [
	{
		id: uid(),
		name: "NFT Launchpad Factory",
		href: window.route("admin.factories.create", {
			factory: "nft-factory",
		}),
		icon: RiBarcodeFill,
		description: "",
		count: props.counts.find((c) => c.type === types.NFTFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Airdrops Factory",
		href: window.route("admin.factories.create", {
			factory: "airdrop-factory",
		}),
		icon: RiContrastDrop2Fill,
		description: "",
		count: props.counts.find((c) => c.type === types.AIRDROPFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Amm Factory",
		href: window.route("admin.factories.create", {
			factory: "dex-factory",
		}),
		icon: RiExchangeBoxFill,
		description: "",
		count: props.counts.find((c) => c.type === types.DEXFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Fairlaunch Factory",
		href: window.route("admin.factories.create", {
			factory: "fairlaunch-factory",
		}),
		icon: FaBalanceScale,
		description: "",
		count: props.counts.find((c) => c.type === types.FAIRLAUNCHFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "ERC20 Factory",
		href: window.route("admin.factories.create", {
			factory: "token-factory",
		}),
		icon: MdGeneratingtokensSharp,
		description: "",
		count: props.counts.find((c) => c.type === types.STANDARDTOKENFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Lp Farm Factory",
		href: window.route("admin.factories.create", {
			factory: "stakingrewards-factory",
		}),
		icon: CoBuzzfeed,
		description: "",
		count: props.counts.find((c) => c.type === types.STAKINGREWARDSFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Staking Factory",
		href: window.route("admin.factories.create", {
			factory: "staking-factory",
		}),
		icon: IoWallet,
		description: "",
		count: props.counts.find((c) => c.type === types.STAKINGFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Tax Token Factory",
		href: window.route("admin.factories.create", {
			factory: "tax-token-factory",
		}),
		icon: BiSpeedometer,
		description: "",
		count: props.counts.find((c) => c.type === types.TAXFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Crosschain Token Factory",
		href: window.route("admin.factories.create", {
			factory: "lzfactory-factory",
		}),
		icon: RiExchangeLine,
		description: "",
		count: props.counts.find((c) => c.type === types.LZFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Private Presale Factory",
		href: window.route("admin.factories.create", {
			factory: "privatesale-factory",
		}),
		icon: OiRepoLocked,
		description: "",
		count: props.counts.find((c) => c.type === types.PRIVATESALEFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "ICO, IDO Factory",
		href: window.route("admin.factories.create", {
			factory: "presale-factory",
		}),
		icon: RiRocket2Fill,
		description: "",
		count: props.counts.find((c) => c.type === types.PRESALEFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Governance Factory",
		href: window.route("admin.factories.create", {
			factory: "governor-factory",
		}),
		icon: RiGovernmentFill,
		description: "",
		count: props.counts.find((c) => c.type === types.GOVERNORFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Token & Native MultiSender",
		href: window.route("admin.factories.create", {
			factory: "multisender",
		}),
		icon: CoMixer,
		description: "",
		count: props.counts.find((c) => c.type === types.MULTISENDERFACTORY)?.totals ?? 0,
	},
	{
		id: uid(),
		name: "Token & LP Locker",
		href: window.route("admin.factories.create", {factory: "token-lock"}),
		icon: MdLockpersonRound,
		description: "",
		count: props.counts.find((c) => c.type === types.LOCKFACTORY)?.totals ?? 0,
	},

	{
		id: uid(),
		name: "Token Migrator",
		href: window.route("admin.factories.create", {factory: "migrator"}),
		icon: FaTruck,
		description: "",
		count: props.counts.find((c) => c.type === types.MIGRATOR)?.totals ?? 0,
	},
]);
</script>
<template>
	<Popover
		v-slot="{open}"
		class="relative"
	>
		<PopoverButton
			:class="open ? '' : 'text-opacity-90'"
			class="group"
			as="div"
		>
			<button
				:disabled="open"
				class="btn space-x-1 px-3 font-semibold text-emerald-500 dark:text-gray-200 rounded-sm flex items-center bg-white dark:bg-gray-900 border border-emerald-500 dark:border-gray-700"
			>
				<span>Create factory</span>
				<VueIcon
					:icon="HiSolidChevronDown"
					:class="open ? 'rotate-180' : 'text-opacity-70'"
					class="ml-2 h-5 w-5 text-emerald-600 transition duration-300 ease-in-out group-hover:text-opacity-80"
					aria-hidden="true"
				/>
			</button>
		</PopoverButton>

		<transition
			enter-active-class="transition duration-200 ease-out"
			enter-from-class="translate-y-1 opacity-0"
			enter-to-class="translate-y-0 opacity-100"
			leave-active-class="transition duration-150 ease-in"
			leave-from-class="translate-y-0 opacity-100"
			leave-to-class="translate-y-1 opacity-0"
		>
			<PopoverPanel
				class="w-full bg-white dark:bg-gray-800 sm:w-[320px] fixed bottom-0 shadow-md left-0 right-0 sm:absolute sm:bottom-[unset] sm:left-[unset] mt-2 sm:rounded-sm rounded-b-none border-t sm:border border-emerald-600 z-50"
			>
				<div
					class="overflow-hidden rounded-sm shadow-lg sm:ring-1 sm:ring-emerald-600 py-3 ring-opacity-5"
				>
					<div class="relative max-h-80 overflow-auto grid gap-1">
						<a
							v-for="item in menu"
							:key="item.name"
							:href="item.href"
							class="flex items-center w-full px-8 sm:px-4 py-2 transition duration-150 ease-in-out hover:bg-emerald-300/40 focus:outline-none focus-visible:ring focus-visible:ring-emerald-500 focus-visible:ring-opacity-50"
						>
							<VueIcon
								:icon="item.icon"
								class="flex h-6 w-6 mr-3 text-emerald-500"
							/>
							<div class="text-base font-medium text-gray-900 dark:text-white">
								{{ item.name }}
							</div>
							<div
								class="ml-auto text-xs px-1.5 rounded-full bg-gray-100 dark:bg-gray-700 font-bold mr-2"
							>
								{{ item.count }}
							</div>
						</a>
					</div>
				</div>
			</PopoverPanel>
		</transition>
	</Popover>
</template>
