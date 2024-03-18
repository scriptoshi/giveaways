<script setup>
import { useFocus } from "@vueuse/core";
import { onMounted, ref } from "vue";
defineProps({
    modelValue: String,
    error: String,
    info: String,
    placeholder:String
});
defineEmits(["update:modelValue"]);
const input = ref(null);
const container = ref(null)
onMounted(() => {
    if (container.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});
defineExpose({ focus: () => input.value.focus() });
const { focused } = useFocus(input)
</script>
<template>
    <div ref="container">
        <div class="relative">
            <div
                v-if="$slots.default"
                class="flex absolute inset-y-0 group left-0 items-center pl-3 pointer-events-none"
            >
                <slot :focused="focused" />
            </div>
            <input
                type="text"
                :ref="input"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :class="[
                    !!error
                        ? 'bg-red-50 border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 dark:bg-gray-900 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500'
                        : 'bg-gray-50 border-gray-300 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500  dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-500',
                    $slots.default?'pl-10' : 'pl-3',
                ]"
                class="border text-base rounded-lg block w-full p-3.5"
                :placeholder="placeholder"
            />
        </div>
        <p
            v-if="!!error"
            class=" mt-2 text-sm text-red-600 dark:text-red-500"
        >
            {{ error }}
        </p>
        <p
            v-else-if="$slots.info"
            class="mt-2 text-sm text-gray-600 dark:text-gray-400"
        >
            <slot name="info" />
        </p>
        <p
            v-else-if="info"
            class="mt-2  text-sm text-gray-600 dark:text-gray-400"
        >
            {{ info }}
        </p>
    </div>
</template>