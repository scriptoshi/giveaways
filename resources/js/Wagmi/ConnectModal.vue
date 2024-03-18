<script setup>
import {computed, ref} from "vue";

import {ChevronDoubleDownIcon, XMarkIcon} from "@heroicons/vue/24/solid";
import {Link, usePage} from "@inertiajs/vue3";
import {useAccount, useConnect} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import {busy as modalBusy, error as modalError, useAuth} from "@/Wagmi/hooks/authentication";
import {useWagmiModalToggle} from "@/Wagmi/hooks/useWagmiModalToggle.js";
import CoinbaseWalletIcon from "@/Wagmi/Icons/CoinbaseWalletIcon.vue";
import FrameIcon from "@/Wagmi/Icons/FrameIcon.vue";
import GnosisSafeIcon from "@/Wagmi/Icons/GnosisSafeIcon.vue";
import LedgerIcon from "@/Wagmi/Icons/LedgerIcon.vue";
import MetamaskIcon from "@/Wagmi/Icons/MetamaskIcon.vue";
import TrustWalletIcon from "@/Wagmi/Icons/TrustWalletIcon.vue";
import WalletConnectIcon from "@/Wagmi/Icons/WalletConnectIcon.vue";
const {isOpen, close} = useWagmiModalToggle();
const {connect, connectors, error, isPending} = useConnect({
	// onSuccess: () => close(),
});
const pendingConnector = ref(null);
modalBusy.value = false;
modalError.value = null;
const {SignIn} = useAuth();
const {connector: provider, isReconnecting} = useAccount();
const attemptConnection = ({connector}) => {
	if (provider?.value?.ready) {
		SignIn();
	}
	pendingConnector.value = connector;
	connect({connector});
};
const appName = computed(() => usePage().props.appName ?? "Betn");
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
</script>
<template>
	<div v-if="isOpen">
		<teleport to="body">
			<div
				v-bind="$attrs"
				id="crypto-modal"
				tabindex="-1"
				class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full justify-center items-center flex"
				aria-modal="true"
				role="dialog"
			>
				<div class="relative w-full h-full max-w-md md:h-auto">
					<!-- Modal content -->
					<div
						class="relative bg-white border border-gray-300 dark:border-gray-600 rounded-lg shadow dark:bg-gray-800"
					>
						<button
							type="button"
							class="absolute top-3 text-gray-600 hover:text-red-500 dark:hover:text-red-400 dark:text-gray-300 right-4 btn -mr-1.5 h-7 w-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
							@click="close"
							icon
						>
							<XMarkIcon class="w-5 h-5" />
						</button>
						<!-- Modal header -->
						<div class="px-6 py-4 border-b rounded-t dark:border-gray-600">
							<h3
								class="text-base font-semibold text-gray-900 lg:text-xl dark:text-white"
							>
								{{ $t("Welcome to {appName}", {appName}) }}
							</h3>
						</div>
						<!-- Modal body -->
						<div class="p-6">
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
							<p
								v-else
								class="text-sm font-normal text-gray-500 dark:text-gray-400"
							>
								{{ $t("Please select sign-in method") }}
							</p>
							<ul class="my-4 space-y-3">
								<li
									v-for="connector in connectors"
									:key="connector.id"
								>
									<button
										@click="attemptConnection({connector})"
										:disabled="isPending || modalBusy"
										type="button"
										class="flex items-center w-full p-3 text-base mb-3 font-bold text-gray-900 border border-emerald-500 bg-gray-50 rounded-lg hover:bg-emerald-100 hover:text-emerald-600 dark:hover:text-white group hover:shadow dark:bg-gray-800/20 dark:hover:bg-emerald-500 dark:text-white"
									>
										<Loading
											v-if="
												(isPending || modalBusy || isReconnecting) &&
												connector.id === pendingConnector?.id
											"
											class="!w-6 !h-6 !text-emerald-500 dark:!text-white"
										/>
										<component
											v-else
											class="h-6 w-auto mr-6"
											:is="Icons[connector.name]"
										/>

										<span class="ml-3 whitespace-nowrap">{{
											connector.name
										}}</span>

										<span
											v-if="
												(isLoading || modalBusy || isReconnecting) &&
												connector.id === pendingConnector?.id
											"
											class="inline-flex items-center justify-center px-2 py-0.5 ml-3 text-xs font-medium text-gray-500 bg-gray-200 rounded dark:bg-gray-700 dark:text-gray-400"
											>{{ $t("Loading") }}</span
										>
									</button>
								</li>
							</ul>
							<div class="mt-4 text-center text-xs+">
								<p class="line-clamp-1">
									<span class="text-gray-600 dark:text-gray-300 mr-3"
										>{{ $t("Dont have Account") }}?</span
									>
									<Link
										class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
										:href="route('register')"
										>{{ $t("Create account") }}</Link
									>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div
				@click="close"
				class="bg-gray-900 opacity-50 dark:opacity-80 fixed inset-0 z-40"
			></div>
		</teleport>
	</div>
</template>
