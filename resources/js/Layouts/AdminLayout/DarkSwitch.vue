<script setup>
import {useDark} from "@vueuse/core";
const darkMode = useDark();
const toggle = () => (darkMode.value = !darkMode.value);
</script>
<template>
	<button
		@click="toggle"
		class="btn h-8 w-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
	>
		<transition
			enter-from-class="scale-75"
			enter-active-class="transition-transform duration-200 ease-out absolute origin-top"
			enter-to-class="scale-100 static"
			leave-from-class="scale-100 static"
			leave-active-class="transition-transform duration-200 ease-out absolute origin-top"
			leave-to-class="scale-75"
		>
			<svg
				v-show="darkMode"
				class="h-6 w-6 text-amber-400"
				fill="currentColor"
				viewBox="0 0 24 24"
			>
				<path
					d="M11.75 3.412a.818.818 0 01-.07.917 6.332 6.332 0 00-1.4 3.971c0 3.564 2.98 6.494 6.706 6.494a6.86 6.86 0 002.856-.617.818.818 0 011.1 1.047C19.593 18.614 16.218 21 12.283 21 7.18 21 3 16.973 3 11.956c0-4.563 3.46-8.31 7.925-8.948a.818.818 0 01.826.404z"
				></path>
			</svg>
		</transition>
		<transition
			enter-from-class="scale-75"
			enter-active-class="transition-transform duration-200 ease-out absolute origin-top"
			enter-to-class="scale-100 static"
			leave-from-class="scale-100 static"
			leave-active-class="transition-transform duration-200 ease-out absolute origin-top"
			leave-to-class="scale-75"
		>
			<svg
				v-show="!darkMode"
				xmlns="http://www.w3.org/2000/svg"
				x-transition:enter="transition-transform duration-200 ease-out absolute origin-top"
				x-transition:enter-start="scale-75"
				x-transition:enter-end="scale-100 static"
				class="h-6 w-6 text-amber-400"
				viewBox="0 0 20 20"
				fill="currentColor"
			>
				<path
					fill-rule="evenodd"
					d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
					clip-rule="evenodd"
				></path>
			</svg>
		</transition>
	</button>
</template>
