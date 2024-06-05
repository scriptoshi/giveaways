<script setup>
import {ref} from "vue";

import {router as Inertia} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";

import Pagination from "@/Components/Pagination.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Address from "@/Pages/Admin/AccountAddress.vue";

defineProps({
	users: Array,
	title: {required: false, type: String},
});

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");

debouncedWatch(
	[search],
	([search]) => {
		Inertia.get(
			window.route("admin.users.index"),
			{search},
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
</script>
<template>
	<Head :title="title ?? Users" />
	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Users") }}</h3>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center justify-between mb-4 px-6">
								<div></div>
								<SearchInput
									class="w-1/2 max-w-md"
									v-model="search"
								/>
							</div>
							<div>
								<div>
									<div class="overflow-x-auto">
										<table
											class="table-default table-hover"
											role="table"
										>
											<thead>
												<tr role="row">
													<th role="columnheader">
														{{ $t("Username") }}
													</th>
													<th role="columnheader">
														{{ $t("Accounts") }}
													</th>
													<th role="columnheader">
														{{ $t("Email") }}
													</th>
													<th role="columnheader">
														{{ $t("verified") }}
													</th>
													<th role="columnheader text-right">
														{{ $t("Joined") }}
													</th>
												</tr>
											</thead>
											<tbody role="rowgroup">
												<tr
													v-for="user in users.data"
													:key="user.id"
													role="row"
												>
													<td role="cell">
														<div
															class="flex items-center space-x-2 align-middle"
														>
															<img
																:src="user.profile_photo_url"
																class="w-6 h-6 rounded-full"
															/>
															<span class="font-semibold">{{
																user.name
															}}</span>
														</div>
													</td>

													<td role="cell">
														<Address
															class="block"
															v-for="acc in user.accounts"
															:key="acc.addresss"
															:address="acc.address"
														/>
													</td>
													<td role="cell">
														<span> {{ user.email }}</span>
													</td>

													<td role="cell">
														<span> {{ user.verified_at }}</span>
													</td>
													<td role="cell">
														<span> {{ user.created_at }}</span>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<Pagination :meta="users.meta" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
