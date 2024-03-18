<script setup>
import {ref} from "vue";

import {router as Inertia, Link, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {useAccount} from "use-wagmi";

import Address from "@/Components/Address.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import PenToolIcon from "@/Feather/PenToolIcon";
import PlusIcon from "@/Feather/PlusIcon";
import Trash2Icon from "@/Feather/Trash2Icon";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
defineProps({
	amms: Array,
	title: {required: false, type: String},
});

const params = useUrlSearchParams("history");
const search = ref(params.search);
const {chain} = useAccount();
const deleteAmmForm = useForm({});
const ammBeingDeleted = ref(null);
const deleteAmm = () => {
	deleteAmmForm.delete(window.route("admin.amms.destroy", ammBeingDeleted.value), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => (ammBeingDeleted.value = null),
	});
};
debouncedWatch(
	[search, chain],
	([search, chain]) => {
		Inertia.get(
			window.route("admin.amms.index"),
			{search, chain: chain?.chainId},
			{
				preserveState: true,
				preserveScroll: true,
			},
		);
	},
	{
		maxWait: 500,
	},
);

const toggle = (amm) => {
	amm.busy = true;
	Inertia.put(
		window.route("admin.amms.toggle", amm.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => (amm.busy = false),
		},
	);
};
</script>
<template>
	<Head :title="title ?? Amms" />
	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Amm Exchanges</h3>
							<p>This info is used for launchpads</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								type="button"
								:href="route('admin.amms.create')"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<PlusIcon class="w-4 h-4 -ml-2 mr-2 inline-block" /> Add an Exchange
							</Link>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0">
							<div class="lg:flex items-center justify-between mb-4 px-6">
								<h3 class="mb-4 lg:mb-0">
									<slot />
								</h3>
								<div
									class="sm:flex space-y-3 sm:space-x-3 sm:space-y-0 w-full lg:w-2/3"
								>
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
							<div class="overflow-x-auto">
								<table
									class="table-default table-hover"
									role="table"
								>
									<thead>
										<tr role="row">
											<th role="columnheader">Chain</th>
											<th role="columnheader">Name</th>
											<th role="columnheader">Router</th>
											<th role="columnheader">Factory</th>
											<th role="columnheader">Status</th>
											<th role="columnheader">Actions</th>
										</tr>
									</thead>
									<tbody role="rowgroup">
										<template
											v-for="amm in amms.data"
											:key="amm.id"
										>
											<tr
												v-if="amm.chainId"
												role="row"
											>
												<td role="cell">
													<ChainInfo :chainId="amm.chainId" />
												</td>
												<td role="cell">
													<a :href="amm.url">{{ amm.name }}</a>
												</td>
												<td role="cell">
													<Address
														:address="amm.router"
														:chainId="amm.chainId"
														:chars="6"
													/>
												</td>
												<td role="cell">
													<Address
														:address="amm.factory"
														:chainId="amm.chainId"
														:chars="6"
													/>
												</td>

												<td role="cell">
													<label
														class="inline-flex items-center space-x-2"
													>
														<input
															@change="toggle(amm)"
															v-model="amm.inactive"
															class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-error checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox"
														/>
														<span v-if="amm.status">Enabled</span>
														<span v-else>Disabled</span>
														<Loading
															class="!w-4 !h-4"
															v-if="amm.busy"
														/>
													</label>
												</td>
												<td role="cell">
													<div class="flex justify-end text-lg">
														<Link
															:href="route('admin.amms.edit', amm.id)"
															class="cursor-pointer p-2 hover:text-blue-600"
														>
															<PenToolIcon class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																ammBeingDeleted = amm.id
															"
															class="cursor-pointer p-2 hover:text-red-500"
														>
															<Trash2Icon class="w-4 h-4" />
														</a>
													</div>
												</td>
											</tr>
										</template>
									</tbody>
								</table>
							</div>
							<Pagination :meta="amms.meta" />
						</div>
					</div>
				</div>
			</div>
		</main>
		<jet-confirmation-modal
			:show="ammBeingDeleted"
			@close="ammBeingDeleted = null"
		>
			<template #title>
				{{ $t("Delete Amm Exchange") }}
			</template>

			<template #content>
				{{ $t("Are you sure you would like to delete this Amm exchange?") }}
			</template>

			<template #footer>
				<jet-secondary-button @click="ammBeingDeleted = null">
					{{ $t("Cancel") }}
				</jet-secondary-button>

				<jet-danger-button
					class="ml-2"
					@click="deleteAmm"
					:class="{'opacity-25': deleteAmmForm.processing}"
					:disabled="deleteAmmForm.processing"
				>
					{{ $t("Delete") }}
				</jet-danger-button>
			</template>
		</jet-confirmation-modal>
	</AdminLayout>
</template>
