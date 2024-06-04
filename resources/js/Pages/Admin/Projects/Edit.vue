<script setup>
import {Head, Link, useForm} from "@inertiajs/vue3";
import {useI18n} from "vue-i18n";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import Switch from "@/Components/Switch.vue";
import ArrowLeftIcon from "@/Feather/ArrowLeftIcon";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const {t: $t} = useI18n();
const props = defineProps({
	title: {required: false, type: String},
	project: String,
});

const form = useForm(props.project);
const save = () =>
	form
		.transform((data) => ({
			kyc_link: data.kyc_link,
			audit_link: data.audit_link,
			doxx_link: data.doxx_link,
			isReviewed: data.isReviewed ?? false,
			isVerified: data.isVerified ?? false,
			isPromoted: data.isPromoted ?? false,
			rank: data.rank,
		}))
		.put(window.route("admin.projects.update", props.project.uuid));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Edit Project`" />
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Review Project Badges</h3>
							<p>Project is listed on site</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.projects.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
							>
								<ArrowLeftIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to Project list") }}</Link
							>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
								@submit.prevent="save"
								class="container lg:w-4/5"
							>
								<div class="grid md:grid-cols-2 mt-6 gap-4">
									<FormInput
										v-model="form.kyc_link"
										:error="form.errors.kyc_link"
										:label="$t('Project Kyc Link')"
										placeholder="https://"
									>
										<template #help>
											<a
												target="blank"
												:href="project.kyc_link"
												>View Link</a
											>
										</template>
									</FormInput>
									<FormInput
										v-model="form.audit_link"
										:error="form.errors.audit_link"
										:label="$t('Project Audit Link')"
										placeholder="https://"
									>
										<template #help>
											<a
												target="blank"
												:href="project.audit_link"
												>View Link</a
											>
										</template>
									</FormInput>
									<FormInput
										v-model="form.doxx_link"
										:error="form.errors.doxx_link"
										:label="$t('Doxx Link')"
										placeholder="https://"
									>
										<template #help>
											<a
												target="blank"
												:href="project.doxx_link"
												>View Link</a
											>
										</template>
									</FormInput>
									<FormInput
										v-model="form.rank"
										:error="form.errors.rank"
										:label="$t('Project Rank')"
									/>
								</div>
								<div class="grid mt-6 gap-4">
									<Switch v-model="form.isReviewed">
										Submitted Links are Reviewed</Switch
									>
									<Switch v-model="form.isVerified">
										Submitted Links are Verified</Switch
									>
									<Switch v-model="form.isPromoted"> Promote this project</Switch>
								</div>
								<CollapseTransition>
									<div
										v-show="form.isReviewed || form.isVerified"
										class="grid mt-6 gap-4"
									>
										<div class="max-w-xl">
											<label
												class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
												>{{ $t("Review Feedback for owner") }}</label
											>
											<ProjectDescriptionTextArea
												v-model="form.review"
												:placeholder="
													$t('Enter a brief review of the project')
												"
											/>
											<p
												v-if="form.errors?.review"
												class="text-red"
											>
												{{ form.errors?.review }}.
											</p>
											<p v-else>
												{{ $t("Provide a brief review of the project.") }}
											</p>
										</div>
									</div>
								</CollapseTransition>
								<div class="pt-12">
									<div class="flex justify-end">
										<Link
											as="button"
											:href="route('admin.projects.index')"
											type="button"
											class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
										>
											{{ $t("Cancel") }}
										</Link>
										<LoadingButton
											type="submit"
											:busy="form.processing"
											class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
										>
											{{ $t("Update") }} Project
										</LoadingButton>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
