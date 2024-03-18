<script></script>
<script setup>
import {ref} from "vue";

import {router as Inertia, Link} from "@inertiajs/vue3";
import {debouncedWatch} from "@vueuse/core";
import {useI18n} from "vue-i18n";

import SearchInput from "@/Components/TableSearchInput.vue";
import PlusIcon from "@/Feather/PlusIcon";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const {t: $t} = useI18n();
defineProps({
	leaderboards: Array,
	title: {required: false, type: String},
});
const search = ref("");
debouncedWatch(
	search,
	() => {
		Inertia.get(
			window.route("admin.leaderboards.index"),
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
</script>
<template>
	<Head :title="title ?? Leaderboards" />
	<AdminLayout>
		<div class="mb-6 flex justify-between items-center">
			<div class="flex items-center w-full max-w-md mr-4">
				<SearchInput v-model="search" />
			</div>
			<Link
				type="button"
				as="button"
				:href="route('admin.leaderboards.create')"
				class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
			>
				<PlusIcon class="-ml-1 mr-2 h-5 w-5" /> Create
			</Link>
		</div>
		<div class="flex flex-col">
			<div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
				<div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
					<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-sm">
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("leaderboards.avatar") }}
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("leaderboards.username") }}
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("leaderboards.address") }}
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("leaderboards.rank") }}
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("leaderboards.points") }}
									</th>

									<td
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("Edit") }}
									</td>
									<td
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										{{ $t("Delete") }}
									</td>
								</tr>
							</thead>
							<tbody
								class="bg-white divide-y divide-gray-200"
								x-max="1"
							>
								<tr
									v-for="leaderboard in leaderboards.data"
									:key="leaderboard.id"
								>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										{{ leaderboard.avatar }}
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										{{ leaderboard.username }}
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										{{ leaderboard.address }}
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										{{ leaderboard.rank }}
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										{{ leaderboard.points }}
									</td>

									<td
										class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
									>
										<a
											:href="route('admin.leaderboards.edit')"
											class="text-indigo-600 hover:text-indigo-900"
											>{{ $t("Edit") }}</a
										>
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
									>
										<Link
											method="delete"
											as="button"
											:href="
												route('admin.leaderboards.delete', leaderboard.id)
											"
											class="text-red-400 hover:text-red-600 mx-auto"
											><TrashCanIcon
												class="w-6 h6 text-red-400 hover:text-red-600"
										/></Link>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<Pagination :v-model="leaderboards.links" />
	</AdminLayout>
</template>
