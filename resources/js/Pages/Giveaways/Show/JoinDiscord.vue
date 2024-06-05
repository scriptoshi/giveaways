<script setup>
import {useForm} from "@inertiajs/vue3";
import {BiDiscord, HiRefresh, HiSolidCheck, RiDiscordLine} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
const form = useForm({});
const connect = () => {
	form.post(window.route("connections.connect", "discord"));
};
defineProps({
	giveaway: Object,
	quest: Object,
	connected: Boolean,
	verifying: Boolean,
});

defineEmits(["verify"]);
</script>
<template>
	<div
		class="flex justify-between items-center p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex items-center">
			<VueIcon
				class="w-10 h-10 mr-4 text-indigo-500"
				:icon="RiDiscordLine"
			/>
			<div>
				<h3
					:class="{'text-emerald-500': quest.complete}"
					class="text-sm"
				>
					{{ $t("Join {name} server on discord", {name: `@${giveaway.project.name}`}) }}
				</h3>
			</div>
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
						>{{ $t("[ Join discord server ]") }}
					</PrimaryButton>
				</ExternalLink></template
			>

			<ConnectWalletLink
				type="button"
				v-else
				as="button"
				@clicked="connect"
				class="text-white font-semibold bg-indigo-500 hover:bg-indigo-500/90 focus:ring-4 focus:outline-none focus:ring-indigo-500/50 rounded-sm text-sm px-3 py-1.5 text-center inline-flex items-center dark:focus:ring-indigo-500/55 mr-2 mb-2"
			>
				<Loading v-if="form.processing" />
				<VueIcon
					v-else
					class="w-4 h-4 mr-2 -ml-1"
					:icon="BiDiscord"
				/>

				{{ $t("Connect") }}
			</ConnectWalletLink>
		</div>
	</div>
</template>
