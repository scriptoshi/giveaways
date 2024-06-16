<script setup>
import {computed, ref} from "vue";

import {usePage} from "@inertiajs/vue3";
import {debouncedWatch} from "@vueuse/core";
import axios from "axios";
import {HiRefresh, HiSolidCheck} from "oh-vue-icons/icons";

import ExternalLink from "@/Components/ExternalLink.vue";
import FormInput from "@/Components/FormInput.vue";
import LoadingIcon from "@/Components/Loading.vue";
import SocialIcon from "@/Components/SocialIcon.vue";
import VueIcon from "@/Components/VueIcon.vue";
const props = defineProps({
	modelValue: String,
	error: String,
});
const emit = defineEmits(["update:modelValue"]);
const telegram = computed({
	get: () => props.modelValue,
	set: (v) => emit("update:modelValue", v),
});
const AuthCheck = computed(() => usePage().props.AuthCheck);
const loading = ref(false);
const verified = ref(true);
const ierror = ref();

const checkTelegram = async (telegram) => {
	if (!AuthCheck.value) return;
	ierror.value = null;
	verified.value = true;
	loading.value = true;
	const {data} = await axios.post(window.route("connections.check.telegram"), {
		telegram,
	});
	ierror.value = data.error;
	verified.value = data.verified;
	loading.value = false;
};
debouncedWatch(
	[telegram, AuthCheck],
	([telegram, authcheck]) => {
		ierror.value = null;
		if (
			authcheck &&
			telegram &&
			telegram !== "" &&
			(telegram.includes("t.me/") || telegram.includes("telegram.com/"))
		)
			checkTelegram(telegram);
	},
	{debounce: 800, immediate: true},
);
</script>
<template>
	<FormInput
		v-model="telegram"
		:error="ierror ?? error"
		class="mt-1"
	>
		<template #trail>
			<LoadingIcon
				class="w-5 h-5"
				v-if="loading"
			/>
			<VueIcon
				class="w-5 h-5 text-green-600"
				:icon="HiSolidCheck"
				v-else-if="verified"
			/>
			<SocialIcon
				v-else
				class="w-5 h-5"
				icon="telegram"
			/>
		</template>
		<template
			v-if="!verified"
			#label
		>
			<div class="flex justify-between items-center">
				<ExternalLink href="https://t.me/GasQuestBot"> [ ADD GAS BOT]</ExternalLink>
				<a
					v-if="telegram"
					class="text-sky-500 flex items-center dark:text-sky-400 hover:font-semibold hover:text-sky-700 dark:hover:text-sky-200"
					@click.prevent="checkTelegram(telegram)"
					href="#"
				>
					Check
					<VueIcon
						class="w-5 h-5 ml-0.5 inline-flex text-green-600"
						:icon="HiRefresh"
				/></a>
			</div>
		</template>
	</FormInput>
</template>
