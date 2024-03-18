<script setup>
import {computed, watch} from "vue";

import {Link, useForm, usePage} from "@inertiajs/vue3";
import {HiSolidArrowSmLeft, MdQrcodeTwotone} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {formatEther} from "viem";
import {avalanche, bsc, bscTestnet, fantom, polygon} from "viem/chains";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useDeployFactory} from "@/Factories/hooks/useDeployFactory";
import DangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/JetButton.vue";
import {NetworkIcon} from "@/Wagmi/components/Icons";

const props = defineProps({
	foundryInfo: Object,
	name: String,
});
const eip712 = computed(() => usePage().props.config.eip712);
const admin = computed(() => usePage().props.deployer);
const form = useForm({
	feeAddress: null,
	owner: null,
	fee: "0.01",
	feePercent: "15",
	deployer: admin.value,
	eip712: eip712.value,
});
const {chainId, chain, address: account} = useAccount();
watch(chainId, (chainId) => {
	if (parseInt(chainId) === bsc.id || parseInt(chainId) === bscTestnet.id) {
		form.fee = "0.2";
	}
	if (parseInt(chainId) === fantom.id) {
		form.fee = "130";
	}
	if (parseInt(chainId) === avalanche.id) {
		form.fee = "3";
	}
	if (parseInt(chainId) === polygon.id) {
		form.fee = "60";
	}
});
const isSupported = computed(() => !!props.foundryInfo.contracts[chainId.value]);
const deploy = useDeployFactory(props.foundryInfo, form);
const pay = computed(() => formatEther(deploy.deployPrice ?? "0"));
watch(
	account,
	(account) => {
		form.owner = form.owner ?? account;
		form.feeAddress = form.feeAddress ?? account;
	},
	{immediate: true},
);
</script>
<template>
	<div class="mb-12 card p-14 card-border">
		<div
			v-if="!isSupported"
			class="card-body"
		>
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
				v-if="deploy.error"
			>
				<span class="font-medium">{{ $t("An Error Occured: ") }} </span> {{ deploy.error }}
			</div>
		</div>
		<div class="card-body">
			<div class="grid sm:grid-cols-2 gap-4">
				<FormInput
					v-model="form.feeAddress"
					:disabled="!isSupported"
					:label="$t('Fee to Address')"
					:error="form.errors.feeAddress"
					help="This is the address that will collect  your deployment fees"
				>
					<template #trail>
						<VueIcon
							:icon="MdQrcodeTwotone"
							class="w-4 h-4 text-emerald-600"
						/>
					</template>
				</FormInput>
				<FormInput
					v-model="form.owner"
					:disabled="!isSupported"
					:error="form.errors.owner"
					:label="$t('Admin  Address')"
					help="This Address will be used to manage contract settings"
				>
					<template #trail>
						<VueIcon
							:icon="MdQrcodeTwotone"
							class="w-4 h-4 text-emerald-600"
						/>
					</template>
				</FormInput>
			</div>

			<div class="grid grid-cols-2 mt-6 gap-x-4">
				<FormInput
					v-model="form.fee"
					:disabled="!isSupported"
					:error="form.errors.fee"
					:label="
						(foundryInfo.name == 'MultiSenderFoundry'
							? $t('Sending Fees')
							: foundryInfo.name == 'LockFoundry'
							  ? $t('Unlock Fees')
							  : $t('Deployment Fees')) +
						' (' +
						chain?.nativeCurrency?.symbol +
						')'
					"
					:help="
						foundryInfo.name == 'MultiSenderFoundry'
							? $t('how much would you charge users to send tokens?')
							: foundryInfo.name == 'LockFoundry'
							  ? $t('how much would you charge users unlock tokens?')
							  : $t('how much would you charge users to deploy contracts?')
					"
				>
					<template #trail>
						<NetworkIcon
							class="w-4 h-4"
							:chainId="chainId"
						/>
					</template>
				</FormInput>
			</div>
			<div
				v-if="foundryInfo.name === 'AirdropFoundry'"
				class="grid sm:grid-cols-2 mt-6 gap-x-4"
			>
				<FormInput
					v-model="form.feePercent"
					:error="form.errors.feePercent"
					:label="$t('Airdrop fee Percentage')"
					help="what percentage of airdrop tokens would you like to take as commission?"
				>
					<template #trail><span class="text-emerald-600">%</span></template>
				</FormInput>
			</div>
			<div
				v-if="foundryInfo.name === 'NftFoundry'"
				class="grid sm:grid-cols-2 mt-6 gap-x-4"
			>
				<FormInput
					v-model="form.feePercent"
					:error="form.errors.feePercent"
					:label="$t('NFT Sale fee Percentage')"
					help="what percentage of nft slaes would you like to take as commission?"
				>
					<template #trail><span class="text-emerald-600">%</span></template>
				</FormInput>
			</div>
			<div class="flex items-center justify-end mt-8 gap-x-4">
				<h3 class="text-sm">
					<p
						v-if="deploy.error"
						class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
					>
						{{ deploy.error }}
					</p>
					<p
						v-else
						class="col-span-3 text-xs font-semibold"
					>
						{{ deploy.status
						}}<a
							:href="deploy.txlink"
							target="_blank"
							class="ml-2 text-emerald-500"
							v-if="deploy.busy && deploy.tx"
							>{{ deploy.tx }}</a
						>
					</p>
				</h3>
				<Link
					v-if="!deploy.busy"
					:href="route('admin.factories.index')"
				>
					<DangerButton>
						<VueIcon
							:icon="HiSolidArrowSmLeft"
							class="-ml-1 mr-2 w-5 h-5"
						/>Cancel
					</DangerButton>
				</Link>
				<button
					type="button"
					@click="deploy.deploy"
					:disabled="!isSupported || !deploy.ready"
				>
					<JetButton>
						<Loading
							class="-ml-2 inline-block"
							v-if="deploy.busy"
						/>Create {{ name }} factory {{ pay }}
						{{ chain?.nativeCurrency?.symbol }}
					</JetButton>
				</button>
			</div>
		</div>
	</div>
</template>
