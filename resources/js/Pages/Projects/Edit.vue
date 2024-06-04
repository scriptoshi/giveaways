<script setup>
import {ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import SocialIcon from "@/Components/SocialIcon.vue";
import {useFormUploadParams} from "@/hooks/useFormUploadParams";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
import AppLayout from "@/Layouts/AppLayout.vue";
const props = defineProps({
	project: Object,
});

const form = useForm({
	...useFormUploadParams("logo", props.project.logo),
	description: props.project.description,
	// project info
	youtube: "",
	website: "",
	docs: "",
	whitepaper: "",
	telegram: "",
	linktree: "",
	snapchat: "",
	twitter: "",
	tiktok: "",
	github: "",
	discord: "",
	facebook: "",
	instagram: "",
	medium: "",
	reddit: "",
});
props.project.socials.forEach((s) => {
	form[s.network] = s.link;
});

const commons = ["website", "docs", "telegram", "twitter", "discord"];
const socials = [
	"whitepaper",
	"facebook",
	"github",
	"instagram",
	"medium",
	"reddit",
	"tiktok",
	"youtube",
	"linktree",
	"snapchat",
];
const {t} = useI18n();
const titles = {
	website: {label: t("Website Url"), placeholder: t("https://projectwebsite.com")},
	docs: {label: t("Documentation Url"), placeholder: t("https://docs.projectwebsite.com")},
	telegram: {
		label: t("Telegram Group Url"),
		placeholder: t("https://t.me/yourTelegramOfficial"),
	},
	twitter: {label: t("Twitter Url"), placeholder: t("https://twitter.com/yourTwitterOfficial")},
	discord: {
		label: t("Discord Invite Url"),
		placeholder: t("https://discord.gg/yOurChaNNelInvItE"),
	},
	whitepaper: {
		label: t("White Paper Link"),
		placeholder: t("https://docs.projectwebsite.com/whitepaper.pdf"),
	},
	facebook: {label: t("Facebook Page"), placeholder: t("https://fb.me/myFbPage")},
	github: {label: t("Github Repo"), placeholder: t("https://github.com/YourGithubUsername")},
	instagram: {label: t("Instagram Profile"), placeholder: t("https://instagram.com/YourProfile")},
	medium: {label: t("Medium Blog"), placeholder: t("https://medium.com/YourProfile")},
	reddit: {label: t("Project Reddit"), placeholder: t("https://www.reddit.com/YourReddit")},
	tiktok: {label: t("Tiktok Profile"), placeholder: t("https://www.tiktok.com/YourProfile")},
	youtube: {label: t("YouTube Channel"), placeholder: t("https://www.youtu.be/YourChannel")},
	linktree: {label: t("Link Tree Page"), placeholder: t("https://www.linktr.ee/YourLinkTree")},
	snapchat: {
		label: t("Snapchat Channel"),
		placeholder: t("https://www.snapchat.com/add/yoursnapchat"),
	},
};

const showMore = ref(false);

const updateProject = async () => {
	form.put(window.route("projects.update", {project: props.project.uuid}), {
		preserveScroll: true,
	});
};
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div class="card mt-12 p-8 border dark:border-gray-600 border-gray-300">
					<div class="grid mx-auto w-full max-w-3xl">
						<div class="mb-8">
							<h3>{{ $t("Update Project") }} #{{ project.name }}</h3>

							<div class="flex mt-4 items-end space-x-2">
								<PrimaryButton
									class="!py-1 !text-xs uppercase"
									secondary
									link
									:href="route('projects.show', {project: project.slug})"
								>
									View Project
								</PrimaryButton>
							</div>
						</div>

						<div class="grid gap-y-4 w-full">
							<div class="w-full grid">
								<label
									class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
									>{{ $t("Brief Description") }}</label
								>
								<ProjectDescriptionTextArea
									v-model="form.description"
									:rows="2"
									:placeholder="
										$t(
											'Donot copy and paste from another site and use AI gibberish. Write a fresh description. We optimize for seo. We shall delete listings with duplicate or AI content',
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
							<div class="gap-x-3 mt-2 grid md:grid-cols-2">
								<FormInput
									v-model="form.logo_uri"
									:disabled="form.logo_upload"
									placeholder="https://"
									:error="form.errors.logo_uri"
									:help="$t('Supports png, jpeg or svg')"
								>
									<template #label>
										<div class="flex">
											<span class="mr-3">{{ $t("Token Logo Url") }}</span>
											<label class="inline-flex items-center space-x-2">
												<input
													v-model="form.logo_upload"
													class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
													type="checkbox"
												/>
												<span>{{ $t("Upload to server") }}</span>
											</label>
										</div>
									</template>
								</FormInput>
								<template v-if="form.logo_upload">
									<LogoInput
										v-if="$page.props.config.s3"
										v-model="form.logo_uri"
										v-model:file="form.logo_path"
										auto
									/>
									<LogoInputLocal
										v-else
										v-model="form.logo_uri"
									/>
								</template>
								<img
									v-else
									class="w-12 h-12 my-auto rounded-full b-0"
									:src="form.logo_uri ?? fakeLogo"
								/>
							</div>

							<div class="gap-x-3 w-full my-6 grid md:grid-cols-3">
								<h3 class="text-base text-emerald-600 dark:text-emerald-400">
									{{ $t("Online Presence") }}
								</h3>
							</div>
							<div class="gap-x-3 w-full grid md:grid-cols-2 gap-y-6">
								<FormInput
									v-for="link in commons"
									:key="link"
									v-model="form[link]"
									:label="titles[link].label"
									:placeholder="titles[link].placeholder"
									:error="form.errors?.[link]"
								>
									<template #trail>
										<SocialIcon :icon="link" />
									</template>
								</FormInput>
							</div>
							<div class="gap-x-3 my-6 w-full grid">
								<label class="inline-flex items-center space-x-2">
									<input
										v-model="showMore"
										class="form-checkbox is-basic h-5 w-5 rounded-sm border-slate-400/70 checked:!border-emerald-600 checked:bg-emerald-600 hover:!border-emerald-600 focus:!border-emerald-600 dark:border-navy-400"
										type="checkbox"
									/>
									<h3 class="text-sm">{{ $t("Show More Social Media") }}</h3>
								</label>
							</div>
							<CollapseTransition>
								<div
									v-show="showMore"
									class="gap-x-3 w-full grid md:grid-cols-2 gap-y-6"
								>
									<FormInput
										v-for="social in socials"
										:key="social"
										v-model="form[social]"
										:label="titles[social]?.label"
										:placeholder="titles[social]?.placeholder"
										:error="errors?.[social]"
									>
										<template #trail>
											<SocialIcon :icon="social" />
										</template>
									</FormInput>
								</div>
							</CollapseTransition>
						</div>

						<div class="mt-3 w-full flex space-x-3 items-center justify-end">
							<PrimaryButton
								secondary
								link
								:href="route('projects.show', {project: project.slug})"
							>
								View Project
							</PrimaryButton>
							<PrimaryButton
								@click.prevent="updateProject"
								primary
								:disabled="form.processing"
							>
								<Loading
									class="mr-2 -ml-1"
									v-if="form.processing"
								/>
								{{ $t("Update Project") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
