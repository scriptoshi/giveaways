<script setup>
import {computed, ref} from "vue";

import {Link, usePage} from "@inertiajs/vue3";
import {CoChevronDoubleRight} from "oh-vue-icons/icons";
import {useAccount, useChains, useSwitchChain} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/SecondaryButton.vue";
// eslint-disable-next-line import/order
import {NetworkIcon} from "@/Wagmi/components/Icons/index";
const neededChain = computed(() => Number(usePage().props.chainId));
const {chain} = useAccount();
const chains = useChains();

const requiredChain = computed(
	() => chains.value.find((c) => c.id.toString() === neededChain.value?.toString?.()) ?? chain,
);
const {switchChain} = useSwitchChain();
const closed = ref(false);
const isOpen = false;
/*
const isOpen = computed(
	() =>
		!!neededChain.value &&
		!!chain?.value?.id &&
		parseInt(chain.value?.id) !== parseInt(neededChain.value),
); */
const loading = ref(false);
</script>
<template>
	<div v-if="isOpen && !!chain && !closed">
		<teleport to="body">
			<div
				tabindex="-1"
				class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center flex"
				aria-modal="true"
				role="dialog"
			>
				<div class="relative p-4 w-full max-w-lg h-full md:h-auto">
					<div
						class="relative py-4 bg-white border border-gray-300 dark:border-gray-600 rounded-lg shadow dark:bg-gray-800"
					>
						<div class="p-6 text-center">
							<div class="flex justify-center w-full flex-row">
								<div class="flex justify-center flex-row w-full h-full mb-8">
									<div class="block w-16">
										<div
											class="text-gray-500 rounded-full border-2 border-emerald-600 dark:border-gray-600 bg-white dark:bg-gray-700 inline-flex items-center justify-center h-16 w-16"
										>
											<NetworkIcon
												:chainId="chain.id"
												class="w-8 h-8"
											/>
										</div>
									</div>
									<div
										class="relative h-16 flex flex-row items-center justify-center"
									>
										<div
											class="flex absolute flex-row justify-center items-center w-10 h-10 text-emerald-600 dark:text-white bg-white dark:bg-gray-700 border-emerald-600 border-2 dark:border-gray-600 rounded-full"
										>
											<VueIcon
												:icon="CoChevronDoubleRight"
												class="w-6 h-6 mx-auto self-center justify-self-center"
											/>
										</div>
										<div
											class="w-32 border-b-2 border-emerald-600 dark:border-gray-600 border-dashed"
										></div>
									</div>
									<div class="block w-16">
										<div
											class="text-gray-500 rounded-full border-2 border-emerald-600 dark:border-gray-600 bg-white dark:bg-gray-700 inline-flex items-center justify-center h-16 w-16"
										>
											<NetworkIcon
												:chainId="neededChain"
												class="w-8 h-8"
											/>
										</div>
									</div>
								</div>
							</div>
							<h3 class="mb-5 text-lg font-normal text-gray-800 dark:text-gray-400">
								<slot>{{
									$t("This Resource Requires {chain} Chain", {
										chain: requiredChain.name,
									})
								}}</slot>
							</h3>
							<div class="flex gap-2">
								<Link
									data-modal-hide="popup-modal"
									type="button"
									:href="route('home')"
									class="mr-4"
								>
									<JetDangerButton>
										{{ $t("Home") }}
									</JetDangerButton>
								</Link>
								<JetButton
									class="uppercase"
									type="button"
									@click="switchChain(neededChain)"
								>
									<Loading
										v-if="loading"
										class="!-ml-1 !mr-2"
									/>{{ $t("Switch to {chain}", {chain: requiredChain.name}) }}
								</JetButton>
							</div>
						</div>
					</div>
				</div>
			</div>
		</teleport>
		<div
			@click="closed = false"
			class="bg-gray-900 opacity-50 dark:opacity-80 fixed inset-0 z-40"
		></div>
	</div>
</template>
