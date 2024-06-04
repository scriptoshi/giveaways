<script setup>
import {Popover, PopoverButton} from "@headlessui/vue";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {HiSolidChevronDown} from "oh-vue-icons/icons";
import {useAccount, useSwitchChain} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AddAccountModal from "@/Wagmi/components/AddAccountModal.vue";
import {NetworkIcon} from "@/Wagmi/components/Icons";
import NetworkPanel from "@/Wagmi/components/NetworkPanel.vue";
const {chain} = useAccount();
const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("md");
const {error, status} = useSwitchChain();
</script>
<template>
	<div>
		<Popover
			v-slot="{open}"
			v-if="chain?.id"
			className="relative"
		>
			<PopoverButton as="div">
				<button
					:class="
						open
							? 'border-emerald-500 dark:border-emerald-400 text-emerald-500 dark:text-emerald-400  bg-emerald-50 dark:bg-gray-900'
							: 'border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-200 bg-white dark:bg-gray-900'
					"
					class="btn max-w-xs space-x-1 px-3 rounded-sm flex items-center border transition-colors"
				>
					<Loading
						v-if="status === 'loading'"
						class="w-6 h-6 sm:-ml-1 sm:mr-2"
					/>
					<NetworkIcon
						v-else
						class="w-6 h-6 sm:-ml-1 sm:mr-2"
						:chainId="chain.id"
					/>
					<div v-if="error">{{ error?.message ?? "Failed to switch" }}</div>
					<div
						:class="{'text-error': chain.testnet}"
						class="hidden font-semibold sm:flex"
					>
						{{ chain.name?.split(" ")[0] }}
					</div>
					<VueIcon
						:icon="HiSolidChevronDown"
						:v-if="!isSm"
						:class="[
							open ? 'rotate-180' : 'rotate-0',
							'transition-transform w-5 h-5 hidden sm:flex  sm:ml-2 sm:-mr-1',
						]"
					/>
				</button>
			</PopoverButton>
			<teleport
				to="body"
				v-if="isSm"
			>
				<NetworkPanel />
			</teleport>
			<NetworkPanel v-else />
		</Popover>
		<AddAccountModal />
	</div>
</template>
