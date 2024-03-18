<script setup>
import {ref} from "vue";

import {Head, router as Inertia, Link, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";

import Address from "@/Components/Address.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import ChainSelect from "@/Components/ChainSelect/ChainSelect.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import PenToolIcon from "@/Feather/PenToolIcon";
import PlusIcon from "@/Feather/PlusIcon";
import Trash2Icon from "@/Feather/Trash2Icon";
import {useUpdateCoins} from "@/hooks/useUpdateCoins";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";
const props = defineProps({
	allCoins: Object,
	coins: Object,
	title: {required: false, type: String},
});

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
// const {chain: currentChain} = useAccount();
const chain = ref();
const deleteCoinForm = useForm({});
const coinBeingDeleted = ref(null);

const deleteCoin = () => {
	deleteCoinForm.delete(window.route("admin.coins.destroy", coinBeingDeleted.value), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => (coinBeingDeleted.value = null),
	});
};
debouncedWatch(
	[search, chain],
	([search, chain]) => {
		Inertia.get(
			window.route("admin.coins.index"),
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

const toggle = (coin) => {
	coin.busy = true;
	Inertia.put(
		window.route("admin.coins.toggle", coin.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => (coin.busy = false),
		},
	);
};
const loading = ref(false);
const updateCoins = useUpdateCoins(props.allCoins);
const updateDecimals = async () => {
	loading.value = true;
	await updateCoins();
	loading.value = false;
};
</script>
<template>
	<Head :title="title ?? 'Coins'" />

	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Accepted Coins") }}</h3>
							<p>{{ $t("These coins are used for launchpad contributions") }}</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<button
								type="button"
								@click="updateDecimals"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<Loading
									v-if="loading"
									class="w-4 h-4 -ml-2 mr-2 inline-block"
								/>
								{{ $t("Update Coin Info") }}
							</button>
							<Link
								type="button"
								:href="route('admin.coins.create')"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<PlusIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Add a Token") }}
							</Link>
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
													{{ $t("coins.name") }}
												</th>
												<th role="columnheader">
													{{ $t("symbol") }}
												</th>
												<th role="columnheader">
													{{ $t("contract") }}
												</th>
												<th role="columnheader">
													{{ $t("decimals") }}
												</th>
												<th role="columnheader">
													{{ $t("active") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="coin in coins.data"
												:key="coin.id"
												role="row"
											>
												<td role="cell">
													<ChainInfo :chainId="coin.chain.chainId" />
												</td>
												<td role="cell">
													<a :href="coin.url">
														{{ coin.name }}
													</a>
												</td>
												<td role="cell">
													<div
														class="flex flex-row align-middle items-center"
													>
														<img
															class="w-5 h-5 mr-2"
															:src="coin.logo_uri"
														/>
														<span>{{ coin.symbol }}</span>
													</div>
												</td>
												<td role="cell">
													<Address
														:address="coin.contract"
														:chainId="coin.chain.chainId"
														>{{
															shortenAddress(coin.contract, 4)
														}}</Address
													>
												</td>

												<td role="cell">
													<span>{{ coin.decimals }}</span>
												</td>
												<td role="cell">
													<label
														class="inline-flex items-center space-x-2"
													>
														<input
															@change="toggle(coin)"
															v-model="coin.inactive"
															class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-error checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox"
														/>
														<span v-if="coin.active">Enabled</span>
														<span v-else>Disabled</span>
													</label>
												</td>
												<td role="cell">
													<div class="flex justify-end text-lg">
														<Link
															:href="
																route('admin.coins.edit', coin.id)
															"
															class="cursor-pointer p-2 hover:text-blue-600"
														>
															<PenToolIcon class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																coinBeingDeleted = coin.id
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
								<Pagination :meta="coins.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<jet-confirmation-modal
			:show="coinBeingDeleted"
			@close="coinBeingDeleted = null"
		>
			<template #title>
				{{ $t("Delete Coin") }}
			</template>

			<template #content>
				{{ $t("Are you sure you would like to delete this Coin?") }}
			</template>

			<template #footer>
				<jet-secondary-button @click="coinBeingDeleted = null">
					{{ $t("Cancel") }}
				</jet-secondary-button>

				<jet-danger-button
					class="ml-2"
					@click="deleteCoin"
					:class="{'opacity-25': deleteCoinForm.processing}"
					:disabled="deleteCoinForm.processing"
				>
					{{ $t("Delete") }}
				</jet-danger-button>
			</template>
		</jet-confirmation-modal>
	</AdminLayout>
</template>
