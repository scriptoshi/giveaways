<script setup>
import {computed, useAttrs} from "vue";

import {DateTime} from "luxon";

import ArrowRightIcon from "@/Feather/ArrowRightIcon";
import XIcon from "@/Feather/XIcon";
import {useBillions} from "@/hooks/useBillions";
const attrs = useAttrs();
const props = defineProps({
	isOpen: Boolean,
	baseToken: Object,
	balance: [Number, String],
	form: Object,
	launch: Object,
	info: Object,
});
const date = computed(() =>
	DateTime.fromJSDate(props.form.startTime).toLocaleString(DateTime.DATETIME_MED)
);
const emit = defineEmits(["close", "deploy"]);
const close = () => emit("close");
const deploy = () => emit("deploy");
</script>
<template>
	<div v-if="isOpen">
		<teleport to="body">
			<div
				v-bind="attrs"
				tabindex="-1"
				class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center flex"
				aria-modal="true"
				role="dialog"
			>
				<div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
					<!-- Modal content -->
					<div class="relative bg-white rounded-sm shadow dark:bg-gray-700">
						<!-- Modal header -->
						<div
							class="flex justify-between items-center px-5 py-2 rounded-t border-b dark:border-gray-600"
						>
							<h3 class="text-lg font-medium text-gray-900 dark:text-white">
								Confirm Presale Settings!
							</h3>
							<button
								type="button"
								@click="close"
								class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-sm text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
							>
								<XIcon class="w-5 h-5" />
							</button>
						</div>
						<!-- Modal body -->
						<div class="">
							<div class="mb-4 mt-2">
								<div
									class="flex justify-between px-5 py-1 border-b border-gray-200 dark:border-gray-600"
								>
									<h3 class="text-sm">{{ $t("Tokens for Presale") }}</h3>
									<div
										class="font-semibold text-right dark:text-white text-gray-900"
									>
										{{ info.amount }}
										<span class="text-gray-600 dark:text-gray-400 font-mono"
											>({{ useBillions(info.amount) }})</span
										>
										{{ form.symbol }}
									</div>
								</div>
								<div
									class="flex justify-between px-5 py-1 border-b border-gray-200 dark:border-gray-600"
								>
									<h3 class="text-sm">
										{{ $t("Tokens for Liquidity") }} ({{
											form.liquidityPercent
										}}%)
									</h3>
									<div
										class="font-semibold text-right dark:text-white text-gray-900"
									>
										{{ info.liquidity }}
										<span class="text-gray-600 dark:text-gray-400 font-mono"
											>({{ useBillions(info.liquidity) }})</span
										>
										{{ form.symbol }}
									</div>
								</div>
								<div
									class="flex justify-between items-center px-5 py-1 border-b border-gray-200 dark:border-gray-600"
								>
									<h3 class="text-sm">{{ $t("Base Fees") }}</h3>
									<div
										class="font-semibold text-right dark:text-white text-gray-900"
									>
										{{
											$t("{fee}% of {symbol} raised", {
												fee: info.basePercent,
												symbol: baseToken?.symbol,
											})
										}}
									</div>
								</div>
								<div
									class="flex justify-between items-center px-5 py-1 border-b border-gray-200 dark:border-gray-600"
								>
									<h3 class="text-sm">
										{{ $t("Referral fees") }} ({{ info.refPercent }}%)
									</h3>
									<div
										class="font-semibold text-right dark:text-white text-gray-900"
									>
										{{ info.referral }}
										<span class="text-gray-600 dark:text-gray-400 font-mono"
											>({{ useBillions(info.referral) }})</span
										>
										{{ form.symbol }}
									</div>
								</div>
								<div
									class="flex justify-between items-center px-5 py-1 border-b border-gray-200 dark:border-gray-600"
								>
									<h3 class="text-sm">
										{{ $t("Launch fees") }} ({{ info.feePercent }}%)
									</h3>
									<div
										class="font-semibold text-right dark:text-white text-gray-900"
									>
										{{ info.fee }}
										<span class="text-gray-600 dark:text-gray-400 font-mono"
											>({{ useBillions(info.fee) }})</span
										>
										{{ form.symbol }}
									</div>
								</div>
								<div class="grid grid-cols-2 px-5 py-1 gay-x-6">
									<h3 class="text-base">
										{{ $t("Total Required") }}
										<small
											:class="
												parseFloat(balance) <= parseFloat(info.totalTokens)
													? 'text-red-500'
													: 'text-emerald-500 dark:text-emerald-300'
											"
											>(Bal:{{ useBillions(balance) }}
											{{ form.symbol }})</small
										>
									</h3>
									<h3
										class="text-base font-semibold text-right dark:text-white text-gray-900"
									>
										{{ info.totalTokens }}
										<span class="text-gray-600 dark:text-gray-400 font-mono"
											>({{ useBillions(info.totalTokens) }})</span
										>
										{{ form.symbol }}
									</h3>
								</div>
							</div>
							<div class="px-4">
								<div
									class="border bg-gray-100 dark:bg-gray-800 grid grid-cols-2 gap-x-4 border-gray-300 dark:border-gray-600 px-8 py-4 rounded-md"
								>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Start Time") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ date }}
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Duration in days") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ form.presalePeriod }} Days
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div>{{ $t("Refund Type") }}</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ form.burn ? $t("Burn") : $t("Refund Owner") }}
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<template v-if="form.isFairLaunch">
											<div class="text-gray-700 dark:text-gray-200">
												{{ $t("Tokens For Sale") }}
											</div>
											<div
												class="font-semibold text-emerald-700 dark:text-emerald-500"
											>
												{{ form.total }} {{ form?.symbol }}
											</div>
										</template>
										<template v-else>
											<div class="text-gray-700 dark:text-gray-200">
												{{ $t("Hardcap") }}
											</div>
											<div
												class="font-semibold text-emerald-700 dark:text-emerald-500"
											>
												{{ form.hardcap }} {{ baseToken?.symbol }}
											</div>
										</template>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Softcap") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ form.softcap }} {{ baseToken?.symbol }}
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Min Purchase") }}
										</div>
										<div
											class="font-semibold text-emerald-700 dark:text-emerald-500"
										>
											{{ form.minSpendPerBuyer }} {{ baseToken?.symbol }}
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Max Purchase") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ form.maxSpendPerBuyer }} {{ baseToken?.symbol }}
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div>{{ $t("Amm Exchange") }}</div>
										<a
											:href="form.router.url"
											class="font-semibold text-right text-sky-700 hover:text-sky-800 dark:text-sky-500 dark:hover:text-sky-400"
											>{{ form.router?.name }}</a
										>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ form.router?.name }} {{ $t("Liquidity %") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ form.liquidityPercent }} %
										</div>
									</div>
									<div
										v-if="!form.isFairLaunch"
										class="flex flex-row py-1 space-x-3"
									>
										<div class="text-gray-700 dark:text-gray-200">
											{{ form.router?.name }} {{ $t("Listing Price") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											1 {{ baseToken?.symbol }} == {{ form.listingRate }}
											{{ form.symbol }}
										</div>
									</div>
									<div
										v-if="!form.isFairLaunch"
										class="flex flex-row py-1 space-x-3"
									>
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Presale Price") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											1 {{ baseToken?.symbol }} == {{ form.tokenPrice }}
											{{ form.symbol }}
										</div>
									</div>
									<div class="flex flex-row py-1 space-x-3">
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Liquidity Lock Period") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ form.lockPeriod }} {{ $t("Days") }}
										</div>
									</div>
									<template v-if="form.useVesting">
										<div class="col-span-2 mt-3 mb-1">
											<h3 class="text-base">Vesting Settings</h3>
										</div>

										<div class="flex flex-row py-1 space-x-3">
											<div class="text-gray-700 dark:text-gray-200">
												{{ $t("Vesting Initial Release") }}
											</div>
											<div
												class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
											>
												{{ form.initialPercent }} %
											</div>
										</div>
										<div class="flex flex-row py-1 space-x-3">
											<div class="text-gray-700 dark:text-gray-200">
												{{ $t("Vesting Cycle") }}
											</div>
											<div
												class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
											>
												{{ form.cycle }} {{ $t("Days") }}
											</div>
										</div>
										<div class="flex flex-row py-1 space-x-3">
											<div class="text-gray-700 dark:text-gray-200">
												{{ $t("Percentage Per Cycle") }}
											</div>
											<div
												class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
											>
												{{ form.cyclePercent }} %
											</div>
										</div>
									</template>
									<div
										v-else
										class="flex flex-row py-1 space-x-3"
									>
										<div class="text-gray-700 dark:text-gray-200">
											{{ $t("Vesting") }}
										</div>
										<div
											class="font-semibold text-right text-emerald-700 dark:text-emerald-500"
										>
											{{ $t("Disabled") }}
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Modal footer -->
						<div
							class="flex items-center mt-4 px-4 py-2 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
						>
							<button
								type="button"
								@click="deploy"
								class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								Deploy Presale Contract
								<ArrowRightIcon class="mr-2 -ml-1 inline-block w-5 h-5" />
							</button>
							<button
								type="button"
								@click="close"
								class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-sm border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
							>
								Decline
							</button>
						</div>
					</div>
				</div>
			</div>
		</teleport>
		<div
			@click="close"
			class="bg-gray-900 opacity-50 dark:opacity-80 fixed inset-0 z-40"
		></div>
	</div>
</template>
