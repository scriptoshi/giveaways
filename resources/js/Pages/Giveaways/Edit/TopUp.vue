<script setup>
import {computed, reactive, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {AiCeur} from "oh-vue-icons/icons";
import {useAccount, usePublicClient} from "use-wagmi";
import {erc20Abi, parseUnits, zeroAddress} from "viem";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";

const props = defineProps({
	prizeClaim: Object,
	giveaway: Object,
});

const chainId = computed(() => props.giveaway.chainId);
const prizeContract = computed(() => props.prizeClaim.addresses[chainId.value]);
const usdt = reactive({
	address: zeroAddress,
	abi: erc20Abi,
	decimals: 6,
	symbol: "USDT",
});
const publicClient = usePublicClient();
const updateUsdt = async (prizeContract) => {
	usdt.address = await publicClient.value
		.readContract({
			address: prizeContract,
			abi: props.prizeClaim.abi,
			functionName: "usdt",
		})
		.catch((e) => {
			console.log(e);
		});
	usdt.decimals = await publicClient.value
		.readContract({
			address: usdt.address,
			abi: usdt.abi,
			functionName: "decimals",
		})
		.catch((e) => {
			console.log(e);
		});
};
watch(
	prizeContract,
	(prizeContract) => {
		if (!prizeContract) return;
		updateUsdt(prizeContract);
	},
	{immediate: true},
);
const usdtAddress = computed(() => usdt.address);
const state = useReactiveContractCall(usdt.abi, usdtAddress);

const {address} = useAccount();
const form = useForm({
	prize: props.giveaway.prize,
	fee: 0,
	gas: 0,
	symbol: "USDT",
	num_winners: props.giveaway.num_winners,
	hash: null,
});

const reset = () => {
	form.reset();
};

const saveLive = ref(false);
const {t} = useI18n();

const totalPrize = computed(() => {
	if (form.prize === "" || !form.prize || !form.num_winners || form.num_winners === "") return 0;
	return (parseFloat(form.prize) * parseFloat(form.num_winners)).toFixed(2) * 2;
});

const payable = computed(() => totalPrize.value - props.giveaway.paid);

const totalFee = computed(() => {
	if (form.prize === "" || !form.prize || !form.num_winners || form.num_winners === "") return 0;
	return (parseFloat(form.prize) * parseFloat(form.num_winners)).toFixed(2);
});
watch(
	() => form.prize,
	(prize) => {
		if (prize === "" || !prize) return (form.fee = 0);
		form.fee = prize;
	},
);

const draft = async () => {
	if (payable.value <= 0) return;
	saveLive.value = false;
	form.clearErrors();
	if (!form.prize || form.prize === "" || parseFloat(form.prize) === 0)
		form.setError("prize", t("Please provide a prize to give to participants"));
	if (!form.num_winners || parseInt(form.num_winners) === 0)
		form.setError("num_winners", t("Minimum is 1 winner"));
	if (form.hasErrors) return;
	await updateUsdt(prizeContract.value);
	const fee = parseUnits(payable.value.toString(), usdt.decimals);
	await state.call("transfer", [prizeContract.value, fee], null, t("Topup Prize"));
	if (state.error) return;
	form.transform((data) => ({
		...data,
		hash: state.txhash,
		account: address.value,
		chainId: chainId.value,
		token: usdtAddress.value,
	})).post(window.route("topups.store", {giveaway: props.giveaway.id}), {
		preserveScroll: true,
		onSuccess() {
			reset();
		},
	});
};
</script>
<template>
	<div class="grid mx-auto max-w-4xl">
		<div class="grid w-full p-5 my-6 border">
			<div>
				<h3 class="text-base text-emerald-500">
					<VueIcon :icon="AiCeur" /> {{ $t("You can only increase prize!") }}
				</h3>
				<p class="text-xs mb-2">
					Topups may take upto 5 minutes to reflect as we update our cache, So be patient!
				</p>
			</div>
			<div class="gap-3 my-5 w-full grid md:grid-cols-4">
				<FormInput
					:placeholder="$t('20')"
					:label="$t('Prize Per Winner')"
					help="Only USDT prize allowed"
					v-model="form.prize"
					:error="form.errors.prize"
				>
					<template #trail>USDT</template>
				</FormInput>
				<div class="md:col-span-3 md:grid gap-3 md:grid-cols-4">
					<FormInput
						:label="$t('Number of Winners')"
						:placeholder="$t('Eg 10')"
						v-model="form.num_winners"
						:error="form.errors.num_winners"
					/>
					<FormInput
						:label="$t('Participant Fees')"
						:help="$t('Distributed to all participants')"
						v-model="totalFee"
						:error="form.errors.fee"
						class="md:col-span-2"
						disabled
					>
						<template #lead>$</template>
						<template #trail
							><span class="text-emerald-500"
								>{{ (totalPrize * 1000) / 2 }} GAS</span
							></template
						>
					</FormInput>
					<FormInput
						:label="$t('Total Spend')"
						v-model="totalPrize"
						disabled
					>
						<template #trail>
							<span class="text-xs font-semibold"> USDT</span>
						</template>
					</FormInput>
				</div>
			</div>
			<div class="w-full flex items-center justify-end">
				<TxStatus :state="state" />
			</div>
			<div class="mt-3 w-full flex space-x-3 items-center justify-end">
				<p
					v-if="state.busy"
					class="text-red-500"
				>
					Dont leave this page! We are updating your prize.
				</p>
				<ConnectWalletButton>
					<SwitchChainButton
						v-if="chainId"
						:to-chain="chainId"
					>
						<PrimaryButton
							@click.prevent="draft"
							primary
							:disabled="state.busy || form.processing || payable <= 0"
						>
							<Loading
								class="mr-2 -ml-1"
								v-if="state.busy || form.processing"
							/>
							{{ $t("Topup") }} ({{ payable > 0 ? payable : 0 }} USDT)
						</PrimaryButton>
					</SwitchChainButton>
				</ConnectWalletButton>
			</div>
		</div>
	</div>
</template>
