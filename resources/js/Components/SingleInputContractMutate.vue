<script setup>
import {computed, ref, watch} from "vue";

import _camelCase from "lodash/camelCase";
import {parseUnits} from "viem";
import {useI18n} from "vue-i18n";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import {addTransaction, useTxLink} from "@/Web3Modal";
import {checkTransaction, useActiveWeb3Vue} from "@/Web3Modal/hooks";
import {useContract} from "@/Web3Modal/hooks/useContract";
import {calculateGasMargin, isAddress} from "@/Web3Modal/utils";

const {account} = useActiveWeb3Vue();
const props = defineProps({
	abi: Object,
	state: Object,
	decimals: {type: Number, default: 18},
	contractAddress: String,
	validateType: String,
	percentage: Boolean,
});
const toSentenceCase = (camelCase) => {
	if (camelCase) {
		const cc = _camelCase(camelCase);
		const result = cc.replace(/([A-Z])/g, " $1");
		return result[0].toUpperCase() + result.substring(1).toLowerCase();
	}
	return "";
};
const contract = useContract(props.contractAddress, [props.abi]);
const currentValue = computed(() => props.state);
const model = ref(props.state);
watch(currentValue, (currentValue) => (model.value = currentValue));
const busy = ref(false);
const error = ref("");
const txhash = ref("");
const [tx, txlink] = useTxLink(txhash);
const status = ref("");
const {t} = useI18n();
const isPercentage = computed(() => props.validateType === "percent");
const isBips = computed(() => props.validateType === "bips");
const isTokenAmount = computed(() => props.validateType === "tokenAmount");
const isAddressParam = computed(() => props.abi.inputs[0].type === "address");
const update = async () => {
	const method = contract.value.methods[props.abi.name];
	let param = model.value;
	if (isPercentage.value) {
		param = model.value;
	}
	if (isBips.value) {
		if (model.value > 100) return (error.value = t("Invalid Percentage"));
		param = parseUnits(model.value, 2);
	}
	if (isAddressParam.value) {
		if (!isAddress(model.value)) return (error.value = t("Invalid Address"));
		param = isAddress(model.value);
	}
	if (isTokenAmount.value) {
		param = parseUnits(model.value, props.state.decimals);
	}
	error.value = null;
	busy.value = true;
	const estimatedGas = await method(param)
		.estimateGas({
			from: account.value,
		})
		.catch((e) => {
			busy.value = false;
			if (e.message.toString().includes("Internal JSON-RPC error.")) {
				const err = JSON.parse(
					e.message.toString().replace("Internal JSON-RPC error.", ""),
				);
				return (error.value = err?.message);
			}
			return (error.value = e.message ?? e.data?.message ?? e);
		});

	const gasLimit = calculateGasMargin(estimatedGas);
	await method(param)
		.send({
			from: account.value,
			gasLimit,
			gasPrice: null,
			maxPriorityFeePerGas: null,
			maxFeePerGas: null,
		})
		.once("transactionHash", async (hash) => {
			status.value = t("Sendin Tx..");
			txhash.value = hash;
			addTransaction({
				hash,
				type: "tx",
				summary: toSentenceCase(props.abi.name),
				onSuccess: async (receipt) => {
					status.value = toSentenceCase(props.abi.name) + ": " + t("successful");
					setTimeout(() => {
						busy.value = false;
						txhash.value = null;
					}, 3000);
				},
				onError: (e) => {
					error.value = e.message ?? e;
					setTimeout(() => {
						busy.value = false;
						txhash.value = null;
						error.value = null;
					}, 3000);
				},
			});
		})
		.once("confirmation", (confirmationNumber, receipt) => {
			checkTransaction(receipt.transactionHash);
		})
		.on("error", () => {
			checkTransaction(txhash.value);
		})
		.catch((e) => {
			if (e.code === 4001) {
				error.value = "Signature Denied";
				busy.value = false;
				setTimeout(() => {
					txhash.value = null;
					error.value = null;
				}, 3000);
			}
		});
};
</script>
<template>
	<div class="space-y-6 mt-6 pb-8">
		<div class="md:grid grid-cols-5 gap-x-4">
			<FormInput
				v-model="model"
				class="col-span-3"
				:label="toSentenceCase(abi.inputs[0].name)"
			>
				<template
					v-if="isPercentage"
					#trail
					>%</template
				>
				<template
					v-else-if="isTokenAmount"
					#trail
					>{{ symbol }}</template
				>
			</FormInput>
			<div class="flex col-span-2 flex-col mt-4 lg:mt-0 justify-end">
				<button
					type="button"
					@click.prevent="update()"
					class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
				>
					<Loading
						class="-ml-2 inline-block"
						v-if="busy"
					/>{{ toSentenceCase(abi.name) }}
				</button>
			</div>
			<p
				v-if="error"
				class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
			>
				{{ error }}
			</p>
			<p
				v-else
				class="col-span-3 text-xs font-semibold"
			>
				{{ busy ? status : ""
				}}<a
					:href="busy && txlink"
					class="ml-2 mt-1 text-emerald-500"
					v-if="tx"
					>{{ tx }}</a
				>
			</p>
		</div>
	</div>
</template>
