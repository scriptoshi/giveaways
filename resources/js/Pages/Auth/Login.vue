<script setup>
import {ref} from "vue";

import {ChevronDoubleDownIcon} from "@heroicons/vue/24/solid";
import {Head} from "@inertiajs/vue3";
import {useAccount, useConnect, useDisconnect} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Loading from "@/Components/Loading.vue";
import SiteLogo from "@/Components/SiteLogo.vue";
import WeCopy from "@/Components/WeCopy.vue";
import RegisterModal from "@/Pages/Auth/RegisterModal.vue";
import {
	isSignining,
	busy as modalBusy,
	error as modalError,
	signatureRejected,
	useAuth,
} from "@/Wagmi/hooks/authentication";
import CoinbaseWalletIcon from "@/Wagmi/Icons/CoinbaseWalletIcon.vue";
import FrameIcon from "@/Wagmi/Icons/FrameIcon.vue";
import GnosisSafeIcon from "@/Wagmi/Icons/GnosisSafeIcon.vue";
import LedgerIcon from "@/Wagmi/Icons/LedgerIcon.vue";
import MetamaskIcon from "@/Wagmi/Icons/MetamaskIcon.vue";
import TrustWalletIcon from "@/Wagmi/Icons/TrustWalletIcon.vue";
import WalletConnectIcon from "@/Wagmi/Icons/WalletConnectIcon.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";
const {init, SignIn} = useAuth();
init();
const {connect, connectors, error} = useConnect();
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
const {disconnect} = useDisconnect();
const pendingConnector = ref(null);

const leave = () => {
	disconnect();
	pendingConnector.value = null;
	isSignining.value = false;
	modalBusy.value = false;
	modalError.value = null;
};
const {address, isConnected, connector} = useAccount();
const attemptConnection = ({connector}) => {
	if (isConnected.value) {
		SignIn();
	}
	pendingConnector.value = connector;
	connect({connector});
};
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
					<h2 class="text-4xl font-semibold text-gray-600 dark:text-gray-100">
						Welcome Back
					</h2>
					<p
						v-if="error"
						class="text-red-500 dark:text-red-400"
					>
						{{ error }}
					</p>
					<p class="text-gray-400 dark:text-gray-300">
						Please Connect a wallet to Proceed
					</p>
				</div>
			</div>
			<div class="mt-10">
				<div
					class="p-6"
					v-if="isConnected"
				>
					<div class="py-2 mb-5">
						<p
							v-if="modalError"
							class="text-sm font-normal text-red-500 dark:text-red-400"
						>
							{{ modalError?.message ?? modalError.toString() }}
						</p>
						<p
							v-else-if="error"
							class="text-sm font-normal text-red-500 dark:text-red-400"
						>
							{{ error?.message ?? error.toString() }}
						</p>
						<ul class="my-4 space-y-3">
							<li>
								<button
									@click="SignIn"
									type="button"
									class="flex items-center w-full p-3 text-base mb-3 font-bold text-gray-900 border border-emerald-500 bg-gray-50 rounded-[4px] hover:bg-emerald-100 hover:text-emerald-600 dark:hover:text-white group hover:shadow dark:bg-gray-800/20 dark:hover:bg-emerald-500 dark:text-white"
								>
									<component
										class="h-6 w-auto mr-6"
										:is="Icons[connector.name]"
									/>
									<div class="size-2 rounded-full bg-success mr-1"></div>
									<span class="whitespace-nowrap">{{ connector.name }}</span>

									<span
										class="inline-flex items-center justify-center px-2 py-0.5 ml-4 text-xs font-medium text-gray-500 bg-gray-200 rounded dark:bg-gray-700 dark:text-gray-400"
										>{{ $t("Signature") }}</span
									>
								</button>
							</li>
						</ul>
					</div>

					<h3 class="text-base text-gray-800 dark:text-gray-300 mb-4">
						{{ $t("A signature request has been sent to your wallet.") }}
					</h3>
					<div
						class="text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-[4px] p-4"
					>
						{{
							$t(
								"In order to authenticate your account, sign the onetime code provided using your connected address.",
							)
						}}
						<WeCopy
							after
							:text="address"
							class="mt-2"
						>
							<div
								class="text-sm px-3 border rounded-[4px] border-gray-200 dark:border-gray-600 font-semibold"
							>
								{{ shortenAddress(address) }}
							</div>
						</WeCopy>
					</div>

					<div class="flex items-start space-x-3 justify-end w-full mt-12">
						<PrimaryButton
							v-if="isConnected && signatureRejected"
							@click="SignIn()"
							secondary
							>Try Again</PrimaryButton
						>
						<PrimaryButton
							@click="leave"
							error
							>Disconnect</PrimaryButton
						>
					</div>
				</div>
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
							@click="attemptConnection({connector: provider})"
							class="flex items-center p-3 text-base mb-3 font-bold text-gray-900 border border-emerald-500 bg-gray-50 rounded-[4px] hover:bg-emerald-100 hover:text-emerald-600 dark:hover:text-white group hover:shadow dark:bg-gray-800 dark:hover:bg-emerald-500 dark:text-white"
						>
							<Loading
								v-if="provider.id === pendingConnector?.id"
								class="!w-8 !h-8 !text-emerald-500"
							/>
							<component
								v-else
								class="h-8 w-auto mr-6"
								:is="Icons[provider.name]"
							/>
							<span class="flex-1 ml-3 whitespace-nowrap">
								Connect {{ provider.name }}</span
							>
							<span
								v-if="provider.name == 'MetaMask'"
								class="inline-flex items-center justify-center px-2 py-0.5 ml-3 text-xs font-medium text-gray-500 bg-gray-200 rounded dark:bg-gray-700 dark:text-gray-400"
								>Popular</span
							>
						</a>
					</li>
				</ul>
				<div class="mt-4 text-xs+">
					<p class="line-clamp-1">
						<span class="text-gray-600 dark:text-gray-300 mr-3"
							>Dont have Account?</span
						>

						<a
							class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
							:href="route('register')"
							>Create account</a
						>
					</p>
				</div>
			</div>
			<div class="mt-8 flex text-xs text-gray-400 dark:text-gray-300">
				<a href="#">Privacy Notice</a>
				<div class="mx-3 my-1 w-px bg-gray-500 dark:bg-gray-500"></div>
				<a href="#">Term of service</a>
			</div>
		</div>
		<RegisterModal />
	</main>
</template>
