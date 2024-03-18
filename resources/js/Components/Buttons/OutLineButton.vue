
<script setup>
import { computed } from "vue";

import { Link } from "@inertiajs/vue3";

const props = defineProps({
    primary: Boolean,
    default: Boolean,
    secondary: Boolean,
    info: Boolean,
    warning: Boolean,
    error: Boolean,
    success: Boolean,
    rounded: Boolean,
    link: Boolean,
});
const colors = computed(() => {
    if (props.default)
        return "border-slate-300  text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90";
    if (props.primary)
        return "border-emerald-500  text-emerald-500 hover:bg-emerald-500 hover:text-white focus:bg-emerald-500 focus:text-white active:bg-emerald-500/90";
    if (props.secondary)
        return "border-secondary  text-secondary hover:bg-secondary hover:text-white focus:bg-secondary focus:text-white active:bg-secondary/90 dark:text-secondary-light dark:hover:bg-secondary dark:hover:text-white dark:focus:bg-secondary dark:focus:text-white dark:active:bg-secondary/90";
    if (props.success)
        return "border-success  text-success hover:bg-success hover:text-white focus:bg-success focus:text-white active:bg-success/90";
    if (props.warning)
        return "border-warning   text-warning hover:bg-warning hover:text-white focus:bg-warning focus:text-white active:bg-warning/90";
    if (props.error)
        return "border-error text-error hover:bg-error hover:text-white focus:bg-error focus:text-white active:bg-error/90";
    return "border-sky-500  text-sky-500 hover:bg-sky-500 hover:text-white focus:bg-sky-500 focus:text-white active:bg-sky-500/90 dark:text-sky-500 dark:hover:bg-sky-600 dark:hover:text-white dark:focus:bg-sky-600 dark:focus:text-white dark:active:bg-sky-500/90";
});
</script>
<template>
    <Link
        v-if="link"
        :class="[rounded?'rounded-full':'', colors]"
        class="btn border font-semibold disabled:opacity-70 disabled:pointer-events-none"
    >
    <slot />
    </Link>
    <button
        v-else
        :class="[rounded?'rounded-full':'', colors]"
        class="btn border font-semibold disabled:opacity-70 disabled:pointer-events-none"
    >
        <slot />
    </button>
</template>