<script setup>
import {
    BiGlobe,
    BiTelegram,
    BiTwitter,
    BiFacebook,
    BiInstagram,
    BiGithub,
    BiReddit,
    BiDiscord,
    BiTiktok,
    BiYoutube,
    BiTwitch,
    BiWhatsapp,
    BiBehance,
    CoBloggerB,
    CoBuffer,
    CoCodepen,
    BiDribbble,
    CoFlickr,
    BiLinkedin,
    BiMedium,
    BiMessenger,
    BiPinterest,
    BiSkype,
    BiSlack,
    BiSnapchat,
    BiStackOverflow,
    CoTrello,
    CoTumblr,
    BiVimeo,
    CoYelp,
} from "oh-vue-icons/icons";
import { addIcons } from "oh-vue-icons";
defineProps({
    links:Array,
    website:String
})
addIcons(
    BiGlobe,
    BiTelegram,
    BiDiscord,
    BiTwitter,
    BiFacebook,
    BiInstagram,
    BiGithub,
    BiReddit,
    BiTiktok,
    BiYoutube,
    BiTwitch,
    BiWhatsapp,
    BiBehance,
    CoBloggerB,
    CoBuffer,
    CoCodepen,
    BiDribbble,
    CoFlickr,
    BiLinkedin,
    BiMedium,
    BiMessenger,
    BiPinterest,
    BiSkype,
    BiSlack,
    BiSnapchat,
    BiStackOverflow,
    CoTrello,
    CoTumblr,
    BiVimeo,
    CoYelp
);
const co = [
    "yelp",
    "tumblr",
    "trello",
    "flickr",
    "codepen",
    "buffer",
    "blocgger",
];
</script>
<template>
    <div class="flex flex-row space-x-1">
        <a
            :href="website"
            target="_blank"
            class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300  stransition duration-200 hover:-translate-y-1 w-7 h-7 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-900"
        >
            <OhVueIcon
                name="bi-globe"
                class="w-4 h-4"
            />
        </a>
        <a
            :href="link.link"
            target="_blank"
            v-for="link in links.slice(0,12)"
            :key="link.id"
            class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition duration-200 hover:-translate-y-1 w-7 h-7 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-900"
        >
            <OhVueIcon
                :name="`bi-${link.network}`"
                v-if="co.includes('link.network')"
                class="w-4 h-4"
            />
            <OhVueIcon
                v-else
                :name="`bi-${link.network}`"
                class="w-4 h-4"
            />
        </a>
    </div>
</template>