<script setup>
import {ref} from "vue";

import {router} from "@inertiajs/vue3";
import axios from "axios";
import {useAccount, useSignMessage} from "use-wagmi";

import Pagination from "@/Components/Pagination.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import AccountManager from "@/Pages/Profile/Partials/AccountManager.vue";

defineProps({
	accounts: {type: Array, default: () => []},
});
const account = ref();
const nonce = ref();
const error = ref();
const {address} = useAccount();

const requestRemove = async (toDelete) => {
	error.value = null;
	nonce.value = null;
	const {data} = await axios.post(window.route("nonce"), {
		address: address.value,
	});
	nonce.value = data.nonce;
	account.value = toDelete;
};
const cancel = () => {
	nonce.value = null;
	account.value = null;
};
const {signMessageAsync} = useSignMessage();
const requestSignature = async () => {
	let signature;
	try {
		signature = await signMessageAsync({message: nonce.value});
	} catch (e) {
		error.value = e.message ?? e.toString();
		return;
	}
	if (!signature) return;
	router.delete(window.route("accounts.delete", account.value.id), {
		data: {signature},
		preserveScroll: true,
		onFinish: () => {
			account.value = null;
			nonce.value = null;
		},
	});
};
</script>
<template>
	<AppLayout>
		<div
			class="container px-8 lg:px-16 mx-auto pt-8 mb-8 min-h-[calc(100vh-138px)] relative pb-14"
		>
			<div class="space-y-6 sm:px-6 lg:px-0 md:col-span-9">
				<section aria-labelledby="plan-heading">
					<div class="gap-y-3 mt-6">
						<div class="mt-5 bg-white dark:bg-gray-800 rounded-lg py-5">
							<div
								class="pb-5 px-5 border-b border-gray-200 dark:border-gray-600 flex flex-row justify-between"
							>
								<h3 class="text-gray-900 dark:text-white text-base">
									{{ $t("Connected Wallet Accounts") }}
								</h3>
							</div>
							<table class="is-hoverable w-full text-left text-base font-medium">
								<tbody v-if="accounts.data.length > 0">
									<AccountManager
										v-for="account in accounts.data"
										:key="account.id"
										:account="account"
										@remove="requestRemove"
									/>
								</tbody>
								<tbody v-else>
									<tr>
										<td>
											<h3 class="w-full py-5 text-lg text-center">
												No Allocations
											</h3>
										</td>
									</tr>
								</tbody>
							</table>
							<Pagination :meta="accounts.meta" />
							<JetConfirmationModal
								:show="!!account"
								@close="showConfirmation = false"
							>
								<template #title>
									<span
										class="text-red"
										v-if="error"
										>{{ error }}</span
									>
									<span v-else>{{ $t("Delete Account") }}</span>
								</template>
								<template #content>
									<p>
										{{
											$t(
												"This will disconnect and permanently remove this account. Are you sure you want to proceed ?",
											)
										}}
									</p>
									<p>
										{{
											$t("Your Signature is required to complete this action")
										}}
									</p>
								</template>
								<template #footer>
									<JetSecondaryButton @click="cancel">
										{{ $t("Cancel") }}
									</JetSecondaryButton>
									<JetDangerButton
										class="ml-2"
										@click="requestSignature"
									>
										{{ $t("Approve Account Removal") }}
									</JetDangerButton>
								</template>
							</JetConfirmationModal>
						</div>
					</div>
				</section>
			</div>
		</div>
	</AppLayout>
</template>
