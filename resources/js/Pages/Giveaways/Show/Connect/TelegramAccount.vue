<script setup>
import {telegramLoginTemp as TelegramButton} from "vue3-telegram-login";
defineProps({
	bot: String,
});
</script>
<template>
	<TelegramButton
		mode="redirect"
		:telegram-login="bot"
		requestAccess="write"
		@loaded="loading = false"
		:redirect-url="route('connections.callback', {provider: providers.TELEGRAM})"
	/>
</template>
