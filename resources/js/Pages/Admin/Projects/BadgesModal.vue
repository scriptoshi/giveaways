<script setup>
import {ref} from "vue";

import {ArrowRightIcon} from "@heroicons/vue/24/outline";
import {useForm} from "@inertiajs/vue3";
import {onClickOutside} from "@vueuse/core";

import Loading from "@/Components/Loading.vue";
import XIcon from "@/Feather/XIcon";
const props = defineProps({
	project: Object,
});

const emit = defineEmits(["close"]);
const close = () => emit("close");
const outside = ref(null);
onClickOutside(outside, close);
const form = useForm({
	badges: [...(props.project.badges?.map((b) => b.id) ?? [])],
});
const saveForm = async () => {
	form.put(window.route("admin.projects.badges", {project: props.project.uuid}));
};
</script>
<template>
	<div>
		<teleport to="body">
			<div
				tabindex="-1"
				class="overflow-y-auto overflow-x-hidden z-50 fixed top-0 right-0 left-0 w-full md:inset-0 h-modal md:h-full justify-center items-center flex"
				aria-modal="true"
				role="dialog"
			>
				<div
					ref="outside"
					class="relative p-4 w-full max-w-xl h-full md:h-auto"
				>
					<!-- Modal content -->
					<div class="relative bg-white rounded-sm shadow dark:bg-gray-700">
						<!-- Modal header -->
						<div class="flex justify-between items-center p-5 rounded-t">
							<h3 class="text-xl font-medium text-gray-900 dark:text-white">
								{{ $t("Allocate Badges") }}
							</h3>
							<button
								type="button"
								@click="close"
								class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-sm text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
							>
								<XIcon class="w-5 h-5" />
							</button>
						</div>
						<!-- Modal body -->
						<div class="px-4 py-8 pt-0 overflow-x-auto">
							<div class="gap-3 mx-auto grid sm:grid-cols-2">
								<div
									v-for="badge in $page.props.badges"
									:key="badge.id"
									class="flex items-center"
								>
									<span
										v-if="badge.badge == 'SAFU'"
										class="tag rounded-full border border-emerald-500 bg-white dark:bg-gray-800 text-emerald-500 hover:bg-emerald-500/20 focus:bg-emerald-500/20 active:bg-emerald-500/25"
									>
										{{ badge.badge }}
									</span>
									<span
										v-else-if="badge.badge == 'KYC'"
										class="tag rounded-full border border-success bg-white dark:bg-gray-800 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
									>
										{{ badge.badge }}
									</span>
									<span
										v-else-if="badge.badge == 'FEATURE'"
										class="tag rounded-full border border-warning bg-white dark:bg-gray-800 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
									>
										{{ badge.badge }}
									</span>

									<span
										v-else-if="badge.badge == 'AUDIT'"
										class="tag rounded-full border border-error bg-white dark:bg-gray-800 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
									>
										{{ badge.badge }}
									</span>

									<span
										v-else
										class="tag border rounded-full border-purple-600 bg-white dark:bg-gray-800 text-purple-600 hover:bg-purple-600/10 focus:bg-purple-600/10 active:bg-purple-600/10"
									>
										{{ badge.badge }}
									</span>
									<label class="inline-flex ml-4 items-center space-x-2">
										<input
											v-model="form.badges"
											:value="badge.id"
											class="form-checkbox is-basic w-5 h-5 rounded border-slate-400/70 checked:bg-success checked:!border-success hover:!border-success focus:!border-success dark:border-navy-400"
											type="checkbox"
										/>
										<p>Enable</p>
									</label>
								</div>
							</div>
						</div>
						<!-- Modal footer -->
						<div class="p-4 rounded-b">
							<div class="flex items-center justify-end space-x-2">
								<button
									type="button"
									@click="close"
									class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-sm border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
								>
									{{ $t("Cancel") }}
								</button>
								<button
									type="button"
									:disabled="form.processing"
									@click="saveForm"
									class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
								>
									<Loading
										v-if="form.processing"
										class="mr-2 -ml-1 inline-flex w-5 h-5"
									/>
									<ArrowRightIcon
										v-else
										class="w-5 h-5 -ml-1 mr-2 inline-flex"
									/>
									{{ form.processing ? $t("Updating...") : $t("Save Badges") }}
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</teleport>
		<div class="bg-gray-900 opacity-50 dark:opacity-80 fixed inset-0 z-40"></div>
	</div>
</template>
