<script setup>
import {computed, ref} from "vue";

import {MagnifyingGlassIcon} from "@heroicons/vue/24/outline";
import {router as Inertia} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";

import FormInput from "@/Components/FormInput.vue";
import Pagination from "@/Components/Pagination.vue";
import {useActiveWeb3Vue} from "@/Web3Modal";
import {CHAIN_INFO, SupportedChainId} from "@/Web3Modal/constants";
import {truncateTx} from "@/Web3Modal/utils";
import TxHash from "../Services/TxHash.vue";
import Types from "./Show/Types.vue";

const {chainId} = useActiveWeb3Vue();
defineProps({
	services: Object,
});

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
debouncedWatch(
	[search],
	([search]) => {
		Inertia.get(
			window.route("admin.badges.index", {chainId: chainId.value}),
			{search},
			{
				preserveState: true,
				preserveScroll: true,
			}
		);
	},
	{
		maxWait: 500,
	}
);
const chain = computed(() => CHAIN_INFO[chainId.value ?? SupportedChainId.MAINNET]);
</script>
<template>
	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Badge request") }}</h3>
							<a
								target="_blank"
								class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition duration-200"
								:href="route('projects.show', project.uid)"
								>Project: {{ project.uid }}</a
							>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								type="button"
								v-if="chainId"
								:href="route('admin.badges.index', {chainId})"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<ArrowLeftIcon class="mr-2 -ml-1 w-5 h-5 inline" />
								{{ $t("Go Back") }}
							</Link>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center justify-between mb-4 px-6">
								<h3 class="mb-4 lg:mb-0">Service Requests</h3>
								<div class="flex items-center">
									<FormInput
										placeholder="Search"
										v-model="search"
									>
										<template #lead>
											<MagnifyingGlassIcon class="w-5 h-5" />
										</template>
									</FormInput>
								</div>
								<Types v-model:filter="filter" />
							</div>
							<div>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover"
										role="table"
									>
										<thead>
											<tr role="row">
												<th role="columnheader">Badge</th>
												<th role="columnheader">Fees Earned</th>
												<th role="columnheader">Date</th>
												<th role="columnheader">Telegram</th>
												<th role="columnheader">Transaction</th>
												<th role="columnheader">Actions</th>
											</tr>
										</thead>
										<tbody
											v-if="services.length < 1"
											role="rowgroup"
										>
											<tr>
												<td colspan="7">
													<div class="w-full text-center">
														<h3>No Requests Were Found</h3>
													</div>
												</td>
											</tr>
										</tbody>
										<tbody role="rowgroup">
											<tr
												v-for="tx in services.data"
												:key="tx.presale"
												role="row"
											>
												<td role="cell">
													<div
														v-if="tx.badge == 'SAFE'"
														class="badge space-x-2.5 text-info"
													>
														<div
															class="h-2 w-2 rounded-full bg-current"
														></div>
														<span>{{ tx.badge }}</span>
													</div>
													<div
														v-else-if="tx.badge == 'KYC'"
														class="badge space-x-2.5 text-success"
													>
														<div
															class="h-2 w-2 rounded-full bg-current"
														></div>
														<span>{{ tx.badge }}</span>
													</div>
													<div
														v-else-if="tx.badge == 'FEATURE'"
														class="badge space-x-2.5 text-warning"
													>
														<div
															class="h-2 w-2 rounded-full bg-current"
														></div>
														<span>{{ tx.badge }}</span>
													</div>
													<div
														v-else-if="tx.badge == 'AUDIT'"
														class="badge space-x-2.5 text-error"
													>
														<div
															class="h-2 w-2 rounded-full bg-current"
														></div>
														<span>{{ tx.badge }}</span>
													</div>
													<div
														v-else
														class="badge space-x-2.5 text-slate-800 dark:text-navy-100"
													>
														<div
															class="h-2 w-2 rounded-full bg-current"
														></div>
														<span>{{ tx.badge }}</span>
													</div>
												</td>
												<td role="cell">
													<div
														class="flex flex-row align-middle items-center"
													>
														<img
															:src="chain.logoUrl"
															class="w-5 h-5 rounded-full inline-table mr-3"
														/>
														<span
															class="text-emerald-600 dark:text-emerald-400 font-bold text-xs"
															>{{
																parseFloat(tx.amount).toFixed(6) *
																1
															}}{{
																chain.nativeCurrency.symbol
															}}</span
														>
													</div>
												</td>
												<td role="cell">
													<span>{{ tx.created_at }}</span>
												</td>
												<td role="cell">
													<a
														target="blank"
														class="text-emerald-600 font-semibold hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200"
														:href="'https://t.me/' + tx.telegram"
														>{{ tx.telegram }}</a
													>
												</td>
												<TxHash :txhash="tx.txhash">
													{{ truncateTx(tx.txhash, 16) }}
												</TxHash>
												<td role="cell">
													<a
														:href="
															route('admin.badges.show', {
																bagde: service.uid,
															})
														"
														class="tag rounded-full bg-info text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90"
													>
														View
													</a>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="services.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
