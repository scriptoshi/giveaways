<script setup>
import {BiEmojiSunglasses, HiRefresh, HiSolidCheck} from "oh-vue-icons/icons";

import Address from "@/Components/Address.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";

defineProps({
	giveaway: Object,
	quest: Object,
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
				class="w-10 h-10 mr-4 text-emerald-500"
				:icon="BiEmojiSunglasses"
			/>
			<div>
				<h3
					:class="{'text-emerald-500': quest.complete}"
					class="text-sm"
				>
					{{ $t("Own one NFT of") }} {{ quest.data.name }}
				</h3>
				<div class="flex items-center">
					<ChainInfo :chain-id="quest.data.chainId" />
					<span class="mx-1 sm:mx-3">|</span>
					<Address
						:address="quest.username"
						:chain-id="quest.data.chainId"
						>{{ $t("View") }}</Address
					>
					<span class="mx-1 sm:mx-3">|</span>
					<WeCopy
						after
						:text="quest.username"
					>
						{{ shortenAddress(quest.username, 3) }}</WeCopy
					>
				</div>
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
			<ExternalLink :href="quest.data.url">
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
					>{{ $t("Mint NFT") }}
				</PrimaryButton>
			</ExternalLink>
		</div>
	</div>
</template>
