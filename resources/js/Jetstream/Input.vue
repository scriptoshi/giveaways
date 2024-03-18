<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-gray-300 border dark:border-gray-600 dark:bg-gray-900 focus:border-emerald-300 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200 dark:focus:ring-emerald-400 focus:ring-opacity-50 rounded-md shadow-sm transition duration-300 focus-visible:outline-none"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
