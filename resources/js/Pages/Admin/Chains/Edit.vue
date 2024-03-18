<script setup>
import {computed} from "vue";

import {Head, Link, useForm} from "@inertiajs/vue3";
import {useChains} from "use-wagmi";
import {useI18n} from "vue-i18n";

import FormInput from "@/Components/FormInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import ArrowLeftIcon from "@/Feather/ArrowLeftIcon";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const {t: $t} = useI18n();
const props = defineProps({
	title: {required: false, type: String},
	chainModel: Object,
});
const chains = useChains();
const form = useForm(props.chainModel);
form.chain = chains.value.find((c) => c.id === form.chainId);
const save = () => form.put(window.route("admin.chains.update", form.id));
const https = computed({
	get: () => form.https?.join("\n"),
	set: (val) => (form.https = val.replaceAll(" ", "").split("\n")),
});

const websockets = computed({
	get: () => form.websockets?.join("\n"),
	set: (val) => (form.websockets = val.split("\n")),
});
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Add New Chain`" />
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Edit Launchpad Chains</h3>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.chains.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
							>
								<ArrowLeftIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to Chain list") }}</Link
							>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
								@submit.prevent="save"
								class="container lg:w-4/5"
							>
								<div class="grid md:grid-cols-2 mt-6 gap-x-4">
									<FormInput
										v-model="form.name"
										:label="$t('Name of the Chain')"
										disabled
									/>
									<div>
										<FormInput
											v-model="form.chainId"
											:label="$t('Chain ID')"
											class="max-w-xs"
											disabled
										/>
									</div>
								</div>
								<div class="grid md:grid-cols-3 mt-6 gap-x-4">
									<FormInput
										v-model="form.explorer"
										:error="form.errors.explorer"
										class="col-span-2"
										:label="$t('Chain Explorer')"
									/>
								</div>
								<div class="grid md:grid-cols-2 mt-6 gap-x-4">
									<div>
										<label
											for="name"
											class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
											>Http Rpcs</label
										>
										<ProjectDescriptionTextArea
											v-model="https"
											:emoji="false"
										/>
										<span
											v-if="form.errors.https"
											class="text-red"
											>{{ form.errors.https }}</span
										>
										<p v-else>{{ $t("Enter Each RPC in a new line") }}</p>
									</div>
									<div>
										<label
											for="name"
											class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
											>Websocket Rpcs</label
										>
										<ProjectDescriptionTextArea
											v-model="websockets"
											:emoji="false"
										/>
										<span
											v-if="form.errors.websockets"
											class="text-red"
											>{{ form.errors.websockets }}</span
										>
										<p v-else>
											{{ $t("Enter Each Websocket URL in a new line") }}
										</p>
									</div>
								</div>
								<div class="pt-12">
									<div class="flex justify-end">
										<Link
											as="button"
											:href="route('admin.chains.index')"
											type="button"
											class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
										>
											{{ $t("Go Back") }}
										</Link>
										<LoadingButton
											type="submit"
											:busy="form.processing"
											class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
										>
											{{ $t("Update") }} Chain
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
