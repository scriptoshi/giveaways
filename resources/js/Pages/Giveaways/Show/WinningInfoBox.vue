<script setup>
import {computed} from "vue";

import {useChains} from "use-wagmi";

import SiteLogo from "@/Components/SiteLogo.vue";
import {useBillions} from "@/hooks/useBillions";
import UsdtLogo from "@/images/usdt.svg";
import {NetworkIcon} from "@/Wagmi/components/Icons";

const chains = useChains();

const props = defineProps({
	giveaway: Object,
});
const chain = computed(() =>
	chains.value.find((c) => c.id.toString() === props.giveaway.chainId.toString()),
);
</script>
<template>
	<div class="border rounded-sm mt-8 dark:border-gray-600 p-6">
		<div class="flex flex-col lg:flex-row gap-3 justify-between items-center">
			<p>{{ $t("Reward Per Winner") }}</p>
			<div class="flex items-center space-x-2">
				<UsdtLogo class="w-5 h-5" />
				<h3 class="text-base">{{ giveaway.prize }} {{ giveaway.symbol }}</h3>
			</div>
		</div>
		<div class="border-t mt-5 border-dashed dark:border-gray-600 p-2" />
		<div class="flex flex-col lg:flex-row gap-3 justify-between mt-2 items-center">
			<p><span class="font-semibold">GAS</span> Pump</p>
			<div class="flex items-center space-x-2">
				<SiteLogo class="w-5 h-5" />
				<h3 class="text-base">
					{{ useBillions(giveaway.gas_balance) }} / {{ useBillions(giveaway.sleep) }} GAS
				</h3>
			</div>
		</div>
		<div class="flex mt-8 justify-center">
			<div class="flex items-center space-x-2">
				<NetworkIcon
					class="w-8 h-8"
					:chain-id="giveaway.chainId"
				/>
				<div
					class="rounded-2xl border dark:border-gray-700 px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 text-md font-Walsheim-Bold"
				>
					{{ chain.name }}
				</div>
			</div>
		</div>
	</div>
</template>
