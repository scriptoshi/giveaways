<script setup>
import { computed, ref } from "vue";

import { HiSolidX } from "oh-vue-icons/icons";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import SelectToken from "@/Components/SelectToken.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import { useBillions } from "@/hooks/useBillions";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
import { isAddress } from "@/Wagmi/utils/utils";

const props = defineProps({
    modelValue: String,
    logo_uri: String,
    upload_logo: Boolean,
    loading: Boolean,
    error: String,
    logo_error: String,
    name: String,
    symbol: String,
    decimals: String,
    balance: String,
});
const emit = defineEmits(["update:modelValue", "update:logo_uri", "update:upload_logo"]);
const contract = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});
const logoUri = computed({
    get: () => props.logo_uri,
    set: (val) => emit("update:logo_uri", val),
});
const uploadLogo = computed({
    get: () => props.upload_logo,
    set: (val) => emit("update:upload_logo", val),
});
const token = ref(null);
</script>
<template>
    <div class="w-full border border-gray-200 p-5 dark:border-gray-600">
        <div class="w-full mb-3">
            <SelectToken class="max-w-lg w-full" v-model="token" />
            <div v-if="!!token">
                <Switch v-if="isAddress(contract)" v-model="showDetails">
                    {{ showDetails ? $t("Hide Details") : $t("View Details") }}</Switch>
                <slot v-else></slot>
            </div>
        </div>
        <CollapseTransition>
            <div v-show="!token?.contract" class="gap-3 mb-5  w-full grid md:grid-cols-3">
                <FormInput :label="$t('Token address')" v-model="contract" :error="error" class="md:col-span-2">
                    <template #trail>
                        <Loading v-if="loading" />
                        <a v-else href="#" @click.prevent="contract = null" class="p-2 group">
                            <VueIcon :icon="HiSolidX" class="w-4 h-5 group-hover:text-red" />
                        </a>
                    </template>
                </FormInput>
                <div class="flex items-end">
                    <Switch v-if="isAddress(contract)" v-model="showDetails">
                        {{ showDetails ? $t("Hide Details") : $t("View Details") }}</Switch>
                    <slot v-else></slot>
                </div>
            </div>
        </CollapseTransition>
        <CollapseTransition>
            <div v-show="showDetails" class="p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex mb-3 items-center">
                    <h3 class="text-sm mr-3 sm:mr-6">{{ $t("Token Information") }}</h3>
                    <Loading v-if="loading" />
                </div>
                <CollapseTransition>
                    <div v-show="!!name && !!symbol" class="gap-3 w-full grid md:grid-cols-3">
                        <FormInput :label="$t('Token Name')" :model-value="name" class="md:col-span-2" disabled />
                        <FormInput :label="$t('Token Symbol')" :model-value="symbol" disabled />
                        <FormInput :label="$t('Token Decimals')" :model-value="decimals" disabled />
                        <FormInput :label="$t('Token Supply') + ` (${useBillions(totalSupply ?? 0)})`"
                            :model-value="totalSupply" disabled><template #trail>{{ symbol }}</template>
                        </FormInput>
                        <FormInput :label="$t('Your Balance') + ` (${useBillions(balance ?? 0)})`"
                            :model-value="balance" class="md:col-span-2" disabled><template #trail>{{ symbol
                                }}</template>
                        </FormInput>
                    </div>
                </CollapseTransition>
            </div>
        </CollapseTransition>
        <div class="gap-x-3 mx-auto grid md:grid-cols-2">
            <FormInput v-model="logoUri" :disabled="upload_logo" placeholder="https://" :error="logo_error"
                :help="$t('Supports png, jpeg or svg')">
                <template #label>
                    <div class="flex">
                        <span class="mr-3">{{ $t("Token Logo Url") }}</span>
                        <label class="inline-flex items-center space-x-2">
                            <input v-model="uploadLogo"
                                class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
                                type="checkbox" />
                            <span>{{ $t("Upload to server") }}</span>
                        </label>
                    </div>
                </template>
            </FormInput>
            <template v-if="uploadLogo">
                <LogoInput v-if="$page.props.config.s3" v-model="logoUri" :errors="logo_error" auto />
                <LogoInputLocal v-else v-model="logoUri" :errors="logo_error" />
            </template>
            <img v-else class="w-12 h-12 my-auto rounded-full b-0" :src="logoUri ?? fakeLogo" />
        </div>
    </div>
</template>
