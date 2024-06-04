<script setup>
import {ref} from "vue";

import {Head, router as Inertia, Link} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiSolidPlus, HiX} from "oh-vue-icons/icons";

import Pagination from "@/Components/Pagination.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import PenToolIcon from "@/Feather/PenToolIcon.js";
import Trash2Icon from "@/Feather/Trash2Icon";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import BadgesModal from "@/Pages/Admin/Projects/BadgesModal.vue";
import DelistModal from "@/Pages/Admin/Projects/DelistModal.vue";
import ProjectChain from "@/Pages/Admin/Projects/ProjectChain.vue";
import ChainSelect from "@/Pages/Projects/Create/ChainSelect/ChainSelect.vue";
import {shortenAddress, truncateTx} from "@/Wagmi/utils/utils";
defineProps({
	projects: Object,
	title: {required: false, type: String},
});

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
// const {chain: currentChain} = useAccount();
const chain = ref();
const projectBeingDeleted = ref(null);
const projectBeingEdited = ref(null);

debouncedWatch(
	[search, chain],
	([search, chain]) => {
		Inertia.get(
			window.route("admin.projects.index"),
			{search, chain: chain?.id},
			{
				preserveState: true,
				preserveScroll: true,
			},
		);
	},
	{
		maxWait: 700,
	},
);

const toggle = (project) => {
	project.busy = true;
	Inertia.put(
		window.route("admin.projects.toggle", project.uuid),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => (project.busy = false),
		},
	);
};
</script>
<template>
	<Head :title="title ?? 'projects'" />

	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-start mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Listed projects") }}</h3>
							<p>{{ $t("These projects have been listed by users") }}</p>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center justify-between mb-4 px-6">
								<h3 class="mb-4 lg:mb-0">
									<slot />
								</h3>
								<div class="flex gap-x-3 w-1/2">
									<ChainSelect
										class="w-full"
										v-model="chain"
									/>
									<SearchInput
										class="max-w-md"
										v-model="search"
									/>
								</div>
							</div>
							<div>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover"
										role="table"
									>
										<thead>
											<tr role="row">
												<th role="columnheader">
													{{ $t("Chain") }}
												</th>
												<th role="columnheader">
													{{ $t("Name") }}
												</th>
												<th role="columnheader">
													{{ $t("Token") }}
												</th>
												<th role="columnheader">
													{{ $t("contract") }}
												</th>

												<th role="columnheader">
													{{ $t("Listed") }}
												</th>
												<th role="columnheader">
													{{ $t("Badges") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="project in projects.data"
												:key="project.id"
												role="row"
											>
												<td role="cell">
													<div
														class="flex flex-row align-middle items-center"
													>
														<ProjectChain
															:chainId="project.chainId"
															v-slot="{chain}"
														>
															<img
																class="w-5 h-5 rounded-full inline-table mr-3"
																:src="chain.logo"
															/>
															<span
																class="text-emerald-600 dark:text-emerald-400 font-bold text-xs"
																>{{ chain.name }}</span
															>
														</ProjectChain>
													</div>
												</td>
												<td role="cell">
													<a :href="project.url">
														{{ truncateTx(project.name, 18) }}
													</a>
												</td>
												<td role="cell">
													<div
														class="flex flex-row align-middle items-center"
													>
														<img
															class="w-5 h-5 mr-2"
															:src="project.logo?.url"
														/>
														<span>{{ project.symbol }}</span>
													</div>
												</td>
												<td role="cell">
													<WeCopy
														:text="project.address"
														after
														>{{
															shortenAddress(project.address, 4)
														}}</WeCopy
													>
												</td>

												<td role="cell">
													<div
														v-if="project.is_force_failed"
														class="flex items-center"
													>
														<VueIcon
															:icon="HiX"
															class="mr-3 text-red"
														/>
														<h3 class="text-xs text-red">
															{{ $t("Force Failed") }}
														</h3>
													</div>
													<label
														v-else
														class="inline-flex items-center space-x-2"
													>
														<input
															@change="toggle(project)"
															v-model="project.inactive"
															class="form-switch h-5 w-10 rounded-full bg-gray-300 before:rounded-full before:bg-gray-50 checked:!bg-error checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox"
														/>
														<span v-if="project.active">Listed</span>
														<span v-else>Delisted</span>
													</label>
												</td>
												<td role="cell">
													<div class="flex items-center space-x-1">
														<div
															v-for="badge in project?.badges"
															:key="badge.id"
														>
															<span
																v-if="badge.badge === 'KYC'"
																class="text-xs px-2 py-1 rounded-sm bg-green-600 text-white font-semibold"
															>
																KYC
															</span>
															<span
																v-else-if="badge.badge === 'AUDIT'"
																class="text-xs px-2 py-1 rounded-sm bg-yellow-600 text-white font-semibold"
															>
																AUDIT
															</span>
															<span
																v-else-if="badge.badge === 'SAFU'"
																class="text-xs px-2 py-1 rounded-sm bg-emerald-600 text-white font-semibold"
															>
																SAFU
															</span>
															<span
																v-else-if="
																	badge.badge === 'FEATURE'
																"
																class="text-xs px-2 py-1 rounded-sm bg-gray-500 text-white font-semibold"
															>
																{{ badge.badge }}
															</span>
															<span
																v-else
																class="text-xs px-2 py-1 rounded-sm bg-purple-600 text-white font-semibold"
															>
																{{ badge.badge }}
															</span>
														</div>
														<a
															href="#"
															@click.prevent="
																projectBeingEdited = project
															"
															class="btn h-7 w-7 rounded-full bg-gray-150 p-0 font-medium text-gray-800 hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
														>
															<VueIcon
																:icon="HiSolidPlus"
																class="w-4 h-4"
															/>
														</a>
													</div>
												</td>
												<td role="cell">
													<div class="flex space-x-3 justify-end text-lg">
														<Link
															:href="
																route('admin.projects.edit', {
																	project: project.uuid,
																})
															"
															:class="{
																'text-green-500':
																	project.kyc_link ||
																	project.audit_link ||
																	project.doxx_link,
															}"
															class="cursor-pointer p-2 disabled:pointer-events-none"
														>
															<PenToolIcon class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																projectBeingDeleted = project
															"
															class="cursor-pointer p-2 hover:text-red-500 disabled:pointer-events-none"
														>
															<Trash2Icon class="w-4 h-4" />
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="projects.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<DelistModal
			@close="projectBeingDeleted = null"
			:project="projectBeingDeleted"
			v-if="projectBeingDeleted"
		/>
		<BadgesModal
			:project="projectBeingEdited"
			v-if="projectBeingEdited"
			@close="projectBeingEdited = null"
		/>
	</AdminLayout>
</template>
