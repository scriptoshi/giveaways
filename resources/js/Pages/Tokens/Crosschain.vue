<script setup>
import {useForm} from "@inertiajs/vue3";

import Address from "@/Components/Address.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import {useCrossChain} from "@/hooks/token/useCrossChain";
import AppLayout from "@/Layouts/AppLayout.vue";
const props = defineProps({
	token: Object,
});
const form = useForm({
	amount: null,
	chain: null,
});
const state = useCrossChain(props.token, form);
</script>
<template>
	<AppLayout>
		<div class="mx-auto w-full container px-4">
			<div class="flex mt-8 items-center text-center justify-center w-full max-w-2xl mx-auto">
				<div class="w-full">
					<h3>Send Tokens To another chain</h3>
					<div
						class="p-10 border mt-8 rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
					>
						<div class="border p-5 border-gray-200 rounded-md dark:border-gray-700">
							<div class="flex items-center justify-center space-x-3">
								<Loading
									class="w-8 h-8"
									v-if="state.loading"
								/>
								<img
									class="w-8 h-8"
									v-else
									:src="token.logo_uri"
								/>
								<h3 class="text-sm">{{ token.name }}</h3>
							</div>
							<div class="flex mt-4 items-center justify-center space-x-3">
								<Address :address="token.contract"> </Address>
							</div>
						</div>
						<FormInput
							class="mt-6"
							:placeholder="$t('Amount')"
							size="xl"
							v-model="form.amount"
							:error="form.errors.amount"
						>
							<template #trail>
								<div
									class="px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md"
								>
									<ChainInfo :chain-id="token.chainId" />
								</div>
							</template>
						</FormInput>
						<div class="flex sm:space-x-3 justify-end mt-6">
							<ChainSelect
								v-model="form.chain"
								class="w-full max-w-xs"
							>
								{{ $t("Destination Chain") }}
							</ChainSelect>
							<SwitchChainButton
								@switched="state.sendFrom"
								:to-chain="token.chainId"
							>
								<PrimaryButton
									class="rounded-md"
									primary
									@click.prevent="state.sendFrom"
								>
									{{ $t("Send") }}
								</PrimaryButton>
							</SwitchChainButton>
						</div>
						<p
							v-if="form.errors.chain"
							class="text-red text-xs font-semibold"
						>
							{{ form.errors.chain }}
						</p>
					</div>
					<div
						class="p-6 border rounded-md mt-8 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
					>
						<TxStatus
							v-if="state.called === 'sendFrom'"
							:state="state"
						/>
						<h3 v-else>Balance: {{ state.balance }} MKX</h3>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
