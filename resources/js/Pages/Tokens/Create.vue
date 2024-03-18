<script setup>
import {HiSolidPlus} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CrossChainToken from "@/Pages/Tokens/Create/CrossChainToken.vue";
import StandardToken from "@/Pages/Tokens/Create/StandardToken.vue";
import TaxToken from "@/Pages/Tokens/Create/TaxToken.vue";
defineProps({
	type: String,
	factory: Object,
});
const {chainId} = useAccount();
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div
					class="mt-4 mb-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0"
				>
					<h3
						v-if="type === 'standard'"
						class="text-xl"
					>
						{{ $t("Create ERC20 Standard Token") }}
					</h3>
					<h3
						v-if="type === 'taxtoken'"
						class="text-xl"
					>
						{{ $t("Create Tax Token") }}
					</h3>
					<h3
						v-if="type === 'crosschain'"
						class="text-xl"
					>
						{{ $t("Create Cross Chain Token") }}
					</h3>
					<div
						v-if="chainId"
						class="flex flex-col space-y-2 sm:space-y-0 sm:flex-row sm:space-x-2"
					>
						<PrimaryButton
							:secondary="type !== 'standard'"
							:primary="type === 'standard'"
							:disabled="type === 'standard'"
							link
							:href="route('tokens.create', {type: 'standard', chainId})"
						>
							<VueIcon
								:icon="HiSolidPlus"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							{{ $t("Standard Token") }}
						</PrimaryButton>
						<PrimaryButton
							:secondary="type !== 'taxtoken'"
							:primary="type === 'taxtoken'"
							:disabled="type === 'taxtoken'"
							link
							:href="route('tokens.create', {type: 'taxtoken', chainId})"
						>
							<VueIcon
								:icon="HiSolidPlus"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							{{ $t("Tax Token") }}
						</PrimaryButton>
						<PrimaryButton
							:secondary="type !== 'crosschain'"
							:primary="type === 'crosschain'"
							:disabled="type === 'crosschain'"
							link
							:href="route('tokens.create', {type: 'crosschain', chainId})"
						>
							<VueIcon
								:icon="HiSolidPlus"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							{{ $t("Crosschain Token") }}
						</PrimaryButton>
					</div>
				</div>
				<div class="flex flex-row mx-auto">
					<div class="card p-8 border dark:border-gray-600 border-gray-300">
						<StandardToken
							v-if="type === 'standard'"
							:factory="factory"
						/>
						<TaxToken
							v-if="type === 'taxtoken'"
							:factory="factory"
						/>
						<CrossChainToken
							v-if="type === 'crosschain'"
							:factory="factory"
						/>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
