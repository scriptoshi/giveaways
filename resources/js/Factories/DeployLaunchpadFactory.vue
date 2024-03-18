<script setup>
import {computed, watch} from "vue";

import {Link, useForm} from "@inertiajs/vue3";
import {HiSolidArrowSmLeft, MdQrcodeTwotone} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {formatEther} from "viem";

import OutLineButton from "@/Components/Buttons/OutLineButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useDeployPresaleFactory} from "@/Factories/hooks/useDeployPresaleFactory";
import DangerButton from "@/Jetstream/DangerButton.vue";
import PrimaryButton from "@/Jetstream/JetButton.vue";

const props = defineProps({
	locks: Array,
	foundryInfo: Object,
	name: String,
});
const {chainId, chain, address: account} = useAccount();
const form = useForm({
	timeLock: null,
	owner: account.value,
	feeAddress: account.value,
	baseOnlyFee: "5",
	baseFee: "2",
	tokenFee: "2",
	referralFee: "2",
	ethCreationFee: "0.2",
	maxPresalePeriod: "30",
	minLockPeriod: "60",
	minLiqiudityPercent: "60",
});
watch(
	chainId,
	(chainId) => {
		form.timeLock = form.timeLock ?? props.locks[chainId];
		form.owner = form.owner ?? account.value;
		form.feeAddress = form.feeAddress ?? account.value;
	},
	{immediate: true},
);

const deploy = useDeployPresaleFactory(props.foundryInfo, form);
const pay = computed(() => formatEther(deploy.deployPrice ?? "0"));
const isSupported = computed(() => !!props.foundryInfo.contracts[chainId.value]);
</script>
<template>
	<div class="card p-14">
		<div
			v-if="!isSupported"
			class="flex card-body items-center group justify-between my-6"
		>
			<div class="flex align-middle items-center">
				<div>
					<div
						class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-sm dark:bg-red-200 dark:text-red-800"
						role="alert"
					>
						<template
							><span class="font-medium"
								>{{ $t("Unsupported Network!") }} : {{ chain?.name }}</span
							>
							{{ $t("This network is currently not supported") }}</template
						>
					</div>
					<div
						class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-sm dark:bg-red-200 dark:text-red-800"
						role="alert"
						v-if="error"
					>
						<span class="font-medium">{{ $t("An Error Occured") }} </span> {{ error }}
					</div>
				</div>
			</div>
		</div>
		<div class="space-y-6 card-body">
			<div class="grid lg:grid-cols-3 lg:gap-x-3">
				<FormInput
					:label="$t('Liquidity Locker  Contract')"
					class="lg:col-span-2"
					v-model="form.timeLock"
					:error="form.errors.timeLock"
				>
					<template #trail>
						<VueIcon
							:icon="MdQrcodeTwotone"
							class="text-emerald-600 w-5 h-5 dark:text-emerald-400 font-semibold"
						/>
					</template>
				</FormInput>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<OutLineButton
						type="button"
						v-if="chainId"
						:href="route('admin.factories.create', {factory: 'token-lock'})"
						:disabled="!!form.timeLock"
						class="self-start"
						link
						primary
					>
						{{ $t("Create Liquidity Locker") }}
					</OutLineButton>
				</div>
			</div>
			<div class="grid lg:grid-cols-2 lg:gap-x-3">
				<FormInput
					:label="$t('Fees and Commission Destination Address')"
					:help="$t('All collected fees and Commision sent here!')"
					v-model="form.feeAddress"
					:error="form.errors.feeAddress"
				/>
				<FormInput
					:label="$t('Contract Owner Role')"
					help="The Contracts Super Admin Role"
					v-model="form.owner"
					:error="form.errors.owner"
				/>
			</div>
			<div class="grid lg:grid-cols-3 lg:gap-x-3">
				<span class="lg:col-span-3 text-lg mb-3 font-semibold">Admin Commission</span>
				<FormInput
					:label="$t('Base ONLY Percent')"
					:help="$t('When user pays only in ETH')"
					v-model="form.baseOnlyFee"
					:error="form.errors.baseOnlyFee"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
							>%</span
						></template
					>
				</FormInput>
				<FormInput
					:label="$t('Base Fee Percent')"
					:help="$t('When pays ETH & Presale Token')"
					v-model="form.baseFee"
					:error="form.errors.baseFee"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
							>%</span
						></template
					>
				</FormInput>
				<FormInput
					:label="$t('Presale Token Fee Percent')"
					:help="$t('When pays ETH & Presale Token')"
					v-model="form.tokenFee"
					:error="form.errors.tokenFee"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
							>%</span
						></template
					>
				</FormInput>
			</div>
			<div class="grid lg:grid-cols-4 lg:gap-4">
				<FormInput
					class="lg:col-span-2"
					:label="$t('Native Fee for Creating {name}', {name})"
					:help="$t('These Fees are charged when creating {name}', {name})"
					v-model="form.ethCreationFee"
					:error="form.errors.ethCreationFee"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{
							chain?.nativeCurrency.symbol
						}}</span></template
					>
				</FormInput>

				<FormInput
					:label="$t('Minimum Referral Percent')"
					:help="$t('Paid to referrals')"
					v-model="form.referralFee"
					:error="form.errors.referralFee"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
							>%</span
						></template
					>
				</FormInput>
				<div></div>
				<FormInput
					:label="$t('Min Liqiudity Percent')"
					:help="$t('Min Basetoken for Liquidtiy')"
					v-model="form.minLiqiudityPercent"
					:error="form.errors.minLiqiudityPercent"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
							>%</span
						></template
					>
				</FormInput>
				<FormInput
					:label="$t('Max Presale Period')"
					:help="$t('Max days for presales')"
					v-model="form.maxPresalePeriod"
					:error="form.errors.maxPresalePeriod"
				>
					<template #trail
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
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
						><span class="text-emerald-600 dark:text-emerald-400 font-semibold"
							>Days</span
						></template
					>
				</FormInput>
			</div>
			<div class="flex space-x-2 pt-4 justify-end items-center align-middle">
				<div
					v-if="deploy.error || deploy.busy"
					class="text-sm font-semibold"
				>
					<span
						v-if="deploy.error"
						class="col-span-3 text-red-600 text-sm font-semibold"
						>{{ deploy.error }}</span
					>
					<span
						v-else
						class="col-span-3 text-sm font-semibold"
						>{{
							deploy.busy
								? deploy.status
								: $t("Deployments are final cannot be deleted!")
						}}<a
							:href="deploy.txlink"
							class="ml-2 text-emerald-500"
							v-if="deploy.tx"
							>{{ deploy.tx }}</a
						></span
					>
				</div>
				<Link
					v-else
					:href="route('admin.factories.index')"
				>
					<DangerButton>
						<VueIcon
							:icon="HiSolidArrowSmLeft"
							class="-ml-1 mr-2 w-5 h-5"
						/>Cancel And Go Back
					</DangerButton>
				</Link>
				<PrimaryButton
					type="button"
					@click="deploy.deploy"
					:disabled="!isSupported || !deploy.ready"
					primary
				>
					<Loading
						class="-ml-2 inline-block"
						v-if="deploy.busy"
					/>Create {{ name }} factory {{ pay }} {{ chain?.nativeCurrency?.symbol }}
				</PrimaryButton>
			</div>
		</div>
	</div>
</template>
