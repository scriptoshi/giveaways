<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    link:Boolean,
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <Link v-if="link" as="button" :type="type" class="text-white dark:text-gray-300 dark:hover:text-gray-100 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-base px-4 py-1.5 dark:bg-gray-900 dark:hover:bg-gray-900 border border-transparent dark:hover:border-gray-600 dark:focus:ring-gray-800 dark:border-gray-700 transition-colors duration-300">
        <slot />
    </Link>
     <button v-else :type="type" class="text-white dark:text-gray-300 dark:hover:text-gray-100 bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-base px-4 py-1.5 dark:bg-gray-900 dark:hover:bg-gray-900 border border-transparent dark:hover:border-gray-600 dark:focus:ring-gray-800 dark:border-gray-700 transition-colors duration-300">
        <slot />
    </button>
</template>
