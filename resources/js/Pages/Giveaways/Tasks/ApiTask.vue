<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidChevronDown, RiExchangeLine} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import {truncateTx} from "@/Wagmi/utils/utils";
const props = defineProps({
	giveaway: Object,
	quest: Object,
	min: Number,
});
function dec2hex(dec) {
	return dec.toString(16).padStart(2, "0");
}
function generateId(len) {
	const arr = new Uint8Array((len || 40) / 2);
	window.crypto.getRandomValues(arr);
	return Array.from(arr, dec2hex).join("");
}
const form = useForm({
	id: props.quest?.id ?? null,
	live: props.quest?.live ?? false,
	username: props.quest?.username ?? "",
	groupId: props.quest?.groupId ?? generateId(40),
	type: "api",
	instruction: props.quest?.data?.instruction,
	url: props.quest?.data?.url,
});

const open = ref(false);
const questId = computed(() => props.quest?.id);
const route = computed(() =>
	props.quest?.id
		? window.route("quests.update", {quest: props.quest?.id})
		: window.route("quests.store", {giveaway: props.giveaway.uuid}),
);
const save = () => {
	form.post(route.value, {preserveScroll: true});
};
watch(
	() => form.live,
	(live) => {
		if (props.quest?.id) {
			save();
		}
	},
);
const {address} = useAccount();
const link = computed(() => {
	const addresses = `${address.value},0xf80D950E447BcCA7723a10Ab5c99865E55b4e20C,0xbac82795b8d7722Ae9f46a9235224f7767dDf471`;
	if (form.username) {
		return `${form.username}?key=${form.groupId}&addresses=${addresses}`;
	}
	return window.route("quests.check.task", {key: form.groupId, addresses});
});
</script>
<template>
	<div
		:class="giveaway.prize < min ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900'"
		class="p-2 hover:bg-gray-50 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex justify-between items-center">
			<div class="flex items-center">
				<VueIcon
					class="w-10 h-10 mr-4 text-gray-500"
					:icon="RiExchangeLine"
				/>
				<div>
					<h3 class="text-sm">
						{{ $t("Api Remote Task") }}
						<span
							v-if="!form.live || !questId"
							class="bg-gray-500 text-xs text-white px-3 py-0.5 ml-2 rounded-xl"
							>{{ $t("Off") }}</span
						>
					</h3>
					<p
						v-if="giveaway.prize < min"
						class="text-xs text-red-500"
					>
						{{ $t("Minimum prize amount is ") }} {{ min }} USDT
					</p>
				</div>
			</div>
			<div class="flex items-center justify-end space-x-3">
				<Switch
					:disabled="giveaway.prize < min"
					v-model="form.live"
					>{{ $t("Enable") }}</Switch
				>
				<PrimaryButton
					class="!p-1"
					secondary
					@click.prevent="open = !open"
				>
					<Loading v-if="!open && form.processing" />
					<VueIcon
						v-else
						:class="{'rotate-180 text-emerald-500': open}"
						class="transition-all duration-300"
						:icon="HiSolidChevronDown"
					/>
				</PrimaryButton>
			</div>
		</div>
		<CollapseTransition>
			<div
				class="p-4 mt-3"
				v-show="open"
			>
				<CollapseTransition>
					<div
						v-show="form.recentlySuccessful && !$page.props.error"
						class="text-green-500 font-semibold my-3"
					>
						{{ $t("Giveway task was saved successfully.") }}
					</div>
				</CollapseTransition>
				<FormInput
					:label="$t('Task Instructions')"
					v-model="form.instruction"
					:error="form.errors.instruction"
					:placeholder="$t('E.g Register for an account at sleep.finance')"
				/>
				<FormInput
					class="my-4 max-w-sm"
					:label="$t('Task URL')"
					v-model="form.url"
					:error="form.errors.url"
					:placeholder="$t('https://sleep.finance/register')"
				/>
				<p>
					We shall perform a <span class="font-bold">GET</span> request on the url you
					provide below with a comma seperated list of addresses belonging to the user.
					You must return http status <code class="text-emerald-500">200</code> always
					with a json response. The Response json should have one key,
					<code class="font-semibold">complete</code> set to
					<code class="text-emerald-500">true</code> or
					<code class="text-red-500">false</code>
				</p>
				<pre class="mt-2"> { "complete":<span class="text-emerald-500">true</span> } </pre>

				<div class="mt-4 mb-2 space-x-2 flex items-end justify-center">
					<FormInput
						class="w-full"
						label="Api Url"
						v-model="form.username"
						placeholder="https://sleepfinance.io/check-task/registered"
					/>
					<PrimaryButton
						:disabled="form.processing"
						primary
						@click.prevent="save"
					>
						<Loading
							v-if="form.processing"
							class="mr-2 -ml-2 !w-4 !h-4"
						/>
						{{ $t("Save") }}
					</PrimaryButton>
				</div>
				<div class="flex space-x-3 mt-4">
					<WeCopy
						after
						:text="form.groupId"
					>
						APIKEY:<span class="font-semibold ml-1 text-gray-900 dark:text-gray-200">
							{{ truncateTx(form.groupId, 6) }}</span
						></WeCopy
					>
					<ExternalLink
						class="hover:underline"
						:href="link"
						>[ Try it Out ]</ExternalLink
					>
				</div>
			</div>
		</CollapseTransition>
	</div>
</template>
