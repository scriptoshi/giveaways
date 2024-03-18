<script setup>
import {computed, ref} from "vue";

import {Link} from "@inertiajs/vue3";
import {
	BiUiChecksGrid,
	FaCog,
	MdGroupworkOutlined,
	RiAwardFill,
	RiExchangeBoxFill,
	RiExchangeFill,
	RiStackFill,
	RiUserSettingsFill,
} from "oh-vue-icons/icons";
import {uid} from "uid";

import VueIcon from "@/Components/VueIcon.vue";
import TopMenu from "@/Layouts/AdminLayout/TopMenu.vue";
import AlertMessages from "@/Layouts/AlertMessages.vue";
import NetworkSwitcherModal from "@/Wagmi/NetworkSwitcherModal.vue";
const menus = computed(() => {
	return [
		{
			text: "Dashboard",
			url: window.route("admin.dashboard"),
			active: window.route().current("admin.dashboard"),
			value: "dashboard",
			icon: BiUiChecksGrid,
			id: uid(),
		},
		{
			text: "Config",
			value: "settings",
			url: window.route("admin.settings"),
			active: window.route().current("admin.settings"),
			icon: FaCog,
			id: uid(),
		},
		{
			text: "Members",
			url: window.route("admin.users.index"),
			active: window.route().current("admin.users.index"),
			value: "users",
			icon: RiUserSettingsFill,
			id: uid(),
		},
		{
			text: "Payment Coins",
			url: window.route("admin.coins.index"),
			active: window.route().current("admin.coins.index"),
			value: "coins",
			icon: RiStackFill,
			id: uid(),
		},

		{
			text: "Chains",
			value: "chains",
			url: window.route("admin.chains.index"),
			active:
				window.route().current("admin.chains.index") ||
				window.route().current("admin.chains.create") ||
				window.route().current("admin.chains.edit"),
			icon: RiExchangeBoxFill,
			id: uid(),
		},
		{
			text: "Exchanges",
			value: "amms",
			url: window.route("admin.amms.index"),
			active:
				window.route().current("admin.amms.index") ||
				window.route().current("admin.amms.create") ||
				window.route().current("admin.amms.edit"),
			icon: RiExchangeFill,
			id: uid(),
		},
		{
			text: "Badges",
			value: "tokenfactory",
			url: window.route("admin.badges.index"),
			active:
				window.route().current("admin.badges.index") ||
				window.route().current("admin.badges.create") ||
				window.route().current("admin.badges.show") ||
				window.route().current("admin.badges.edit"),
			icon: RiAwardFill,
			id: uid(),
		},
		{
			text: "Factories",
			value: "tokenfactory",
			url: window.route("admin.factories.index"),
			active:
				window.route().current("admin.factories.index") ||
				window.route().current("admin.factories.create") ||
				window.route().current("admin.factories.show"),
			icon: MdGroupworkOutlined,
			id: uid(),
		},
	];
});

const showSidebar = ref(false);
</script>
<template>
	<body class="bg-gray-50 h-screen">
		<AlertMessages />
		<NetworkSwitcherModal />
		<TopMenu v-model="showSidebar" />
		<div class="flex overflow-hidden bg-white dark:bg-gray-800 h-full pt-16">
			<aside
				:class="showSidebar ? 'flex' : 'hidden'"
				class="fixed z-20 h-full top-0 left-0 pt-16 lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75"
			>
				<div
					class="relative flex-1 flex flex-col min-h-0 border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pt-0"
				>
					<div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
						<div class="flex-1 divide-y divide-gray-200 dark:divide-gray-600 space-y-1">
							<ul class="space-y-2 pb-2">
								<li
									v-for="menu in menus"
									:key="menu.id"
								>
									<Link
										:href="menu.url"
										:class="
											menu.active
												? 'text-emerald-600 dark:text-emerald-400'
												: 'text-gray-900 dark:text-gray-300'
										"
										class="text-base font-normal flex items-center px-3 py-2 hover:bg-emerald-200/50 hover:text-emerald-600 dark:hover:bg-gray-900 group"
									>
										<VueIcon
											:class="
												menu.active
													? 'text-emerald-500  group-hover:text-emerald-700 dark:group-hover:text-emerald-300'
													: 'text-gray-500  group-hover:text-gray-900 dark:group-hover:text-gray-100'
											"
											class="w-6 h-6 flex-shrink-0 transition duration-75 group-hover:text-emerald-700"
											:icon="menu.icon"
										/>
										<span class="ml-3">{{ menu.text }}</span>
									</Link>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</aside>
			<div
				class="bg-gray-900 opacity-50 fixed inset-0 z-10"
				:class="{hidden: !showSidebar}"
			></div>
			<div
				class="h-full w-full bg-gray-50 dark:bg-gray-900 relative overflow-y-auto lg:ml-64"
			>
				<slot />
				<p class="text-center text-sm text-gray-500 my-10">
					© {{ new Date().getFullYear() }}
					<a
						href="https://codeplaces.com"
						class="hover:underline"
						target="_blank"
						>CodePlaces</a
					>. Licenced Software.
				</p>
			</div>
		</div>
	</body>
</template>
