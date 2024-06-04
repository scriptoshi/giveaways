<script setup>
import {ref} from "vue";

import {BiTelegram} from "oh-vue-icons/icons";
import {telegramLoginTemp as TelegramButton} from "vue3-telegram-login";

import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import providers from "@/Pages/Projects/Show/Connect/providers";

defineProps({
	airdrop: Object,
	action: Object,
});
const loading = ref(true);
</script>
<template>
	<button
		type="button"
		class="absolute top-0 w-full h-full left-0 backdrop-blur-sm bg-white/30 dark:bg-gray-900/30 rounded-md"
	>
		<div class="flex w-full justify-center space-x-1 flex-row mb-3">
			<VueIcon
				:icon="BiTelegram"
				name="bi-telegram"
				class="text-sky-500 w-12 h-12 group-hover:text-sky-600 dark:group-hover:text-sky-400"
			/>
		</div>
		<h2 class="text-gray-800 dark:text-gray-100 font-semibold text-center">
			{{ $t("Connect your Telegram account") }}
		</h2>
		<h2 class="text-gray-800 dark:text-gray-100 font-semibold text-center">
			{{ $t("to claim rewards") }}
		</h2>
		<div class="flex justify-center mt-4">
			<Loading
				v-if="loading"
				class="w-12 h-12 !text-emerald-500"
			/>
			<TelegramButton
				mode="redirect"
				telegram-login="MakxAidropBot"
				requestAccess="write"
				@loaded="loading = false"
				:redirect-url="
					route('connections.callback', {provider: providers.TELEGRAM, action: action.id})
				"
			/>
		</div>
	</button>
</template>
