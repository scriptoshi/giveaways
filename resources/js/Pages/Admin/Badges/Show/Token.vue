<script setup>
import { useAddress } from "@/Web3Modal";

const props = defineProps({
    appName:String,
    token:Object
});
const [, addLink] = useAddress(props.token.contract)
</script>
<template>
    <li class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600">
        <a
            :href="addLink"
            target="_blank"
            class="flex space-x-1 text-emerald-500 hover:text-emerald-600 text-xs md:text-sm"
        >{{token.contract}}</a>
        <h2 v-if="token.external" class="text-gray-900 dark:text-white text-xs md:text-sm">External</h2>
        <h2 class="text-emerald-700 dark:text-emerald-300 text-xs md:text-sm">{{appName}} Token</h2>
    </li>
</template>