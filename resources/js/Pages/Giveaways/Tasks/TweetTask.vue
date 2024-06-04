<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidChevronDown, LaTwitterSquare} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
const props = defineProps({
	giveaway: Object,
	quest: Object,
});
const form = useForm({
	id: props.quest?.id ?? null,
	live: props.quest?.live ?? false,
	username: props.quest?.username ?? "",
	comment: props.quest?.groupId === "comment",
	type: "tweet",
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
</script>
<template>
	<div
		class="p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex justify-between items-center">
			<div class="flex items-center">
				<VueIcon
					class="w-10 h-10 mr-4 text-sky-500"
					:icon="LaTwitterSquare"
				/>
				<h3 class="text-sm">
					{{ $t("Like & Retweet task") }}
					<span
						v-if="!form.live || !questId"
						class="bg-gray-500 text-xs text-white px-3 py-0.5 ml-2 rounded-xl"
						>{{ $t("Off") }}</span
					>
				</h3>
			</div>
			<div class="flex items-center justify-end space-x-3">
				<Switch v-model="form.live">{{ $t("Enable") }}</Switch>
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
			<div v-show="open">
				<CollapseTransition>
					<div
						v-show="form.recentlySuccessful && !$page.props.error"
						class="text-green-500 font-semibold mt-3"
					>
						{{ $t("Giveway task was saved successfully.") }}
					</div>
				</CollapseTransition>
				<div class="mt-4 mb-3 space-x-2 flex items-end justify-center">
					<FormInput
						class="w-full"
						label="Tweet Url"
						v-model="form.username"
						placeholder="https://x.com/sleeprotocol/status/1764280752135442586"
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
				<Switch
					v-model="form.comment"
					class="mb-2"
					>Users should comment on the tweet</Switch
				>
			</div>
		</CollapseTransition>
	</div>
</template>
