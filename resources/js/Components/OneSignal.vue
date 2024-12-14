<script setup>
import { useOneSignalButton } from "@/hooks/useOneSignalButton";
import Loading from "@/Components/Loading.vue";
import { BellIcon } from "@heroicons/vue/24/outline";
import { BellIcon as FilledBellIcon } from "@heroicons/vue/24/solid";
const { state, toggle } = useOneSignalButton();
</script>
<template>
    <button
        v-if="state !=='not-supported'"
        v-tippy="'Get Reminders'"
        @click.prevent="toggle()"
        :disabled="state ==='loading'"
        class="btn h-9 w-9 rounded-full p-0 hover:bg-slate-300/20 hover:text-success focus:bg-slate-300/20 focus:text-success active:bg-slate-300/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
    >
        <Loading
            v-if="state ==='loading'"
            class="h-5.5 w-5.5"
        />
        <template>
            <FilledBellIcon
                v-if="state==='subscribed'"
                class="h-5.5 w-5.5 text-success"
            />
            <BellIcon
                v-if="state==='not-subscribed'"
                class="h-5.5 w-5.5"
            />
        </template>

    </button>
</template>