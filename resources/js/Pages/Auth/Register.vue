<script setup>
import {ref} from "vue";

import {ChevronRightIcon, EnvelopeIcon as MailIcon, UserIcon} from "@heroicons/vue/24/outline";
import {Head, Link, useForm} from "@inertiajs/vue3";
import {get} from "@vueuse/core";
import axios from "axios";
import {useAccount, useSignMessage} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import SiteLogo from "@/Components/SiteLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import Meta from "@/Layouts/AppLayout/Meta.vue";
import RegisterInput from "@/Pages/Auth/RegisterInput.vue";
import {useAuth} from "@/Wagmi/hooks/authentication";
import {useAddress} from "@/Wagmi/hooks/useTxHash";
import {useWagmiModalToggle} from "@/Wagmi/hooks/useWagmiModalToggle";
const {address, connector, status} = useAccount();
const {error, busy: signIn, init} = useAuth();
init();
// import { router as Inertia } from "@inertiajs/vue3";
// const {account, web3, error, provider, active, signIn, signOut} = useActiveWeb3Vue();
const [shortAddress, AddressLink] = useAddress(address);
const {open, isOpen} = useWagmiModalToggle();
const {signMessageAsync} = useSignMessage();
isOpen.value = false;
const form = useForm({
	username: "",
	email: "",
	address: "",
	signature: null,
	terms: false,
});

const loading = ref(false);
const submit = async ({address, connector}) => {
	const signature = await signRegistration(address);
	if (signature) {
		form.transform((form) => {
			form.signature = signature;
			form.address = get(address);
			form.provider = get(connector).name;
			return form;
		}).post(window.route("register"), {
			onSuccess: () => {
				loading.value = false;
				error.value = null;
				signIn.value = false;
			},
		});
	} else {
		loading.value = false;
	}
};

const signRegistration = async (address) => {
	// avoid redirect loop
	const {data} = await axios.post(window.route("nonce"), {
		address: get(address),
	});
	const signature = await signMessageAsync({message: data.nonce});
	return signature;
};
const save = () => {
	if (status.value === "connected") {
		loading.value = true;
		return submit({connector, address});
	}
	open({onConnect: submit, onError: (e) => (error.value = e.message ?? e.toString())});
};
const appName = window.document.getElementsByTagName("title")[0]?.innerText || "gas";
</script>
<template>
	<Meta />
	<Head title="Register" />
	<div class="w-full min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-1">
		<main class="container w-full">
			<div class="flex px-4 flex-col justify-between min-h-full">
				<div class="max-w-3xl mx-auto pt-[8rem]">
					<div class="text-left">
						<div class="flex items-center">
							<SiteLogo class="h-12 w-24 sm:w-12 mr-4" />
							<h2 class="text-5xl font-semibold text-gray-600 dark:text-gray-100">
								{{ $t("Connect Your Web3 Account") }}
							</h2>
						</div>
						<div class="text-2xl my-3 text-gray-700 dark:text-gray-300">
							{{
								$t(
									"Manage your profile and claim rewards from launches, staking and bookies you participated in. Join communities and start building your web3 reputation.",
								)
							}}
						</div>
						<div
							v-if="error"
							class="mt-4 mb-4 font-semibold text-red-500 dark:text-red-400"
						>
							{{ error }}
						</div>
						<div
							v-else
							class="mt-4 mb-4 font-semibold text-emerald-500 dark:text-emerald-300"
						>
							{{ $t("It seems  you have not created an account") }}
						</div>
					</div>
					<form @submit.prevent="save">
						<JetValidationErrors />
						<div class="mt-4 space-y-4">
							<div class="grid md:grid-cols-2 gap-y-4 gap-x-2">
								<div class="space-y-1">
									<label class="text-base text-gray-600 dark:text-gray-300">{{
										$t("Public Username")
									}}</label>
									<RegisterInput
										placeholder="Eg Quest Pro"
										v-model="form.username"
										:error="form.errors.username"
									>
										<UserIcon class="w-5 h-5 text-emerald-500" />
									</RegisterInput>
								</div>
								<div class="space-y-1">
									<label class="text-base text-gray-600 dark:text-gray-300">{{
										$t("Email Address")
									}}</label>
									<RegisterInput
										placeholder="Email address"
										v-model="form.email"
										:error="form.errors.email"
									>
										<MailIcon class="w-5 h-5 text-emerald-500" />
									</RegisterInput>
								</div>
							</div>
							<div class="mt-4 flex items-center space-x-2">
								<input
									class="form-checkbox is-basic h-5 w-5 rounded border-gray-400/70 bg-gray-100 checked:!border-emerald-500 checked:!bg-emerald-500 hover:!border-emerald-500 focus:!border-emerald-500 dark:border-gray-500 dark:bg-gray-900"
									type="checkbox"
									v-model="form.terms"
								/>
								<div class="text-gray-600 dark:text-gray-400">
									{{ $t("I Accept the") }}
									<a
										target="_blank"
										:href="route('terms.show')"
										class="underline text-sm text-gray-600hover:text-gray-900"
										>{{ $t("Terms") }}
									</a>
									{{ $t("and") }}
									<a
										target="_blank"
										:href="route('policy.show')"
										class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900"
										>{{ $t("Privacy Policy") }}</a
									>
								</div>
							</div>
						</div>
						<div
							v-if="address"
							class="font-semibold text-gray-600 dark:text-gray-300 my-3"
						>
							{{ $t("Connected Account:") }}
							<a
								target="_blank"
								:href="AddressLink"
								class="text-emerald-500 hover:text-emerald-700 transition-colors"
								>{{ shortAddress }}</a
							>
						</div>
						<div class="flex items-center mt-4">
							<JetButton
								class="w-full flex justify-between py-4"
								:class="{'opacity-25': form.processing}"
								:disabled="form.processing || isOpen"
							>
								<span v-if="address">{{ $t("Connect Your Wallet") }}</span>
								<span v-else>Create An Account</span>
								<Loading
									v-if="loading.value || form.processing"
									class="-mr-1 ml-2"
								/>
								<ChevronRightIcon
									v-else
									class="text-white dark:text-gray-300 w-6 h-6 -mr-1 ml-2"
								/>
							</JetButton>
						</div>
						<div class="mt-4 text-xs+">
							<p class="line-clamp-1">
								<span class="text-gray-600 dark:text-gray-300"
									>{{ $t("Already have an account ?") }}
								</span>
								<Link
									:href="route('login')"
									class="text-primary ml-2 transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
									>{{ $t("Sign In") }}</Link
								>
							</p>
						</div>
					</form>
					<div>
						<span class="block text-gray-600 dark:text-gray-400 font-"
							>© 2019-<span id="currentYear">{{ new Date().getFullYear() }}</span>
							<a
								class="ml-2 text-primary"
								href="/"
							>
								{{ appName }}</a
							>
							{{ $t("All Rights Reserved.") }}
						</span>
					</div>
				</div>
			</div>
		</main>
	</div>
</template>
