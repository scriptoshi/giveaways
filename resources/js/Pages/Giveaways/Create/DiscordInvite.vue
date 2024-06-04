<script setup>
import {computed, ref} from "vue";

import {usePage} from "@inertiajs/vue3";
import {debouncedWatch} from "@vueuse/core";
import axios from "axios";
import {HiSolidCheck} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import LoadingIcon from "@/Components/Loading.vue";
import SocialIcon from "@/Components/SocialIcon.vue";
import VueIcon from "@/Components/VueIcon.vue";
const props = defineProps({
	modelValue: String,
	error: String,
});
const emit = defineEmits(["update:modelValue"]);
const invite = computed({
	get: () => props.modelValue,
	set: (v) => emit("update:modelValue", v),
});
const loading = ref(false);
const verified = ref(true);
const ierror = ref();
const AuthCheck = computed(() => usePage().props.AuthCheck);
const checkInvite = async (invite) => {
	if (!AuthCheck.value) return;
	ierror.value = null;
	verified.value = true;
	loading.value = true;
	const {data} = await axios.post(window.route("connections.check.invite"), {
		invite,
		redirect: window.location.href,
	});
	ierror.value = data.error;
	verified.value = data.verified;
	loading.value = false;
};
debouncedWatch(
	[invite, AuthCheck],
	([invite, authcheck]) => {
		ierror.value = null;
		if (authcheck && invite && invite !== "" && invite.includes("discord.gg/"))
			checkInvite(invite);
	},
	{debounce: 800, immediate: true},
);
</script>
<template>
	<FormInput
		v-model="invite"
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
				icon="discord"
			/>
		</template>
		<template
			v-if="!verified"
			#label
		>
			<a
				class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200"
				:href="route('connections.discord.add.bot')"
			>
				[ ADD SLEEP BOT]</a
			>
		</template>
	</FormInput>
</template>
