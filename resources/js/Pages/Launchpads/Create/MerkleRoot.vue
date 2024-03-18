<script setup>
import { computed, watch } from "vue";

import { ArrowDownIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import isArray from "lodash/isArray";
import MerkleTree from "merkletreejs";
import { keccak256 } from "viem";

import { isAddress } from "@/Wagmi/utils/utils";

const emit = defineEmits(["update:participants", "update:merkleRoot"]);
const props = defineProps({
    participants: Array,
    merkleRoot: String,
});
const whitelist = computed({
    set(val) {
        if (isArray(val)) emit("update:participants", val);
        const splt = val.replace(/['"]+/g, "").split(/[\n,\s+]+/);
        emit("update:participants", splt);
    },
    get() {
        return props.participants.join("\n");
    },
});

const clear = () => (whitelist.value = []);
const sort = () => {
    whitelist.value = props.participants.slice().sort((a, b) => a?.toString?.().localeCompare?.(b));
};
const validate = () => {
    const unique = [...new Set(props.participants)];
    whitelist.value = unique.map((a) => isAddress(a));
    sort();
};
const merkleRoot = computed(() => {
    if (props.participants.length < 1) return null;
    const leaves = props.participants
        .filter((a) => !!a)
        .map((a) => isAddress(a))
        .map((w) => keccak256(w));
    const merkleTree = new MerkleTree(leaves, keccak256, { sortPairs: true });
    return merkleTree.getHexRoot();
});
watch(merkleRoot, (merkleRoot) => {
    emit("update:merkleRoot", merkleRoot);
});
</script>
<template>
    <div class="w-full">
        <h3 class="text-sm">{{ $t("Private Sale Participants") }}</h3>
        <div class="mt-5 mb-1 flex overflow-x-auto">
            <div
                class="w-full rounded-sm rounded-tl-none border border-gray-300 transition-colors duration-200 focus-within:!border-gray-200 hover:border-gray-400 dark:border-gray-600 dark:focus-within:!border-emerald-500 dark:hover:border-gray-500 dark:bg-gray-900">
                <label class="block">
                    <textarea rows="5" v-model="whitelist"
                        placeholder="Paste address list seperated by space comma or new line"
                        class="form-textarea w-full resize-none bg-transparent p-3 pb-0 placeholder:text-gray-400/70"></textarea>
                </label>
                <div class="flex justify-between p-2.5">
                    <div class="flex items-end  space-x-1">
                        <button @click.prevent="sort"
                            class="btn -ml-1 h-8 w-8 rounded-full p-0 hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                            <ArrowDownIcon class="h-5 w-5" />
                        </button>
                        <button @click.prevent="clear"
                            class="btn h-8 w-8 rounded-full p-0 hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                        <h3 class="font-mono text-base">
                            {{ participants.filter((p) => isAddress(p)).length }}
                        </h3>
                    </div>
                    <button @click.prevent="validate()"
                        class="btn rounded-sm bg-emerald-500 px-4 text-xs+ font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-emerald-500 dark:hover:bg-emerald-500-focus dark:focus:bg-emerald-500-focus dark:active:bg-emerald-500/90">
                        {{ $t("Validate") }}
                    </button>
                </div>
            </div>
        </div>
        <p class="text-red" v-if="form.errors.participants">
            {{ form.errors.participants }}
        </p>
        <p v-else>{{ $t("Please Note You will not be able to add Participants later") }}</p>
    </div>
</template>
