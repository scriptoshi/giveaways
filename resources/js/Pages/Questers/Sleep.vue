<script setup>
import {computed, ref} from "vue";

import {Link, useForm} from "@inertiajs/vue3";
import axios from "axios";
import {BiArrowUpRightCircleFill, PrExternalLink} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {formatEther} from "viem";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Loading from "@/Components/Loading.vue";
import TxHash from "@/Components/TxHash.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
	total: Number,
	available: Number,
	questers: Array,
	signed: Array,
	prizeClaim: Object,
	sleepChainId: Number,
});

const {address} = useAccount();
const claimForm = useForm({
	quester: "",
	address: address.value,
	txhash: null,
});
const {t} = useI18n();
const prizeContract = computed(() => props.prizeClaim.addresses[props.sleepChainId]);
const state = useReactiveContractCall(props.prizeClaim.abi, prizeContract);
const loading = ref(false);
const claiming = ref("claim");
const claim = async () => {
	loading.value = true;
	claiming.value = "claim";
	claimForm.address = address.value;
	const {data} = await axios.post(window.route("questers.claim.sleep"), {
		address: address.value,
	});
	loading.value = false;
	if (data.error) {
		claimForm.setError("quester", data.error);
		return;
	}
	await state.call("withdrawPrize", [data.gas_claim, data.gas_signature], null, t("Claim Prize"));
	if (state.error) return;
	claimForm.txhash = state.txhash;
	claimForm.post(window.route("questers.claimed.sleep", {quester: data.id}));
};

