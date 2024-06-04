<script setup>
import {computed, onMounted, ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {BiTwitter, HiRefresh, HiSolidCheck, LaTwitterSquare} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DialogModal from "@/Jetstream/DialogModal.vue";
const form = useForm({});
const connect = () => {
	form.post(window.route("connections.connect", "twitter"));
};
const props = defineProps({
	giveaway: Object,
	quest: Object,
	connected: Boolean,
	verifying: Boolean,
	comment: String,
	errors: String,
});
const verify = ref(false);
const emit = defineEmits(["verify", "update:comment"]);
const comment = computed({
	get: () => props.comment,
	set: (val) => emit("update:comment", val),
});
const verifyTask = () => {
	emit("verify");
	verify.value = false;
};
onMounted(() => (comment.value = props.quest.tasks?.[0]?.response ?? ""));
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
			<div>
				<h3
					v-if="quest.groupId === 'comment'"
					:class="{'text-emerald-500': quest.complete}"
					class="text-sm"
				>
					{{ $t("Like and Retweet & Reply on project tweet") }}
				</h3>
				<h3
					v-else
					:class="{'text-emerald-500': quest.complete}"
					class="text-sm"
				>
					{{ $t("Like and Retweet project tweet on X") }}
				</h3>
				<p
					v-if="errors"
					class="text-red-500"
				>
					{{ $t("Tweet reply Url is required") }}
				</p>
			</div>
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
						>{{ $t("[ Like & Retweet ]") }}
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

				{{ $t("Connect") }}
			</ConnectWalletLink>
		</div>
		<DialogModal
			:show="verify"
			@close="verify = false"
			closeable
		>
			<template #title> Twitter Changes </template>
			<template #content>
				<p>
					Due to price change on X API, we will only verify winners. Ensure you have liked
					and retweeted before you proceed.
				</p>
				<p class="mt-2">
					When you are chosen as the winner and found not to have liked or retweeted, you
					will be disqualified and another participant selected.
				</p>
				<p
					v-if="quest.groupId === 'comment'"
					class="mt-2 text-sky-500"
				>
					You can like, retweet and reply on project
					<ExternalLink :href="quest.username"> tweet here </ExternalLink>
				</p>
				<p
					v-else
					class="mt-2 text-sky-500"
				>
					You can like and retweet project
					<ExternalLink :href="quest.username"> tweet here </ExternalLink>
				</p>
				<FormInput
					v-model="comment"
					v-if="quest.groupId === 'comment'"
					label="Tweet Reply Url"
					class="my-3"
					help="Copy the link to your tweet comment and paste the url here."
				/>
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
						I have liked and retweeted
					</PrimaryButton>
				</div>
			</template>
		</DialogModal>
	</div>
</template>
