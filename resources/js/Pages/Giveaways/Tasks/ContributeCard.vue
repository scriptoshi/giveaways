<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidChevronDown} from "oh-vue-icons/icons";
import {useChains} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import CreateLaunchpad from "@/Pages/Giveaways/Tasks/CreateLaunchpad.vue";
const chains = useChains();
const props = defineProps({
	giveaway: Object,
	quest: Object,
	launchpad: Object,
	min: Number,
});
const form = useForm({
	username: props.launchpad?.contract, // sale
	groupId: props.launchpad?.address, // token
	id: props.quest?.id ?? null,
	live: props.quest?.live ?? false,
	min: props.quest?.min ?? 0,
	type: "contribute",
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
const symbol = computed(() => {
	const chain = chains.value.find((c) => c.id === parseInt(props.launchpad?.chainId));
	if (!chain) return "BNB";
	return chain.nativeCurrency.symbol;
});
</script>
<template>
	<div
		:class="giveaway.prize < min ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900'"
		class="p-2 hover:bg-gray-50 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex justify-between items-center">
			<div class="flex items-center">
				<img
					class="w-8 h-8 mr-4"
					:src="giveaway.project?.logo?.url"
				/>

				<div>
					<h3 class="text-sm">
						{{ $t("FairLaunch Contribution") }}
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
				v-show="open"
				class="p-5"
			>
				<CollapseTransition>
					<div
						v-show="form.recentlySuccessful && !$page.props.error"
						class="text-green-500 font-semibold mt-3"
					>
						{{ $t("Giveway task was saved successfully.") }}
					</div>
				</CollapseTransition>
				<div
					v-if="launchpad"
					class="mt-4 mb-2 space-x-2 flex items-end"
				>
					<h3 class="text-base">
						{{ $t("Contribution task for {name} Launchpad", {name: launchpad.name}) }}
					</h3>
				</div>
				<div
					class="mt-4 mb-2"
					v-if="launchpad"
				>
					<div class="space-x-2 flex items-end justify-center">
						<FormInput
							class="w-full"
							:label="$t('Minimum amount to contribute', {symbol})"
							v-model="form.min"
						>
							<template #trail
								><span class="font-semibold">{{ symbol }}</span></template
							>
						</FormInput>
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
					{{ $t("Set as 0 (Zero) to accept any amount of contribution") }}
				</div>
				<CreateLaunchpad
					v-else
					:project="giveaway.project"
					:factory="$page.props.factory"
					:WETH9="$page.props.WETH9"
					:NFPM="$page.props.NFPM"
					class="mt-4 mb-2 p-3"
				/>
			</div>
		</CollapseTransition>
	</div>
</template>
