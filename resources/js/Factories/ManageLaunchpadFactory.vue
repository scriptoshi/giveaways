<script setup>
import {computed, ref, watch} from "vue";

import {RectangleStackIcon} from "@heroicons/vue/24/outline";
import {QrCodeIcon} from "@heroicons/vue/24/solid";
import {useForm} from "@inertiajs/vue3";
import {useChains} from "use-wagmi";
import {zeroAddress} from "viem";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import {useUpdateFeeToken, useUpdateOwner} from "@/Factories/hooks/factory";
import {
	useLaunchPadData,
	useUpdateLp,
	useUpdateSettings,
} from "@/Factories/hooks/useManagePresaleFactory";
import {RefreshCcwIcon} from "@/Feather";
import {useToken} from "@/hooks/useUpdateCoins";
import DangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/JetButton.vue";
import {useAddress} from "@/Wagmi/hooks/useTxHash";

const props = defineProps({
	factory: Object,
});
const form = useForm({
	timeLock: "",
	feeAddress: "",
	baseOnlyFee: "",
	baseFee: "",
	tokenFee: "",
	referralFee: "",
	ethCreationFee: "",
	maxPresalePeriod: "",
	minLockPeriod: "",
	minLiqiudityPercent: "",
});
const chains = useChains();
const factoryChain = computed(() =>
	chains.value.find((c) => c.id.toString() === props.factory?.chainId?.toString()),
);
const lpform = useForm({
	maxLockCycle: null,
	minCyclePercent: null,
	maxLockPeriod: null,
});
const owner = ref();
const feeToken = ref();
const useTokenForFees = ref(false);
const presaleData = useLaunchPadData(props.factory);
const {
	name,
	symbol,
	decimals,
	error,
	loading: loadToken,
	updateToken,
} = useToken(feeToken, parseInt(props.factory?.chainId));
watch(presaleData, (presaleData) => {
	form.timeLock = presaleData.timeLock;
	form.feeAddress = presaleData.feeAddress;
	form.baseOnlyFee = presaleData.baseOnlyFee;
	form.baseFee = presaleData.baseFee;
	form.tokenFee = presaleData.tokenFee;
	form.referralFee = presaleData.referralFee;
	form.ethCreationFee = presaleData.ethCreationFee;
	form.maxPresalePeriod = presaleData.maxPresalePeriod;
	form.minLockPeriod = presaleData.minLockPeriod;
	form.minLiqiudityPercent = presaleData.minLiqiudityPercent;
	lpform.maxLockCycle = presaleData.maxLockCycle;
	lpform.minCyclePercent = presaleData.minCyclePercent;
	lpform.maxLockPeriod = presaleData.maxLockPeriod;
	owner.value = presaleData.owner;
	feeToken.value = presaleData.feeToken;
	useTokenForFees.value = presaleData.useTokenForFees;
});
const lp = useUpdateLp(props.factory, lpform);
const settings = useUpdateSettings(props.factory, form);
const updateOwer = useUpdateOwner(props.factory, owner);
const isEther = computed(() => presaleData.feeToken === zeroAddress);
const updateFeeToken = useUpdateFeeToken(props.factory, feeToken);
const [, adLink] = useAddress(props.factory.contract);
const saveFeeToken = async () => {
	await updateFeeToken.update(true);
	presaleData.update();
};
const saveOwner = async () => {
	await updateOwer.update();
	presaleData.update();
};
const saveLp = async () => {
	await lp.update();
	presaleData.update();
};

