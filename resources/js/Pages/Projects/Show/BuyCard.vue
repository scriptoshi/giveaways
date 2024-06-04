<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {useAccount, useChainId, useChains} from "use-wagmi";
import {parseEther} from "viem";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CountDownText from "@/Components/CountDownText.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import TxStatus from "@/Components/TxStatus.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import {useBuyCardInfo} from "@/hooks/useBuyCard";
import DialogModal from "@/Jetstream/DialogModal.vue";
import NetworkIcon from "@/Wagmi/components/Icons/NetworkIcon.vue";
const props = defineProps({
	launchpad: Object,
});
const {address} = useAccount();
const form = useForm({
	txhash: "",
	chainId: "",
});
watch(address, (address) => {
	if (address) form.address = address;
});
const chainId = useChainId();
const chains = useChains();
const chain = computed(() => chains.value.find((c) => c.id === parseInt(props.launchpad.chainId)));
const state = useReactiveContractCall(props.launchpad.abi, props.launchpad.contract);
const sale = useBuyCardInfo(props.launchpad.abi, props.launchpad.contract);
const {t} = useI18n();
const buy = async () => {
	form.clearErrors();
	if (parseFloat(form.amount) === 0 || !form.amount || form.amount === "") {
		form.setError("amount", t("Cannot contribute zero"));
		return;
	}
	const amount = parseEther(form.amount);
	console.log(amount, form.amount);
	await state.call("contribute", [], amount, t("Contribute Fairsale"));
	form.txhash = state.txhash;
	form.chainId = chainId.value;
	form.post(window.route("contributions.store", {launchpad: props.launchpad.id}), {
		preserveScroll: true,
	});
	if (!state.error) sale.update(address.value);
};

