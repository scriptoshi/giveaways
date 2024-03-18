<!-- eslint-disable no-unused-vars -->
<script setup>
import Github from "./Icons/Github.vue";
import Facebook from "./Icons/Facebook.vue";
import Twitter from "./Icons/Twitter.vue";
import Twitch from "./Icons/Twitch.vue";
import Telegram from "./Icons/Telegram.vue";
import Instagram from "./Icons/Instagram.vue";
import Reddit from "./Icons/Reddit.vue";
import Discord from "./Icons/Discord.vue";
import Web from "./Icons/Globe.vue";
const props = defineProps({
    github: Boolean,
    facebook: Boolean,
    twitter: Boolean,
    twitch: Boolean,
    telegram: Boolean,
    instagram: Boolean,
    reddit: Boolean,
    discord: Boolean,
    web: Boolean,
});
</script>
<template>
    <a
        href="#"
        class="text-emerald-500 hover:text-emerald-600 hover:-translate-y-1 cursor-pointer transistion duration-150"
    >
        <Github
            v-if="github"
            class="w-5 h-5"
        />
        <Facebook
            v-else-if="facebook"
            class="w-5 h-5 fill-emerald-500 hover:fill-emerald-600 dark:hover:fill-emerald-400"
        />
        <Twitter
            v-else-if="twitter"
            class="w-5 h-5"
        />
        <Twitch
            v-else-if="twitch"
            class="w-5 h-5 fill-emerald-500 hover:fill-emerald-600 dark:hover:fill-emerald-400"
        />
        <Telegram
            v-else-if="telegram"
            class="w-5 h-5"
        />
        <Instagram
            v-else-if="instagram"
            class="w-5 h-5"
        />
        <Reddit
            v-else-if="reddit"
            class="w-5 h-5 fill-emerald-500 hover:fill-emerald-600 dark:hover:fill-emerald-400"
        />
        <Discord
            v-else-if="discord"
            class="w-5 h-5"
        /> 
        <Web
            v-else-if="web"
            class="w-5 h-5"
        />
    </a>

</template>