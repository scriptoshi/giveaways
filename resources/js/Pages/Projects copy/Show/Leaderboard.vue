<script setup>
import {router} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiSearch} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import PaginationSquare from "@/Components/PaginationSquare.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopySvg from "@/Components/WeCopySvg.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";
const props = defineProps({votes: Object, url: String});
const params = useUrlSearchParams("history");
debouncedWatch(
	() => params.filter,
	(search) => {
		router.visit(props.url, {
			method: "get",
			data: {filter: search},
			preserveScroll: true,
		});
	},
	{debounce: 800},
);
</script>
<template>
	<div
		class="shadow-sm dark:shadow-lg mt-8 text-sm dark:text-brand-text-dark p-4 border border-emerald-500 rounded-md bg-white dark:bg-gray-800"
	>
		<div class="mb-3 flex justify-between">
			<h3 class="text-sm text-emerald-500">{{ $t("Leaderboad") }}</h3>
			<FormInput
				size="xs"
				v-model="params.filter"
				class="max-w-xs w-full"
				placeholder="Filter address or VoteID"
				input-classes="!pl-5"
			>
				<template #lead>
					<VueIcon
						class="w-3 h-h3"
						:icon="HiSearch"
					/>
				</template>
			</FormInput>
		</div>
		<div
			class="flex items-center gap-2 font-medium border-b border-dashed dark:border-neutral-800"
		>
			<div class="flex-1 font-semibold">{{ $t("Rank") }}</div>
			<div class="flex-1 font-semibold">{{ $t("Address") }}</div>
			<div class="flex-1 font-semibold">{{ $t("Pumps") }}</div>
			<div class="flex-1 font-semibold">{{ $t("24h") }}</div>
			<div class="flex-1 font-semibold">{{ $t("7days") }}</div>
			<div class="w-[64px] font-semibold">{{ $t("30days") }}</div>
		</div>
		<div class="mt-2 divide-y divide-dashed dark:divide-gray-700">
			<div
				v-for="voter in votes.data"
				:key="voter.voteId"
				class="flex items-center gap-2 py-2 cursor-pointer group hover:bg-gray-50 dark:hover:bg-gray-900"
			>
				<div class="flex-1">#{{ voter.rank }}</div>
				<div class="flex-1 group-hover:text-emerald-500">
					<WeCopySvg
						after
						:text="voter.address"
						>{{ shortenAddress(voter.address) }}</WeCopySvg
					>
				</div>
				<div class="flex-1">{{ voter.pump }}</div>
				<div class="flex-1">{{ voter.pumps_24 }}</div>
				<div class="flex-1">{{ voter.pumps_7 }}</div>
				<div class="w-[64px]">
					<span class="text-gray-600 dark:text-gray-200">{{ voter.pumps_30 }}</span>
				</div>
			</div>
		</div>
		<PaginationSquare :meta="votes.meta" />
	</div>
</template>