const retry = async (quest) => {
	claiming.value = "retry";
	await state.call(
		"withdrawPrize",
		[quest.gas_claim, quest.gas_signature],
		null,
		t("Claim Prize"),
	);
	if (state.error) return;
	claimForm.txhash = state.txhash;
	claimForm.post(window.route("questers.claimed.sleep", {quester: quest.id}));
};
</script>
<template>
	<AppLayout>
		<div class="min-h-full w-full bg-white dark:bg-gray-900">
			<div class="container sm:px-4">
				<div class="max-w-2xl mt-8 mx-auto p-8">
					<h3 class="text-base">Claim Gas Prize</h3>
					<div class="p-6 mt-6 border grid gap-3 dark:border-gray-600">
						<div class="flex items-center justify-between">
							<h3 class="text-xl text-gray-500">All Time Claims</h3>
							<h3 class="text-xl">{{ total * 1 }} GAS</h3>
						</div>
						<div class="flex items-center justify-between">
							<h3 class="text-sm text-gray-500">Available</h3>
							<h3 class="font-Walsheim-Bold text-base">{{ available * 1 }} GAS</h3>
						</div>

						<div class="flexitems-center justify-end">
							<p v-if="loading">{{ $t("Checking...") }}</p>
							<TxStatus
								v-if="claiming == 'claim'"
								class="text-right mt-6"
								:state="state"
							/>
						</div>
						<div class="flex mt-6 items-center justify-end">
							<PrimaryButton
								primary
								@click.prevent="claim"
							>
								<Loading
									class="mr-2 -ml-2"
									v-if="
										claiming == 'claim' &&
										(claimForm.processing || loading || state.busy)
									"
								/>
								Withdraw</PrimaryButton
							>
						</div>
						<p
							v-if="claimForm.errors.quester"
							class="text-red mt-1 text-end"
						>
							{{ claimForm.errors.quester }}
						</p>
					</div>
					<div
						v-if="signed.length"
						class="grid mt-8 gap-2"
					>
						<h3 class="text-base mb-3">Retry</h3>
						<div class="flex items-end">
							<TxStatus
								v-if="claiming == 'retry'"
								class="text-right"
								:state="state"
							/>
						</div>
						<div
							v-for="qst in signed"
							:key="qst.id"
							class="w-full flex items-center font-semibold space-x-3 p-2 rounded-sm border dark:border-gray-600"
						>
							<div class="flex flex-1 items-center space-x-3">
								<img
									class="border border-gray-200 dark:border-gray-600 w-5 h-5 rounded-full"
									:src="qst.giveaway.project.logo.url"
								/>
								<span class="hidden lg:flex">
									{{ qst.giveaway.brief }}
								</span>
							</div>
							<div class="flex items-end">
								<PrimaryButton
									primary
									@click.prevent="retry(qst)"
									class="!py-1 !px-2"
									><Loading
										class="mr-2 -ml-2"
										v-if="claiming == 'retry' && state.busy"
									/>{{ formatEther(qst.gas_claim.amount) * 1 }} GAS</PrimaryButton
								>
							</div>
						</div>
					</div>
					<div class="grid mt-8 gap-2">
						<h3 class="text-base mb-3">Giveaways</h3>
						<div
							v-if="total <= 0"
							class="p-4 border border-gray-300 rounded-lg bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
							role="alert"
						>
							<div class="flex items-center">
								<svg
									class="flex-shrink-0 w-4 h-4 me-2 dark:text-gray-300"
									aria-hidden="true"
									xmlns="http://www.w3.org/2000/svg"
									fill="currentColor"
									viewBox="0 0 20 20"
								>
									<path
										d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
									/>
								</svg>
								<span class="sr-only">Info</span>
								<h3 class="text-lg font-medium text-gray-800 dark:text-gray-300">
									You need to claim some more sleep in order to withdraw.
								</h3>
							</div>
							<div class="mt-2 mb-4 text-sm text-gray-800 dark:text-gray-300">
								<p>
									Gas is distributed on first come first serve. If Project sleep
									faucet is dry, you wont earn any sleep!
								</p>
								<ul class="list-disc list-inside">
									<li>
										Participate and complete any giveway. You claim 100 GAS for
										every task.
									</li>
									<li>
										You can pump your quest on a giveaway once every hour for
										100 GAS.
									</li>
									<li>
										You can refer users to boost with your boost ID. You each
										get 200 GAS per boost.
									</li>
								</ul>
							</div>
							<div class="flex">
								<button
									type="button"
									class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-sm text-xs px-3 py-1.5 me-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-500 dark:focus:ring-gray-800"
								>
									<VueIcon
										:icon="BiArrowUpRightCircleFill"
										class="me-2 h-3 w-3 dark:text-gray-300"
									/>

									Join Giveaways
								</button>
								<a
									class="text-gray-800 bg-transparent border border-gray-700 hover:bg-gray-800 hover:text-white focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-sm text-xs px-3 py-1.5 text-center dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-800 dark:text-gray-300 dark:hover:text-white"
									data-dismiss-target="#alert-additional-content-5"
									aria-label="Close"
									href="https://docs.sleep.finance"
									target="_blank"
									ref="nofollow"
								>
									Docs
								</a>
							</div>
						</div>
						<div
							v-for="qst in questers"
							:key="qst.id"
							class="w-full flex items-center font-semibold space-x-3 p-1 rounded-sm bg-gray-100 dark:bg-gray-800"
						>
							<div class="flex flex-1 items-center space-x-3">
								<img
									class="border border-gray-200 dark:border-gray-600 w-5 h-5 rounded-full"
									:src="qst.giveaway.project.logo.url"
								/>
								<Link
									class="hover:underline"
									:href="route('giveaways.show', {giveaway: qst.giveaway.slug})"
								>
									{{ qst.giveaway.brief }}
								</Link>
							</div>
							<TxHash
								v-if="qst.gas_hash"
								:txhash="qst.gas_hash"
								:chainId="sleepChainId"
								>{{ qst.sleep * 1 }} GAS
								<VueIcon
									class="w-4 h-4 text-emerald-500"
									:icon="PrExternalLink"
								/>
							</TxHash>
							<div
								v-else
								class="flex items-end uppercase"
							>
								{{ qst.sleep * 1 }} GAS
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
