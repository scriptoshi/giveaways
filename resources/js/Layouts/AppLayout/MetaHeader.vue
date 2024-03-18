<script setup>
import { computed } from "vue";

import { Head, usePage } from "@inertiajs/vue3";
const meta = computed(() => usePage().props.meta ?? []);
console.log(meta.value);
const site = computed(() => usePage().props.config?.appName);
</script>
<template>
    <Head :title="meta['title']??site">
        <meta
            v-for="name in Object.keys(meta)"
            :key="name"
            :name="name"
            :content="meta[name]"
        />
    </Head>
</template>