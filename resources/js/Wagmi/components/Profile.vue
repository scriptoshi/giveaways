<script setup>
import {computed, ref} from "vue";

import {ChevronRightIcon} from "@heroicons/vue/24/solid";
import {router} from "@inertiajs/vue3";
import axios from "axios";
import {GiPowerButton, HiRefresh, RiExternalLinkFill} from "oh-vue-icons/icons";
import {useAccount, useBalance, useDisconnect} from "use-wagmi";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import Jazzicon from "@/Wagmi/components/Jazzicon.vue";
import {useChain} from "@/Wagmi/hooks/useChain";
import {shortenAddress} from "@/Wagmi/utils/utils";
const {disconnect} = useDisconnect();
const {chain, address} = useAccount();
const load = ref(false);
const logout = async () => {
	load.value = true;
	disconnect();
	await axios.post(window.route("logout"));
	router.reload({
		onFinish() {
			load.value = false;
		},
	});
};
const {
	data: etherBalance,
	refetch: accountRefetch,
	status,
} = useBalance({
	address,
	watch: true,
});
const etherScanLink = computed(() => useChain(chain.value).getAccountUrl(address.value));
defineProps({
	showTxs: Boolean,
});
const emit = defineEmits(["showTransactions"]);
const showTransactions = () => emit("showTransactions");
</script>
<template>
	<CollapseTransition>
		<div>
			<div
				v-show="!showTxs"
				class="flex flex-col gap-4 pt-4"
			>
				<div class="px-4 flex justify-between gap-3">
					<div
						class="text-sm leading-5 font-semibold flex items-center gap-1.5 text-gray-700 dark:text-gray-200"
					>
						<Loading
							class="h-5 w-5"
							v-if="status === 'loading'"
						/>
						<Jazzicon
							v-else
							:address="address"
							:diameter="20"
							class="h-full flex items-center"
						/>
						<WeCopy
							:text="address"
							after
						>
							{{ shortenAddress(address) }}
						</WeCopy>
					</div>
					<div class="flex gap-3">
						<a
							:href="etherScanLink"
							target="_blank"
							ref="nofollow"
							class="flex items-center text-emerald-500 hover:text-emerald-700 justify-center -mr-1.5 h-8 w-8 rounded-full p-0 bg-emerald-300/20 hover:border hover:border-emerald-500/50 focus:bg-emerald-400/20 active:border-emerald-500/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
						>
							<VueIcon
								class="h-5 w-5"
								:icon="RiExternalLinkFill"
							/>
						</a>
						<button
							@click="logout"
							class="flex items-center text-red-500 hover:text-red-700 justify-center -mr-1.5 h-8 w-8 rounded-full p-0 bg-red-300/20 hover:border hover:border-red-500/50 focus:bg-red-400/20 active:border-red-500/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
						>
							<Loading
								v-if="load"
								class="!h-4 !mr-0 !w-4 !text-red-500"
							/>
							<VueIcon
								class="h-5 w-5"
								v-else
								:icon="GiPowerButton"
							/>
						</button>
					</div>
				</div>
				<div class="flex items-center justify-center gap-2">
					<div class="text-4xl text-emerald-500 font-normal whitespace-nowrap">
						{{ parseFloat(etherBalance?.formatted ?? 0).toFixed(6) * 1 }}
						{{ chain?.nativeCurrency?.symbol }}
					</div>
					<a
						href="#"
						@click="accountRefetch"
						class="flex items-center text-emerald-500 hover:text-emerald-700 justify-center -mr-1.5 h-8 w-8 rounded-full p-0 bg-emerald-300/20 hover:border hover:border-emerald-500/50 focus:bg-emerald-400/20 active:border-emerald-500/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
					>
						<Loading
							class="h-5 w-5"
							v-if="status === 'loading'"
						/>
						<VueIcon
							class="h-5 w-5"
							:icon="HiRefresh"
							v-else
						/>
					</a>
				</div>
				<button
					@click="showTransactions"
					class="flex text-sm font-semibold w-full hover:text-gray-700 text-gray-600 dark:text-gray-300 dark:hover:text-white justify-between items-center hover:bg-gray-200/40 dark:hover:bg-gray-700/30 px-6 py-2.5"
				>
					<span class="px-2">{{ $t("View My Transactions") }}</span>
					<ChevronRightIcon class="w-5 h-5" />
				</button>
			</div>
			<div class="px-2">
				<div class="w-full h-px mt-3 bg-emerald-500/60"></div>
			</div>
			<div class="pt-2">
				<slot />
			</div>
		</div>
	</CollapseTransition>
</template>
