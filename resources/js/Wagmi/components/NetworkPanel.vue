<script setup>
import {computed, ref} from "vue";

import {PopoverButton, PopoverPanel} from "@headlessui/vue";
import {MagnifyingGlassIcon} from "@heroicons/vue/24/outline";
import upperFirst from "lodash/upperFirst";
import {useAccount, useChains, useSwitchChain} from "use-wagmi";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import Loading from "@/Components/Loading.vue";
import NetworkIcon from "@/Wagmi/components/Icons/NetworkIcon.vue";
import {DEFAULT_INPUT_UNSTYLED} from "@/Wagmi/components/Inputs";
import Typography from "@/Wagmi/components/Typography.vue";
const {error, switchChain, status} = useSwitchChain();
const pendingChainId = ref(null);
const {chain} = useAccount();
const chains = useChains();
const switchNetwork = ({chainId}) => {
	pendingChainId.value = chainId;
	switchChain({chainId});
};
const filter = ref("");
const filteredChains = computed(() => {
	if (filter.value === "") return chains.value;
	return chains.value
		.filter((chain) => {
			return (
				chain.name.includes(filter.value) ||
				chain.network.includes(filter.value) ||
				chain.nativeCurrency.symbol.includes(filter.value) ||
				chain.nativeCurrency.name.includes(filter.value)
			);
		})
		.filter((c) => !!c);
});
</script>
<template>
	<PopoverPanel
		class="flex flex-col w-full bg-white dark:bg-gray-800 sm:w-[320px] fixed bottom-0 top-[55px] shadow-md left-0 right-0 sm:absolute sm:top-[unset] sm:bottom-[unset] sm:left-[unset] mt-2 sm:rounded-xl rounded-b-none border-t sm:border border-gray-300 dark:border-gray-600 z-[100]"
	>
		<div class="flex gap-2 items-center p-4 pb-3">
			<MagnifyingGlassIcon class="text-gray-500 w-5 h-5" />
			<input
				v-model="filter"
				:class="[
					DEFAULT_INPUT_UNSTYLED,
					'w-full bg-transparent placeholder:font-medium text-sm',
				]"
				placeholder="Search networks"
			/>
		</div>
		<div class="mx-4 pb-2 border-t-2 border-gray-200 dark:border-gray-700" />
		<div
			class="py-2 max-h-screen sm:max-h-[300px] overflow-y-auto scroll scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700 scrollbar-thumb-rounded-sm"
		>
			<CollapseTransition>
				<div
					v-show="error"
					class="text-red-500 text-sm font-semibold dark:text-red-400 my-3 px-5"
				>
					{{ error?.message ?? "Failed to switch" }}
				</div>
			</CollapseTransition>
			<template
				v-for="x in filteredChains"
				:key="x?.id"
			>
				<PopoverButton
					as="div"
					:disabled="x?.id === chain?.id"
					@click.prevent="switchNetwork({chainId: x?.id})"
					:class="x?.id === chain.id ? 'bg-green-500/10' : ''"
					class="hover:bg-emerald-200/30 dark:hover:bg-emerald-600/30 group px-3 flex justify-between gap-2 items-center cursor-pointer transform-all py-1.5"
				>
					<div class="flex items-center gap-2">
						<Loading
							v-if="status === 'loading' && x?.id === pendingChainId"
							class="w-6 h-6 sm:-ml-1 sm:mr-2"
						/>
						<NetworkIcon
							v-else
							type="naked"
							:chainId="x?.id"
							:width="24"
							:height="24"
							class="!w-6 !h-6"
						/>
						<Typography
							variant="sm"
							weight="500"
							:class="
								x?.id === chain.id
									? 'text-green-900 dark:text-green-400 font-semibold'
									: 'text-gray-700  dark:text-gray-200'
							"
							class="font-medium group-hover:hover:text-emerald-700 dark:group-hover:hover:text-white"
						>
							{{ upperFirst(x.name) }} {{ x.testnet ? "(Testnet)" : "" }}
							{{ status === "loading" && x.id === pendingChainId ? "…" : "" }}
						</Typography>
					</div>
					<div
						v-show="x?.id === chain.id"
						class="w-2 h-2 mr-1 rounded-full bg-green-600"
					/>
				</PopoverButton>
			</template>
		</div>
	</PopoverPanel>
</template>
