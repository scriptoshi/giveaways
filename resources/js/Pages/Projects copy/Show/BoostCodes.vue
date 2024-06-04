<script setup>
import {useForm} from "@inertiajs/vue3";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";

const props = defineProps({
	project: Object,
	chain: Object,
});
const verifyform = useForm({
	kyc_link: props.project.kyc_link,
	doxx_link: props.project.doxx_link,
	audit_link: props.project.audit_link,
});

const verify = () => {
	verifyform.put(window.route("projects.verify", {project: props.project.slug}), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div
		class="shadow-sm dark:shadow-lg text-sm dark:text-gray-300 dark-text-emerald-400-text-dark p-4 rounded-md bg-white dark:bg-gray-800"
	>
		<div class="flex mb-2 justify-between">
			<h3 class="text-base">Verification Info</h3>
			<h3
				v-if="project.isSubmitted"
				class="text-base border px-2 rounded-sm ml-2"
				:class="{
					'text-green-500 border-green-500': project.isVerified,
					'text-amber-500  border-amber-500': project.isReviewed && !project.isVerified,
					'text-purple-500  border-purple-500': project.isPending,
				}"
			>
				{{ project.isVerified ? "Verified" : project.isPending ? "Pending" : "Reviewed" }}
			</h3>
		</div>
		<p class="text-xs mt-1">
			Changing this info will require fresh verification. Badge will be be removed during this
			period
		</p>
		<form @submit.prevent="verify">
			<div class="transition-all py-2 text-sm border-b border-dashed dark:border-gray-600">
				<FormInput
					v-model="verifyform.kyc_link"
					:error="verifyform.errors.kyc_link"
					label="Kyc link"
				/>
				<p class="text-xs">
					We dont do kyc, we accept kyc links from pinksale or other recognized launchpad
					or kyc doxxers
				</p>
			</div>
			<div class="transition-all py-2 text-sm border-b border-dashed dark:border-gray-600">
				<FormInput
					v-model="verifyform.audit_link"
					:error="verifyform.errors.audit_link"
					label="Audit link"
				/>
				<p class="text-xs">
					We dont audit contracts!, we accept audit links from pinksale or other
					recognized launchpad or kyc doxxers
				</p>
			</div>
			<div class="transition-all py-2 text-sm border-b border-dashed dark:border-gray-600">
				<FormInput
					v-model="verifyform.doxx_link"
					:error="verifyform.errors.doxx_link"
					label="Doxx Video link"
				/>
				<p class="text-xs">
					We accept youtube video links for a AMA with project owner clear visible
				</p>
			</div>
			<div class="transition-all py-2 text-sm border-b border-dashed dark:border-gray-600">
				<CollapseTransition>
					<div
						v-show="verifyform.recentlySuccessful && !$page.props.error"
						class="text-green-500 max-w-sm font-semibold my-6 border rounded-[3px] text-center px-4 py-2 border-green-500"
					>
						{{ $t("Projects Verification Queued for next 48 hours") }}
					</div>
				</CollapseTransition>
			</div>
			<div
				class="transition-all flex justify-end py-2 text-sm border-b border-dashed dark:border-gray-600"
			>
				<div class="flex items-end gap-1 text-right">
					<PrimaryButton
						:disabled="verifyform.processing"
						primary
						type="submit"
					>
						<Loading
							v-if="verifyform.processing"
							class="mr-2 -ml-1 !text-white"
						/>
						{{ $t("Request Review") }}
					</PrimaryButton>
				</div>
			</div>
		</form>
	</div>
</template>
