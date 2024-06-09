<script setup>
import {computed, ref, watch} from "vue";

import {useForm, usePage} from "@inertiajs/vue3";
import {useAccount, useChainId, useChains, usePublicClient} from "use-wagmi";
import {formatEther, parseEther, parseEventLogs} from "viem";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import TxStatus from "@/Components/TxStatus.vue";
import UnsupportedFactory from "@/Components/UnsupportedFactory.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import {useBillions} from "@/hooks/useBillions";
import DialogModal from "@/Jetstream/DialogModal.vue";
const {chainId: uChainId} = useAccount();
const props = defineProps({
	project: Object,
	factory: Object,
	NFPM: Object,
	WETH9: Object,
});
const showHowItWorks = ref(false);
const chainId = computed(() => {
	if (usePage().props.AuthCheck) return uChainId.value;
	return useChainId().value;
});
const chains = useChains();
const chain = computed(() => chains.value.find((c) => c.id === chainId.value));
const factoryAddress = computed(() => props.factory?.addresses?.[chainId.value]);
const nfpm = computed(() => props.NFPM?.[chainId.value]);
const weth9 = computed(() => props.WETH9?.[chainId.value]);
const state = useReactiveContractCall(props.factory.abi, factoryAddress);
const fees = ref(0n);
const feesFormatted = computed(() => formatEther(fees.value));
const publicClient = usePublicClient();
const loadFees = async () => {
	fees.value = await publicClient.value.readContract({
		abi: props.factory.abi,
		address: factoryAddress.value,
		functionName: "fees",
	});
};
watch(
	chainId,
	(chainId) => {
		if (!chainId) return;
		if (!factoryAddress.value) return;
		loadFees();
	},
	{immediate: true},
);
const form = useForm({
	name: "",
	symbol: "",
	supply: "",
	chainId: null,
	address: null,
	contract: null,
});
const {t} = useI18n();
const deploy = async () => {
	form.clearErrors();
	if (!form.name) form.setError("name", t("Token needs a name"));
	if (!form.symbol) form.setError("symbol", t("Token needs a symbol"));
	if (!form.supply) form.setError("supply", t("Token needs some supply"));
	if (form.hasErrors) {
		state.error = "It seems you missed you stuff we need. check the form!";
		return;
	}
	await state.call(
		"create",
		[
			{
				name: form.name,
				symbol: form.symbol,
				supply: parseEther(form.supply.toString()),
				weth: weth9.value,
				uniswap: nfpm.value,
			},
		],
		fees.value,
		t("Deploy Meme Sale"),
	);
	const logs = parseEventLogs({
		abi: props.factory.abi,
		logs: state.receipt.logs,
		eventName: ["CLONE"],
	});
	form.contract = logs?.[0]?.args?.fairlaunch;
	form.address = logs?.[0]?.args?.token;
	form.transform((data) => ({
		...data,
		chainId: chainId.value,
	})).post(window.route("launchpads.store", {project: props.project.uuid}));
};
</script>
<template>
	<div class="w-full">
		<div class="grid mx-auto">
			<CollapseTransition>
				<div
					v-show="form.recentlySuccessful && !$page.props.error"
					class="text-green-500 font-semibold mt-3"
				>
					{{ $t("Launchpad was created successfully") }}
				</div>
			</CollapseTransition>
			<p class="mb-1">It seems your project does not have a launch scheduled or running!</p>
			<div class="mb-4 flex space-x-3 items-center">
				<h3 class="text-sm">Create a fairlaunch</h3>
				<a
					href="#"
					@click.prevent="showHowItWorks = true"
					class="hover:text-emerald-500 hover:underline transition-colors duration-200"
				>
					[See how it works?]</a
				>
			</div>
			<div class="gap-3 w-full grid md:grid-cols-3">
				<FormInput
					:placeholder="$t('Token Name')"
					v-model="form.name"
					:error="form.errors.name"
				/>

				<FormInput
					:placeholder="$t('Token Symbol')"
					v-model="form.symbol"
					:error="form.errors.symbol"
				/>

				<FormInput
					:placeholder="$t('Token Supply')"
					v-model="form.supply"
					:error="form.errors.supply"
				>
					<template #trail>
						<span
							class="text-xs font-semibold text-emerald-600 dark:text-emerald-400"
							>{{ useBillions(form.supply) }}</span
						>
					</template>
				</FormInput>
			</div>
			<UnsupportedFactory
				class="mt-4"
				v-if="!factoryAddress || !nfpm || !weth9"
			/>
			<div
				v-else
				class="mt-6 w-full flex space-x-3 items-center justify-end"
			>
				<TxStatus :state="state" />
				<ConnectWalletButton>
					<PrimaryButton
						@click.prevent="deploy"
						primary
						:disabled="state.busy || form.processing"
					>
						<Loading
							class="mr-2 -ml-1"
							v-if="state.busy || form.processing"
						/>
						{{ $t("Create Launchpad") }} [{{ feesFormatted
						}}{{ chain.nativeCurrency.symbol }}]
					</PrimaryButton>
				</ConnectWalletButton>
			</div>
			<div
				v-if="state.busy"
				class="text-right mt-2 text-red"
			>
				Dont leave this page. We have to save your token after deploy
			</div>
		</div>
		<DialogModal
			:show="showHowItWorks"
			@close="showHowItWorks = false"
			closeable
		>
			<template #title> How it works </template>
			<template #content>
				<ul class="list-disc list-inside">
					<li>This is a prize fairlaunch, with 10% Team allocation and 85% liquidity.</li>
					<li>
						The launch starts as soon as you create and lasts for 10 days. You Cannot
						cancel or stop.
					</li>
					<li>
						10% of sales will be sent to your deploy address when liquidity is added.
					</li>
					<li>
						10% of of minted tokens will available for you to claim 25 days after
						liquidity is added.
					</li>
					<li>5% sales will added to your winner prize if prize more than 2000$.</li>
					<li>
						Users withdraw is in stages. Every user can claim 10 days after they buy.
						This should give you enough time to build momentum for the pump stage.
					</li>
					<li>Prize payout is after sale ends.</li>
					<li>Launchpads without running prizes are listed after those with prizes.</li>
				</ul>
			</template>
		</DialogModal>
	</div>
</template>
