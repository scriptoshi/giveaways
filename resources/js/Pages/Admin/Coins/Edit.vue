<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { useChains } from "use-wagmi";
import { useI18n } from "vue-i18n";

import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import FormInput from "@/Components/FormInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import ArrowLeftIcon from "@/Feather/ArrowLeftIcon";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const { t: $t } = useI18n();
defineProps({
    title: { required: false, type: String },
});
const chains = useChains();
const form = useForm(usePage().props.coin);
form.chain = chains.value?.find?.((c) => c.id.toString() === form.chainId.toString());
const save = () => form.put(window.route("admin.coins.update", form.id));
</script>
<template>
    <AdminLayout>

        <Head :title="title ?? `Add New Coin`" />
        <main class="h-full">
            <div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
                <div class="flex flex-col gap-4 h-full">
                    <div class="lg:flex items-center justify-between mb-4 gap-3">
                        <div class="mb-4 lg:mb-0">
                            <h3 class="h3">Edit Accepted Coins</h3>
                            <p>Coin is used to make purchases on the site</p>
                        </div>
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                            <Link :href="route('admin.coins.index')"
                                class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            <ArrowLeftIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
                            {{ $t("Go Back to Coin list") }}</Link>
                        </div>
                    </div>
                    <div class="card h-full border-0 card-border">
                        <div class="card-body px-0 card-gutterless h-full">
                            <form @submit.prevent="save" class="container lg:w-4/5">
                                <div class="grid md:grid-cols-2 mt-6 gap-x-4">
                                    <FormInput v-model="form.name" :error="form.errors.name"
                                        :label="$t('Name of the Exchange')" :placeholder="$t('Eg sushiswap')" />
                                    <div>
                                        <label for="name"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Network
                                            chainId</label>
                                        <ChainSelect v-model="form.chain">{{
                                            $t("Select a Network")
                                            }}</ChainSelect>
                                        <span v-if="form.errors.chain" class="text-red">{{ form.errors.chain }}</span>
                                    </div>
                                </div>
                                <div class="grid md:grid-cols-3 mt-6 gap-x-4">
                                    <FormInput v-model="form.contract" :error="form.errors.contract" class="col-span-2"
                                        :label="$t('Token Contract address')"
                                        :help="$t('Use Zero Address for Native Currency')" />
                                </div>
                                <div class="grid md:grid-cols-7 mt-6 gap-x-4">
                                    <FormInput v-model="form.symbol" class="col-span-2" :error="form.errors.symbol"
                                        :label="$t('Coin Symbol')" />
                                    <FormInput v-model="form.decimals" class="col-span-2" :error="form.errors.decimals"
                                        :label="$t('Url to Analytics interface')" />
                                    <FormInput v-model="form.logo_uri" class="col-span-3" :error="form.errors.logo_uri"
                                        :label="$t('Coin Logo')" :placeholder="$t('You can copy from coinmarketcap')" />
                                </div>
                                <div class="pt-12">
                                    <div class="flex justify-end">
                                        <Link as="button" :href="route('admin.coins.index')" type="button"
                                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                        {{ $t("Cancel") }}
                                        </Link>
                                        <LoadingButton type="submit" :busy="form.processing"
                                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            {{ $t("Update") }} Coin
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
