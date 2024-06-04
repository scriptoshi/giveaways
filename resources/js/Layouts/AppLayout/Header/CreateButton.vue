<script setup>
import {computed} from "vue";

import {Popover, PopoverButton} from "@headlessui/vue";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {HiSolidChevronDown, HiSolidPlusSm} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
import CreateMenu from "@/Layouts/AppLayout/Header/CreateButton/CreateMenu.vue";

const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("sm");
const active = computed(() => {
	return (
		window.route().current("giveaways.create") ||
		window.route().current("services.create") ||
		window.route().current("services.create")
	);
});
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
				:class="
					open || active
						? 'border-emerald-500 dark:border-emerald-400 text-emerald-500 dark:text-emerald-400  bg-emerald-50 dark:bg-gray-900'
						: 'border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-200 bg-white dark:bg-gray-900'
				"
				class="btn space-x-1 px-3 font-semibold rounded-sm flex items-center border transition-colors duration-300"
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
					class="ml-2 h-5 w-5 transition duration-300 ease-in-out group-hover:text-opacity-80"
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
