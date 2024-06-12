<script setup>
import {computed, nextTick, ref} from "vue";

import {PopoverButton} from "@headlessui/vue";
import {ChevronDoubleDownIcon} from "@heroicons/vue/24/solid";
import {Link as InertiaLink, Link, usePage} from "@inertiajs/vue3";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {
	FaPowerOff,
	HiSolidGift,
	HiSolidPlusSm,
	HiTicket,
	LaUserPlusSolid,
	MdSettingsOutlined,
	RiAwardLine,
	RiUserLine,
} from "oh-vue-icons/icons";
import {useAccount, useDisconnect} from "use-wagmi";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Loading from "@/Components/Loading.vue";
import SiteLogo from "@/Components/SiteLogo.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DarkSwitch from "@/Layouts/AdminLayout/DarkSwitch.vue";
import SiteMenu from "@/Layouts/AppLayout/Header/SiteMenu.vue";
import RegisterModal from "@/Pages/Auth/RegisterModal.vue";
import ConnectModal from "@/Wagmi/ConnectModal.vue";
import {logOut, useAuth} from "@/Wagmi/hooks/authentication";
import {useWagmiModalToggle} from "@/Wagmi/hooks/useWagmiModalToggle";
import CoinbaseWalletIcon from "@/Wagmi/Icons/CoinbaseWalletIcon.vue";
import FrameIcon from "@/Wagmi/Icons/FrameIcon.vue";
import GnosisSafeIcon from "@/Wagmi/Icons/GnosisSafeIcon.vue";
import LedgerIcon from "@/Wagmi/Icons/LedgerIcon.vue";
import MetamaskIcon from "@/Wagmi/Icons/MetamaskIcon.vue";
import TrustWalletIcon from "@/Wagmi/Icons/TrustWalletIcon.vue";
import WalletConnectIcon from "@/Wagmi/Icons/WalletConnectIcon.vue";
import Web3Menu from "@/Wagmi/Menu.vue";
import Network from "@/Wagmi/Network.vue";

const Icons = {
	Injected: ChevronDoubleDownIcon,
	MetaMask: MetamaskIcon,
	"Trust Wallet": TrustWalletIcon,
	WalletConnect: WalletConnectIcon,
	WalletConnectLegacy: WalletConnectIcon,
	"Coinbase Wallet": CoinbaseWalletIcon,
	Safe: GnosisSafeIcon,
	Frame: FrameIcon,
	Ledger: LedgerIcon,
};

