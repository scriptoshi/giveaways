<script setup>
import {ref} from "vue";

import {HiChevronDown, HiRefresh, HiSolidCheck} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import VueIcon from "@/Components/VueIcon.vue";
import BuyRow from "@/Pages/Giveaways/Show/ContributeCard/BuyRow.vue";
defineProps({
	giveaway: Object,
	quest: Object,
	coin: Object,
	launchpad: Object,
	verifying: Boolean,
});
const isOpen = ref(false);
defineEmits(["verify"]);
</script>
<template>
	<div
		class="bg-white dark:bg-gray-900 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex justify-between p-2 items-center hover:bg-gray-50 dark:hover:bg-gray-800">
			<div class="flex items-center">
				<img
					class="w-8 h-8 mr-4"
					:src="giveaway.project?.logo?.url"
				/>
				<div>
					<h3
						v-if="quest.complete"
						class="text-sm text-emerald-500 font-semibold"
					>
						{{
							$t("You Contributed {contribution} to fairlaunch", {
								contribution: `${$page.props.contribution * 1}${coin?.symbol}`,
							})
						}}
					</h3>
					<h3
						v-else-if="quest.min > 0"
						class="text-sm"
					>
						{{
							$t("Contribute min: {min} to fairlaunch", {
								min: `${quest.min * 1}${coin?.symbol}`,
							})
						}}
					</h3>
					<h3
						v-else
						class="text-sm"
					>
						{{
							$t("Contribute any amount of {symbol} to fairlaunch", {
								symbol: coin?.symbol,
							})
						}}
					</h3>
					<p class="text-xs">
						{{
							$t("This task requires you to spend {symbol} ", {
								symbol: coin?.symbol,
							})
						}}
					</p>
				</div>
			</div>
			<div class="flex items-center space-x-3">
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
				<PrimaryButton
					secondary
					v-if="quest.complete"
					class="!py-1 !px-2 !text-emerald-500"
					@click.prevent="isOpen = !isOpen"
				>
					<VueIcon :icon="HiSolidCheck"></VueIcon>
					{{ $t("Done") }}
					<VueIcon
						class="ml-2 -mr-1 transition-transform duration-300"
						:class="{'rotate-180': isOpen}"
						:icon="HiChevronDown"
					/>
				</PrimaryButton>

				<PrimaryButton
					:secondary="!isOpen"
					:primary="isOpen"
					v-else
					@click.prevent="isOpen = !isOpen"
					class="!py-1 !px-2 transition-colors duration-500"
				>
					{{
						parseFloat(quest.min) == 0
							? "Any amount"
							: `Min ${quest.min} ${coin?.symbol}`
					}}
					<VueIcon
						class="ml-2 -mr-1 transition-transform duration-300"
						:class="{'rotate-180': isOpen}"
						:icon="HiChevronDown"
					/>
				</PrimaryButton>
			</div>
		</div>
		<CollapseTransition>
			<div
				v-show="isOpen"
				class="w-full"
			>
				<BuyRow
					:min="parseFloat(quest.min ?? 0)"
					:launchpad="launchpad"
					:usd-rate="coin.usd_rate"
				/>
			</div>
		</CollapseTransition>
	</div>
</template>