const saveSettings = async () => {
	console.log("saving");
	await settings.updateSettings(decimals.value);
	presaleData.update();
};
</script>
<template>
	<div class="grid grid-cols-1">
		<div class="card card-border">
			<div class="card-body p-14">
				<div class="mx-auto">
					<div class="flex items-center group justify-between mb-12">
						<div class="flex align-middle items-center">
							<div>
								<h3
									class="text-gray-600 dark:text-gray-300 dark:group-hover:text-gray-100 group-hover:text-gray-900"
								>
									Factory Address
								</h3>
								<a
									target="_blank"
									class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition duration-200"
									:href="adLink"
									>{{ factory.contract }}</a
								>
							</div>
						</div>
					</div>
					<div class="space-y-6 border-b pb-8 border-gray-300 dark:border-gray-600">
						<div class="md:grid gap-x-4">
							<h3 class="text-2xl">Custom Token For Fees</h3>
						</div>
						<div class="md:grid grid-cols-3 gap-x-4">
							<Switch v-model="useTokenForFees"
								><span>Use Custom Token For Fees</span></Switch
							>
						</div>
						<div
							v-if="useTokenForFees"
							class="md:grid grid-cols-3 mt-6 gap-x-4"
						>
							<FormInput
								v-model="feeToken"
								class="col-span-2"
								:label="$t('Custom Token for Fees')"
							>
								<template #label>
									{{ $t("Custom Token for Fees") }}
									<RefreshCcwIcon
										v-tipp="$t('Reload Token Information')"
										v-if="!isEther"
										@click="updateToken"
										:class="{'animate-spin': loadToken}"
										class="ml-2 w-4 h-4 inline-block cursor-pointer"
									/>
								</template>
							</FormInput>
							<div class="flex flex-col mt-4 lg:mt-0 justify-end">
								<PrimaryButton
									@click.prevent="updateFeeToken.update()"
									type="button"
									:disabled="presaleData.feeToken === feeToken || feeToken === ''"
									primary
								>
									<Loading
										class="!-ml-1 !mr-2 inline-block"
										v-if="updateFeeToken.busy"
									/>Save Fee Token
								</PrimaryButton>
							</div>
							<p
								v-if="error"
								class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
							>
								{{ error }}
							</p>
							<p
								v-else-if="updateFeeToken.error"
								class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
							>
								{{ updateFeeToken.error }}
							</p>
							<p
								v-else
								class="col-span-3 text-xs font-semibold"
							>
								{{
									updateFeeToken.busy
										? updateFeeToken.status
										: $t("Deploy Fees will be collected in this Token")
								}}<a
									:href="updateFeeToken.txlink"
									target="_blank"
									class="ml-2 text-emerald-500"
									v-if="updateFeeToken.busy && updateFeeToken.tx"
									>{{ updateFeeToken.tx }}</a
								>
							</p>
							<div class="col-span-2 mt-2 gap-2 grid md:grid-cols-3">
								<FormInput
									disabled
									:label="$t('name')"
									v-model="name"
								/>
								<FormInput
									disabled
									:label="$t('symbol')"
									v-model="symbol"
								/>
								<FormInput
									disabled
									:label="$t('decimals')"
									v-model="decimals"
								/>
							</div>
						</div>
						<div
							v-else
							class="md:grid grid-cols-3 mt-6 gap-x-4"
						>
							<div class="flex col-span-2 flex-col mt-4 lg:mt-0 justify-end">
								<JetButton
									@click.prevent="saveFeeToken"
									type="button"
									:disabled="isEther"
								>
									<Loading
										class="!text-white !-ml-3 !mr-2 inline-block"
										v-if="updateFeeToken.busy"
									/>
									{{
										isEther
											? $t("Currently Using {chain} for Fees", {
													chain: factoryChain?.nativeCurrency?.symbol,
											  })
											: $t("Click to switch to {chain}", {
													chain: factoryChain?.nativeCurrency?.symbol,
											  })
									}}
								</JetButton>
							</div>
						</div>
					</div>
					<div class="space-y-6 border-b pb-8 mt-6 border-gray-300 dark:border-gray-600">
						<div class="md:grid gap-x-4">
							<h3 class="text-2xl">Transfer Ownership</h3>
						</div>
						<div class="md:grid grid-cols-3 gap-x-4">
							<FormInput
								v-model="owner"
								class="col-span-2"
								:label="$t('OwnerAddress')"
							/>
							<div class="flex flex-col mt-4 lg:mt-0 justify-end">
								<DangerButton
									type="button"
									:disabled="currentOwner == owner"
									@click.prevent="saveOwner"
								>
									<Loading
										class="!text-red-600 !-ml-1 !mr-2 inline-block"
										v-if="updateOwer.busy"
									/>Change Owner
								</DangerButton>
							</div>
							<p
								v-if="updateOwer.error"
								class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
							>
								{{ updateOwer.error }}
							</p>
							<p
								v-else
								class="col-span-3 text-xs font-semibold"
							>
								{{
									updateOwer.busy
										? updateOwer.status
										: "The Factory Owners Address"
								}}<a
									:href="updateOwer.busy && updateOwer.txlink"
									class="ml-2 mt-1 text-emerald-500"
									v-if="updateOwer.tx"
									>{{ updateOwer.tx }}</a
								>
							</p>
						</div>
					</div>
					<div
						v-if="factory.isFairLaunch"
						class="space-y-6 mt-8 mb-8 pb-5 border-b border-gray-300 dark:border-gray-600"
					>
						<div class="md:grid gap-x-4">
							<h3 class="text-2xl">Fair Liquidity Settings</h3>
						</div>
						<div class="grid lg:grid-cols-3 lg:gap-x-3">
							<FormInput
								:label="$t('Max Lock Cycle')"
								v-model="lpform.maxLockCycle"
								:error="lpform.errors.maxLockCycle"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>Days</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Max Lock Period')"
								v-model="lpform.maxLockPeriod"
								:error="lpform.errors.maxLockPeriod"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>Days</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Min Cycle Percent')"
								v-model="lpform.minCyclePercent"
								:error="lpform.errors.minCyclePercent"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>%</span
									></template
								>
							</FormInput>
						</div>
						<div class="flex justify-end items-center">
							<p
								v-if="lp.error"
								class="text-red dark:text-red-400 text-xs font-semibold"
							>
								{{ lp.error }}
							</p>
							<p
								v-else
								class="text-xs font-semibold"
							>
								{{ lp.status
								}}<a
									:href="lp.busy && lp.txlink"
									class="ml-2 mt-1 text-emerald-500"
									v-if="lp.tx"
									>{{ lp.tx }}</a
								>
							</p>
							<div class="flex flex-col ml-3 mt-4 lg:mt-0 justify-end">
								<PrimaryButton
									type="button"
									@click.prevent="saveLp"
									primary
								>
									<Loading
										class="!-ml-1 !mr-2 inline-block"
										v-if="lp.busy"
									/>Update Fair Liquidity Settings
								</PrimaryButton>
							</div>
						</div>
					</div>

					<div class="space-y-6 mt-8 mb-8">
						<div class="md:grid gap-x-4">
							<h3 class="text-2xl">Presale Settings</h3>
						</div>
						<div class="grid lg:grid-cols-3 lg:gap-x-3">
							<FormInput
								:label="$t('Token Lock address')"
								class="lg:col-span-2"
								v-model="form.timeLock"
								:error="form.errors.timeLock"
							>
								<template #trail>
									<QrCodeIcon
										class="text-emerald-600 w-5 h-5 dark:text-emerald-400 font-semibold"
									/>
								</template>
							</FormInput>
						</div>
						<div class="grid lg:grid-cols-2 lg:gap-x-3">
							<FormInput
								:label="$t('Fees Recipient address')"
								:help="
									$t('Your address where Base Token fees (eg ETH) will be sent')
								"
								v-model="form.feeAddress"
								:error="form.errors.feeAddress"
							/>
							<FormInput
								:label="$t('Creation Fee')"
								:help="
									$t(
										'These Fees will be charged when user is creating Fair Launch ',
									)
								"
								v-model="form.ethCreationFee"
								:error="form.errors.ethCreationFee"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>{{ symbol }}</span
									></template
								>
							</FormInput>
						</div>

						<div class="grid lg:grid-cols-3 lg:gap-x-3">
							<span class="lg:col-span-3 text-base mb-3 font-semibold"
								>Admin Share of raised funds</span
							>
							<FormInput
								:label="$t('Base ONLY Percent')"
								:help="$t('When user pays only in ETH')"
								v-model="form.baseOnlyFee"
								:error="form.errors.baseOnlyFee"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>%</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Base Fee Percent')"
								:help="$t('When pays ETH & Fair Launch  Token')"
								v-model="form.baseFee"
								:error="form.errors.baseFee"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>%</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Fair Launch  Token Fee Percent')"
								:help="$t('When pays ETH & Fair Launch  Token')"
								v-model="form.tokenFee"
								:error="form.errors.tokenFee"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>%</span
									></template
								>
							</FormInput>
						</div>

						<div class="grid lg:grid-cols-4 lg:gap-x-3">
							<FormInput
								:label="$t('Minimum Referral Percent')"
								:help="$t('Paid to referrals')"
								v-model="form.referralFee"
								:error="form.errors.referralFee"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>%</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Min Liqiudity Percent')"
								:help="$t('Min Basetoken for Liquidtiy')"
								v-model="form.minLiqiudityPercent"
								:error="form.errors.minLiqiudityPercent"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>%</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Max Fair Launch  Period')"
								:help="$t('Max days for Fair Launch')"
								v-model="form.maxPresalePeriod"
								:error="form.errors.maxPresalePeriod"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>Days</span
									></template
								>
							</FormInput>
							<FormInput
								:label="$t('Min Lock Period')"
								:help="$t('Min days to lock Lp tokens')"
								v-model="form.minLockPeriod"
								:error="form.errors.minLockPeriod"
							>
								<template #trail
									><span
										class="text-emerald-600 dark:text-emerald-400 font-semibold"
										>Days</span
									></template
								>
							</FormInput>
						</div>
						<div class="flex justify-end space-x-3 items-center pt-6 align-middle">
							<div class="text-sm font-semibold">
								<span
									v-if="settings.error"
									class="col-span-3 text-red text-sm font-semibold"
									>{{ settings.error }}</span
								>
								<span
									v-else
									class="col-span-3 text-sm font-semibold"
									>{{ settings.status
									}}<a
										:href="settings.txlink"
										class="ml-2 text-emerald-500"
										v-if="settings.busy && settings.tx"
										>{{ settings.tx }}</a
									></span
								>
							</div>
							<PrimaryButton
								@click="saveSettings"
								primary
							>
								<Loading
									class="!-ml-1 !mr-2 inline-block"
									v-if="settings.busy"
								/>
								<RectangleStackIcon
									v-else
									class="w-5 h-5"
								/>
								<span class="ml-2"
									>Update Settings on {{ factoryChain?.name }}</span
								>
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
