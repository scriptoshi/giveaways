<script setup>
import {computed, ref} from "vue";

import {useI18n} from "vue-i18n";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import SocialIcon from "@/Components/SocialIcon.vue";

const props = defineProps({
	title: String,
	description: String,
	discord: String,
	facebook: String,
	github: String,
	instagram: String,
	medium: String,
	reddit: String,
	twitter: String,
	tiktok: String,
	telegram: String,
	youtube: String,
	website: String,
	docs: String,
	whitepaper: String,
	snapchat: String,
	linktree: String,
	errors: Object,
});
const emit = defineEmits([
	"update:title",
	"update:description",
	"update:website",
	"update:docs",
	"update:telegram",
	"update:twitter",
	"update:discord",
	"update:whitepaper",
	"update:facebook",
	"update:github",
	"update:instagram",
	"update:medium",
	"update:reddit",
	"update:tiktok",
	"update:youtube",
	"update:linktree",
	"update:snapchat",
]);

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
	website: {label: t("Website Url *"), placeholder: t("https://projectwebsite.com")},
	docs: {label: t("Documentation Url"), placeholder: t("https://docs.projectwebsite.com")},
	telegram: {
		label: t("Telegram Group Url *"),
		placeholder: t("https://t.me/yourTelegramOfficial"),
	},
	twitter: {label: t("Twitter Url *"), placeholder: t("https://twitter.com/yourTwitterOfficial")},
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
const title = computed({
	get: () => props.title,
	set: (v) => emit("update:title", v),
});
const description = computed({
	get: () => props.description,
	set: (v) => emit("update:description", v),
});
</script>
<template>
	<div class="w-full border border-gray-200 p-5 dark:border-gray-600">
		<slot name="title" />
		<div class="grid gap-y-4 w-full">
			<div class="w-full grid md:grid-cols-3">
				<FormInput
					:label="$t('Project Title')"
					v-model="title"
					class="md:col-span-2"
					:placeholder="$t('E.g Cool Token Launch')"
					:error="errors?.title"
				/>
			</div>
			<div class="w-full grid">
				<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{
					$t("Brief Description")
				}}</label>
				<ProjectDescriptionTextArea
					v-model="description"
					:placeholder="
						$t(
							'Donot copy and paste from another site and use AI gibberish. Write a fresh description. We optimize for seo. We shall delete listings with duplicate or AI content',
						)
					"
				/>
				<p
					v-if="errors?.description"
					class="text-red"
				>
					{{ errors?.description }}.
				</p>
				<p
					v-else
					class="text-red"
				>
					{{
						$t(
							"We spend hours optimizing for seo daily. Donot copy and paste from another site or use AI gibberish. Write a fresh description. We shall delete listings with duplicate or AI content",
						)
					}}
				</p>
			</div>

			<div class="gap-x-3 w-full my-6 grid md:grid-cols-3">
				<h3 class="text-base text-emerald-600 dark:text-emerald-400">
					{{ $t("Online Presence") }}
				</h3>
				<small>{{ $t("items marked * are required") }}</small>
			</div>
			<div class="gap-x-3 w-full grid md:grid-cols-2 gap-y-6">
				<FormInput
					v-for="link in commons"
					:key="link"
					:model-value="props[link]"
					@update:model-value="(val) => $emit(`update:${link}`, val)"
					:label="titles[link].label"
					:placeholder="titles[link].placeholder"
					:error="errors?.[link]"
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
						:model-value="props[social]"
						@update:model-value="(val) => $emit(`update:${social}`, val)"
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
		<slot name="footer"></slot>
	</div>
</template>
