<script setup>
import {computed, reactive} from "vue";

import {ComputerDesktopIcon} from "@heroicons/vue/24/outline";
import {uid} from "uid";
import {useAccount} from "use-wagmi";

import CheckIcon from "@/Feather/CheckIcon";
import XIcon from "@/Feather/XIcon";
import Layout from "@/Layouts/AdminLayout.vue";
import History from "@/Pages/Admin/Dashboard/History.vue";

const {chain, chainId} = useAccount();
const props = defineProps({
	factories: Array,
});
const launchFactory = computed(() =>
	props.factories?.find(
		(f) => parseInt(f.chainId) === chainId.value && f.type === "PresaleFactory",
	),
);

const tokenFactory = computed(() =>
	props.factories?.find(
		(f) => parseInt(f.chainId) === chainId.value && f.type === "StandardTokenFactory",
	),
);
const fairFactory = computed(() =>
	props.factories?.find(
		(f) => parseInt(f.chainId) === chainId.value && f.type === "FairLaunchFactory",
	),
);

const tokenAddress = computed(() => tokenFactory.value?.contract);
const launchAddress = computed(() => launchFactory.value?.contract);
const fairAddress = computed(() => fairFactory.value?.contract);
const stats = reactive([
	{
		name: "Token deployments",
		earned: "0.0856",
		tag: tokenAddress.value ? "Deployed" : "Pending",
		deployed: !!tokenAddress.value,
		tagIcon: tokenAddress.value ? CheckIcon : XIcon,
		desc: "Total from Token Factory",
		id: uid(),
	},
	{
		name: "Launchpad deployments",
		earned: "0.23",
		tag: launchAddress.value ? "Deployed" : "Pending",
		deployed: !!launchAddress.value,
		tagIcon: launchAddress.value ? CheckIcon : XIcon,
		desc: "Total from Launchpad Factory",
		id: uid(),
	},
	{
		name: "FairLaunch",
		earned: "0.6",
		tag: fairAddress.value ? "Deployed" : "Pending",
		deployed: !!fairAddress.value,
		tagIcon: fairAddress.value ? CheckIcon : XIcon,
		desc: "Total from Fairlaunch factory",
		id: uid(),
		totalFees: 0,
	},
]);
const orders = computed(() => stats.reduce((memo, stat) => [...memo, stat.events], []));
</script>
<template>
	<Layout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-4 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Welcome Admin</h3>
							<p>View your current earnings summary for {{ chain?.label }}</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<button
								type="button"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<ComputerDesktopIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								Enable Maintenance Mode
							</button>
						</div>
					</div>
					<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
						<div
							v-for="stat in stats"
							:key="stat.id"
							class="card card-border"
						>
							<div class="card-body">
								<h6
									class="font-semibold text-gray-800 dark:text-gray-400 mb-4 text-sm"
								>
									{{ stat.name }}
								</h6>
								<div class="flex justify-between items-center">
									<div>
										<div class="flex items-center">
											<img
												:src="chain?.logoUrl"
												class="w-5 h-5 mr-2 inline-block"
											/>
											<h3 class="h3 font-bold">
												<span
													>{{ stat.totalFees }}
													{{ chain?.nativeCurrency?.symbol }}</span
												>
											</h3>
										</div>

										<p>{{ stat.desc }}</p>
									</div>
									<div
										:class="
											stat.deployed
												? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-100'
												: ' text-red-600  bg-red-100 dark:bg-red-500/20 dark:text-red-100'
										"
										class="tag gap-1 font-bold border-0"
									>
										<span>
											<component
												class="w-4 h-4"
												:is="stat.tagIcon"
											/>
										</span>
										<span>{{ stat.tag }}</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<History :orders="orders" />
				</div>
			</div>
		</main>
	</Layout>
</template>
