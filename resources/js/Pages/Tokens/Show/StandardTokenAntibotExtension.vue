<script setup>
import {useForm} from "@inertiajs/vue3";
import {HiChevronDoubleRight} from "oh-vue-icons/icons";

import AddressInputTextArea from "@/Components/AddressInputTextArea.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useAntibotTokenAdmin} from "@/hooks/token/useAntibotTokenAdmin";

const props = defineProps({
	token: Object,
	antibotAddress: String,
});
const form = useForm({
	remove: false,
	addressList: [],
	stopInDays: null,
	maxPerTrade: null,
	increment: null,
	delay: null,
});
const state = useAntibotTokenAdmin(props.token, props.antibotAddress, form);
state.update();
</script>
<template>
	<div class="shadow sm:rounded-sm sm:overflow-hidden mt-12">
		<div class="card shadow-none rounded-none py-6 px-4 space-y-6 sm:p-6">
			<div class="space-y-6">
				<div class="flex space-x-3">
					<h3 class="text-lg">{{ $t("ANTI BOT") }}</h3>
					<span
						v-if="state.stopInDays < 0"
						class="bg-red-100 self-center text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300"
						>Disabled</span
					>
					<span
						v-else
						class="bg-green-100 self-center text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300"
						>Enabled</span
					>
				</div>

				<div class="">
					<div class="p-8 border border-gray-200 dark:border-gray-500">
						<h3 class="text-base mb-6">{{ $t("Manage Antibot Settings") }}</h3>
						<div class="md:grid grid-cols-4 gap-x-4">
							<div>
								<FormInput
									v-model="form.stopInDays"
									:error="form.errors.stopInDays"
									:help="$t('Number of days from now')"
									:label="
										state.stopInDays > 0
											? $t('Stop in {days} days from now', {
													days: state.stopInDays.toFixed(),
											  })
											: $t('Stopped {days} days ago', {
													days: Math.abs(state.stopInDays.toFixed()),
											  })
									"
								/>
							</div>
							<FormInput
								v-model="form.delay"
								:error="form.errors.delay"
								:help="$t('Wait Seconds Between Tranfers')"
								:label="$t('Transfer Delay')"
							/>
							<FormInput
								v-model="form.maxPerTrade"
								:error="form.errors.maxPerTrade"
								:label="$t('Max Per Tx')"
							>
								<template #trail>{{ token.symbol }}</template>
							</FormInput>
							<FormInput
								v-model="form.increment"
								:error="form.errors.increment"
								:label="$t('Max Increment')"
								:help="$t('Increase Max Per Tx every block')"
							>
								<template #trail>{{ token.symbol }}</template>
							</FormInput>
						</div>
						<div class="md:grid grid-cols-3 mt-4 gap-x-4">
							<div class="flex flex-col mt-4 lg:mt-0 justify-center">
								<SwitchChainButton :to-chain="token.chainId">
									<button
										type="button"
										@click.prevent="state.updateAntiBotSettings"
										class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
									>
										<Loading
											class="-ml-2 inline-block"
											v-if="state.busy === 'update'"
										/>
										{{ $t("Update Antibot Setting") }}
									</button>
								</SwitchChainButton>
							</div>
							<div
								v-if="state.called === 'update'"
								class="flex items-center"
							>
								<TxStatus :state="state" />
							</div>
						</div>
					</div>
					<div class="p-8 border mt-10 border-gray-200 dark:border-gray-500">
						<h3 class="text-base mb-6">{{ $t("Manage Antibot White list") }}</h3>

						<div class="mt-4">
							<div class="flex justify-between gap-3">
								<AddressInputTextArea
									v-model="state.whitelist"
									class="max-w-lg w-full"
									help=""
									label="Current White List"
									disabled
								>
									<template #status>
										<TxStatus
											class="mt-1"
											v-if="
												['unTokenWhiteList', 'addTokenWhiteList'].includes(
													state.called,
												)
											"
											:state="state"
										/>
									</template>
									<template #actions>
										<PrimaryButton
											secondary=""
											class="!py-1 ml-3 !px-2.5 !text-sm"
											@click.prevent="form.addressList = state.whitelist"
										>
											{{ $t("Copy") }}
											<VueIcon
												class="w-4 h-4 ml-2 -mr-1"
												:icon="HiChevronDoubleRight"
											/>
										</PrimaryButton>
									</template>
								</AddressInputTextArea>
								<AddressInputTextArea
									v-model="form.addressList"
									:key="form.remove"
									:error="form.errors.addressList"
									label="Update White List"
									class="max-w-lg w-full"
								>
									<template #status>
										<TxStatus
											class="mt-1"
											v-if="
												['unTokenWhiteList', 'addTokenWhiteList'].includes(
													state.called,
												)
											"
											:state="state"
										/>
									</template>
								</AddressInputTextArea>
							</div>
							<div class="mt-4 flex items-center space-x-4 justify-end">
								<label class="inline-flex items-center space-x-2">
									<input
										v-model="form.remove"
										class="form-switch h-5 w-10 rounded-full bg-gray-300 before:rounded-full before:bg-gray-50 checked:!bg-emerald-500 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
										type="checkbox"
									/>
									<span v-if="form.remove">{{ $t("Add") }}</span>
									<span v-else>{{ $t("Remove") }}</span>
								</label>
								<SwitchChainButton :to-chain="token.chainId">
									<PrimaryButton
										error
										v-if="form.remove"
										@click="state.unTokenWhiteList"
									>
										<Loading
											class="-ml-2 inline-block"
											v-if="state.busy === 'unTokenWhiteList'"
										/>
										{{ $t("Remove Whitelist") }}
									</PrimaryButton>
									<PrimaryButton
										primary
										v-else
										@click="state.addTokenWhiteList"
									>
										<Loading
											class="-ml-2 inline-block"
											v-if="state.busy === 'addTokenWhiteList'"
										/>
										{{ $t("Add Whitelist") }}
									</PrimaryButton>
								</SwitchChainButton>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
