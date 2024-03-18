<script setup>
import behance from "@/Social/behance.svg";
import blogger from "@/Social/blogger.svg";
import buffer from "@/Social/buffer.svg";
import codepen from "@/Social/codepen.svg";
import discord from "@/Social/discord.svg";
import dribbble from "@/Social/dribbble.svg";
import facebook from "@/Social/facebook.svg";
import flickr from "@/Social/flickr.svg";
import github from "@/Social/github.svg";
import instagram from "@/Social/instagram.svg";
import linkedin from "@/Social/linkedin.svg";
import medium from "@/Social/medium.svg";
import messenger from "@/Social/messenger.svg";
import pinterest from "@/Social/pinterest.svg";
import reddit from "@/Social/reddit.svg";
import skype from "@/Social/skype.svg";
import slack from "@/Social/slack.svg";
import snapchat from "@/Social/snapchat.svg";
import stackoverflow from "@/Social/stackoverflow.svg";
import telegram from "@/Social/telegram.svg";
import tiktok from "@/Social/tiktok.svg";
import trello from "@/Social/trello.svg";
import tumblr from "@/Social/tumblr.svg";
import twitch from "@/Social/twitch.svg";
import twitter from "@/Social/twitter.svg";
import vimeo from "@/Social/vimeo.svg";
import whatsapp from "@/Social/whatsapp.svg";
import yelp from "@/Social/yelp.svg";
import youtube from "@/Social/youtube.svg";
defineProps({
	icon: String,
});
</script>
<template>
	<telegram
		class="w-auto h-5"
		v-if="icon == 'telegram'"
	/>
	<twitter
		class="w-auto h-5"
		v-if="icon == 'twitter'"
	/>
	<tiktok
		class="w-auto h-5"
		v-if="icon == 'tiktok'"
	/>
	<github
		class="w-auto h-5"
		v-if="icon == 'github'"
	/>
	<whatsapp
		class="w-auto h-5"
		v-if="icon == 'whatsapp'"
	/>
	<youtube
		class="w-auto h-5"
		v-if="icon == 'youtube'"
	/>
	<discord
		class="w-auto h-5"
		v-if="icon == 'discord'"
	/>
	<twitch
		class="w-auto h-5"
		v-if="icon == 'twitch'"
	/>
	<facebook
		class="w-auto h-5"
		v-if="icon == 'facebook'"
	/>
	<behance
		class="w-auto h-5"
		v-if="icon == 'behance'"
	/>
	<blogger
		class="w-auto h-5"
		v-if="icon == 'blogger'"
	/>
	<buffer
		class="w-auto h-5"
		v-if="icon == 'buffer'"
	/>
	<codepen
		class="w-auto h-5"
		v-if="icon == 'codepen'"
	/>
	<dribbble
		class="w-auto h-5"
		v-if="icon == 'dribbble'"
	/>
	<flickr
		class="w-auto h-5"
		v-if="icon == 'flickr'"
	/>
	<instagram
		class="w-auto h-5"
		v-if="icon == 'instagram'"
	/>
	<linkedin
		class="w-auto h-5"
		v-if="icon == 'linkedin'"
	/>
	<medium
		class="w-auto h-5"
		v-if="icon == 'medium'"
	/>
	<messenger
		class="w-auto h-5"
		v-if="icon == 'messenger'"
	/>
	<pinterest
		class="w-auto h-5"
		v-if="icon == 'pinterest'"
	/>
	<reddit
		class="w-auto h-5"
		v-if="icon == 'reddit'"
	/>
	<skype
		class="w-auto h-5"
		v-if="icon == 'skype'"
	/>
	<slack
		class="w-auto h-5"
		v-if="icon == 'slack'"
	/>
	<snapchat
		class="w-auto h-5"
		v-if="icon == 'snapchat'"
	/>
	<stackoverflow
		class="w-auto h-5"
		v-if="icon == 'stackoverflow'"
	/>
	<trello
		class="w-auto h-5"
		v-if="icon == 'trello'"
	/>
	<tumblr
		class="w-auto h-5"
		v-if="icon == 'tumblr'"
	/>
	<vimeo
		class="w-auto h-5"
		v-if="icon == 'vimeo'"
	/>
	<yelp
		class="w-auto h-5"
		v-if="icon == 'yelp'"
	/>
</template>
