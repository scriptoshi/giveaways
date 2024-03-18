<script setup>
import { computed } from "vue";

import { Link as ForwardLink } from "@inertiajs/vue3";
const props = defineProps({
    href: { type: String, required: true },
});
const currentSubdomain = computed(() => {
    return window.location.host.split(".")[1]
        ? window.location.host.split(".")[0]
        : null;
});
const linkedSubdomain = computed(() => {
    const subdomain = props.href?.split(".")[1]
        ? props.href.split(".")[0]
        : null;
    return subdomain?.replace("https://", "");
});
const canVisitLink = computed(
    () => currentSubdomain.value === linkedSubdomain.value
);
const visitLink = () => {
    window.location.href = props.href;
};
</script>
<template>
    <ForwardLink v-if="canVisitLink" :href="href">
        <slot />
    </ForwardLink>
    <a v-else href="#" @click="visitLink">
        <slot />
    </a>
</template>
