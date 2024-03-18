<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    href: String,
    as: String,
});
const isButton = computed(()=>props.as === 'button')
const isA = computed(()=>props.as === 'a')
</script>

<template>
    <div>
        <button v-if="isButton" type="submit" class="block w-full px-4 py-2 text-base leading-5 text-gray-700 dark:text-gray-300 text-left hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 transition">
            <slot />
        </button>

        <a v-else-if="isA" :href="href" class="block px-4 py-2 text-base leading-5 text-gray-700 dark:text-gray-300  hover:bg-gray-100 dark:hover:bg-gray-900  focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900  transition">
            <slot />
        </a>

        <Link v-else :href="href" class="block px-4 py-2 text-base leading-5 text-gray-700 dark:text-gray-300  hover:bg-gray-100  dark:hover:bg-gray-900  focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900  transition">
            <slot />
        </Link>
    </div>
</template>
