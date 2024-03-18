<script setup>
import {ref} from "vue";

import {Head, router as Inertia, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiTrash} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";

import Address from "@/Components/Address.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import Pagination from "@/Components/Pagination.vue";
import Switch from "@/Components/Switch.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import VueIcon from "@/Components/VueIcon.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import DangerButton from "@/Jetstream/DangerButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import CreateButton from "@/Pages/Admin/Factories/CreateButton.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";

const {chainId} = useAccount();
defineProps({
	factories: [Array, Object],
	counts: [Array, Object],
});
const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const chain = ref(params.chain);
const deleteFactoryForm = useForm({});
const factoryBeingDeleted = ref();
const deleteFactory = () => {
	deleteFactoryForm.delete(window.route("admin.factories.destroy", factoryBeingDeleted.value), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => (factoryBeingDeleted.value = null),
	});
};
debouncedWatch(
	[search, chain],
	([search, chain]) => {
		Inertia.get(
			window.route("admin.factories.index"),
			{...(search ? {search} : {}), ...(chain ? {chain: chain?.id} : {})},
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

const toggle = (factory) => {
	factory.busy = true;
	Inertia.put(
		window.route("admin.factories.toggle", factory.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => (factory.busy = false),
		},
	);
};
</script>
<template>
	<AdminLayout>
		<Head title="Manage Contract Factories" />
		<main class="min-h-screen container">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex mt-8 items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="text-4xl font[Walsheim-Light]">
								{{ $t("Manage Factories") }}
							</h3>
							<p>{{ $t("Factories are used to deploy contracts") }}</p>
						</div>
						<div class="flex gap-x-3 sm:w-2/3">
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
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center justify-between px-5 mb-4">
								<div></div>
								<CreateButton :counts="counts[chainId] ?? []" />
							</div>
							<div>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover w-full table-auto"
										role="table"
									>
										<thead>
											<tr role="row">
												<th class="text-left px-2 py-4">
													{{ $t("Chain") }}
												</th>
												<th class="text-left px-2 py-4">
													{{ $t("Factory") }}
												</th>
												<th class="text-left px-2 py-4">
													{{ $t("Contract") }}
												</th>
												<th class="text-left px-2 py-4">
													{{ $t("Version") }}
												</th>
												<th class="text-left px-2 py-4">
													{{ $t("Active") }}
												</th>
												<td class="text-right px-2 py-4">
													{{ $t("Actions") }}
												</td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="factory in factories.data"
												:key="factory.id"
												role="row"
											>
												<td
													class="py-4"
													role="cell"
												>
													<ChainInfo :chainId="factory.chainId" />
												</td>
												<td role="cell">{{ factory.name }} Factory</td>
												<td role="cell">
													<Address
														v-if="factory.chainId"
														:address="factory.contract"
														:chainId="factory.chainId"
														>{{
															shortenAddress(factory.contract, 6)
														}}</Address
													>
												</td>
												<td role="cell">
													<span>{{ factory.version }}</span>
												</td>
												<td role="cell">
													<Switch
														@updateModelValue="toggle(factory)"
														:modelValue="factory.active"
														xs
														><span v-if="factory.active">Enabled</span>
														<span v-else>Disabled</span>
													</Switch>
												</td>
												<td role="cell">
													<div class="flex justify-end space-x-2">
														<PrimaryButton
															v-if="
																chainId && factory.type == 'Badge'
															"
															link
															primary
															class="!py-1 text-xs self-center !px-2"
															:href="
																route('admin.badges.index', {
																	chainId,
																})
															"
														>
															Manage
														</PrimaryButton>
														<PrimaryButton
															v-else
															link
															primary
															class="!py-1 text-xs self-center !px-2"
															:href="
																route(
																	'admin.factories.show',
																	factory.id,
																)
															"
														>
															Manage
														</PrimaryButton>
														<PrimaryButton
															href="#"
															error
															@click.prevent="
																factoryBeingDeleted = factory.id
															"
															class="!p-1 text-xs self-center cursor-pointer"
														>
															<VueIcon
																:icon="HiTrash"
																class="w-4 h-4"
															/>
														</PrimaryButton>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination
									class="mt-6"
									:meta="factories.meta"
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<jet-confirmation-modal
			:show="!!factoryBeingDeleted"
			@close="factoryBeingDeleted = null"
		>
			<template #title>
				{{ $t("Delete Factory") }}
			</template>
			<template #content>
				Are you sure you would like to delete this Factory?. Deploying contracts using this
				factory will cease!
			</template>
			<template #footer>
				<button @click="factoryBeingDeleted = null">
					<button
						class="btn space-x-1 px-3 text-gray-600 dark:text-gray-200 rounded-sm flex items-center bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700"
					>
						{{ $t("Cancel") }}
					</button>
				</button>

				<button
					class="ml-2"
					@click="deleteFactory"
					:class="{'opacity-25': deleteFactoryForm.processing}"
					:disabled="deleteFactoryForm.processing"
				>
					<DangerButton :disabled="deleteFactoryForm.processing">{{
						$t("Delete")
					}}</DangerButton>
				</button>
			</template>
		</jet-confirmation-modal>
	</AdminLayout>
</template>
