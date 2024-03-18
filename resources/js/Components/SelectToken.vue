<script setup>
import { computed } from "vue";

import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from "@headlessui/vue";
import { CheckIcon, ChevronDownIcon, XCircleIcon } from "@heroicons/vue/24/solid";
import { invoke, until } from "@vueuse/core";
import { useAccount } from "use-wagmi";

import { shortenAddress } from "@/Wagmi/utils/utils";


const { chainId } = useAccount();

const props = defineProps({
    modelValue: Object,
    label: { type: String, default: "Use previously deployed token" },
});
const emit = defineEmits(["update:modelValue"]);
invoke(async () => {
    await until(chainId).not.toBeNull();
    if (!props.modelValue?.contract) emit("update:modelValue", null);
});

const selected = computed({
    set(value) {
        emit("update:modelValue", value);
    },
    get() {
        return props.modelValue;
    },
});
</script>

<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <Listbox as="div" v-if="$page.props.tokens && $page.props.tokens?.length > 0" v-model="selected">
        <ListboxLabel class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
        </ListboxLabel>
        <div class="mt-2 relative">
            <ListboxButton
                class="relative w-full py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-sm shadow-sm pl-3 pr-10 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <span class="flex items-center">
                    <img :src="selected?.logo_uri" v-if="selected?.logo_uri" alt=""
                        class="flex-shrink-0 h-6 w-6 rounded-full" />
                    <span class="ml-3 block truncate">{{ selected?.name ?? "Select A Token" }}
                        {{ selected?.contract && shortenAddress(selected?.contract, 8) }}</span>
                </span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <ChevronDownIcon class="h-5 w-5 text-gray-400 dark:text-gray-600" aria-hidden="true" />
                </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <ListboxOptions
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-sm py-1 text-base ring-1 ring-gray-300 dark:ring-gray-600 ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                    <ListboxOption as="template" :value="null" v-slot="{ active, selected }">
                        <li :class="[
        active
            ? 'text-white bg-emerald-600 dark:bg-emerald-400'
            : 'text-gray-900 dark:text-gray-200',
        'cursor-default select-none relative py-2 pl-8 pr-4',
    ]">
                            <div class="flex items-center">
                                <XCircleIcon class="flex-shrink-0 h-6 w-6 rounded-full" />
                                <span :class="[
        selected ? 'font-semibold' : 'font-normal',
        'ml-3 block truncate text-base',
    ]">
                                    {{ $t("Enter A Token Address") }}
                                </span>
                            </div>
                            <span v-if="selected" :class="[
        active
            ? 'text-white'
            : 'text-emerald-600 dark:text-emerald-400',
        'absolute inset-y-0 left-0 flex items-center pl-1.5',
    ]">
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                    <ListboxOption as="template" v-for="token in $page.props.tokens" :key="token.id" :value="token"
                        v-slot="{ active, selected }">
                        <li :class="[
        active
            ? 'text-white bg-emerald-600 dark:bg-emerald-400'
            : 'text-gray-900 dark:text-gray-200',
        'cursor-default select-none relative py-2 pl-8 pr-4',
    ]">
                            <div class="flex items-center">
                                <img :src="token.logo_uri" v-if="token.logo_uri" alt="token.logo"
                                    class="flex-shrink-0 h-6 w-6 rounded-full" />
                                <span :class="[
        selected ? 'font-semibold' : 'font-normal',
        'ml-3 block truncate',
    ]">
                                    {{ token?.name }} ( {{ token?.symbol }} ) |
                                    {{ shortenAddress(token?.contract, 8) }}
                                </span>
                            </div>
                            <span v-if="selected" :class="[
        active
            ? 'text-white'
            : 'text-emerald-600 dark:text-emerald-400',
        'absolute inset-y-0 left-0 flex items-center pl-1.5',
    ]">
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
