<script setup>
import {computed} from "vue";

import {Popover, PopoverButton} from "@headlessui/vue";
import {ChevronDownIcon} from "@heroicons/vue/20/solid";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {useAccount} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import Jazzicon from "@/Wagmi/components/Jazzicon.vue";
import Panel from "@/Wagmi/components/Panel.vue";
import {useTxs} from "@/Wagmi/hooks/txs";
import {shortenAddress} from "@/Wagmi/utils/utils";
const txs = useTxs();
const {address} = useAccount();
const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("md");
const loading = computed(() => Object.values(txs).filter((t) => !t.receipt).length > 0);
</script>

<template>
	<Popover
		as="div"
		class="relative"
		v-slot="{open}"
	>
		<PopoverButton
			class="group flex flex-row items-center border border-gray-300 dark:border-gray-600 rounded-3xl px-4 py-1.5 focus:outline-none bg-white dark:bg-gray-900"
		>
			<Loading
				v-if="loading"
				:diameter="24"
				class="h-6 w-6 -ml-1 mr-2 !text-emerald-500"
			/>
			<slot
				name="profile"
				v-else
			>
				<Jazzicon
					:address="address"
					:diameter="24"
					class="h-6 -ml-1 mr-2"
				/>
			</slot>
			<span class="hidden font-semibold text-gray-700 dark:text-gray-300 sm:flex">{{
				shortenAddress(address, 2)
			}}</span>
			<ChevronDownIcon
				:class="{'rotate-180': open}"
				class="sm:ml-2 -mr-1 h-6 w-6 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-transform duration-300"
				aria-hidden="true"
			/>
		</PopoverButton>
		<teleport
			to="body"
			v-if="isSm"
		>
			<Panel>
				<slot />
			</Panel>
		</teleport>
		<Panel v-else>
			<slot />
		</Panel>
	</Popover>
</template>
