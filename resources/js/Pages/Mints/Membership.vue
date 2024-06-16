<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";
import {parse as uuidParse, v4 as uuidv4} from "uuid";
import {parseEther} from "viem";
import {useI18n} from "vue-i18n";

import Address from "@/Components/Address.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Loading from "@/Components/Loading.vue";
import TxStatus from "@/Components/TxStatus.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
	membership: Object,
	accounts: Array,
	gasChainId: Number,
	hasAccess: Boolean,
});
const nft = {
	name: "Gas Finance Access Badge",
	symbol: "ACCESS",
	description:
		"The giveaways.finance access badge grants you limited access to claim gas tokens earned via the quest rewards system ",
	external_url: "https://giveaways.finance/access",
	image: "https://nft.giveaways.finance/gas-finance-access.png",
	attributes: [
		{
			trait_type: "Access",
			value: "Quest Claims",
		},
		{
			trait_type: "Level",
			value: 1,
			display_type: "number",
		},
		{
			trait_type: "Reward",
			value: "GAS Tokens",
		},
		{
			trait_type: "Validity",
			value: "One Year",
		},
	],
};
const membersContract = computed(() => props.membership.addresses[props.gasChainId]);
const {address} = useAccount();
const uuidBigInt = () => {
	const uuid = uuidv4();
	const parsed = uuidParse(uuid);
	const hex = [...parsed].map((v) => v.toString(16).padStart(2, "0")).join("");
	return BigInt("0x" + hex).toString();
};
const form = useForm({
	owner: address.value,
	nft_contract: membersContract,
	tokenId: uuidBigInt(),
	chainId: props.gasChainId,
	txhash: null,
});

watch(address, (addr) => (form.owner = addr));
const {t} = useI18n();
const state = useReactiveContractCall(props.membership.abi, membersContract);
const loading = ref(false);
const claiming = ref("claim");

const mintNft = async () => {
	loading.value = true;
	claiming.value = "claim";
	loading.value = false;
	const feePerNft = parseEther("0.004"); // to do, pull from contract
	await state.call("safeMint", [form.owner, form.tokenId], feePerNft, t("Mint membership"));
	if (state.error) return;
	form.txhash = state.txhash;
	form.post(window.route("mints.store"));
};
</script>
<template>
	<AppLayout>
		<div class="min-h-full w-full bg-white dark:bg-gray-900">
			<div class="container sm:px-4">
				<div class="max-w-2xl mt-8 mx-auto p-8">
					<h3 class="text-base">{{ nft.name }}</h3>
					<p>{{ nft.description }}</p>
					<div class="p-6 mt-6 border grid gap-3 dark:border-gray-600">
						<div class="flex items-center">
							<img
								class="w-40 h-40"
								:src="nft.image"
							/>
							<div class="grid gap-2 flex-1">
								<div class="flex items-center justify-between">
									<h3 class="text-xl text-gray-500">symbol</h3>
									<h3 class="text-xl">{{ nft.symbol }}</h3>
								</div>
								<div class="flex items-center justify-between">
									<h3 class="text-sm text-gray-500">Contract</h3>
									<Address
										:address="membersContract"
										:chain-id="gasChainId"
										class="font-Walsheim-Bold text-base"
									/>
								</div>
							</div>
						</div>
						<div
							v-if="hasAccess"
							class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
							role="alert"
						>
							<svg
								class="flex-shrink-0 w-4 h-4"
								aria-hidden="true"
								xmlns="http://www.w3.org/2000/svg"
								fill="currentColor"
								viewBox="0 0 20 20"
							>
								<path
									d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
								/>
							</svg>
							<div class="ms-3 text-sm font-medium">
								Your account has full <span class="font-semibold">access</span>. You
								do not not need to mint another NFT
							</div>
						</div>
						<div
							v-if="state.busy"
							class="flexitems-center justify-end"
						>
							<TxStatus
								class="text-right mt-6"
								:state="state"
							/>
						</div>

						<div class="flex mt-6 items-center justify-end">
							<PrimaryButton
								primary
								:disabled="!form.owner"
								@click.prevent="mintNft"
							>
								<Loading
									class="mr-2 -ml-2"
									v-if="state.busy"
								/>
								Mint NFT (0.004 BNB)</PrimaryButton
							>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
