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

import VueIcon from "@/Components/VueIcon.vue";

defineProps({
	socials: Object,
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
	<div class="flex flex-row space-x-3">
		<template
			v-for="link in socials.slice(0, 8)"
			:key="link.id"
		>
			<a
				:href="link.link"
				target="_blank"
				v-if="link.network !== 'youtube_trailer'"
				class="text-emerald-500 hover:text-emerald-600 transition duration-200 hover:-translate-y-1"
			>
				<VueIcon
					:icon="networks[link.network]"
					class="w-6 h-6"
				/>
			</a>
		</template>
	</div>
</template>
