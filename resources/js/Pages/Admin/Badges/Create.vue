<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import {HiArchive, HiSolidArrowSmLeft} from "oh-vue-icons/icons";
import {useI18n} from "vue-i18n";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DangerButton from "@/Jetstream/DangerButton.vue";
import JetButton from "@/Jetstream/JetButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const {t: $t} = useI18n();
const form = useForm({
	badge: "",
	description: "",
});
const save = () => form.post(window.route("admin.badges.store"));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Add New Badge`" />
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full container lg:w-4/5 mt-6">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="text-3xl">Create New Badge</h3>
							<p>
								Badge are awarded to projects if they complete due-deligience
								service
							</p>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body p-6 card-gutterless h-full">
							<form @submit.prevent="save">
								<div class="grid md:grid-cols-2 my-6 gap-x-4">
									<FormInput
										v-model="form.badge"
										:error="form.errors.badge"
										:label="$t('Badge Name')"
										:placeholder="$t('Eg VERIFIED')"
										:help="
											$t(
												'Changing this later will invalidate price on the contract!',
											)
										"
									/>
								</div>
								<FormInput
									v-model="form.description"
									:error="form.errors.description"
									:label="$t('Brief Description')"
									:help="$t('Provide brief info on the badge')"
								/>
								<div class="pt-12">
									<div class="flex justify-end space-x-3">
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
											{{ $t("Cancel") }}
										</DangerButton>
										<JetButton
											type="submit"
											:busy="form.processing"
										>
											<Loading
												class="!text-white inline-flex w-5 h-6 -ml-1 mr-2"
												v-if="form.processing"
											/>
											<VueIcon
												v-else
												class="w-5 h-6 -ml-1 mr-2"
												:icon="HiArchive"
											/>{{ $t("Save Bagde") }}
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
