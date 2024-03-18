<template>
	<JetActionSection>
		<template #title> Delete Account </template>
		<template #description> Permanently delete your account. </template>
		<template #content>
			<div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
				Once your account is deleted, all of its resources and data will be permanently
				deleted. Before deleting your account, please download any data or information that
				you wish to retain.
			</div>

			<div class="mt-5">
				<JetDangerButton @click="confirmUserDeletion"> Delete Account </JetDangerButton>
			</div>

			<!-- Delete Account Confirmation Modal -->
			<JetDialogModal
				:show="confirmingUserDeletion"
				@close="closeModal"
			>
				<template #title> Delete Account </template>

				<template #content>
					Are you sure you want to delete your account? Once your account is deleted, all
					of its resources and data will be permanently deleted. Please provide your valid
					Signature one last time, to confirm you would like to permanently delete your
					account.
					<div class="mt-4 mb-4"></div>
				</template>

				<template #footer>
					<JetSecondaryButton @click="closeModal"> Cancel </JetSecondaryButton>

					<JetDangerButton
						class="ml-3"
						:class="{'opacity-25': form.processing}"
						:disabled="form.processing"
						@click="deleteUser"
					>
						Delete Account
					</JetDangerButton>
				</template>
			</JetDialogModal>
		</template>
	</JetActionSection>
</template>

<script>
import {defineComponent} from "vue";

import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import {useSignature} from "@/Pages/Profile/Partials/signature";

export default defineComponent({
	components: {
		JetActionSection,
		JetDangerButton,
		JetDialogModal,
		JetSecondaryButton,
	},
	setup() {
		return useSignature();
	},
	data() {
		return {
			confirmingUserDeletion: false,

			form: this.$inertia.form({
				password: "",
			}),
		};
	},

	methods: {
		confirmUserDeletion() {
			this.confirmingUserDeletion = true;
			setTimeout(() => this.$refs.password.focus(), 250);
		},

		async deleteUser() {
			this.form.password = await this.signMessage();
			this.form.delete(window.route("current-user.destroy"), {
				preserveScroll: true,
				onSuccess: () => this.closeModal(),
				onError: () => this.$refs.password.focus(),
				onFinish: () => this.form.reset(),
			});
		},

		closeModal() {
			this.confirmingUserDeletion = false;
			this.form.reset();
		},
	},
});
</script>
