<script setup>
import {ref} from "vue";

import {ArrowTrendingUpIcon} from "@heroicons/vue/24/outline";
import {useDark} from "@vueuse/core";
import axios from "axios";
import {Vue3Marquee} from "vue3-marquee";

import CollapseTransition from "@/Components/CollapseTransition.vue";

import "vue3-marquee/dist/style.css";
const darkMode = useDark();
const trending = ref([]);
const load = async () => {
	const {data} = await axios(window.route("home.trending"));
	trending.value = data;
};
// onMounted(load);
</script>
<template>
	<CollapseTransition>
		<div
			v-show="trending.length > 0"
			class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
		>
			<div class="max-w-screen-2xl mx-auto px-3 sm:px-10">
				<div class="text-gray-700 py-2 font-sans text-xs font-medium flex">
					<div class="flex items-center">
						<ArrowTrendingUpIcon
							class="mr-2 w-4 h-4 text-emerald-500 dark:text-emerald-400"
						/>
						<span class="text-emerald-500 font-semibold">Trending:</span>
					</div>
					<Vue3Marquee
						:gradient="true"
						:duration="60"
						:gradientColor="darkMode ? [41, 37, 36] : [255, 255, 255]"
						:gradientLength="`5%`"
						pauseOnHover
					>
						<div
							v-for="(trend, index) in trending"
							:key="trend.uuid"
							class="px-3 flex space-x-2 text-xs"
						>
							<span class="text-gray-600 dark:text-gray-300">#{{ index + 1 }}</span
							><a
								class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300"
								:href="route('games.show', {slug: trend.slug})"
								>{{ trend.name }}</a
							>
						</div>
					</Vue3Marquee>
				</div>
			</div>
		</div>
	</CollapseTransition>
</template>
