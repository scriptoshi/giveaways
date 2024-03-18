<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import {HiArchive, HiSolidArrowSmLeft} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/JetButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const props = defineProps({
	badge: Object,
});

const form = useForm(props.badge);

const save = () => form.put(window.route("admin.badges.update", props.badge.id));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Add New Badge`" />
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col mt-8 px-4 sm:px-6 py-12 sm:py-6 md:px-8"
			>
				<div class="flex flex-col gap-4 h-full container lg:w-4/5">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="text-3xl">Edit {{ props.badge.name }}</h3>
							<p>
								Badge are awarded to projects if they complete due-deligience
								service
							</p>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body p-8 card-gutterless h-full">
							<form @submit.prevent="save">
								<div class="grid md:grid-cols-2 mt-6 gap-x-4">
									<FormInput
										v-model="form.badge"
										:error="form.errors.badge"
										:label="$t('Badge Name')"
										:placeholder="$t('Eg VERIFIED')"
									/>
								</div>
								<div class="grid mt-6 gap-x-4">
									<FormInput
										v-model="form.description"
										:error="form.errors.description"
										:label="$t('Brief Description')"
										:help="$t('Provide brief info on the badge')"
									/>
								</div>

								<div class="pt-12">
									<div class="flex space-x-3 justify-end">
										<DangerButton
											as="button"
											:href="route('admin.badges.index')"
											type="button"
											link
										>
											<VueIcon
												:icon="HiSolidArrowSmLeft"
												class="w-4 h-4 -ml-2 mr-2 inline-block"
											/>
											{{ $t("Cancel and Go back") }}
										</DangerButton>

										<JetButton
											type="submit"
											:disabled="form.processing"
										>
											<Loading
												class="-ml-1 mr-2 !text-white w-5 h-5 inline-flex"
												v-if="form.processing"
											/>
											<VueIcon
												:icon="HiArchive"
												v-else
												class="w-5 h-5 -ml-1 mr-2 !text-white"
											/>
											{{ $t("Save changes") }}
										</JetButton>
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
