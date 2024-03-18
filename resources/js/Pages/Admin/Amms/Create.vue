<script setup>
import {Head, Link, useForm} from "@inertiajs/vue3";
import {useI18n} from "vue-i18n";

import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import FormInput from "@/Components/FormInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import ArrowLeftIcon from "@/Feather/ArrowLeftIcon";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const {t: $t} = useI18n();
defineProps({
	title: {required: false, type: String},
	chains: {type: Object, required: true},
});
const form = useForm({
	chain: "",
	name: "",
	url: "",
	info_url: "",
	token_url: "",
	pair_url: "",
	router: "",
	factory: "",
});
const save = () => form.post(window.route("admin.amms.store"));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Add New Dex`" />
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Create New Dex Exchange</h3>
							<p>Dex is used to add automatically add liquidity in launchpad</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.amms.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-sm border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
							>
								<ArrowLeftIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to Dex list") }}</Link
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
									<FormInput
										v-model="form.name"
										:error="form.errors.name"
										:label="$t('Name of the Exchange')"
										:placeholder="$t('Eg sushiswap')"
									/>
									<div>
										<label
											for="name"
											class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
											>Network Id</label
										>
										<ChainSelect v-model="form.chain">{{
											$t("Select a Network")
										}}</ChainSelect>
										<span
											v-if="form.errors.chain"
											class="text-red"
											>{{ "Please select a chain" }}</span
										>
									</div>
								</div>
								<div class="grid md:grid-cols-3 mt-6 gap-x-4">
									<FormInput
										v-model="form.router"
										:error="form.errors.router"
										class="col-span-2"
										:label="$t('Router address')"
										:help="$t('UniswapV2Router contract address')"
									/>
								</div>
								<div class="grid md:grid-cols-3 mt-6 gap-x-4">
									<FormInput
										v-model="form.factory"
										:error="form.errors.factory"
										class="col-span-2"
										:label="$t('Factory address')"
										:help="$t('UniswapV2Factory contract address')"
									/>
								</div>
								<div class="grid md:grid-cols-2 mt-6 gap-x-4">
									<FormInput
										v-model="form.url"
										:error="form.errors.url"
										:label="$t('Url to Swap interface')"
										:placeholder="$t('Eg https://app.uniswap.org')"
									/>
									<FormInput
										v-model="form.info_url"
										:error="form.errors.info_url"
										:label="$t('Url to Analytics interface')"
										:placeholder="$t('Eg https://info.uniswap.org/#/')"
									/>
								</div>
								<div class="grid md:grid-cols-2 mt-6 gap-x-4">
									<FormInput
										v-model="form.token_url"
										:error="form.errors.token_url"
										:label="$t('Analytics Token URL')"
										:help="
											$t(
												'Analytics Token URL without the token address, eg: https://info.uniswap.org/#/tokens/',
											)
										"
										:placeholder="$t('Eg https://info.uniswap.org/#/tokens/')"
									/>
									<FormInput
										v-model="form.pair_url"
										:error="form.errors.pair_url"
										:label="$t('Analytics Pair URL')"
										:help="
											$t(
												'Analytics Pair URL without the pair address, eg: https://info.uniswap.org/#/pools/',
											)
										"
										:placeholder="$t('Eg https://info.uniswap.org/#/pools/')"
									/>
								</div>
								<div class="pt-12">
									<div class="flex justify-end">
										<Link
											as="button"
											:href="route('admin.amms.index')"
											type="button"
											class="bg-white py-2 px-4 border border-gray-300 rounded-sm shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
										>
											{{ $t("Cancel") }}
										</Link>
										<LoadingButton
											type="submit"
											:busy="form.processing"
											class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
										>
											{{ $t("Save") }} Exchange
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
