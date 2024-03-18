<script setup>
import {ref} from "vue";

import {onClickOutside} from "@vueuse/core";

import {usePopper} from "@/hooks/usePopper";
const {showPopper, popperRef, popperRoot} = usePopper({
	placement: "bottom-end",
	offset: 4,
});
defineProps({
	filter: String,
	types: Array,
});
const emit = defineEmits(["update:filter", "update:order"]);
const outside = ref(null);
onClickOutside(outside, () => (showPopper.value = false));
const setFilter = (filter) => {
	emit("update:filter", filter);
	showPopper.value = false;
};
</script>
<template>
	<div ref="outside">
		<button
			ref="popperRef"
			@click="showPopper = !showPopper"
			class="btn h-8 w-8 rounded-full p-0 hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25 sm:h-9 sm:w-9"
		>
			<svg
				xmlns="http://www.w3.org/2000/svg"
				class="h-5 w-5"
				fill="none"
				viewBox="0 0 24 24"
			>
				<path
					fill="currentColor"
					d="M3 5.109C3 4.496 3.47 4 4.05 4h16.79c.58 0 1.049.496 1.049 1.109 0 .612-.47 1.108-1.05 1.108H4.05C3.47 6.217 3 5.721 3 5.11zM5.798 12.5c0-.612.47-1.109 1.05-1.109H18.04c.58 0 1.05.497 1.05 1.109s-.47 1.109-1.05 1.109H6.848c-.58 0-1.05-.497-1.05-1.109zM9.646 18.783c-.58 0-1.05.496-1.05 1.108 0 .613.47 1.109 1.05 1.109h5.597c.58 0 1.05-.496 1.05-1.109 0-.612-.47-1.108-1.05-1.108H9.646z"
				></path>
			</svg>
		</button>
		<div
			ref="popperRoot"
			class="popper-root show"
			v-show="showPopper"
		>
			<div
				class="popper-box rounded-sm border border-slate-150 bg-white py-1.5 font-inter dark:border-gray-500 dark:bg-gray-700"
			>
				<ul>
					<li>
						<a
							href="#"
							@click.prevent="setOrder('latest')"
							class="flex h-8 cursor-pointer items-center px-3 pr-8 font-medium tracking-wide outline-none transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-gray-600 dark:hover:text-gray-100 dark:focus:bg-gray-600 dark:focus:text-gray-100"
							>{{ $t("Order By Latest") }}</a
						>
					</li>
					<li>
						<a
							href="#"
							@click.prevent="setOrder('oldest')"
							class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-none transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-gray-600 dark:hover:text-gray-100 dark:focus:bg-gray-600 dark:focus:text-gray-100"
							>{{ $t("Order By Oldest") }}</a
						>
					</li>
					<li>
						<a
							href="#"
							@click.prevent="setOrder('name')"
							class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-none transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-gray-600 dark:hover:text-gray-100 dark:focus:bg-gray-600 dark:focus:text-gray-100"
							>{{ $t("Order by Name") }}</a
						>
					</li>
				</ul>
				<div class="my-1 h-px bg-slate-150 dark:bg-gray-500"></div>
				<ul>
					<li>
						<a
							href="#"
							@click.prevent="setFilter('Sleep Finance')"
							class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-none transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-gray-600 dark:hover:text-gray-100 dark:focus:bg-gray-600 dark:focus:text-gray-100"
							>{{ $t("Only Sleep Finance") }}</a
						>
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>
