<script setup>
import {computed, ref, watch} from "vue";

import {useChains} from "use-wagmi";
import {parseUnits, zeroAddress} from "viem";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import {
	useUpdateFeePercent,
	useUpdateFeeTo,
	useUpdateFeeToken,
	useUpdateFees,
	useUpdateOwner,
	useUpdateRefPercent,
} from "@/Factories/hooks/factory";
import {useManageFactory} from "@/Factories/hooks/useManageFactory";
import {RefreshCcwIcon} from "@/Feather";
import DangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/JetButton.vue";
import {NetworkIcon} from "@/Wagmi/components/Icons";
import {useAddress} from "@/Wagmi/hooks/useTxHash";
// eslint-disable-next-line import/order
import {useToken} from "@/hooks/useUpdateCoins";

const props = defineProps({
	factory: Object,
});
const chains = useChains();
const factoryChain = computed(() =>
	chains.value.find((c) => c.id.toString() === props.factory?.chainId?.toString()),
);
const [, adLink] = useAddress(props.factory.contract);
const owner = ref("");
const feeTo = ref("");
const fees = ref("");
const feePercent = ref("");
const refPercent = ref("");
const feeToken = ref("");
const useTokenForFees = ref(false);
const data = useManageFactory(props.factory);
watch(
	[
		() => data.owner,
		() => data.feeTo,
		() => data.fees,
		() => data.feePercent,
		() => data.refPercent,
		() => data.feeToken,
		() => data.useTokenForFees,
	],
	([iowner, ifeeTo, ifees, ifeePercent, irefPercent, ifeeToken, iuseTokenForFees]) => {
		owner.value = iowner;
		feeTo.value = ifeeTo;
		fees.value = ifees;
		feePercent.value = ifeePercent;
		refPercent.value = irefPercent;
		feeToken.value = ifeeToken;
		useTokenForFees.value = iuseTokenForFees;
	},
);
const {
	name,
	symbol,
	decimals,
	error,
	loading: loadToken,
	updateToken,
} = useToken(feeToken, parseInt(props.factory?.chainId));
const parsedFees = computed(() => parseUnits(fees.value, decimals.value));
const updateFees = useUpdateFees(props.factory, parsedFees);
const updateFeeTo = useUpdateFeeTo(props.factory, feeTo);
const updateOwer = useUpdateOwner(props.factory, owner);
const updateFeeToken = useUpdateFeeToken(props.factory, feeToken);
const fpercent = useUpdateFeePercent(props.factory, feePercent);
const refFee = useUpdateRefPercent(props.factory, refPercent);
const updateFeeTokenContract = async (toEther = false) => {
	await updateFeeToken.update(toEther);
	data.update();
};
</script>
<template>
	<div class="mb-12 card py-8 px-14">
		<div class="mb-8">
			<h3 class="mb-2">Update Factory</h3>
			<a
				target="_blank"
				class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition duration-200"
				:href="adLink"
				>{{ factory.contract }}</a
			>
		</div>
		<div class="vertical">
			<div class="md:grid grid-cols-3 gap-x-4">
				<Switch v-model="useTokenForFees">
					<span>Use Custom Token For Fees</span>
				</Switch>
			</div>
			<div
				v-if="data.loading"
				class="p-4 mt-4 border border-gray-200 dark:border-gray-600 w-full flex items-center justify-start"
			>
				{{ $t("Laoding contract Information") }}<Loading class="ml-4 text-emerald-500" />
			</div>
			<template v-else>
				<CollapseTransition>
					<div
						v-show="useTokenForFees"
						class="md:grid grid-cols-3 mt-6 gap-x-4"
					>
						<FormInput
							v-model="feeToken"
							class="col-span-2"
						>
							<template #label>
								{{ $t("Custom Token for Fees") }}
								<RefreshCcwIcon
									v-tipp="$t('Reload Token Information')"
									v-if="feeToken !== zeroAddress"
									@click="updateToken"
									:class="{'animate-spin': loadToken}"
									class="ml-2 w-4 h-4 inline-block cursor-pointer"
								/>
							</template>
						</FormInput>
						<div class="flex flex-col mt-4 lg:mt-0 justify-end">
							<PrimaryButton
								@click.prevent="updateFeeTokenContract()"
								type="button"
								:disabled="data.feeToken === feeToken || feeToken === ''"
							>
								<Loading
									class="!-ml-1 !mr-2 !text-white"
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
				</CollapseTransition>
				<CollapseTransition>
					<div
						v-show="!useTokenForFees"
						class="md:grid grid-cols-3 mt-6 gap-x-4"
					>
						<div class="flex col-span-2 flex-col mt-4 lg:mt-0 justify-end">
							<JetButton
								@click.prevent="updateFeeTokenContract(true)"
								type="button"
								:disabled="data.isEther"
							>
								<Loading
									class="!-ml-1 !mr-2 inline-block"
									v-if="updateFeeToken.busy"
								/>
								{{
									data.isEther
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
				</CollapseTransition>
			</template>
			<div class="md:grid grid-cols-3 gap-x-4 mt-6">
				<FormInput
					v-model="feeTo"
					class="col-span-2"
					:label="$t('Fee Recipient')"
				/>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<PrimaryButton
						@click.prevent="updateFeeTo.update()"
						type="button"
						:disabled="data.feeTo === feeTo"
						primary
					>
						<Loading
							class="-ml-2 inline-block"
							v-if="updateFeeTo.busy"
						/>Save Recipient
					</PrimaryButton>
				</div>
				<p
					v-if="updateFeeTo.error"
					class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
				>
					{{ updateFeeTo.error }}
				</p>
				<p
					v-else
					class="col-span-3 text-xs font-semibold"
				>
					{{ updateFeeTo.busy ? updateFeeTo.status : "Fees collected will send here"
					}}<a
						:href="updateFeeTo.txlink"
						target="_blank"
						class="ml-2 text-emerald-500"
						v-if="updateFeeTo.busy && updateFeeTo.tx"
						>{{ updateFeeTo.tx }}</a
					>
				</p>
			</div>
			<div class="md:grid grid-cols-3 mt-6 gap-x-4">
				<FormInput
					v-model="owner"
					class="col-span-2"
					:label="$t('OwnerAddress')"
				/>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<DangerButton
						type="button"
						:disabled="data.owner === owner"
						@click.prevent="updateOwer.update()"
					>
						<Loading
							class="!-ml-1 !mr-2 !text-red-600 inline-block"
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
					{{ updateOwer.busy ? updateOwer.status : "The Factory Owners Address"
					}}<a
						:href="updateOwer.busy && updateOwer.txlink"
						class="ml-2 mt-1 text-emerald-500"
						v-if="updateOwer.tx"
						>{{ updateOwer.tx }}</a
					>
				</p>
			</div>

			<div class="md:grid grid-cols-3 mt-6 gap-x-4">
				<FormInput
					v-model="fees"
					class="col-span-2"
					:label="
						(factory.type == 'MultiSenderFactory'
							? $t('Sending Fees')
							: factory.type == 'TokenLock'
							  ? $t('Unlock Fees')
							  : $t('Deployment Fees')) +
						' (' +
						symbol +
						')'
					"
				>
					<template #trail>
						<NetworkIcon
							class="w-4 h-4"
							v-if="data.isEther"
							:chainId="factory.chainId"
						/>
						<span v-else>{{ symbol }}</span>
					</template>
				</FormInput>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<PrimaryButton
						type="button"
						:disabled="data.fees === fees"
						@click.prevent="updateFees.update()"
						primary
					>
						<Loading
							class="!-ml-1 !mr-2"
							v-if="updateFees.busy"
						/>Update fees
					</PrimaryButton>
				</div>
				<p
					v-if="updateFees.error"
					class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
				>
					{{ updateFees.error }}
				</p>
				<p
					v-else
					class="col-span-3 mt-1 text-xs font-semibold"
				>
					{{ updateFees.status }}
					<a
						:href="updateFees.busy && updateFees.txlink"
						class="ml-2 text-emerald-500"
						v-if="updateFees.tx"
						>{{ updateFees.tx }}</a
					>
				</p>
			</div>

			<div
				v-if="factory.type == 'AirdropFactory'"
				class="md:grid grid-cols-3 mt-6 gap-x-4"
			>
				<FormInput
					v-model="feePercent"
					class="col-span-2"
					:label="$t('Airdrop Admin Percent')"
				>
					<template #trail>%</template>
				</FormInput>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<PrimaryButton
						type="button"
						:disabled="data.feePercent === feePercent"
						@click.prevent="fpercent.update()"
						primary
					>
						<Loading
							class="-ml-2 inline-block"
							v-if="fpercent.busy"
						/>Update Fee Percent
					</PrimaryButton>
				</div>
				<p
					v-if="fpercent.error"
					class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
				>
					{{ fpercent.error ? fpercent.error : "Fees admin gets on airdrop" }}
				</p>
				<p
					v-else
					class="col-span-3 mt-1 text-xs font-semibold"
				>
					{{ fpercent.status }}
					<a
						:href="fpercent.busy && fpercent.txlink"
						class="ml-2 text-emerald-500"
						v-if="fpercent.tx"
						>{{ fpercent.tx }}</a
					>
				</p>
			</div>
			<div
				v-if="factory.type == 'NftFactory'"
				class="md:grid grid-cols-3 mt-6 gap-x-4"
			>
				<FormInput
					v-model="feePercent"
					class="col-span-2"
					:label="$t('NFT Admin Percent')"
				>
					<template #trail>%</template>
				</FormInput>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<PrimaryButton
						type="button"
						:disabled="data.feePercent === feePercent"
						@click.prevent="fpercent.update()"
						primary
					>
						<Loading
							class="-ml-2 inline-block"
							v-if="fpercent.busy"
						/>Update Fee Percent
					</PrimaryButton>
				</div>
				<p
					v-if="fpercent.error"
					class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
				>
					{{ fpercent.error ? fpercent.error : "Admin Sales Percentage" }}
				</p>
				<p
					v-else
					class="col-span-3 mt-1 text-xs font-semibold"
				>
					{{ fpercent.status }}
					<a
						:href="fpercent.busy && fpercent.txlink"
						class="ml-2 text-emerald-500"
						v-if="fpercent.tx"
						>{{ fpercent.tx }}</a
					>
				</p>
			</div>
			<div
				v-if="factory.type == 'NftFactory'"
				class="md:grid grid-cols-3 mt-6 gap-x-4"
			>
				<FormInput
					v-model="refPercent"
					class="col-span-2"
					:label="$t('NFT Admin Percent')"
				>
					<template #trail>%</template>
				</FormInput>
				<div class="flex flex-col mt-4 lg:mt-0 justify-end">
					<PrimaryButton
						type="button"
						:disabled="data.refPercent === refPercent"
						@click.prevent="refFee.update()"
						primary
					>
						<Loading
							class="-ml-2 inline-block"
							v-if="refFee.busy"
						/>Update Referral Percent
					</PrimaryButton>
				</div>
				<p
					v-if="refFee.error"
					class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
				>
					{{ refFee.error ? refFee.error : "Admin Sales Percentage" }}
				</p>
				<p
					v-else
					class="col-span-3 mt-1 text-xs font-semibold"
				>
					{{ refFee.status }}
					<a
						:href="refFee.busy && refFee.txlink"
						class="ml-2 text-emerald-500"
						v-if="refFee.tx"
						>{{ refFee.tx }}</a
					>
				</p>
			</div>
		</div>
	</div>
</template>
