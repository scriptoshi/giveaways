<!-- eslint-disable vue/no-dupe-keys -->
<script setup>
import {ref} from "vue";

import {Head, router, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {HiPencil, HiSolidPlusSm, HiTrash} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";

import OutLineButton from "@/Components/Buttons/OutLineButton.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import Switch from "@/Components/Switch.vue";
import SearchInput from "@/Components/TableSearchInput.vue";
import VueIcon from "@/Components/VueIcon.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import DangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/JetButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";

defineProps({
	badges: Object,
	title: {required: false, type: String},
});
const {chainId} = useAccount();
const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const deleteBadgeForm = useForm({});
const badgeBeingDeleted = ref();
const deleteBadge = () => {
	deleteBadgeForm.delete(window.route("admin.badges.destroy", badgeBeingDeleted.value), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => (badgeBeingDeleted.value = null),
	});
};
debouncedWatch(
	[search],
	([search]) => {
		router.get(
			window.route("admin.badges.index", {chainId: chainId.value}),
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
const toggle = (badge) => {
	badge.busy = true;
	router.put(
		window.route("admin.badges.toggle", badge.id),
		{},
		{
			preserveScroll: true,
			preserveState: true,
			onFinish: () => (badge.busy = false),
		},
	);
};
</script>
<template>
	<Head :title="title ?? 'Badges'" />
	<AdminLayout>
		<main class="min-h-screen container">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col h-full">
					<div class="lg:flex mt-6 items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="text-3xl">{{ $t("Badges Manager") }}</h3>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								type="button"
								:href="route('admin.badges.create')"
								link
								primary
							>
								<VueIcon
									:icon="HiSolidPlusSm"
									class="w-5 h-5 -ml-1 mr-2 inline-block"
								/>
								{{ $t("Add Badge") }}
							</PrimaryButton>
						</div>
					</div>
					<div class="card my-6 h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center justify-between m-4 px-6">
								<div class="flex w-1/2 space-x-3 mb-6 items-center">
									<SearchInput
										class="ml-auto w-2/3"
										v-model="search"
									/>
								</div>
							</div>
							<div>
								<div>
									<div class="overflow-x-auto">
										<table class="w-full table-auto table-default table-hover">
											<tbody role="rowgroup">
												<tr
													v-for="badge in badges.data"
													:key="badge.id"
													role="row"
												>
													<td role="cell">
														<span
															v-if="badge.badge == 'SAFU'"
															class="tag rounded-full border border-emerald-500 bg-white text-emerald-500 hover:bg-emerald-500/20 focus:bg-emerald-500/20 active:bg-emerald-500/25"
														>
															{{ badge.badge }}
														</span>
														<span
															v-else-if="badge.badge == 'KYC'"
															class="tag rounded-full border border-success bg-white text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
														>
															{{ badge.badge }}
														</span>
														<span
															v-else-if="badge.badge == 'FEATURE'"
															class="tag rounded-full border border-warning bg-white text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
														>
															{{ badge.badge }}
														</span>

														<span
															v-else-if="badge.badge == 'AUDIT'"
															class="tag rounded-full border border-error bg-white text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
														>
															{{ badge.badge }}
														</span>

														<span
															v-else
															class="tag border rounded-full border-purple-600 bg-white text-purple-600 hover:bg-purple-600/10 focus:bg-purple-600/10 active:bg-purple-600/10"
														>
															{{ badge.badge }}
														</span>
													</td>
													<td role="cell">
														<span class="font-semibold"
															>{{ badge.price }}
															{{
																chains?.nativeCurrency?.symbol
															}}</span
														>
													</td>
													<td role="cell">
														<span> {{ badge.description }}</span>
													</td>
													<td role="cell">
														<Switch
															@update:modelValue="toggle(badge)"
															:modelValue="badge.active"
														>
															<span v-if="badge.active">Enabled</span>
															<span v-else>Disabled</span>
														</Switch>
													</td>
													<td role="cell">
														<div
															class="flex justify-end space-x-2 text-lg"
														></div>
													</td>
													<td role="cell">
														<div
															class="flex space-x-2 justify-end text-lg"
														>
															<PrimaryButton
																as="a"
																primary
																link
																class="text-sm !py-1 !px-2 self-center"
																:href="
																	route('admin.badges.index', {
																		search: badge.badge,
																	})
																"
															>
																Manage
															</PrimaryButton>
															<PrimaryButton
																as="a"
																class="!p-1"
																primary
																link
																:href="
																	route(
																		'admin.badges.edit',
																		badge.id,
																	)
																"
															>
																<VueIcon
																	:icon="HiPencil"
																	class="w-4 h-4"
																/>
															</PrimaryButton>
															<OutLineButton
																@click.prevent="
																	badgeBeingDeleted = badge.id
																"
																class="cursor-pointer hover:text-red-500 !p-1"
																error
															>
																<VueIcon
																	:icon="HiTrash"
																	class="w-4 h-4"
																/>
															</OutLineButton>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<Pagination :meta="badges.meta" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<jet-confirmation-modal
			:show="badgeBeingDeleted"
			@close="badgeBeingDeleted = null"
		>
			<template #title>
				{{ $t("Delete Badge") }}
			</template>

			<template #content>
				{{ $t("Are you sure you would like to delete this Badge?") }}
			</template>

			<template #footer>
				<JetButton @click="badgeBeingDeleted = null">{{ $t("Cancel") }}</JetButton>

				<DangerButton
					class="ml-2"
					@click="deleteBadge"
					:class="{'opacity-25': deleteBadgeForm.processing}"
					:disabled="deleteBadgeForm.processing"
				>
					{{ $t("Delete") }}
				</DangerButton>
			</template>
		</jet-confirmation-modal>
	</AdminLayout>
</template>
