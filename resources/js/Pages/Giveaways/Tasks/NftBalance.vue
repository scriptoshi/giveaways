<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidChevronDown, MdGeneratingtokensOutlined} from "oh-vue-icons/icons";
import {useChains} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useNft} from "@/hooks/useUpdateCoins";
import {isAddress} from "@/Wagmi/utils/utils";
const props = defineProps({
	giveaway: Object,
	quest: Object,
	min: Number,
});
const chains = useChains();
const form = useForm({
	id: props.quest?.id ?? null,
	live: props.quest?.live ?? false,
	username: props.quest?.username ?? "",
	name: props.quest?.data?.name ?? "",
	symbol: props.quest?.data?.symbol ?? "",
	url: props.quest?.data?.url ?? "",
	chainId: null,
	type: "nft",
});
watch(
	chains,
	(chains) => {
		if (!props.quest?.data?.chainId) return;
		if (!chains) return;
		form.chainId = chains.find((c) => c.id === parseInt(props.quest?.data?.chainId));
	},
	{immediate: true},
);
const open = ref(false);
const questId = computed(() => props.quest?.id);
const route = computed(() =>
	props.quest?.id
		? window.route("quests.update", {quest: props.quest?.id})
		: window.route("quests.store", {giveaway: props.giveaway.uuid}),
);
const save = () => {
	form.transform((data) => ({
		...data,
		chainId: parseInt(data.chainId?.id),
	})).post(route.value, {preserveScroll: true});
};
watch(
	() => form.live,
	(live) => {
		if (props.quest?.id) {
			save();
		}
	},
);
const contract = computed(() => isAddress(form.username));
const chainId = computed(() => form.chainId?.id);
const {name, symbol, error, loading} = useNft(contract, chainId);
watch([name, symbol], ([name, symbol]) => {
	form.name = name;
	form.symbol = symbol;
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
					class="w-10 h-10 mr-4 text-emerald-500"
					:icon="MdGeneratingtokensOutlined"
				/>
				<div>
					<h3 class="text-sm">
						{{ $t("User Owns ERC721 (NFT) Token") }}
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
			<div v-show="open">
				<CollapseTransition>
					<div
						v-show="form.recentlySuccessful && !$page.props.error"
						class="text-green-500 font-semibold mt-3"
					>
						{{ $t("Giveway task was saved successfully.") }}
					</div>
				</CollapseTransition>
				<div class="mt-4 mb-2 p-6">
					<div class="p-4 border bg-white">
						<h3 class="text-sm mb-4">EVM ERC721 Token</h3>
						<FormInput
							:label="$t('Nft market or sales page where user can acquire nft')"
							v-model="form.url"
							:error="error ?? form.errors.url"
							class="max-w-lg mb-4"
							:placeholder="$t('https://opensea.io/collection/cool-cats-nft')"
						>
							<template #trail>
								<Loading v-if="loading" />
							</template>
						</FormInput>
						<div class="gap-3 mb-4 w-full grid md:grid-cols-3">
							<FormInput
								:label="$t('NFT Contract Address')"
								v-model="form.username"
								:error="error ?? form.errors.username"
								class="col-span-2"
							>
								<template #trail>
									<Loading v-if="loading" />
								</template>
							</FormInput>
							<div>
								<label
									for="name"
									class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-300"
									>Chain ID</label
								>
								<ChainSelect v-model="form.chainId">{{
									$t("Choose a chain")
								}}</ChainSelect>
								<span
									v-if="form.errors.chainId"
									class="text-red"
									>{{ form.errors.chainId }}</span
								>
							</div>
						</div>
						<div class="gap-3 w-full grid md:grid-cols-2">
							<FormInput
								:label="$t('Name')"
								v-model="form.name"
								:error="form.errors.name"
								disabled
							/>

							<FormInput
								:label="$t('Symbol')"
								v-model="form.symbol"
								:error="form.errors.symbol"
								disabled
							/>
						</div>
						<div class="mt-6 w-full flex space-x-3 items-center justify-end">
							<PrimaryButton
								:disabled="form.processing"
								primary
								@click.prevent="save"
							>
								<Loading
									v-if="form.processing"
									class="mr-2 -ml-2 !w-4 !h-4"
								/>
								{{ $t("Save Nft Information") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</CollapseTransition>
	</div>
</template>
