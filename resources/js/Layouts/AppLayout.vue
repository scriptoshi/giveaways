<script setup>
import {computed, provide, ref, watch} from "vue";

import {Head, router, usePage} from "@inertiajs/vue3";
import {
	breakpointsTailwind,
	debouncedWatch,
	useBreakpoints,
	useUrlSearchParams,
} from "@vueuse/core";

import AlertMessages from "@/Layouts/AlertMessages.vue";
import AppFooter from "@/Layouts/AppLayout/AppFooter.vue";
import AppHeader from "@/Layouts/AppLayout/AppHeader.vue";
import SiteMenu from "@/Layouts/AppLayout/Header/SiteMenu.vue";
import Meta from "@/Layouts/AppLayout/Meta.vue";
import TopAd from "@/Layouts/TopAd.vue";
import VerifyEmailModal from "@/Pages/Auth/VerifyEmailModal.vue";
import NetworkSwitcherModal from "@/Wagmi/NetworkSwitcherModal.vue";
const AuthCheck = computed(() => usePage().props.AuthCheck);
const expanded = ref(true);

const props = defineProps({
	title: String,
	searchRoute: String,
	white: Boolean,
});

const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("md");
watch(
	isSm,
	(isSm) => {
		expanded.value = !isSm;
	},
	{immediate: true},
);
const params = useUrlSearchParams("history");
provide("filter", params);
debouncedWatch(
	[() => params.search, () => params.q],
	([f, q]) => {
		const search = f === "" ? null : f;
		router.visit(window.route(props.searchRoute, {search, q}), {
			preserveScroll: true,
		});
	},
	{debounce: 800},
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
			<div
				class="flex flex-col flex-auto min-h-screen min-w-0 relative w-full bg-gray-100 dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700"
			>
				<AppHeader
					:searchRoute="searchRoute"
					@toggle="expanded = !expanded"
				/>
				<div
					class="bg-gray-50 border-t dark:bg-gray-800 flex sticky top-16 w-full z-20 border-b border-gray-200 dark:border-gray-700 lg:hidden"
				>
					<div
						class="h-10 item items-center flex justify-between py-0 px-4 relative w-full"
					>
						<SiteMenu />
					</div>
				</div>
				<div
					:class="{'bg-white dark:bg-gray-900': white}"
					class="h-full flex flex-auto flex-col justify-between"
				>
					<slot />
					<AppFooter />
				</div>
			</div>
		</div>
		<NetworkSwitcherModal v-if="AuthCheck" />
		<VerifyEmailModal v-if="AuthCheck" />
	</div>
</template>
