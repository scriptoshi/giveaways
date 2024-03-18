<script setup>
import {computed, nextTick, watch} from "vue";

import {router, useForm} from "@inertiajs/vue3";
import axios from "axios";
import {HiRefresh, HiSolidArrowLeft, HiSolidPlus} from "oh-vue-icons/icons";
import {useChains} from "use-wagmi";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useCrossChainAdmin} from "@/hooks/token/useCrossChainAdmin";
import TokenInfo from "@/Pages/Tokens/Show/TokenInfo.vue";
import {isAddress} from "@/Wagmi/utils/utils";
const props = defineProps({
	token: Object,
});
const chains = useChains();

const form = useForm({
	...chains.value.reduce((memo, c) => {
		memo[c.id.toString()] = null;
		return memo;
	}, {}),
});
const state = useCrossChainAdmin(props.token, form);
const tokenChain = computed(() =>
	chains.value.find((c) => c.id.toString() === props.token.chainId.toString()),
);
const {t} = useI18n();
watch(
	() => form.data(),
	(fdata) => {
		console.log(fdata);
		for (const chainId in fdata) {
			if (!fdata[chainId]) continue;
			if (!isAddress(fdata[chainId])) {
				form.setError(chainId, t("Invalid Address"));
				continue;
			}
			axios
				.post(window.route("tokens.crosschain.check"), {
					token: fdata[chainId],
					chainId,
				})
				.then(({data}) => {
					if (!data.isValid) {
						form.setError(chainId, t("Address is not deployed on our system"));
					}
				});
		}
	},
);
const loadToken = (chainId, token) => {
	router.visit(window.route("tokens.show", {chainId, token}), {
		onSuccess() {
			nextTick(() => {
				state.setTrustedRemotes();
			});
		},
	});
};
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
							route('tokens.crosschain', {
								chainId: token.chainId,
								token: token.contract,
							})
						"
					>
						<VueIcon
							:icon="HiSolidPlus"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Send Across Chains") }}
					</PrimaryButton>
				</div>
			</div>
			<div class="card shadow-none rounded-none py-6 px-4 space-y-6 sm:p-6">
				<TokenInfo :token="token" />
				<div class="p-8 border border-gray-200 dark:border-gray-500">
					<h3 class="text-base">{{ $t("Manage Trusted Remotes") }}</h3>
					<p class="mb-6">
						{{ $t("These are the addresses of this token in other chains") }}
					</p>

					<div
						class="max-w-2xl p-4 mb-5 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800"
						role="alert"
					>
						<h3 class="text-base mb-3">Quick Guide on how to setup your token</h3>
						<ol
							class="max-w-2xl font-semibold space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400"
						>
							<li>Deploy Your Crosschain Token on every chain desired.</li>
							<li>
								Enter the address of the token on the corresponding chain below.
							</li>
							<li>
								Update each chain separately with the full list of token addresses
							</li>
							<li>
								If you need to see the list configured on you currenct chain, click
								reload button
							</li>
						</ol>
					</div>
					<div
						v-for="chain in chains"
						:key="chain.id"
						class="flex flex-col mb-3 sm:flex-row sm:justify-start sm:gap-x-3 sm:items-start"
					>
						<FormInput
							v-model="form[chain.id.toString()]"
							:error="form.errors[chain.id.toString()]"
							class="max-w-lg w-full"
						/>
						<SwitchChainButton
							@switched="loadToken(chain.id, form[chain.id.toString()])"
							:to-chain="chain.id"
							class="!py-2"
							:disabled="
								!form[chain.id.toString()] || form.errors[chain.id.toString()]
							"
						>
							<template #switch>
								<ChainInfo :chain-id="chain.id" />
							</template>
							<PrimaryButton
								secondary
								@click="state.setTrustedRemotes"
								class="!py-2"
							>
								<ChainInfo :chain-id="chain.id" />
							</PrimaryButton>
						</SwitchChainButton>
					</div>
					<div class="flex flex-col mt-6 items-center sm:flex-row">
						<TxStatus
							:state="state"
							v-if="state.called === 'setTrustedRemotes'"
							class="mb-3 sm:mb-0 sm:mr-6"
						/>
						<div class="flex items-center">
							<SwitchChainButton
								@switched="state.setTrustedRemotes"
								:to-chain="token.chainId"
							>
								<template #switch>
									{{ $t("Update on {chain}", {chain: tokenChain.name}) }}
								</template>
								<PrimaryButton
									primary
									@click="state.setTrustedRemotes"
									:disabled="state.busy === 'setTrustedRemotes'"
								>
									<Loading v-if="state.busy === 'setTrustedRemotes'" />
									<VueIcon
										v-else
										:icon="HiSolidPlus"
										class="-ml-1 mr-2 w-4 h-4"
									/>
									{{ $t("Update on {chain}", {chain: tokenChain.name}) }}
								</PrimaryButton>
							</SwitchChainButton>
							<PrimaryButton
								secondary
								@click="state.update"
								:disabled="state.loading"
								class="ml-4"
							>
								<VueIcon
									:icon="HiRefresh"
									class="-ml-1 mr-2 w-4 h-4"
									:class="{'animate-spin': state.loading}"
								/>
								{{ $t("Reload") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>
