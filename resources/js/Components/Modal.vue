<script setup>
import {ArrowRightIcon} from "@heroicons/vue/24/solid";
import {HiSolidX} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";

defineProps({
	isOpen: Boolean,
	show: Boolean,
});
const emit = defineEmits(["close", "resolve"]);
const close = () => emit("close");
const resolve = () => emit("resolve");
</script>
<template>
	<div v-if="isOpen || show">
		<teleport to="body">
			<div
				v-bind="attrs"
				tabindex="-1"
				class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center flex"
				aria-modal="true"
				role="dialog"
			>
				<div class="relative p-4 w-full max-w-lg h-full md:h-auto">
					<!-- Modal content -->
					<div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
						<!-- Modal header -->
						<div
							class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600"
						>
							<h3 class="text-xl font-medium text-gray-900 dark:text-white">
								Confirm Presale Settings!
							</h3>
							<button
								type="button"
								@click="close"
								class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
							>
								<VueIcon
									:icon="HiSolidX"
									class="w-5 h-5"
								/>
							</button>
						</div>
						<!-- Modal body -->
						<slot />
						<!-- Modal footer -->
						<div
							class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
						>
							<button
								type="button"
								@click="resolve"
								class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								Proceed
								<ArrowRightIcon class="mr-2 -ml-1 inline-block w-5 h-5" />
							</button>
							<button
								type="button"
								@click="close"
								class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
							>
								Cancel
							</button>
						</div>
					</div>
				</div>
			</div>
		</teleport>
		<div
			@click="close"
			class="bg-gray-900 opacity-50 dark:opacity-80 fixed inset-0 z-40"
		></div>
	</div>
</template>
