<script setup>
import {ref} from "vue";

import {router as Inertia} from "@inertiajs/vue3";
import axios from "axios";
import {MdErrorTwotone} from "oh-vue-icons/icons";
import {useAccount, useDisconnect, useSignMessage} from "use-wagmi";
import {useI18n} from "vue-i18n";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DangerButton from "@/Jetstream/DangerButton.vue";
import PrimaryButton from "@/Jetstream/SecondaryButton.vue";
import Modal from "@/Wagmi/components/Modal.vue";
import {logOut} from "@/Wagmi/hooks/authentication";
import {close, isOpen} from "@/Wagmi/hooks/useAddAccountModalToggle";
const {address, connector} = useAccount();
const {signMessageAsync} = useSignMessage();
const {disconnect} = useDisconnect();
const {t: $t} = useI18n();

const busy = ref();
const error = ref(true);
const errorMessage = ref();
const errorMessage2 = ref();
const signOut = async () => {
	disconnect();
	close();
	await logOut();
};

const authorize = async () => {
	if (!address.value) signOut();
	busy.value = true;
	errorMessage.value = null;
	errorMessage2.value = null;
	const {data} = await axios.post(window.route("nonce"), {
		address: address.value,
	});
	let signature;
	try {
		signature = await signMessageAsync({message: data.nonce});
	} catch (err) {
		console.log(err);
		error.value = $t("Signature Rejected.");
		busy.value = null;
		errorMessage.value = $t("Signature Rejected.");
		errorMessage2.value = null;
		return;
	}
	try {
		await axios.post(window.route("account.add"), {
			address: address.value,
			provider: connector.value.name,
			signature,
		});
	} catch (e) {
		console.log(e.response);
		if (e?.response?.status === 422) {
			errorMessage.value = e?.response?.data?.message;
			errorMessage2.value = e?.response?.data?.errors?.address[0] ?? null;
			busy.value = null;
			return null;
		}
		signOut();
	}
	error.value = null;
	Inertia.reload({
		onFinish() {
			busy.value = null;
			close();
		},
	});
};
</script>
<template>
	<Modal
		:show="isOpen && address"
		max-width="lg"
		:closeable="false"
	>
		<div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
			<div class="p-6 text-center">
				<div class="mt-8">
					<VueIcon
						class="mx-auto mb-4 text-gray-400 w-20 h-20 dark:text-gray-200"
						:icon="MdErrorTwotone"
					/>
					<CollapseTransition>
						<h3
							v-show="errorMessage"
							class="mb-1 text-lg font-normal text-red-600 dark:text-red-400"
						>
							{{ errorMessage }}
						</h3>
					</CollapseTransition>
					<CollapseTransition>
						<h3
							v-show="errorMessage2"
							class="mb-1 text-lg font-normal text-red-600 dark:text-red-400"
						>
							{{ errorMessage2 }}
						</h3>
					</CollapseTransition>
					<h3 class="mb-1 text-lg font-normal text-gray-500 dark:text-gray-400">
						{{ $t("Would you like to Add this Address to your Account?") }}
					</h3>
					<h2 class="mb-6 text-base text-emerald-700">{{ address }}</h2>
				</div>
				<div class="flex items-center justify-center">
					<DangerButton
						@click="signOut"
						class="mr-3"
					>
						<Loading
							class="-ml-1 mr-2 inline-flex"
							v-if="logout"
						/>
						{{ $t("No! Logout") }}
					</DangerButton>
					<PrimaryButton
						@click="authorize"
						:btn="busy ? '!left-0 !top-0' : ''"
					>
						<Loading
							class="-ml-1 mr-2 !text-gray-500 inline-flex"
							v-if="busy"
						/>{{ $t("Yes! Authorize") }}
					</PrimaryButton>
				</div>
			</div>
		</div>
	</Modal>
</template>
