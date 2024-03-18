<script setup>
import {computed, ref, watch} from "vue";

import {useAccount, usePublicClient} from "use-wagmi";
import {zeroAddress} from "viem";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import Loading from "@/Components/Loading.vue";
import {useContractCall} from "@/hooks/contracts/useContractCall";
import tokenAbi from "@/Wagmi/constants/erc20.json";
const props = defineProps({
	contract: {type: String, required: true},
	spender: {type: String, required: true},
	amount: {type: BigInt, default: 0n},
	shouldApprove: {type: Boolean, default: true},
});
const emit = defineEmits(["approve"]);
const {address} = useAccount();
const client = usePublicClient();
const allowance = ref(0);
const loading = ref(false);
const approved = (reciept) => emit("approve", reciept);
const update = async () => {
	loading.value = true;
	allowance.value = await client.value.readContract({
		address: props.contract,
		abi: tokenAbi,
		functionName: "allowance",
		args: [address.value, props.spender],
	});
	loading.value = false;
};
const {
	error,
	busy,
	shortTx,
	txhash,
	status,
	etherScanLink,
	confirming,
	isConfirmed,
	simulation,
	receipt,
	call,
} = useContractCall(tokenAbi, props.contract);
const approve = async () => {
	await call("approve", [props.spender, props.amount]);
	await update();
	approved(receipt.value);
};

const requiresApproval = computed(
	() => props.contract !== zeroAddress && props.shouldApprove && allowance.value < props.amount,
);
watch(
	() => props.shouldApprove,
	(shouldApprove) => {
		if (shouldApprove) update();
	},
);
</script>
<template>
	<CollapseTransition>
		<div v-show="loading">
			<h3 class="text-sm my-4 text-right">{{ $t("Checking Approvals Please Wait...") }}</h3>
		</div>
	</CollapseTransition>
	<CollapseTransition>
		<div v-show="requiresApproval">
			<slot
				name="approval"
				:error="error"
				:busy="busy"
				:shortTx="shortTx"
				:status="status"
				:etherScanLink="etherScanLink"
				:confirming="confirming"
				:isConfirmed="isConfirmed"
				:simulation="simulation"
				:receipt="receipt"
				:approve="approve"
			>
				<h3 class="text-sm my-4 text-right">
					<p
						v-if="error"
						class="text-red text-xs font-semibold"
					>
						{{ error }}
					</p>
					<p
						v-else
						class="text-xs font-semibold"
					>
						<span
							:class="{'text-success': isConfirmed}"
							class="font-semibold text-xs"
						>
							{{ status }}
						</span>
						<a
							:href="etherScanLink"
							target="_blank"
							class="ml-2 text-gray-500"
							v-if="txhash"
							>{{ shortTx }}</a
						>
					</p>
				</h3>
				<button
					@click.prevent="approve"
					class="px-6 py-3 self-start float-right rounded-lg text-emerald-500 hover:text-emerald-600 font-semibold border border-emerald-500 hover:border-emerald-600 transition duration-150"
				>
					<slot name="button">
						<Loading
							v-if="busy || confirming"
							class="-ml-1 !mr-2"
						/>
						{{ $t("Approve Tokens") }}
					</slot>
				</button>
			</slot>
		</div>
	</CollapseTransition>
	<CollapseTransition>
		<div v-show="!requiresApproval">
			<slot />
		</div>
	</CollapseTransition>
</template>
