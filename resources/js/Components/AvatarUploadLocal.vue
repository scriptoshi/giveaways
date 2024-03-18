<script setup>
import {computed, reactive, ref, watch} from "vue";

import {usePage} from "@inertiajs/vue3";
import {useElementHover} from "@vueuse/core";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import vueFilePond from "vue-filepond";

import Loading from "./Loading.vue";
const props = defineProps({
	modelValue: String,
	preview: String,
	busy: Boolean,
});
const emit = defineEmits(["update:modelValue"]);
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);
const server = reactive({
	url: "/filepond/api",
	process: "/process",
	revert: "/process",
	patch: "?patch=",
	headers: {
		"X-CSRF-TOKEN": computed(() => usePage().props.csrf_token),
	},
});
const uploadError = ref(null);
const upload = ref(false);
const handleProcessFile = (error, file) => {
	if (error) uploadError.value = error;
	emit("update:modelValue", file?.serverId);
};
const avt = ref(null);
const hover = useElementHover(avt);
const initFile = (f) => (upload.value = true);
watch(
	() => props.busy,
	(busy) => {
		if (!busy) upload.value = false;
	},
);
</script>
<template>
	<div
		ref="avt"
		class="avatar group relative w-16 h-16 md:h-20 md:w-20 rounded-full mr-3 bg-gradient-to-r from-sky-400 to-blue-600 p-0.5"
	>
		<img
			class="rounded-full object-cover"
			:src="preview"
			v-if="preview"
			alt="avatar"
		/>
		<div
			v-if="busy"
			class="absolute cursor-pointer rounded-full top-0 left-0 w-full flex flex-col justify-center items-center bg-gray-600/30 h-full opacity-100"
		>
			<Loading class="self-center w-8 h-8 !mr-0" />
		</div>
		<div
			v-show="hover || upload"
			class="absolute h-full cursor-pointer rounded-full top-0 left-0 w-full flex flex-col justify-center items-center bg-gray-600/30 opacity-100 transition-colors duration-300"
		>
			<FilePond
				name="filepond"
				ref="pond"
				class-name="logo filepond fp-bordered label-icon w-16 h-16 md:h-20 md:w-20 "
				:allow-multiple="false"
				:allowImagePreview="true"
				stylePanelAspectRatio="1:1"
				stylePanelLayout="compact circle"
				labelIdle="<svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewbox='0 0 24 24' stroke='currentColor'>
                                      <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'></path>
                                    </svg>"
				accepted-file-types="image/jpeg, image/png"
				:server="server"
				@processfile="handleProcessFile"
				@initfile="initFile"
				@removefile="upload = false"
			/>
		</div>
	</div>
</template>
<style>
.filepond.filepond--root[data-style-panel-layout~="circle"] .filepond--file [data-align*="right"] {
	right: calc(50% - 1em);
	top: calc(50% - 1em);
}
.filepond--root {
	margin-bottom: 0px !important;
}
</style>
