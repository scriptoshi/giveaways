<script setup>
import {
	BiDiscord,
	BiFacebook,
	BiGithub,
	BiGlobe,
	BiInstagram,
	BiMedium,
	BiReddit,
	BiTelegram,
	BiTiktok,
	BiTwitter,
	BiYoutube,
	HiDocument,
	HiDocumentSearch,
} from "oh-vue-icons/icons";

import ExternalLink from "@/Components/ExternalLink.vue";
import VueIcon from "@/Components/VueIcon.vue";
defineProps({
	project: Object,
});

const networks = {
	discord: BiDiscord,
	facebook: BiFacebook,
	github: BiGithub,
	instagram: BiInstagram,
	medium: BiMedium,
	reddit: BiReddit,
	twitter: BiTwitter,
	tiktok: BiTiktok,
	telegram: BiTelegram,
	youtube: BiYoutube,
	website: BiGlobe,
	docs: HiDocument,
	whitepaper: HiDocumentSearch,
};
</script>
<template>
	<div class="flex px-3 items-center space-x-3">
		<div class="flex flex-row space-x-3">
			<template
				v-for="link in project.socials.slice(0, 8)"
				:key="link.id"
			>
				<ExternalLink
					:href="link.url"
					v-if="link.network !== 'youtube_trailer'"
					class="transition duration-200 hover:-translate-y-1"
				>
					<VueIcon
						:icon="networks[link.network]"
						class="w-6 h-6 text-emerald-500 transition-colors duration-300 hover:text-emerald-800 dark:hover:text-emerald-300"
					/>
				</ExternalLink>
			</template>
		</div>
	</div>
</template>
