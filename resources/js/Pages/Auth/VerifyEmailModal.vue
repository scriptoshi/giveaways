<script setup>
import {computed} from "vue";

import {Link, useForm, usePage} from "@inertiajs/vue3";
import {MdErrorTwotone} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import Modal from "@/Components/ModalView.vue";
import VueIcon from "@/Components/VueIcon.vue";
import PrimaryButton from "@/Jetstream/Button.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";

const status = computed(() => usePage().props.status);
const verified = computed(() => usePage().props.verified);
const AuthCheck = computed(() => usePage().props.AuthCheck);

const form = useForm({
	code: null,
});

const resend = () => {
	form.code = null;
	form.post(window.route("verification.send"));
};

const activate = () => {
	form.post(window.route("user.activate"));
};

const verificationLinkSent = computed(() => status.value === "verification-link-sent");
</script>
<template>
	<Modal
		:show="!verified && AuthCheck && !route().current('profile.*')"
		max-width="lg"
		:closeable="false"
	>
		<div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
			<div class="p-6">
				<div class="mt-8">
					<div class="flex mb-4 w-full items-center justify-start space-x-4">
						<VueIcon
							class="text-gray-400 w-14 h-14 dark:text-gray-200"
							:icon="MdErrorTwotone"
						/>
						<h3>Activation Code</h3>
					</div>
					<FormInput
						size="md"
						class="my-3"
						v-model="form.code"
						:error="form.errors.code"
					/>
					<div class="mb-4 text-sm font-semibold text-gray-600 dark:text-gray-400">
						Please enter the actiovation code we just emailed to you? If you didn't
						receive the email, we will gladly send you another.
					</div>

					<div
						v-if="verificationLinkSent"
						class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-300"
					>
						A new verification link has been sent to the email address you provided in
						your profile settings.
					</div>
					<div class="my-4 w-full flex items-center space-x-4">
						<Link
							:href="route('profile.show')"
							class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-200"
						>
							Edit Profile</Link
						>

						<Link
							:href="route('logout')"
							method="post"
							as="button"
							class="underline text-sm text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 hover:text-gray-900 ml-2 transition-colors duration-200"
						>
							Log Out
						</Link>
					</div>
				</div>
				<hr />
				<div class="pt-4">
					<PrimaryButton
						class="!py-2"
						@click.prevent="resend"
					>
						<Loading
							class="-ml-1 mr-2 inline-flex"
							v-if="form.processing && !form.code"
						/>
						{{ $t("Resend") }}
					</PrimaryButton>
					<SecondaryButton @click.prevent="activate">
						<Loading
							class="-ml-1 mr-2 !text-gray-500 inline-flex"
							v-if="form.processing && form.code"
						/>{{ $t("Activate!") }}
					</SecondaryButton>
				</div>
			</div>
		</div>
	</Modal>
</template>
