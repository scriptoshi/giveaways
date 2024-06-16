<script setup>
import {HiRefresh, HiSolidCheck, RiTelegramLine} from "oh-vue-icons/icons";
import {telegramLoginTemp as TelegramButton} from "vue3-telegram-login";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import VueIcon from "@/Components/VueIcon.vue";

defineProps({
	giveaway: Object,
	quest: Object,
	verifying: Boolean,
	connected: Boolean,
});
defineEmits(["verify"]);
</script>
<template>
	<div
		class="flex justify-between items-center p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex items-center">
			<VueIcon
				class="w-10 h-10 mr-4 text-sky-500"
				:icon="RiTelegramLine"
			/>
			<h3
				:class="{'text-emerald-500': quest.complete}"
				class="text-sm"
			>
				Join @{{ quest.name }} Telegram
			</h3>
		</div>
		<div class="flex items-center space-x-3">
			<template v-if="connected">
				<ConnectWalletLink
					:disabled="verifying"
					v-tippy="$t('Verify task')"
					@clicked="$emit('verify')"
					class="flex items-center text-gray-500 hover:text-gray-700 justify-center -mr-1.5 h-8 w-8 rounded-full p-0 bg-gray-300/20 hover:border hover:border-gray-500/50 focus:bg-gray-400/20 active:border-gray-500/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
					href="#"
				>
					<VueIcon
						:class="{'animate-spin': verifying}"
						:icon="HiRefresh"
					/>
				</ConnectWalletLink>
				<ExternalLink :href="quest.username">
					<PrimaryButton
						v-if="quest.complete"
						secondary
						class="!py-1 !px-2 !text-emerald-500"
					>
						<VueIcon :icon="HiSolidCheck"></VueIcon>
						{{ $t("Done") }}
					</PrimaryButton>
					<PrimaryButton
						v-else
						secondary
						class="!py-1 !px-2"
						>{{ $t("[ Join Tg group ]") }}
					</PrimaryButton>
				</ExternalLink></template
			>
			<TelegramButton
				mode="redirect"
				v-else
				telegram-login="GasQuestBot"
				requestAccess="write"
				@loaded="loading = false"
				size="medium"
				:redirect-url="
					route('connections.callback', {provider: 'telegram', uuid: giveaway.uuid})
				"
			/>
		</div>
	</div>
</template>
