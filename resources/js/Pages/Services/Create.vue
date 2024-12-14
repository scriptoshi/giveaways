<script setup>
import {computed, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import upperFirst from "lodash/upperFirst";
import {BiTelegram} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import Multiselect from "@/Components/Multiselect/Multiselect.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useFormUploadParams} from "@/hooks/useFormUploadParams";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
	categories: Object,
	tags: Object,
});

const form = useForm({
	duration: "12",
	period: "hours",
	price: "0",
	brief: "",
	title: "",
	category: null,
	tags: [],
	description: "",
	...useFormUploadParams("image"),
	// basic tasks
	telegram: "",
	twitter: "",
	discord: "",
});

watch(
	() => form.category,
	() => (form.tags = []),
);
const draft = async () => {
	form.post(window.route("services.store"), {
		preserveScroll: true,
		onSuccess() {
			form.reset();
		},
	});
};

const togglePeriod = (d) => {
	if (form.period === "days") form.period = "hours";
	else form.period = "days";
};
const optional = ref(false);
const liveTags = computed(() => (form.category ? props.tags[form.category] ?? [] : []));
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div class="card mt-12 p-8 border dark:border-gray-600 border-gray-300">
					<div class="grid mx-auto max-w-4xl">
						<div class="mb-4 flex space-x-3 items-center">
							<h3>List your service</h3>
						</div>

						<div class="w-full mt-2 mb-6 grid md:grid-cols-5 gap-4">
							<FormInput
								:label="$t('Service Brief')"
								v-model="form.title"
								class="col-span-3"
								:error="form.errors.title"
								:placeholder="$t('Token Logo Design')"
							/>
							<FormInput
								:label="$t('Start Price')"
								v-model="form.price"
								:error="form.errors.price"
								:placeholder="$t('100 USDT')"
							>
								<template #trail> USDT </template>
							</FormInput>

							<FormInput
								:label="$t('Duration')"
								v-model="form.duration"
								:disabled="giveaway?.live"
								:placeholder="$t('24')"
								:error="form.errors.duration"
							>
								<template #trail>
									<a
										href="#"
										class="hover:text-emerald-500 px-1 text-xs font-semibold border border-gray-300 dark:border-gray-600 rounded-sm"
										@click.prevent="togglePeriod"
										>{{ upperFirst(form.period) }}</a
									>
								</template>
								<template #help>
									<div
										class="flex mt-1 text-xs font-semibold items-start space-x-1"
									>
										<a
											v-for="item in ['hours', 'days', 'weeks']"
											:key="item"
											:class="
												form.period == item
													? 'text-emerald-500 dark:text-emerald-400'
													: ''
											"
											class="px-1 bg-white hover:bg-gray-100 dark:hover:text-white dark:bg-gray-900 border rounded-sm border-gray-300 dark:border-gray-600"
											href="#"
											@click.prevent="form.period = item"
										>
											{{ item }}
										</a>
									</div>
								</template>
							</FormInput>
						</div>
						<div class="w-full mb-4 grid gap-4">
							<div class="max-w-md w-full">
								<label
									:class="
										form.errors.category
											? 'text-red-700 dark:text-red-500'
											: ' text-gray-900 dark:text-gray-300'
									"
									class="block mb-3 text-base font-medium"
									>{{ $t("Select a category") }}</label
								>
								<Multiselect
									v-model="form.category"
									:options="categories"
									:searchable="false"
									:close-on-select="true"
									:show-labels="false"
									placeholder="Pick a value"
								>
									<template #option="{option}">
										<div>
											<div>{{ upperFirst(option.value) }}</div>
											<div class="text-xs">{{ option.label }}</div>
										</div>
									</template>
								</Multiselect>
							</div>
							<CollapseTransition>
								<div
									class="w-full"
									v-show="liveTags.length"
								>
									<h3 class="text-sm mb-2">Filter Tags</h3>
									<div class="grid sm:grid-cols-3 lg:grid-cols-4 gap-4">
										<label
											v-for="tag in liveTags"
											:key="tag"
											class="inline-flex items-center space-x-2"
										>
											<input
												class="form-checkbox is-basic w-5 h-5 rounded border-gray-400/70 checked:bg-success checked:!border-success hover:!border-success focus:!border-success dark:border-gray-400"
												type="checkbox"
												:value="tag"
												v-model="form.tags"
											/>
											<p>{{ upperFirst(tag) }}</p>
										</label>
									</div>
								</div>
							</CollapseTransition>
						</div>
						<div>
							<div class="p-6 border mt-8 border-gray-200 dark:border-gray-600">
								<div>
									<Switch v-model="optional"
										><h3 class="text-base">Optional Information</h3></Switch
									>
								</div>
								<CollapseTransition>
									<div v-show="optional">
										<div class="gap-8 mt-5 w-full grid sm:grid-cols-3">
											<FormInput
												:label="$t('Website')"
												v-model="form.url"
												:error="form.errors.url"
												:placeholder="$t(' Your Website if any')"
											/>
											<div class="gap-x-3 sm:col-span-2 grid md:grid-cols-2">
												<FormInput
													v-model="form.image_uri"
													:disabled="form.image_upload"
													placeholder="https://"
													:error="form.errors.image_uri"
													:help="
														$t(
															'png, jpeg or svg. (best: 660px * 440px)',
														)
													"
												>
													<template #label>
														<div class="flex">
															<span class="mr-3">{{
																$t("Service Image")
															}}</span>
															<label
																class="inline-flex items-center space-x-2"
															>
																<input
																	v-model="form.image_upload"
																	class="form-switch h-5 w-10 rounded-full bg-gray-300 before:rounded-full before:bg-gray-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-gray-900 dark:before:bg-gray-300 dark:checked:before:bg-white"
																	type="checkbox"
																/>
																<span>{{
																	$t("Upload to server")
																}}</span>
															</label>
														</div>
													</template>
												</FormInput>
												<template v-if="form.image_upload">
													<LogoInput
														v-if="$page.props.config.s3"
														v-model="form.image_uri"
														v-model:file="form.image_path"
														auto
													/>
													<LogoInputLocal
														v-else
														v-model="form.image_uri"
													/>
												</template>
												<img
													v-else
													class="w-12 h-12 my-auto rounded-full b-0"
													:src="form.image_uri ?? fakeLogo"
												/>
											</div>
										</div>
										<div class="w-full grid mt-6">
											<label
												class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
												>{{ $t("Brief Description") }}</label
											>
											<ProjectDescriptionTextArea
												v-model="form.description"
												:rows="2"
												:placeholder="
													$t(
														'Do not copy and paste from another site or use AI content. Write a brief description. We optimize for seo. We shall delist projects with duplicate or AI content',
													)
												"
											/>
											<p
												v-if="form.errors?.description"
												class="text-red"
											>
												{{ form.errors?.description }}.
											</p>
										</div>
									</div>
								</CollapseTransition>
							</div>
						</div>
						<div class="mt-3 w-full flex space-x-3 items-center justify-end">
							<FormInput
								:label="$t('Telegram Contact')"
								v-model="form.telegram"
								:error="form.errors.telegram"
								:placeholder="$t('https://t.me/mytgusername')"
								class="max-w-xs w-full"
							>
								<template #trail>
									<VueIcon
										class="w-5 h-5"
										:icon="BiTelegram"
									/>
								</template>
							</FormInput>
						</div>
						<div class="mt-12 w-full flex space-x-3 items-center justify-end">
							<PrimaryButton
								@click.prevent="draft"
								secondary
								:disabled="form.processing"
							>
								<Loading
									class="mr-2 -ml-1"
									v-if="form.processing"
								/>
								{{ $t("Create service") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
