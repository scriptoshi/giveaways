<script setup>
import {ChevronLeftIcon} from "@heroicons/vue/24/solid";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import Transaction from "@/Wagmi/components/Transaction.vue";
import {clearAllTransactions, txs as transactions} from "@/Wagmi/hooks/txs";
const emit = defineEmits(["hideTransactions"]);
const hideTransactions = () => emit("hideTransactions");
defineProps({
	showTxs: Boolean,
});
</script>
<template>
	<CollapseTransition>
		<div
			v-show="showTxs"
			class=""
		>
			<div class="grid grid-cols-3 items-center h-12 border-b border-slate-200/20 px-4">
				<div class="flex items-center">
					<button
						type="button"
						@click="hideTransactions"
						class="group relative focus:outline-none border:none"
					>
						<span
							class="rounded-full absolute inset-0 -ml-1 -mr-1 -mb-1 -mt-1 group-hover:bg-white group-hover:bg-opacity-[0.08]"
						></span>
						<ChevronLeftIcon class="w-5 h-5" />
					</button>
				</div>
				<div class="text-base leading-5 font-semibold text-slate-400">Transactions</div>
				<div class="flex items-end justify-end">
					<button
						@click.prevent="clearAllTransactions"
						class="btn flex hover:ring-2 focus:ring-2 items-center justify-center gap-2 rounded-xl cursor-pointer !ring-0 text-purple hover:text-emerald-300 px-3 h-[36px] text-sm font-semibold !p-0"
					>
						{{ $t("Clear all") }}
					</button>
				</div>
			</div>
			<div class="flex flex-col gap-3 max-h-[300px] overflow-y-auto scroll">
				<div
					v-if="Object.values(transactions).length == 0"
					class="text-base h-56 leading-5 font-normal text-emerald-500 text-center py-5"
				>
					Your transactions will appear here
				</div>
				<template v-else>
					<Transaction
						v-for="tx in transactions"
						:key="tx.hash"
						:tx="tx"
					/>
				</template>
			</div>
		</div>
	</CollapseTransition>
</template>
