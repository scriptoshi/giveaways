<script setup>
import {ref} from "vue";

import {Head, router as Inertia, Link, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";

import ChainInfo from "@/Components/ChainInfo.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import PenToolIcon from "@/Feather/PenToolIcon";
import Trash2Icon from "@/Feather/Trash2Icon";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {truncateTx} from "@/Wagmi/utils/utils";
defineProps({
	chainList: Object,
	title: {required: false, type: String},
});

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const deleteChainForm = useForm({});
const chainBeingDeleted = ref(null);

const deleteChain = () => {
	deleteChainForm.delete(window.route("admin.chains.destroy", chainBeingDeleted.value?.id), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => (chainBeingDeleted.value = null),
	});
};
debouncedWatch(
	[search],
	([search]) => {
		Inertia.get(
			window.route("admin.chains.index"),
			{search},
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

const toggle = (chain) => {
	chain.busy = true;
	Inertia.put(
		window.route("admin.chains.toggle", chain.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => {
				chain.busy = false;
				chainBeingDeleted.value = null;
			},
		},
	);
};
</script>
<template>
	<Head :title="title ?? 'Chains'" />
	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Accepted Chains") }}</h3>
							<p>{{ $t("Available Chains on the Launchpad") }}</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<button
								type="button"
								@click="updateChains"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<Loading
									v-if="loading"
									class="w-4 h-4 -ml-2 mr-2 inline-block"
								/>
								{{ $t("Check for New Chains") }}
							</button>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center justify-between mb-4 px-6">
								<div class="flex gap-x-3 sm:w-1/2 lg:w-1/4">
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
													{{ $t("ChainId") }}
												</th>
												<th role="columnheader">
													{{ $t("explorer") }}
												</th>
												<th role="columnheader">
													{{ $t("wss rpcs") }}
												</th>
												<th role="columnheader">
													{{ $t("rpcs") }}
												</th>
												<th role="columnheader">
													{{ $t("status") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="chain in chainList.data"
												:key="chain.id"
												role="row"
											>
												<td role="cell">
													<ChainInfo :chainId="chain.chainId" />
												</td>

												<td role="cell">
													<span>{{ chain.chainId }}</span>
												</td>
												<td role="cell">
													<a
														:href="chain.explorer"
														target="_blank"
														:title="chain.explorer"
														class="cursor-pointer select-none font-semibold text-blue-600 dark:text-blue-500 dark:hover:text-blue-300 hover:text-blue-800"
														>{{ truncateTx(chain.explorer, 14) }}</a
													>
												</td>

												<td role="cell">
													<span>{{ chain.websockets.length }}</span>
												</td>
												<td role="cell">
													<span>{{ chain.https.length }}</span>
												</td>
												<td role="cell">
													<label
														class="inline-flex items-center space-x-2"
													>
														<input
															@change="toggle(chain)"
															v-model="chain.inactive"
															class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-error checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox"
														/>
														<span v-if="chain.active">Enabled</span>
														<span v-else>Disabled</span>
													</label>
												</td>
												<td role="cell">
													<div class="flex justify-end text-lg">
														<Link
															:href="
																route('admin.chains.edit', chain.id)
															"
															class="cursor-pointer p-2 hover:text-blue-600"
														>
															<PenToolIcon class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																chainBeingDeleted = chain
															"
															class="cursor-pointer p-2 hover:text-red-500"
														>
															<Trash2Icon class="w-4 h-4" />
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="chainList.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<jet-confirmation-modal
			:show="chainBeingDeleted"
			@close="chainBeingDeleted = null"
		>
			<template #title>
				{{
					$t("Are you sure about deleting {chain} ?", {
						chain: chainBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This may lead to undesired consquences especially if factories and contracts have already been deployed",
						)
					}}
				</p>
				<p>{{ $t("Its Recommended to Disable the chain Instead") }}</p>
			</template>

			<template #footer>
				<jet-secondary-button @click="chainBeingDeleted = null">
					{{ $t("Cancel") }}
				</jet-secondary-button>

				<jet-secondary-button
					class="ml-2"
					v-if="chainBeingDeleted.active"
					@click="toggle(chainBeingDeleted)"
				>
					<Loading v-if="chainBeingDeleted.busy" />
					{{ $t("Disable") }}
				</jet-secondary-button>

				<jet-danger-button
					class="ml-2"
					@click="deleteChain"
					:class="{'opacity-25': deleteChainForm.processing}"
					:disabled="deleteChainForm.processing"
				>
					{{ $t("Delete") }}
				</jet-danger-button>
			</template>
		</jet-confirmation-modal>
	</AdminLayout>
</template>
