<script setup>
import {ref} from "vue";

import Loading from "@/Components/Loading.vue";
import {useExcludeFromTaxFees} from "@/hooks/token/useAdminTax";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
const props = defineProps({
	token: Object,
	contractName: String,
});

const tax = useExcludeFromTaxFees(props.token, props.contract);
const showConfirmation = ref(false);
</script>
<template>
	<div class="w-full">
		<hr class="my-8 border-gray-200 dark:border-gray-700" />
		<div
			v-if="tax.isExcludedFromFee"
			class="badge space-x-2.5 mb-3"
		>
			<h3 class="text-success text-sm">
				{{ $t("{contractName} is Excluded from Fees", {contractName}) }}
			</h3>
		</div>
		<template v-else>
			<h3 class="text-sm mt-6 mb-3 w-full text-red-500 dark:text-gray-300">
				{{
					$t("Please Exempt {contractName} from Fees on your token contract", {
						contractName,
					})
				}}
			</h3>
			<button
				@click="showConfirmation = true"
				:disabled="showConfirmation"
				class="bg-white dark:bg-gray-700 hover:bg-gray-50 hover:dark:bg-gray-800 rounded-lg py-3 border border-gray-200 dark:border-gray-600 w-full transition duration-200 text-gray-900 dark:text-white font-semibold"
			>
				{{ $t("Exclude {contractName} from Fees", {contractName}) }}
			</button>

			<JetConfirmationModal
				:show="showConfirmation"
				@close="showConfirmation = false"
			>
				<template #title>
					{{ $t("Exclude {contractName} from Fees", {contractName}) }}
				</template>
				<template #content>
					<p v-if="tax.busy && tax.txlink">
						{{
							$t("Please wait as {contractName} is excluded from Fees!", {
								contractName,
							})
						}}
					</p>
					<p v-else>
						{{
							$t(
								"This will Exclude {contractName} from Fees.  This Action is required for {contractName} to work without failure. Proceed?",
								{contractName},
							)
						}}
					</p>
				</template>
				<template #footer>
					<h3
						v-if="tax.busy || tax.error"
						class="text-sm mr-4"
					>
						<p
							v-if="tax.error"
							class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
						>
							{{ tax.error }}
						</p>
						<p
							v-else
							class="col-span-3 text-xs font-semibold"
						>
							{{ tax.busy ? tax.status : $t("You can disable tax anytime")
							}}<a
								:href="tax.txlink"
								target="_blank"
								class="text-emerald-500"
								v-if="tax.busy && tax.tx"
								>{{ tax.tx }}</a
							>
						</p>
					</h3>
					<JetSecondaryButton
						:class="{'opacity-60 pointer-events-none': tax.busy}"
						:disabled="tax.busy"
						@click="showConfirmation = false"
					>
						{{ $t("Cancel") }}
					</JetSecondaryButton>
					<JetDangerButton
						class="ml-2"
						@click="tax.excludeFromFee()"
						:class="{'opacity-60 pointer-events-none': tax.busy}"
						:disabled="tax.busy"
					>
						<Loading
							v-if="tax.busy"
							class="mr-2 -ml-1 inline-block w-5 h-5"
						/>
						{{ $t("Proceed") }}
					</JetDangerButton>
				</template>
			</JetConfirmationModal>
		</template>
	</div>
</template>
