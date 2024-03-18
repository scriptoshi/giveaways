<script setup>
import {computed, watch} from "vue";

import {Head, Link, useForm} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";
import {useI18n} from "vue-i18n";

import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import FormInput from "@/Components/FormInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import ArrowLeftIcon from "@/Feather/ArrowLeftIcon";
import RefreshCcwIcon from "@/Feather/RefreshCcwIcon";
import {useToken} from "@/hooks/useUpdateCoins";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {isAddress} from "@/Wagmi/utils/utils";
const {t: $t} = useI18n();
defineProps({
	title: {required: false, type: String},
	chains: {type: Object, required: true},
});
const {chain} = useAccount();
const form = useForm({
	chain: chain.value,
	name: "Tother",
	logo_uri: "",
	symbol: "",
	contract: "",
	decimals: "",
});
const address = computed(() => form.contract);
const chainId = computed(() => form.chain?.id);
const {name, symbol, decimals, error, loading, updateToken} = useToken(address, chainId);
watch(
	[name, symbol, decimals, error],
	([name, symbol, decimals, error]) => {
		form.clearErrors();
		if (error) form.setError("contract", error);
		form.name = name;
		form.decimals = decimals;
		form.symbol = symbol;
	},
	{immediate: true},
);
const {t} = useI18n();
const save = () => {
	form.clearErrors();
	if (!form.logo_uri) form.setError("logo_uri", t("Please provide a logo url for the token."));
	if (!form.contract) form.setError("contract", t("Please Enter contract address token."));
	if (!isAddress(form.contract))
		form.setError("contract", t("The Contract must be a valid Address."));
	if (!form.chain?.id) form.setError("chain", t("You must select a  chain"));
	if (form.hasErrors) return;
	form.post(window.route("admin.coins.store"));
};
</script>
<template>
	<Head :title="title ?? `New Token`" />
	<AdminLayout>
		<Head :title="title ?? `Add New Token`" />
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Add New Accepted Token</h3>
							<p>Only ERC20 tokens should be added to avoid issues</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.coins.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
							>
								<ArrowLeftIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to Coin list") }}</Link
							>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
								@submit.prevent="save"
								class="container lg:w-4/5"
							>
								<div class="grid md:grid-cols-2 mt-6 gap-x-4">
									<div>
										<label
											for="name"
											class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
											>Select A Network (chain)</label
										>
										<ChainSelect v-model="form.chain">{{
											$t("Select a Network")
										}}</ChainSelect>
										<span
											v-if="form.errors.chain"
											class="text-red"
											>{{ form.errors.chain }}</span
										>
									</div>
									<FormInput
										v-model="form.contract"
										:error="form.errors.contract || error"
										:disabled="!form.chain"
									>
										<template #label>
											{{ $t("Token Contract address") }}
											<RefreshCcwIcon
												v-tipp="$t('Reload Token Information')"
												@click="updateToken"
												:class="{'animate-spin': loading}"
												class="ml-2 w-4 h-4 inline-block cursor-pointer"
											/>
										</template>
									</FormInput>
								</div>
								<div class="grid md:grid-cols-7 mt-6 gap-x-4">
									<FormInput
										v-model="form.name"
										:error="form.errors.name"
										:label="$t('Name of the Token')"
										:placeholder="$t('Eg Tether')"
										class="col-span-3"
									/>
									<FormInput
										v-model="form.symbol"
										:label="$t('Token Symbol')"
										class="col-span-2"
										:disabled="!form.contract"
										:error="form.errors.symbol"
									/>

									<FormInput
										v-model="form.decimals"
										:disable="!form.contract"
										class="col-span-2"
										:error="form.errors.decimals"
										:label="$t('Token Decimals')"
									/>
								</div>
								<div class="grid md:grid-cols-3 mt-6 gap-x-4">
									<FormInput
										v-model="form.logo_uri"
										class="col-span-2"
										:error="form.errors.logo_uri"
										:label="$t('Token Logo')"
										:help="$t('You can copy from coinmarketcap')"
									/>
								</div>

								<div class="pt-12">
									<div class="flex justify-end">
										<Link
											as="button"
											:href="route('admin.coins.index')"
											type="button"
											class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
										>
											{{ $t("Cancel") }}
										</Link>
										<LoadingButton
											type="submit"
											:busy="form.processing"
											class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
										>
											{{ $t("Add New Token") }}
										</LoadingButton>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
