<script setup>
import {computed, ref, watch} from "vue";

import {Head, usePage} from "@inertiajs/vue3";
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";

import AlertMessages from "@/Layouts/AlertMessages.vue";
import AppFooter from "@/Layouts/AppLayout/AppFooter.vue";
import AppHeader from "@/Layouts/AppLayout/AppHeader.vue";
import Meta from "@/Layouts/AppLayout/Meta.vue";
import Sidebar from "@/Layouts/AppLayout/Sidebar.vue";
import TopAd from "@/Layouts/TopAd.vue";
import VerifyEmailModal from "@/Pages/Auth/VerifyEmailModal.vue";
import NetworkSwitcherModal from "@/Wagmi/NetworkSwitcherModal.vue";

const AuthCheck = computed(() => usePage().props.AuthCheck);
const expanded = ref(true);

defineProps({
	title: String,
});
const draw = (val) => (expanded.value = val);
const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("md");
watch(
	isSm,
	(isSm) => {
		expanded.value = !isSm;
	},
	{immediate: true},
);
</script>
<template>
	<Meta />
	<div class="relative">
		<AlertMessages />
		<TopAd />
		<Head
			v-if="title"
			:title="title"
		/>
		<div
			@scroll="onScroll"
			class="flex flex-auto min-w-0"
		>
			<Sidebar
				@draw="draw"
				:expanded="expanded"
			/>
			<div
				class="flex flex-col flex-auto min-h-screen min-w-0 relative w-full bg-gray-100 dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700"
			>
				<AppHeader @toggle="expanded = !expanded" />
				<div class="h-full flex flex-auto flex-col justify-between">
					<slot />
					<AppFooter />
				</div>
			</div>
		</div>
		<NetworkSwitcherModal v-if="AuthCheck" />
		<VerifyEmailModal v-if="AuthCheck" />
	</div>
</template>
