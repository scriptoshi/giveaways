<script setup>
import {ChevronDoubleDownIcon} from "@heroicons/vue/24/solid";
import {Head} from "@inertiajs/vue3";
import {useAccount, useConnect} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import SiteLogo from "@/Components/SiteLogo.vue";
import RegisterModal from "@/Pages/Auth/RegisterModal.vue";
import {useAuth} from "@/Wagmi/hooks/authentication";
import CoinbaseWalletIcon from "@/Wagmi/Icons/CoinbaseWalletIcon.vue";
import FrameIcon from "@/Wagmi/Icons/FrameIcon.vue";
import GnosisSafeIcon from "@/Wagmi/Icons/GnosisSafeIcon.vue";
import LedgerIcon from "@/Wagmi/Icons/LedgerIcon.vue";
import MetamaskIcon from "@/Wagmi/Icons/MetamaskIcon.vue";
import TrustWalletIcon from "@/Wagmi/Icons/TrustWalletIcon.vue";
import WalletConnectIcon from "@/Wagmi/Icons/WalletConnectIcon.vue";
const {init, activate, busy} = useAuth();
init();
const {connect, connectors, error, isLoading, pendingConnector} = useConnect();
const {connector, isConnected} = useAccount();
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
defineProps({
	canResetPassword: Boolean,
	status: String,
});
</script>
<template>
	<Head title="Connect Wallet" />
	<main
		class="grid w-full h-screen grow grid-cols-1 place-items-center bg-white dark:bg-gray-900"
	>
		<div class="w-full max-w-[26rem] p-4 sm:px-5">
			<div class="flex items-center space-x-3">
				<SiteLogo class="h-14 w-auto" />
				<div class="">
					<h2 class="text-4xl font-semibold text-gray-600 dark:text-navy-100">
						{{ $t("Welcome Back") }}
					</h2>
					<p
						v-if="error"
						class="text-red-500 dark:text-red-400"
					>
						{{ error }}
					</p>
					<p class="text-gray-400 dark:text-navy-300">
						{{ $t("Please Connect a wallet to Proceed") }}
					</p>
				</div>
			</div>
			<div class="mt-10">
				<ul
					v-if="isConnected"
					class="my-4 space-y-3"
				>
					<li>
						<a
							href="#"
							@click.prevent="activate"
							class="flex items-center p-3 text-base mb-3 font-bold text-gray-900 border border-emerald-500 bg-gray-50 rounded-lg hover:bg-emerald-100 hover:text-emerald-600 dark:hover:text-white group hover:shadow dark:bg-gray-800 dark:hover:bg-emerald-500 dark:text-white"
						>
							<Loading
								v-if="busy"
								class="!w-8 !h-8 !text-emerald-500"
							/>
							<component
								v-else
								class="h-8 w-auto mr-6"
								:is="Icons[connector.name]"
							/>
							<span class="flex-1 ml-3 whitespace-nowrap">
								{{ $t("Request Signature") }}</span
							>
						</a>
					</li>
				</ul>
				<ul
					v-else
					class="my-4 space-y-3"
				>
					<li
						v-for="provider in connectors"
						:id="`connect-${provider.name}`"
						:key="provider.name"
					>
						<a
							href="#"
							:provider="provider"
							@click="connect({connector: provider})"
							class="flex items-center p-3 text-base mb-3 font-bold text-gray-900 border border-emerald-500 bg-gray-50 rounded-lg hover:bg-emerald-100 hover:text-emerald-600 dark:hover:text-white group hover:shadow dark:bg-gray-800 dark:hover:bg-emerald-500 dark:text-white"
						>
							<Loading
								v-if="isLoading && provider.id === pendingConnector?.id"
								class="!w-8 !h-8 !text-emerald-500"
							/>
							<component
								v-else
								class="h-8 w-auto mr-6"
								:is="Icons[provider.name]"
							/>
							<span class="flex-1 ml-3 whitespace-nowrap">
								{{ $t("Connect") }} {{ provider.name }}</span
							>
							<span
								v-if="provider.name == 'MetaMask'"
								class="inline-flex items-center justify-center px-2 py-0.5 ml-3 text-xs font-medium text-gray-500 bg-gray-200 rounded dark:bg-gray-700 dark:text-gray-400"
								>{{ $t("Popular") }}</span
							>
						</a>
					</li>
				</ul>
				<div class="mt-4 text-xs+">
					<p class="line-clamp-1">
						<span class="text-gray-600 dark:text-gray-300 mr-3">{{
							$t("Dont have Account?")
						}}</span>

						<a
							class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
							:href="route('register')"
							>{{ $t("Create account") }}</a
						>
					</p>
				</div>
			</div>
			<div class="mt-8 flex text-xs text-gray-400 dark:text-navy-300">
				<a href="#">{{ $t("Privacy Notice") }}</a>
				<div class="mx-3 my-1 w-px bg-gray-500 dark:bg-navy-500"></div>
				<a href="#">{{ $t("Term of service") }}</a>
			</div>
		</div>
		<RegisterModal />
	</main>
</template>