const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("sm");
const {init, SignIn} = useAuth();
nextTick(init);
const {disconnect} = useDisconnect();
const signOut = async () => {
	disconnect();
	await logOut();
};
const {open: openModal} = useWagmiModalToggle();
const connect = () => {
	openModal();
};
const AuthCheck = computed(() => usePage().props.AuthCheck);
const user = computed(() => usePage().props.user);
const avatar = computed(
	() => user.value?.avatar?.src ?? user.value?.profile_photo_url ?? user.value?.gravatar,
);
const {t} = useI18n();
const userNavigation = [
	{
		name: t("Your Profile"),
		href: window.route("profile.show", user.value?.username),
		icon: RiUserLine,
		active: window.route().current("profile.show"),
	},
	{
		name: t("Linked Accounts"),
		href: window.route("accounts.index"),
		icon: MdSettingsOutlined,
		active: window.route().current("account.*"),
	},
	{
		name: t("Withdraw Sleep"),
		href: window.route("questers.sleep"),
		icon: HiTicket,
		active: false,
	},
	{
		name: t("Referral Earnings"),
		href: window.route("user.referrals"),
		icon: LaUserPlusSolid,
		active: false,
	},
	{
		name: t("Your Giveaways"),
		href: window.route("projects.mine"),
		icon: RiAwardLine,
		active: false,
	},
	{
		name: t("Documentation"),
		href: "https://docs.sleep.finance",
		icon: HiSolidGift,
		active: false,
	},
];
const load = ref(false);
const {address, connector, isConnected} = useAccount();
const logout = async () => {
	load.value = true;
	await signOut();
	load.value = false;
};
defineProps({
	open: Boolean,
	searchRoute: {type: String, default: "projects.index"},
	blank: Boolean,
});
</script>
<template>
	<header
		class="bg-gray-50 dark:bg-gray-800 flex sticky top-0 w-full z-20 border-b border-gray-200 dark:border-gray-700"
	>
		<div class="h-16 item items-center flex justify-between py-0 px-4 relative w-full">
			<div class="flex justify-start">
				<Link
					href="/"
					class="w-auto flex mr-4 items-center"
				>
					<SiteLogo class="w-7 h-auto mr-3" />
					<h3 class="font-extralight hidden">Give away</h3>
				</Link>
				<SiteMenu class="hidden lg:flex" />
			</div>
			<div class="flex flex-row items-center space-x-3">
				<!--CreateButton /-->
				<PrimaryButton
					secondary
					link
					:href="route('giveaways.create')"
				>
					<VueIcon
						class="w-6 h-6 -ml-2"
						:icon="HiSolidPlusSm"
					/>
					<span v-if="!isSm">Create</span>
				</PrimaryButton>
				<Network />
				<Web3Menu v-if="AuthCheck">
					<template #profile>
						<img
							class="w-6 h-6 sm:-ml-2 border border-gray-400 dark:border-gray-600 sm:mr-2 rounded-full"
							:src="avatar"
						/>
					</template>
					<PopoverButton
						as="div"
						v-for="item in userNavigation"
						:key="item.name"
					>
						<InertiaLink
							:href="item.href"
							:class="[
								item.active
									? 'bg-gray-100 dark:bg-gray-500 text-gray-900 dark:text-gray-400'
									: 'text-gray-700 dark:text-gray-300',
								'group cursor-pointer flex items-center px-6 py-1.5 text-sm hover:bg-emerald-200/50 hover:text-emerald-600 dark:hover:bg-emerald-600/20 dark:hover:text-white',
							]"
						>
							<VueIcon
								:icon="item.icon"
								class="mr-3 h-5 w-5 text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-white"
								aria-hidden="true"
							/>
							{{ item.name }}
						</InertiaLink>
					</PopoverButton>
					<hr class="border-t border-gray-200 dark:border-gray-600 mt-2" />
					<InertiaLink
						href="#"
						@click="logout"
						class="group text-gray-700 hover:text-red-500 dark:text-gray-300 flex items-center px-6 py-3 text-base hover:bg-red-200/20 dark:hover:bg-gray-700/30 sm:rounded-b-sm rounded-b-none"
					>
						<Loading
							v-if="load"
							class="!text-red-500 !mr-3 !h-5 !w-5"
						/>
						<VueIcon
							:icon="FaPowerOff"
							v-else
							class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500 dark:group-hover:text-gray-300"
							aria-hidden="true"
						/>
						{{ $t("Sign out") }}
					</InertiaLink>
				</Web3Menu>
				<template v-else-if="isConnected && address && connector">
					<button
						class="px-4 py-2 ring-1 ring-offset-2 ring-emerald-600 ring-off-white dark:ring-offset-gray-900 rounded-lg bg-transparent border border-emerald-500/30 hover:bg-emerald-100/50 dark:hover:bg-emerald-800/20 text-emerald-600 font-semibold transition duration-150"
						@click="SignIn"
					>
						<component
							class="h-4 w-auto mr-2 -ml-1 inline-flex"
							:is="Icons[connector.name]"
						/>
						{{ $t("Signature") }}
					</button>
				</template>
				<template v-else>
					<button
						class="px-4 py-2 ring-1 ring-offset-2 ring-emerald-600 ring-off-white dark:ring-offset-gray-900 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition duration-150"
						@click="connect"
					>
						{{ $t("Connect") }}
					</button>
					<ConnectModal />
				</template>
				<DarkSwitch class="ml-2" />
				<RegisterModal />
			</div>
		</div>
	</header>
</template>
