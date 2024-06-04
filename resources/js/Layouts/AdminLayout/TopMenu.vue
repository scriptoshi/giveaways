<script setup>
import {computed, ref} from "vue";

import {PopoverButton} from "@headlessui/vue";
import {Link as InertiaLink, usePage} from "@inertiajs/vue3";
import {
	FaPowerOff,
	LaUserPlusSolid,
	MdSettingsOutlined,
	RiAwardLine,
	RiUserLine,
} from "oh-vue-icons/icons";
import {useAccount, useDisconnect} from "use-wagmi";
import {useI18n} from "vue-i18n";

import Loading from "@/Components/Loading.vue";
import Logo from "@/Components/SiteLogo.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AnimatedMobileIcon from "@/Layouts/AdminLayout/AnimatedMobileButton.vue";
import DarkSwitch from "@/Layouts/AdminLayout/DarkSwitch.vue";
import {logOut} from "@/Wagmi/hooks/authentication";
import Web3Menu from "@/Wagmi/Menu.vue";
import Network from "@/Wagmi/Network.vue";
const {disconnect} = useDisconnect();
const signOut = () => {
	disconnect();
	logOut();
};
const user = computed(() => usePage().props.user);
const {address: account} = useAccount();
const {t} = useI18n();
const logoUrl = computed(() => usePage().props.config.appLogo);
const props = defineProps({
	modelValue: Boolean,
});
const emit = defineEmits(["update:modelValue"]);
const toggle = () => emit("update:modelValue", !props.modelValue);
const userNavigation = [
	{
		name: t("Admin Profile"),
		href: window.route("profile.show", account.value),
		icon: RiUserLine,
		active: window.route().current("profile.show"),
	},
	{
		name: t("Manage Nft"),
		href: window.route("accounts.index"),
		icon: MdSettingsOutlined,
		active: window.route().current("account.*"),
	},
	{
		name: t("Manage Tokens"),
		href: window.route("accounts.index"),
		icon: MdSettingsOutlined,
		active: window.route().current("account.*"),
	},

	{
		name: t("Manage Users"),
		href: window.route("user.referrals"),
		icon: LaUserPlusSolid,
		active: false,
	},
	{
		name: t("Manage Bookies"),
		href: "https://docs.sleep.finance",
		icon: RiAwardLine,
		active: false,
	},
];
const load = ref(false);
const logout = async () => {
	load.value = true;
	signOut();
};
</script>
<template>
	<nav
		class="bg-white dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 fixed z-30 w-full"
	>
		<div class="px-3 py-3 lg:px-5 lg:pl-3">
			<div class="flex items-center justify-between">
				<div class="flex items-center justify-start">
					<button
						@click="toggle"
						class="lg:hidden mr-2 text-gray-600 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded"
					>
						<AnimatedMobileIcon :open="modelValue" />
					</button>
					<a
						href="/admin"
						class="text-2xl font-bold flex items-center lg:ml-2.5"
					>
						<img
							v-if="logoUrl"
							:src="logoUrl"
							class="h-6 mr-2"
							alt="Launcho"
						/>
						<Logo
							v-else
							class="h-8 mr-2"
						/>
						<span
							class="self-center hidden md:flex text-emerald-500 whitespace-nowrap"
							>{{ $page.props.config.appName }}</span
						>
					</a>
				</div>
				<div class="flex space-y-0 flex-row items-center space-x-3">
					<Network />
					<Web3Menu>
						<template
							v-if="user?.profile_photo_path"
							#profile
						>
							<img
								class="w-6 h-6 -ml-2 border-2 border-emerald-600 mr-2 rounded-full"
								:src="user?.profile_photo_url"
							/>
						</template>
						<PopoverButton
							as="div"
							v-for="item in userNavigation"
							:key="item.name"
						>
							<InertiaLink
								:href="item.href"
								:class="[
									item.active
										? 'bg-gray-100 dark:bg-gray-500 text-gray-900 dark:text-gray-400'
										: 'text-gray-700 dark:text-gray-300',
									'group flex items-center px-6 py-2 text-base hover:bg-emerald-200/50 hover:text-emerald-600 dark:hover:bg-emerald-600/20 dark:hover:text-emerald-300',
								]"
							>
								<VueIcon
									:icon="item.icon"
									class="mr-3 h-5 w-5 text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-300"
									aria-hidden="true"
								/>
								{{ item.name }}
							</InertiaLink>
						</PopoverButton>
						<hr class="border-t border-gray-200 dark:border-gray-600 mt-2" />
						<InertiaLink
							href="#"
							@click="logout"
							class="group text-gray-700 dark:text-gray-300 flex items-center px-6 py-4 text-base hover:bg-gray-200 dark:hover:bg-gray-600 sm:rounded-b-xl rounded-b-none"
						>
							<Loading
								v-if="load"
								class="!text-emerald-500 !mr-3 !h-5 !w-5"
							/>
							<VueIcon
								:icon="FaPowerOff"
								v-else
								class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300"
								aria-hidden="true"
							/>
							{{ $t("Sign out") }}
						</InertiaLink>
					</Web3Menu>
					<DarkSwitch />
				</div>
			</div>
		</div>
	</nav>
</template>
