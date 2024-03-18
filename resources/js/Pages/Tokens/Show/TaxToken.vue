<script setup>
import {ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidArrowLeft, HiSolidPlus} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Loading from "@/Components/Loading.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import Switch from "@/Components/Switch.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useTaxTokenAdmin} from "@/hooks/token/useTaxTokenAdmin";
import TokenInfo from "@/Pages/Tokens/Show/TokenInfo.vue";
const props = defineProps({
	token: Object,
});
const form = useForm({
	address: "",
	swapAndLiquifyEnabled: null,
	liquidityFee: null,
	taxFee: null,
	maxTxAmount: null,
});
const state = useTaxTokenAdmin(props.token, form);
state.update();
const actions = [
	{
		label: "Exclude from reward",
		id: "excludeFromReward",
		value: "excludeFromReward",
		call: state.excludeFromReward,
	},
	{
		label: "Include in reward",
		id: "includeInReward",
		value: "includeInReward",
		call: state.includeInReward,
	},
	{
		label: "Exclude from Fee",
		id: "excludeFromFee",
		value: "excludeFromFee",
		call: state.excludeFromFee,
	},
	{
		label: "Include in Fee",
		id: "includeInFee",
		value: "includeInFee",
		call: state.includeInFee,
	},
];
const action = ref("excludeFromReward");
</script>
<template>
	<section aria-labelledby="plan-heading">
		<div class="overflow-hidden">
			<div class="my-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0">
				<h3 class="uppercase">{{ $t("Manage Token") }}</h3>

				<div class="flex flex-col space-y-2 sm:space-y-0 sm:flex-row sm:space-x-2">
					<PrimaryButton
						primary
						link
						:href="route('tokens.create', {type: 'standard', chainId: token.chainId})"
					>
						<VueIcon
							:icon="HiSolidArrowLeft"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Go Back") }}
					</PrimaryButton>
					<PrimaryButton
						secondary
						link
						:href="
							route('tokens.create', {
								type: 'crosschain',
								chainId: token.chainId,
								token: token.contract,
							})
						"
					>
						<VueIcon
							:icon="HiSolidPlus"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Convert to Crosschain") }}
					</PrimaryButton>
				</div>
			</div>
			<div class="card shadow-none rounded-none py-6 px-4 space-y-6 sm:p-6">
				<TokenInfo :token="token" />
				<div class="p-8 border border-gray-200 dark:border-gray-500">
					<h3 class="text-base mb-6">{{ $t("Manage Exemptions") }}</h3>
					<FormInput
						v-model="form.address"
						:label="$t('Enter a valid Address')"
						:error="form.errors.address"
						class="max-w-lg"
					/>
					<div class="mt-4">
						<InputLabel class="mb-1">{{ $t("Select an action") }}</InputLabel>
						<RadioSelect
							:list="actions"
							v-model="action"
						/>
					</div>
					<template
						v-for="item in actions"
						:key="item.id"
					>
						<TxStatus
							v-if="
								item.value === action &&
								[
									'excludeFromReward',
									'includeInReward',
									'excludeFromFee',
									'includeInFee',
								].includes(state.called)
							"
							:state="state"
							class="mt-4"
						/>
						<div
							v-if="item.value === action"
							class="flex justify-end mt-8"
						>
							<SwitchChainButton :to-chain="token.chainId">
								<PrimaryButton
									secondary
									@click="item.call"
									:disabled="state.busy === item.value"
								>
									<Loading v-if="state.busy === item.value" />
									<VueIcon
										v-else
										:icon="HiSolidPlus"
										class="-ml-1 mr-2 w-4 h-4"
									/>
									{{ $t("Update Contract") }}
								</PrimaryButton>
							</SwitchChainButton>
						</div>
					</template>
				</div>
			</div>
			<div class="card shadow-none rounded-none py-6 px-4 space-y-6 sm:p-6 mt-8">
				<div class="p-8 border border-gray-200 dark:border-gray-500">
					<h3 class="text-base mb-6">{{ $t("Manage Swap and Liquify") }}</h3>
					<div class="flex flex-col sm:items-center sm:flex-row justify-between">
						<Switch v-model="form.swapAndLiquifyEnabled">{{
							$t("Swap and liquify")
						}}</Switch>
						<div class="flex flex-col items-center sm:flex-row sm:justify-end">
							<TxStatus
								:state="state"
								v-if="state.called === 'setSwapAndLiquifyEnabled'"
								class="mb-3 sm:mb-0 sm:mr-6"
							/>
							<SwitchChainButton :to-chain="token.chainId">
								<PrimaryButton
									primary
									@click="state.setSwapAndLiquifyEnabled"
									:disabled="
										state.busy === 'setSwapAndLiquifyEnabled' ||
										form.swapAndLiquifyEnabled === state.swapAndLiquifyEnabled
									"
								>
									<Loading v-if="state.busy === 'setSwapAndLiquifyEnabled'" />
									<VueIcon
										v-else
										:icon="HiSolidPlus"
										class="-ml-1 mr-2 w-4 h-4"
									/>
									{{ $t("Update Contract") }}
								</PrimaryButton>
							</SwitchChainButton>
						</div>
					</div>
				</div>
				<div class="p-8 mt-8 border border-gray-200 dark:border-gray-500">
					<h3 class="text-base mb-6">{{ $t("Manage Admin Tax Percent") }}</h3>
					<div class="flex flex-col sm:items-center sm:flex-row justify-between">
						<FormInput
							:label="$t('Tax Fee Percent')"
							:error="form.errors.taxFee"
							v-model="form.taxFee"
							class="w-full max-w-sm"
							:help="$t('Do not use decimal fractions')"
						/>
						<div class="flex flex-col items-center sm:flex-row sm:justify-end">
							<TxStatus
								:state="state"
								v-if="state.called === 'setTaxFeePercent'"
								class="mb-3 sm:mb-0 sm:mr-6"
							/>
							<SwitchChainButton :to-chain="token.chainId">
								<PrimaryButton
									secondary
									@click="state.setTaxFeePercent"
									:disabled="state.busy === 'setTaxFeePercent'"
								>
									<Loading v-if="state.busy === 'setTaxFeePercent'" />
									<VueIcon
										v-else
										:icon="HiSolidPlus"
										class="-ml-1 mr-2 w-4 h-4"
									/>
									{{ $t("Update Tax Fee Percent") }}
								</PrimaryButton>
							</SwitchChainButton>
						</div>
					</div>
				</div>
				<div class="p-8 mt-8 border border-gray-200 dark:border-gray-500">
					<h3 class="text-base mb-6">{{ $t("Manage Liquidity Tax Percent") }}</h3>
					<div class="flex flex-col sm:items-center sm:flex-row justify-between">
						<FormInput
							:label="$t('Liquidity Fee Percent')"
							:error="form.errors.liquidityFee"
							v-model="form.liquidityFee"
							class="w-full max-w-sm"
							:help="$t('Do not use decimal fractions')"
						/>
						<div class="flex flex-col items-center sm:flex-row sm:justify-end">
							<TxStatus
								:state="state"
								v-if="state.called === 'setLiquidityFeePercent'"
								class="mb-3 sm:mb-0 sm:mr-6"
							/>
							<SwitchChainButton :to-chain="token.chainId">
								<PrimaryButton
									secondary
									@click="state.setLiquidityFeePercent"
									:disabled="state.busy === 'setLiquidityFeePercent'"
								>
									<Loading v-if="state.busy === 'setLiquidityFeePercent'" />
									<VueIcon
										v-else
										:icon="HiSolidPlus"
										class="-ml-1 mr-2 w-4 h-4"
									/>
									{{ $t("Update Liquidity Fee Percent") }}
								</PrimaryButton>
							</SwitchChainButton>
						</div>
					</div>
				</div>
				<div class="p-8 mt-8 border border-gray-200 dark:border-gray-500">
					<h3 class="text-base mb-6">{{ $t("Update Max Transaction Amount") }}</h3>
					<div class="flex flex-col sm:items-center sm:flex-row justify-between">
						<FormInput
							:label="$t('Max Transaction Amount')"
							:error="form.errors.maxTxAmount"
							v-model="form.maxTxAmount"
							class="w-full max-w-sm"
						/>
						<div class="flex flex-col items-center sm:flex-row sm:justify-end">
							<TxStatus
								:state="state"
								v-if="state.called === 'setMaxTxPercent'"
								class="mb-3 sm:mb-0 sm:mr-6"
							/>
							<SwitchChainButton :to-chain="token.chainId">
								<PrimaryButton
									secondary
									@click="state.setMaxTxAmount"
									:disabled="state.busy === 'setMaxTxPercent'"
								>
									<Loading v-if="state.busy === 'setMaxTxPercent'" />
									<VueIcon
										v-else
										:icon="HiSolidPlus"
										class="-ml-1 mr-2 w-4 h-4"
									/>
									{{ $t("Update Contract") }}
								</PrimaryButton>
							</SwitchChainButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>
