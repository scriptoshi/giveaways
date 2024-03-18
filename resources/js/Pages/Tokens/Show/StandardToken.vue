<script setup>
import {computed, ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidArrowLeft, HiSolidPlus} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import Select from "@/Components/Select.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useStandardTokenAdmin} from "@/hooks/token/useStandardTokenAdmin";
import StandardTokenAntibotExtension from "@/Pages/Tokens/Show/StandardTokenAntibotExtension.vue";
import TokenInfo from "@/Pages/Tokens/Show/TokenInfo.vue";

const props = defineProps({
	token: Object,
});

const hasMint = computed(
	() => props.token.contract_name?.toLowerCase?.().includes?.("capped") ?? false,
);
const hasPause = computed(
	() => props.token.contract_name?.toLowerCase?.().includes?.("pausable") ?? false,
);
const hasAntiBot = computed(
	() => props.token.contract_name?.toLowerCase?.().includes?.("antibot") ?? false,
);
const hasVotes = computed(
	() => props.token.contract_name?.toLowerCase?.().includes?.("votes") ?? false,
);
const form = useForm({
	minto: null,
	user: null,
	amount: 0,
	role: null,
});
const state = useStandardTokenAdmin(props.token, form);
state.update();
const showGrant = ref(false);
const {t} = useI18n();
const {chainId} = useAccount();
</script>
<template>
	<section aria-labelledby="plan-heading">
		<div class="overflow-hidden">
			<div class="my-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0">
				<h3 class="uppercase">{{ $t("Manage Token") }}</h3>

				<div
					v-if="chainId"
					class="flex flex-col space-y-2 sm:space-y-0 sm:flex-row sm:space-x-2"
				>
					<PrimaryButton
						primary
						link
						:href="route('tokens.create', {type: 'standard', chainId})"
					>
						<VueIcon
							:icon="HiSolidArrowLeft"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Go Back") }}
					</PrimaryButton>
					<PrimaryButton
						v-if="hasVotes"
						secondary
						link
						href="#"
					>
						<VueIcon
							:icon="HiSolidPlus"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Governor") }}
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
			<div class="card shadow-none rounded-none pb-6 px-4 space-y-6 sm:p-6">
				<TokenInfo :token="token" />
				<div
					v-if="hasPause || hasMint"
					class="p-8 border border-gray-200 dark:border-gray-500"
				>
					<h3 class="text-base">Manage Roles</h3>
					<RadioSelect
						v-model="showGrant"
						v-if="state.hasAdminRole"
						:list="[
							{id: 'grant', value: true, label: `Grant`},
							{id: 'revoke', value: false, label: 'Renounce'},
						]"
					/>
					<CollapseTransition>
						<div v-show="showGrant">
							<div
								v-if="state.hasAdminRole"
								class="mt-6"
							>
								<h3 class="text-base my-6">{{ "Grant Roles" }}</h3>
								<div class="md:grid grid-cols-4 gap-x-4">
									<FormInput
										v-model="form.user"
										class="col-span-2"
										:label="$t('User Address')"
										:error="form.errors.user"
									/>
									<Select
										:label="$t('Select A Role')"
										v-model="form.role"
										:options="state.roles"
										:error="form.errors.role"
									/>
									<div class="flex flex-row space-x-2 mt-4 lg:mt-0 justify-end">
										<SwitchChainButton :to-chain="token.chainId">
											<button
												type="button"
												@click.prevent="state.grantRole"
												:class="{'w-full': state.busy === 'grantRole'}"
												class="self-end text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-sm text-sm px-5 py-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-all duration-300"
											>
												<Loading
													class="-ml-2 inline-block !text-gray-500"
													v-if="state.busy === 'grantRole'"
												/>
												{{ $t("Grant") }}
											</button>
											<button
												type="button"
												:disabled="state.busy === 'revokeRole'"
												:class="{'w-full': state.busy === 'revokeRole'}"
												@click.prevent="state.revokeRole"
												class="focus:outline-none self-end disabled:bg-emerald-400 disabled:hover:bg-emerald-400 text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-900 transition-all duration-300"
											>
												<Loading
													class="-ml-2 inline-block"
													v-if="state.busy === 'revokeRole'"
												/>
												{{ $t("Revoke") }}
											</button>
										</SwitchChainButton>
									</div>
									<TxStatus
										v-if="['grantRole', 'revokeRole'].includes(state.called)"
										:state="state"
									/>
								</div>
							</div>
						</div>
					</CollapseTransition>
					<CollapseTransition>
						<div v-show="!showGrant">
							<div
								v-if="
									state.hasMinterRole || state.hasPauserRole || state.hasAdminRole
								"
								class="mt-6"
							>
								<h3 class="text-base my-6">{{ "Renounce Roles" }}</h3>
								<div class="md:grid sm:grid-cols-3 gap-x-4">
									<Select
										:label="$t('Select A Role')"
										v-model="form.role"
										:options="state.roles"
										:error="form.errors.role"
									/>
									<div class="flex items-end mt-4 lg:mt-0">
										<SwitchChainButton :to-chain="token.chainId">
											<button
												type="button"
												:disabled="state.busy === 'renounceRole'"
												@click.prevent="state.renounceRole"
												class="focus:outline-none disabled:bg-emerald-400 disabled:hover:bg-emerald-400 text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-900"
											>
												<Loading
													class="-ml-2 inline-block"
													v-if="state.busy === 'renounceRole'"
												/>
												{{ $t("Renounce") }}
											</button>
										</SwitchChainButton>
									</div>
									<div
										v-if="state.called === 'renounceRole'"
										class="flex col-span-2 items-center"
									>
										<TxStatus :state="state" />
									</div>
								</div>
							</div>
						</div>
					</CollapseTransition>
				</div>
				<div
					v-if="hasPause"
					class="p-8 border border-gray-200 dark:border-gray-500"
				>
					<h3 class="text-base mb-6">Pause Contract</h3>
					<div class="md:grid grid-cols-2 gap-x-4">
						<div class="flex flex-col mt-4 lg:mt-0">
							<SwitchChainButton :to-chain="token.chainId">
								<PrimaryButton
									secondary
									type="button"
									v-if="!state.paused"
									@click.prevent="state.pause"
									class="self-start"
								>
									<Loading
										class="-ml-2 inline-block"
										v-if="state.busy === 'pause'"
									/>
									{{ t("Pause Contract") }}
								</PrimaryButton>
								<button
									type="button"
									v-else
									@click.prevent="state.unpause"
									class="text-white self-start bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-sm text-base px-5 py-1.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"
								>
									<Loading
										class="-ml-2 inline-block"
										v-if="state.busy === 'unpause'"
									/>
									Unpause Contract
								</button>
							</SwitchChainButton>
						</div>
						<div
							v-if="['pause', 'unpause'].includes(state.called)"
							class="flex items-center"
						>
							<TxStatus :state="state" />
						</div>
					</div>
				</div>
				<div
					v-if="hasMint"
					class="p-8 border border-gray-200 dark:border-gray-500"
				>
					<h3 class="text-base mb-6">Manage Supply</h3>
					<div class="md:grid grid-cols-4 gap-x-4">
						<FormInput
							v-model="form.minto"
							class="col-span-2"
							:label="$t('Mint To address')"
							:error="form.errors.minto"
						/>
						<FormInput
							v-model="form.amount"
							:error="form.errors.amount"
							:label="$t('Mint Amount')"
						/>
						<div
							:class="
								form.errors.amount || form.errors.minto
									? 'justify-center'
									: 'justify-end'
							"
							class="flex flex-col mt-4 lg:mt-0"
						>
							<button
								type="button"
								@click.prevent="state.mint"
								class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<Loading
									class="-ml-2 inline-block"
									v-if="state.busy === 'mint'"
								/>
								{{ $t("Mint") }}
							</button>
						</div>
					</div>
					<TxStatus
						v-if="state.called === 'mint'"
						:state="state"
						class="mt-1"
					/>
				</div>
			</div>
		</div>
		<StandardTokenAntibotExtension
			v-if="hasAntiBot && state.antibotAddress"
			:antibotAddress="state.antibotAddress"
			:token="token"
		/>
	</section>
</template>