const withdraw = async () => {
	await state.call("claim", [], null, t("Claim Tokens"));
	sale.update(address.value);
};
const teamClaim = async () => {
	await state.call("teamClaim", [], null, t("Claim Team Tokens"));
	sale.update(address.value);
};
const addLiquidity = async () => {
	await state.call("addLiquidity", [], null, t("Add Liquidity"));
	sale.update(address.value);
};
const why = ref(false);
</script>
<template>
	<div
		class="shadow-sm border border-gray-200 dark:border-gray-600 dark:shadow-lg text-sm dark:text-gray-300 dark-text-emerald-400-text-dark p-4 rounded-sm bg-white dark:bg-gray-800"
	>
		<div class="max-w-xs mx-auto rounded-sm">
			<div
				v-if="sale.loading"
				class="flex items-center justify-center mb-4"
			>
				<Loading class="w-8 h-8" />
			</div>
			<template v-else-if="sale.ended">
				<div class="flex items-center justify-between mb-4">
					<h3 class="">Claim Time</h3>
					<CountDownText :timestamp="parseInt(sale.claimTime)" />
				</div>
				<div class="flex items-center justify-between">
					<div class="flex justify-between">
						<h3 class="text-lg">
							{{ sale.available }} {{ chain.nativeCurrency.symbol }}
						</h3>
					</div>
					<div>
						<div class="w-full flex items-center justify-end">
							<PrimaryButton
								@click.prevent="withdraw"
								:disabled="state.busy == 'claim'"
								primary
							>
								<Loading
									v-if="state.busy == 'claim'"
									class="mr-2 -ml-1 !text-white"
								/>
								{{ $t("Claim Purchase") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
				<div class="flex items-center mt-4 space-x-3 justify-between">
					<PrimaryButton
						@click.prevent="addLiquidity"
						class="w-full"
						:disabled="state.busy == 'addLiquidity'"
						secondary
					>
						<Loading
							v-if="state.busy == 'addLiquidity'"
							class="mr-2 -ml-1"
						/>
						{{ $t("Finanlize") }}
					</PrimaryButton>
					<PrimaryButton
						class="w-full"
						@click.prevent="teamClaim"
						:disabled="state.busy == 'teamClaim'"
						secondary
					>
						<Loading
							v-if="state.busy == 'teamClaim'"
							class="mr-2 -ml-1"
						/>
						{{ $t("Team Claim") }}
					</PrimaryButton>
				</div>
				<TxStatus
					class="mt-4 text-right"
					:state="state"
				/>
			</template>
			<template v-else>
				<div class="flex items-center justify-between mb-4">
					<h3 class="">Contribution</h3>
					<CountDownText :timestamp="parseInt(sale.endDate)" />
				</div>
				<div class="flex items-start justify-between">
					<FormInput
						:error="form.errors.amount"
						v-model="form.amount"
					>
						<template #trail>
							<div class="flex relative space-x-1 items-center">
								<div>{{ chain.nativeCurrency.symbol }}</div>
								<NetworkIcon
									class="bg-white rounded-full h-5 w-5"
									:chain-id="chainId"
								/>
							</div>
						</template>
					</FormInput>
					<div>
						<div class="w-full flex items-center justify-end">
							<PrimaryButton
								@click.prevent="buy"
								:disabled="state.busy"
								primary
							>
								<Loading
									v-if="state.busy && form.processing"
									class="mr-2 -ml-1 !text-white"
								/>
								{{ $t("GET") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
				<TxStatus
					class="my-3"
					:state="state"
				/>
				<div class="flex mt-2 text-xs font-semibold items-start space-x-1">
					<a
						v-for="item in ['0.01', '0.05', '0.1', '0.5', '1']"
						:key="item"
						class="px-1 bg-white hover:bg-gray-100 dark:hover:text-white dark:bg-gray-900 border rounded-sm border-gray-300 dark:border-gray-600"
						href="#"
						@click.prevent="form.amount = item"
					>
						{{ item }} {{ chain.nativeCurrency.symbol }}
					</a>
					<a
						class="px-1 bg-white hover:bg-gray-100 dark:hover:text-white dark:bg-gray-900 border rounded-sm border-gray-300 dark:border-gray-600"
						href="#"
						@click.prevent="form.amount = ''"
					>
						X
					</a>
				</div>
			</template>

			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-y mt-4 border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">{{ $t("Market cap") }}</div>
				<div class="flex items-center gap-1 text-right">
					<div class="break-all">
						${{ (sale.available * $page.props.usdRate * 2).toFixed(4) * 1 }} ({{
							sale.available
						}}
						{{ chain.nativeCurrency.symbol }})
					</div>
				</div>
			</div>
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">{{ $t("Contributors") }}</div>
				<div class="flex items-center gap-1 text-right text-gray-500">
					<div class="break-all">{{ sale.count }}</div>
				</div>
			</div>
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">{{ $t("Your Contribution") }}</div>
				<div class="flex items-center gap-1 text-right text-gray-500">
					<div class="break-all">
						{{ sale.contributions }} {{ chain.nativeCurrency.symbol }}
					</div>
				</div>
			</div>
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">{{ $t("Alocated") }} {{ launchpad.symbol }}</div>
				<div class="flex items-center gap-1 text-right text-gray-500">
					<div class="break-all">
						Pending
						<a
							class="ml-4 text-sky-500 hover:text-sky-700 dark:text-sky-300 dark:hover:text-sky-300"
							href="#"
							@click.prevent="why = true"
							>[ why? ]</a
						>
					</div>
				</div>
			</div>
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">{{ $t("Withdraw Date") }}</div>
				<div class="flex items-center gap-1 text-right text-gray-500">
					<div class="break-all">
						{{ sale.claimDate }}
					</div>
				</div>
			</div>
		</div>
		<DialogModal
			:show="why"
			@close="why = false"
			closeable
		>
			<template #title> Why is Token allocation pending ? </template>
			<template #content>
				<ul class="list-disc list-inside">
					<li>
						All launches on sleep finance V2 are prize fairlaunch. It means the final
						price of the token is not known until the the sale ends
					</li>
					<li>
						In a fair launch, all tokens listed for sale are distributed to contributors
						based on contribution. So if you contribute 1% of sales, you get 1% of
						tokens listed
					</li>
					<li>
						In A prize fair launch, every user waits 10 days before they can withdraw.
						this means early contributors get to withdraw first.
					</li>
					<p class="font-semibold">Prize distribution vs Contribution:</p>
					<li>
						All users who contribute WILL RECIEVE TOKENS regardless of if they win prize
						or not. User who win prize but did not contribute will not access tokens.
					</li>
					<li>
						All users who contribute WILL BE legible for 5% sale kickback prize. This
						prize will be distributed by SLEEP finance Team to top 10 pumpers
						leaderboard.!
					</li>
				</ul>
			</template>
		</DialogModal>
	</div>
</template>
