<script setup>
import {Popover, PopoverButton} from "@headlessui/vue";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {HiSolidChevronDown, HiSolidPlusSm} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
import CreateMenu from "@/Layouts/AppLayout/Header/CreateButton/CreateMenu.vue";

const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("sm");
</script>
<template>
	<Popover
		v-slot="{open}"
		class="relative"
	>
		<PopoverButton
			:class="open ? '' : 'text-opacity-90'"
			class="group"
			as="div"
		>
			<button
				:btn="open ? '!left-0 !top-0 ' : ''"
				:disabled="open"
				class="btn space-x-1 px-3 font-semibold text-gray-600 dark:text-gray-200 rounded-sm flex items-center bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700"
			>
				<VueIcon
					class="w-6 h-6"
					:icon="HiSolidPlusSm"
				/>
				<span v-if="!isSm">Create</span>
				<VueIcon
					v-if="!isSm"
					:icon="HiSolidChevronDown"
					:class="open ? 'rotate-180' : 'text-opacity-70'"
					class="ml-2 h-5 w-5 text-gray-600 dark:text-gray-200 transition duration-300 ease-in-out group-hover:text-opacity-80"
					aria-hidden="true"
				/>
			</button>
		</PopoverButton>
		<teleport
			v-if="isSm"
			to="body"
		>
			<CreateMenu />
		</teleport>
		<CreateMenu v-else />
	</Popover>
</template>
