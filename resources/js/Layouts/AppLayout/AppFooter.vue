<script setup>
import {computed} from "vue";

import {usePage} from "@inertiajs/vue3";
const config = computed(() => usePage().props.config);
</script>
<template>
	<footer
		class="footer sticky bottom-0 flex items-center h-[48px] px-4 sm:px-6 md:px-8 bg-gray-200 dark:bg-gray-800"
	>
		<div class="flex items-center justify-between w-full">
			<span
				>Copyright © 2022 <span class="font-semibold">{{ config.appName }}</span> All
				rights reserved.</span
			>
			<div class="">
				<a
					class="text-gray"
					:href="route('terms')"
					>{{ $t("Term & Conditions") }}</a
				><span class="mx-2 text-muted"> | </span
				><a
					class="text-gray"
					:href="route('privacy')"
					>{{ $t("Privacy & Policy") }}</a
				>
			</div>
		</div>
	</footer>
</template>
