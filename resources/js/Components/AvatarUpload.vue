<script setup>
import { computed, ref } from "vue";

import { PencilIcon } from "@heroicons/vue/24/solid";
import { useElementHover } from "@vueuse/core";
import axios from "axios";

import Loading from "@/Components/Loading.vue";

const deleteUrl = ref(null);
const logoError = ref(null);
const busy = ref(false);
const props = defineProps({
    modelValue: {
        type: String,
        default: "http://www.gravatar.com/avatar/?d=mp",
    },
    isBusy:Boolean
});
const emit = defineEmits(["update:modelValue"]);
const spacesPath = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit("update:modelValue", value);
    },
});
const uploadToSpaces = async (file) => {
    if (busy.value) return;
    if (file.size > 512000) {
        return (logoError.value = "Max 512Kb");
    }
    busy.value = true;
    if (deleteUrl.value) {
        await axios.delete(deleteUrl.value);
    }
    const fileExtension = file.type.split("/").pop();
    const {
        data: { url, link, remove },
    } = await axios.post(window.route("s3.sign"), {
        ext: fileExtension,
        name: file.name,
        type: file.type,
    });
    await axios.put(url, file, {
        headers: {
            "x-amz-acl": "public-read",
            "Content-Type": file.type,
        },
        crossdomain: true,
    });
    spacesPath.value = link;
    deleteUrl.value = remove;
    busy.value = false;
};
const avt = ref(null);
const hover = useElementHover(avt);
</script>
<template>
    <div ref="avt" class="avatar group relative w-16 h-16 md:h-20 md:w-20 rounded-full mr-3 bg-gradient-to-r from-sky-400 to-blue-600 p-0.5">
        <input
            tabindex="-1"
            type="file"
            @input="uploadToSpaces($event.target.files[0])"
            class="absolute cursor-pointer inset-0 h-full z-5 w-full opacity-0"
        />
        <img
            class="rounded-full object-cover"
            :src="spacesPath"
            v-if="spacesPath"
            alt="avatar"
        >
        <div class="absolute top-0 right-0 m-1 h-4 w-4 rounded-full border-2 border-white bg-emerald-500 dark:border-gray-700 dark:bg-emerald-500"></div>
        <div v-if="busy || isBusy"  class="absolute cursor-pointer rounded-full top-0 left-0 w-full flex flex-col justify-center items-center bg-gray-600/30 h-full opacity-100">
            <Loading class="self-center w-8 h-8 !mr-0" />
        </div>
        <div v-if="hover" class="absolute h-full cursor-pointer rounded-full top-0 left-0 w-full flex flex-col justify-center items-center bg-gray-600/30 opacity-100 transition-colors duration-300">
            <PencilIcon class="w-6 h-6 self-center text-white" />
        </div>
    </div>
    <span
        v-if="logoError"
        class="text-red-500 my-3"
    >{{logoError}}</span>
</template>