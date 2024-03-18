<script setup>
import {computed, onMounted, ref} from "vue";

import {Square3Stack3DIcon} from "@heroicons/vue/24/outline";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {HiSearch, HiX} from "oh-vue-icons/icons";
import {useChains} from "use-wagmi";

import VueIcon from "@/Components/VueIcon.vue";
import {NetworkIcon} from "@/Wagmi/components/Icons";

const chains = useChains();
defineProps({
	modelValue: Object,
});
const filter = ref("");
const search = ref();
const filteredChains = computed(() =>
	chains.value.filter(
		(f) =>
			f.name?.toLowerCase().includes(filter.value.toLowerCase()) ||
			f?.nativeCurrency?.name?.toLowerCase()?.includes(filter.value.toLowerCase()) ||
			f.nativeCurrency?.symbol?.toLowerCase()?.includes(filter.value.toLowerCase()),
	),
);
const emit = defineEmits(["update:modelValue", "close"]);
const close = () => emit("close");
const select = (currency) => {
	emit("update:modelValue", currency);
	close();
};
const points = useBreakpoints(breakpointsTailwind);
const isSmall = points.smaller("lg");
onMounted(() => search.value.focus());
</script>
<template>
	<div
		ref="inside"
		class="z-50 relative pl-3"
	>
		<div
			:class="isSmall ? $style.fullscreen : ''"
			class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md pb-2.5 lg:absolute fixed top-0 lg:top-full left-0 lg:mt-2 shadow-[0_8px_12px_rgb(51_51_51_/_8%);]"
		>
			<div class="relative box-border">
				<div
					:class="isSmall ? $style.fade : ''"
					class="p-3.5"
				>
					<div
						class="flex items-center justify-between rounded-md bg-gray-100 dark:bg-gray-700 p-2 pl-md-3 mb-2 lg:hidden"
					>
						<span class="h5 m-0">{{ $t("Preferred currency") }}</span>
						<button
							type="button"
							@click="close()"
							class="flex bg-transparent rounded-[50%] p-2 border-0 hover:text-emerald-900 hover:bg-gray-200"
						>
							<VueIcon
								:icon="HiX"
								class="w-5 h-5"
							/>
						</button>
					</div>
					<div
						class="py-[0.625] px-[0.9375rem] text-sm min-h-[2rem] flex content-center items-center rounded-md"
					>
						<VueIcon
							:icon="HiSearch"
							class="w-4 h-4 text-gray-500 mr-2"
						/>
						<div
							class="items-center flex flex-1 flex-wrap p-0 relative overflow-hidden box-border"
						>
							<div
								class="absolute top-1/2 translate-y-1/2 box-border text-gray-600 text-sm leading-4 truncate w-full"
							>
								{{ $t("Search for your currency") }}
							</div>
							<div class="w-full">
								<div class="text-gray-900 block w-full">
									<input
										autocapitalize="none"
										ref="search"
										autocomplete="off"
										autocorrect="off"
										spellcheck="false"
										tabindex="0"
										type="text"
										v-model="filter"
										class="box-content focus:ring-0 focus:border-0 w-full border-transparent ring-transparent focus:ring-transparent focus:border-none opacity-100 outline-none p-0 bg-none bg-transparent text-inherit dark:text-gray-300"
									/>
									<div
										class="absolute font-sans font-mediuml tracking-normal top-0 left-0 invisible h-0 overflow-scroll whitespace-pre text-sm not-italic normal-case"
									></div>
								</div>
							</div>
						</div>
						<div class="items-center self-stretch flex flex-shrink-0 box-border"></div>
					</div>
				</div>
				<div>
					<div class="text-gray-900 bg-white dark:bg-gray-700 pl-4">
						<div
							class="overflow-x-hidden overflow-y-scroll h-screen lg:max-h-[17rem] pr-4"
						>
							<div class="">
								<div
									class="pb-2 pt-1 ml-2 text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase"
								>
									{{ $t("Showing Only Supported") }}
								</div>
								<div class="space-y-2">
									<div
										@click="select(null)"
										:class="
											modelValue == null
												? 'border border-emerald-300 dark:border-emerald-400 bg-emerald-50/60 dark:bg-transparent hover:bg-emerald-100 '
												: 'bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 '
										"
										class="flex content-center text-gray-500 dark:text-gray-300 hover:text-emerald-900 hover:dark:text-emerald-400 items-center text-sm rounded-md cursor-pointer transition-colors pl-2.5 pr-2 py-1.5"
										tabindex="-1"
									>
										<div
											class="flex content-between items-center w-full text-sm"
										>
											<div class="flex items-center content-between">
												<Square3Stack3DIcon class="w-6 h-6 mr-3" />
												<span class="font-medium">{{
													$t("View All Chains")
												}}</span>
											</div>
										</div>
									</div>
									<div
										v-for="chain in filteredChains"
										:key="chain.id"
										@click="select(chain)"
										:class="
											chain.id == modelValue?.id
												? 'border border-emerald-300 dark:border-emerald-400 bg-emerald-100 dark:bg-transparent hover:bg-emerald-100 '
												: 'bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 '
										"
										class="flex content-center text-gray-500 dark:text-gray-300 hover:text-emerald-900 hover:dark:text-emerald-400 items-center text-sm rounded-md cursor-pointer transition-colors pl-2.5 pr-2 py-1.5"
										tabindex="-1"
									>
										<div
											class="flex content-between items-center w-full text-sm"
										>
											<div class="flex items-center content-between">
												<NetworkIcon
													class="mr-2 w-6 h-6"
													:chainId="chain.id"
												/>
												<span class="font-medium">{{
													chain.id === 56
														? "BSC Mainet"
														: chain.id === 97
														  ? "BSC Testnet"
														  : chain.name
												}}</span>
											</div>
											<span
												:class="
													chain.id == modelValue?.id
														? 'border-emerald-600 dark:border-emerald-400 text-emerald-600 dark:text-emerald-400'
														: ' border-gray-300 dark:border-gray-500 '
												"
												class="bg-white dark:bg-gray-900 py-1 min-w-[4rem] border rounded-[4px] font-semibold text-center self-center ml-auto"
												>{{ chain.nativeCurrency.symbol }}</span
											>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style module>
.fade::after {
	content: "";
	position: absolute;
	display: block;
	/*top: 100%;*/
	width: 100%;
	height: 56px;
	background: linear-gradient(180deg, hsla(0, 0%, 100%, 0), #fff);
	transform: matrix(1, 0, 0, -1, 0, 0);
	pointer-events: none;
}
.dark.fade::after {
	background-image: linear-gradient(rgba(24, 26, 27, 0), rgb(24, 26, 27)) !important;
	background-color: initial !important;
}
.fullscreen {
	position: fixed !important;
	top: 0 !important;
	height: 100%;
	z-index: 1020;
}
</style>
