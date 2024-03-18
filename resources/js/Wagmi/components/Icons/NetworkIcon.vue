
<script setup>
import { computed } from 'vue';

import { NETWORK_NAKED_ICON } from './network/svg';
const props = defineProps({
    chainId: [Number,String],
});
const url = computed(() => NETWORK_NAKED_ICON[props.chainId]);
</script>
<template>
    <img :src="url"/>
</template>