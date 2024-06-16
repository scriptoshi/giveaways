<script setup>
import {ArrowUpIcon, MoonIcon, SunIcon} from "@heroicons/vue/24/solid";
import {useDark} from "@vueuse/core";
import {uid} from "uid";

import SiteLogo from "@/Components/SiteLogo.vue";
import Facebook from "@/Social/Facebook.vue";
import Github from "@/Social/Github.vue";
import Medium from "@/Social/Medium.vue";
import Reddit from "@/Social/Reddit.vue";
import Skype from "@/Social/Skype.vue";
import Telegram from "@/Social/Telegram.vue";
import Twitter from "@/Social/Twitter.vue";
const darkMode = useDark();
const socials = [
	{id: uid(), icon: Facebook, url: "#"},
	{id: uid(), icon: Twitter, url: "#"},
	{id: uid(), icon: Github, url: "#"},
	{id: uid(), icon: Medium, url: "#"},
	{id: uid(), icon: Skype, url: "#"},
	{id: uid(), icon: Telegram, url: "#"},
	{id: uid(), icon: Reddit, url: "#"},
];

const links = [
	{id: uid(), text: "Forex Swap", url: "#"},
	{id: uid(), text: "Trade Crypto", url: "#"},
	{id: uid(), text: "Launchpad", url: "#"},
	{id: uid(), text: "Blog", url: "#"},
	{id: uid(), text: "Privacy Policy", url: "#"},
	{id: uid(), text: "Terms", url: "#"},
];
</script>
<template>
	<footer class="p-12">
		<div class="container">
			<div class="row justify-center">
				<div class="col-12 md:col-8 text-center">
					<!-- Footer Items -->
					<div class="footer-items">
						<!-- Logo -->
						<a
							class="m-0"
							href="/"
						>
							<SiteLogo class="w-32 inline-block" />
						</a>
						<!-- Social Icons -->
						<div class="flex justify-center py-6 font-medium overflow-hidden">
							<a
								v-for="social in socials"
								:key="social.id"
								class="transition duration-300 hover:-mt-2 m-1.5 w-11 h-11 text-base bg-transparent rounded-full group"
								:href="social.url"
								target="_blank"
							>
								<component
									:is="social.icon"
									class="flex items-center justify-center h-11 w-11 mx-0 my-auto transition-all duration-500 ease-in-out"
								/>
							</a>
						</div>
						<ul class="list-none p-0">
							<li
								v-for="link in links"
								:key="link.id"
								class="mx-2 my-4 inline-block"
							>
								<a
									class="text-lg font-semibold no-underline text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
									:href="link.url"
									>{{ link.text }}</a
								>
							</li>
						</ul>
						<!-- Copyright Area -->
						<div class="py-6 text-lg font-semibold text-gray-600 dark:text-gray-400">
							©{{ new Date().getFullYear() }} sleep, All Rights Reserved By
							<a
								href="https://giveaways.finance"
								class="font-medium no-underline text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
								target="_blank"
								>sleep</a
							>
						</div>
					</div>
					<!-- Scroll To Top -->
					<div class="w-full flex flex-row justify-center space-x-3">
						<div
							class="h-11 w-11 leading-10 my-0 bg-white hover:bg-sky-500 dark:hover:bg-sky-500 dark:bg-gray-800 shadow rounded-xl transition-all duration-300 ease-out"
						>
							<a
								class="font-medium no-underline block h-full w-full leading-10"
								href="#header"
							>
								<ArrowUpIcon
									class="overflow-visible box-border inline-block text-gray-700 hover:text-white dark:text-white h-6"
								/>
							</a>
						</div>
						<div
							@click="darkMode = !darkMode"
							class="h-11 w-11 leading-10 my-0 bg-white hover:bg-sky-500 dark:hover:bg-sky-500 dark:bg-gray-800 shadow rounded-xl transition-all duration-300 ease-out"
						>
							<a
								class="font-medium no-underline block h-full w-full leading-10"
								href="#"
							>
								<SunIcon
									v-if="darkMode"
									class="overflow-visible box-border inline-block text-gray-700 hover:text-white dark:text-white h-6"
								/>
								<MoonIcon
									v-else
									class="overflow-visible box-border inline-block text-gray-700 hover:text-white dark:text-white h-6"
								/>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
</template>
