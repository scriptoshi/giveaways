<script setup>
import {ref} from "vue";

import {ChevronRightIcon, EnvelopeIcon as MailIcon, UserIcon} from "@heroicons/vue/24/outline";
import {Link, useForm} from "@inertiajs/vue3";
import {get} from "@vueuse/core";
import axios from "axios";
import {HiSolidChevronRight} from "oh-vue-icons/icons";
import {useAccount, useDisconnect, useSignMessage} from "use-wagmi";

import Loading from "@/Components/Loading.vue";
import ModalSkeleton from "@/Components/ModalSkeleton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import RegisterInput from "@/Pages/Auth/RegisterInput.vue";
import {useRegisterModal} from "@/Wagmi/hooks/RegisterModal";
import {useAddress} from "@/Wagmi/hooks/useTxHash";
import {useWagmiModalToggle} from "@/Wagmi/hooks/useWagmiModalToggle";
const {isOpen, close} = useRegisterModal();
const {address, connector, status} = useAccount();
const {signMessageAsync} = useSignMessage();
const [shortAddress, AddressLink] = useAddress(address);
const {open} = useWagmiModalToggle();
const form = useForm({
	username: "",
	registermodal: true,
	email: "",
	address: "",
	signature: null,
	terms: false,
});
const {disconnect} = useDisconnect();
const loading = ref(false);
const error = ref(false);
const submit = async ({address, connector}) => {
	loading.value = true;
	error.value = false;
	const signature = await signRegistration(address);
	if (signature)
		form.transform((form) => {
			form.signature = signature;
			form.address = get(address);
			form.provider = get(connector)?.name;
			return form;
		}).post(window.route("register"), {
			preserveState: false,
			onSuccess: () => {
				close();
				loading.value = false;
			},
		});
};

const signRegistration = async (address) => {
	// avoid redirect loop
	const {data} = await axios.post(window.route("nonce"), {
		address: get(address),
	});
	try {
		const signature = await signMessageAsync({message: data.nonce});
		return signature;
	} catch (e) {
		loading.value = false;
		error.value = e.message ?? e.toString();
	}
};
const save = () => {
	if (status.value !== "connected") {
		close();
		open({onConnect: submit});
		return;
	}
	return submit({address, connector});
};
</script>
<template>
	<ModalSkeleton
		@close="close"
		:isOpen="isOpen"
	>
		<template #header>
			<div class="px-6 lg:px-8">Create an Account</div>
		</template>

		<div class="px-6 pb-6 lg:px-8">
			<span
				v-if="error"
				class="text-red font-semibold"
				>{{ error }}</span
			>
			<form @submit.prevent="save">
				<JetValidationErrors />
				<div class="mt-4 space-y-4">
					<div class="space-y-1">
						<label class="text-base text-gray-600 dark:text-gray-300">{{
							$t("Public Username")
						}}</label>
						<RegisterInput
							placeholder="Eg sleep.finance"
							v-model="form.username"
							:error="form.errors.username"
						>
							<UserIcon class="w-5 h-5 text-gray-400" />
							<template #info>
								<span class="font-semibold">{{ $t("Use One Word!") }}</span>
								{{ $t("This will be shown to other users") }}
							</template>
						</RegisterInput>
					</div>
					<RegisterInput
						placeholder="Email address"
						v-model="form.email"
						:error="form.errors.email"
					>
						<MailIcon class="w-5 h-5 text-gray-400" />
					</RegisterInput>

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
					class="font-semibold flex justify-start space-x-5 items-center text-gray-600 dark:text-gray-300 my-3"
				>
					<div>
						{{ $t("Connected Account:") }}
						<a
							target="_blank"
							:href="AddressLink"
							class="text-emerald-500 hover:text-emerald-700 transition-colors"
							>{{ shortAddress }}</a
						>
					</div>
					<div>
						<a
							target="_blank"
							href="#"
							@click.prevent="disconnect"
							class="text-sky-500 hover:text-sky-700 transition-colors flex items-center"
							><span>{{ $t("Disconnect") }}</span>
							<VueIcon
								class="w-4 h-4"
								:icon="HiSolidChevronRight"
						/></a>
					</div>
				</div>
				<div class="flex items-center justify-center mt-4">
					<JetButton
						class="w-full flex justify-between py-4"
						:class="{'opacity-25': form.processing || loading}"
						:disabled="form.processing || loading"
					>
						<span>{{ $t("Create My Account") }}</span>
						<Loading v-if="form.processing || loading" />
						<ChevronRightIcon
							v-else
							class="text-white dark:text-gray-300 w-6 h-6"
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
							class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
							>{{ $t("Sign In") }}</Link
						>
					</p>
				</div>
			</form>
		</div>
	</ModalSkeleton>
</template>
