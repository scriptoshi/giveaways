<script setup>
import { computed } from "vue";

import { breakpointsTailwind, useBreakpoints } from "@vueuse/core";
import { useI18n } from "vue-i18n";

import Steps from "@/Components/Steps/Steps.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    current: Number,
    isFairLaunch: Boolean,
    isLpLaunch: Boolean,
    isPrivateSale: Boolean,
    title: String,
    type: String,
});
const breakpoints = useBreakpoints(breakpointsTailwind);
const { t } = useI18n();
const steps = [
    {
        title: t(t("Verify Token")),
        description: t("Verify your token deployment"),
        current: "launchpads.token",
    },
    {
        title: t("Launchpad"),
        description: t("Deploy Launchpad Contract"),
        current: "launchpads.project",
    },
    {
        title: t("Project Details"),
        description: t("Project information and links"),
        current: "launchpads.create",
    },
    {
        title: t("Project Team"),
        description: t("Add Team members and Admins"),
        current: "launchpads.deploy",
    },
];
const isLg = breakpoints.greater("sm");
const title = computed(() => {
    if (props.isLpLaunch) return t("Fair Liquidity Launch");
    if (props.isFairLaunch) return t("Fair Token Launch");
    if (props.isPrivateSale) return t("Create Private sale");
    return t("Token Presale");
});
</script>
<template>
    <AppLayout>
        <div class="h-full max-h-full">
            <div class="container-fluid sm:container">
                <div class="hero-content container">
                    <div class="intro  mb-8">
                        <h1 class="mt-4 mb-2 !text-emerald-700 dark:!text-emerald-400  font-normal text-4xl">
                            {{ title ?? $t('Create {Launchpad}', {
                                Launchpad: isFairLaunch
                                    ? 'Fairlaunch'
                                    : isPrivateSale
                                        ? 'Private sale'
                                        : 'Launchpad'
                            }) }}</h1>
                        <div v-if="isLpLaunch">
                            {{ ('A Liquidity Launch will distribute back all liquidity to the community.') }}
                        </div>
                        <div v-else-if="isFairLaunch">
                            {{ $t('A fairlaunch ensures full distrubution of tokens during sale.') }}
                        </div>
                        <div v-else-if="isPrivateSale">
                            {{ $t('A private sale distributes tokens to a closed community.') }}
                        </div>
                        <div v-else>
                            {{ $t('Create and deploy a Presale for your token in no time.') }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-row container mx-auto">
                    <div class="card border dark:border-gray-600 border-gray-300  px-0 pb-8">
                        <div class="grid mt-8 gap-y-8 p-4 md:grid-cols-9">
                            <div class="md:col-span-3 md:ml-3">
                                <Steps :items="steps" :current="current" :vertical="isLg" />
                            </div>
                            <slot />
                        </div>
                    </div>

                </div>
                <slot name="footnote" />
            </div>
        </div>
    </AppLayout>
</template>