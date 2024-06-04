<script setup>
import {OiAlert, PrCopy} from "oh-vue-icons/icons";

import ExternalLink from "@/Components/ExternalLink.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";

defineProps({
	project: Object,
	chain: Object,
});
</script>
<template>
	<div
		class="shadow-sm dark:shadow-lg text-sm dark:text-gray-300 dark-text-emerald-400-text-dark p-4 rounded-md bg-white dark:bg-gray-800"
	>
		<div
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Address</div>
			<div class="flex items-center gap-1 text-right">
				<div class="break-all">
					<div>
						<div
							class="text-emerald-600 dark-text-emerald-400 flex items-center justify-end gap-1"
						>
							<WeCopy :text="project.address">
								<VueIcon
									class="text-emerald-600 dark-text-emerald-400 cursor-pointer inline-block"
									:icon="PrCopy"
								/>
							</WeCopy>
							<div>
								<span class="text-gray-700 dark:text-gray-200">
									{{ shortenAddress(project.address) }}
								</span>
							</div>
						</div>
						<div class="text-xs flex justify-end items-center text-amber-500 gap-1">
							<VueIcon
								class="h-4 w-4"
								:icon="OiAlert"
							/>
							<div>Never send ETH to this address</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Token Name</div>
			<div class="flex items-center gap-1 text-right text-emerald-500">
				<div class="break-all">{{ project.name }}</div>
			</div>
		</div>
		<div
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Token Name</div>
			<div class="flex items-center gap-1 text-right text-emerald-600 dark-text-emerald-400">
				<div class="break-all">{{ project.symbol }}</div>
			</div>
		</div>
		<div
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Chain</div>
			<div class="flex items-center gap-1 text-right">
				<img
					:src="chain.logo"
					class="rounded-full p-[1px] border border-gray-200 w-6 h-6"
				/>
				<div class="break-all">{{ chain?.name }}</div>
			</div>
		</div>
		<div
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Launch Date</div>
			<div class="flex items-center gap-1 text-right">
				<div class="break-all">{{ project.launch_date }}</div>
			</div>
		</div>

		<div
			v-if="project.chart"
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Dex Charts</div>
			<div class="flex items-center gap-1 text-right">
				<ExternalLink
					:href="project.chart"
					class="break-all"
				>
					{{ $t("Dexscreener") }}
				</ExternalLink>
			</div>
		</div>

		<template v-if="project.chart">
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">Price</div>
				<div class="flex items-center gap-1 text-right">
					<ExternalLink
						:href="project.chart"
						class="break-all"
					>
						{{ project.price * 1 }} USD
					</ExternalLink>
				</div>
			</div>
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">Market Cap</div>
				<div class="flex items-center gap-1 text-right">
					<ExternalLink
						:href="project.chart"
						class="break-all"
					>
						{{ project.market_cap * 1 }} USD
					</ExternalLink>
				</div>
			</div>
			<div
				class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex-1 capitalize">Volume</div>
				<div class="flex items-center gap-1 text-right">
					<ExternalLink
						:href="project.chart"
						class="break-all"
					>
						{{ project.volume * 1 }} USD
					</ExternalLink>
				</div>
			</div>
		</template>
		<div
			class="transition-all flex items-center gap-4 py-2 text-sm border-b border-dashed dark:border-gray-600"
		>
			<div class="flex-1 capitalize">Total holders</div>
			<div class="flex items-center gap-1 text-right">
				<div class="break-all">{{ project.holders ?? 0 }}</div>
			</div>
		</div>
	</div>
</template>
