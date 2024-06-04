<script setup>
import {computed} from "vue";

import {PopoverPanel} from "@headlessui/vue";
import {Link} from "@inertiajs/vue3";
import {MdGeneratingtokensSharp, MdLockclock, RiCustomerService2Fill} from "oh-vue-icons/icons";
import {uid} from "uid";

import VueIcon from "@/Components/VueIcon.vue";

const menu = computed(() => [
	{
		id: uid(),
		name: "Give away",
		route: "giveaways.create",
		icon: MdGeneratingtokensSharp,
		description: "",
	},
	{
		id: uid(),
		name: "Services",
		route: "services.create",
		icon: RiCustomerService2Fill,
		description: "",
	},
	{
		id: uid(),
		name: "DX Escrow",
		route: "services.create",
		icon: MdLockclock,
		description: "",
	},
]);
</script>
<template>
	<transition
		enter-active-class="transition duration-200 ease-out"
		enter-from-class="translate-y-1 opacity-0"
		enter-to-class="translate-y-0 opacity-100"
		leave-active-class="transition duration-150 ease-in"
		leave-from-class="translate-y-0 opacity-100"
		leave-to-class="translate-y-1 opacity-0"
	>
		<PopoverPanel
			class="w-full bg-white dark:bg-gray-800 fixed bottom-0 top-[55px] shadow-md left-0 right-0 sm:absolute sm:top-[unset] sm:bottom-[unset] sm:left-[unset] mt-2 sm:rounded-sm rounded-b-none border-t sm:border border-gray-300 dark:border-gray-600 z-50"
		>
			<div class="overflow-hidden sm:rounded-sm py-3">
				<div
					class="relative max-h-screen sm:max-h-96 overflow-auto scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700 scrollbar-thumb-rounded-sm grid gap-1 py-1.5"
				>
					<Link
						v-for="item in menu"
						:key="item.name"
						:href="route(item.route)"
						:class="{
							'bg-emerald-300/30 dark:bg-emerald-400/30 ': route().current(
								item.route,
							),
						}"
						class="flex items-center space-x-3 px-4 sm:px-2 py-1.5 transition duration-150 ease-in-out hover:bg-emerald-300/30 dark:hover:bg-emerald-400/30 focus:outline-none focus-visible:ring focus-visible:ring-emerald-500 focus-visible:ring-opacity-50"
					>
						<VueIcon
							:icon="item.icon"
							class="flex h-5 w-5 text-emerald-600 dark:text-emerald-400"
						/>
						<div class="text-sm font-medium text-gray-700 dark:text-gray-200">
							{{ item.name }}
						</div>
					</Link>
				</div>
			</div>
		</PopoverPanel>
	</transition>
</template>
