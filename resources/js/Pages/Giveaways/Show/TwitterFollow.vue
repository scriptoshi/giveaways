<script setup>
import {ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {BiTwitter, HiRefresh, HiSolidCheck, LaTwitterSquare} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DialogModal from "@/Jetstream/DialogModal.vue";
const form = useForm({});
const connect = () => {
	form.post(window.route("connections.connect", "twitter"));
};
defineProps({
	giveaway: Object,
	quest: Object,
	connected: Boolean,
	verifying: Boolean,
});
const verify = ref(false);
const emit = defineEmits(["verify"]);
const verifyTask = () => {
	emit("verify");
	verify.value = false;
};
</script>
<template>
	<div
		class="flex justify-between items-center p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex items-center">
			<VueIcon
				class="w-10 h-10 mr-4 text-sky-500"
				:icon="LaTwitterSquare"
			/>
			<h3
				:class="{'text-emerald-500': quest.complete}"
				class="text-sm"
			>
				Follow @{{ quest.name }} on Twitter
			</h3>
		</div>
		<div class="flex items-center space-x-3">
			<template v-if="connected">
				<ConnectWalletLink
					:disabled="verifying"
					v-tippy="$t('Verify task')"
					@clicked="verify = true"
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
						>[ Follow On X ]
					</PrimaryButton>
				</ExternalLink></template
			>
			<ConnectWalletLink
				type="button"
				v-else
				as="button"
				@clicked="connect"
				class="text-white font-semibold bg-[#1da1f2] hover:bg-[#1da1f2]/90 focus:ring-4 focus:outline-none focus:ring-[#1da1f2]/50 rounded-sm text-sm px-3 py-1.5 text-center inline-flex items-center dark:focus:ring-[#1da1f2]/55 mr-2 mb-2"
			>
				<Loading v-if="form.processing" />
				<VueIcon
					v-else
					class="w-4 h-4 mr-2 -ml-1"
					:icon="BiTwitter"
				/>

				Connect
			</ConnectWalletLink>
		</div>
		<DialogModal
			:show="verify"
			@close="verify = false"
			closeable
		>
			<template #title> Twitter Follow Changes </template>
			<template #content>
				<p>
					Due to price change on X API, we will only verify winners. Ensure you have
					followed before you proceed.
				</p>
				<p class="mt-2">
					When you are chosen as the winner and found not to have followed, you will be
					disqualified and another participant selected.
				</p>
				<p class="mt-2 text-sky-500">
					You can follow the project here
					<ExternalLink :href="quest.username"> @{{ quest.name }}</ExternalLink>
				</p>
				<div class="w-full mt-4 flex items-center space-x-3 justify-end">
					<PrimaryButton
						error
						@click.prevent="verify = false"
						class="!py-1 !px-2"
						>Cancel
					</PrimaryButton>
					<PrimaryButton
						secondary
						@click.prevent="verifyTask"
						class="!py-1 !px-2"
					>
						I have followed
					</PrimaryButton>
				</div>
			</template>
		</DialogModal>
	</div>
</template>
