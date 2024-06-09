<script setup>
import {ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {HiSolidCheck, RiChargingPileLine} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DialogModal from "@/Jetstream/DialogModal.vue";

const props = defineProps({
	giveaway: Object,
	code: String,
});

const form = useForm({
	code: props.code,
});

const pump = () => {
	form.clearErrors();
	if (form.hasErrors) return;
	form.post(window.route("giveaways.bonus.code", {giveaway: props.giveaway?.uuid}), {
		preserveScroll: true,
	});
};
const showInfo = ref(false);
</script>
<template>
	<div
		class="p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<p class="mb-2 px-2">
			{{ $t("Claim bonus rewards") }}
			<a
				class="ml-1 hover:underline"
				href="#"
				@click.prevent="showInfo = true"
				>[what is this?]</a
			>
		</p>
		<div class="flex justify-between space-x-3 items-start">
			<div class="flex flex-1">
				<VueIcon
					class="w-6 h-6 mr-3 text-sky-500"
					:icon="RiChargingPileLine"
					:class="{'animate-shake': form.recentlySuccessful}"
				/>
				<FormInput
					size="xs"
					placeholder="Enter Bonus Code"
					class="w-full"
					v-model="form.code"
					:error="form.errors.code"
				/>
			</div>
			<PrimaryButton
				secondary
				@click.prevent="pump"
				:class="{'!text-emerald-500': form.recentlySuccessful}"
				class="!py-1 !px-2 !text-xs uppercase"
				><Loading
					v-if="form.processing"
					class="mr-2 -ml-1 !w-4 !h-4"
				/>
				<VueIcon
					:icon="HiSolidCheck"
					v-if="form.recentlySuccessful"
					class="mr-2 -ml-1 !w-4 !h-4"
				/>
				LOAD
			</PrimaryButton>
		</div>
		<DialogModal
			:show="showInfo"
			@close="showInfo = false"
			closeable
		>
			<template #title> About bonus reward. </template>
			<template #content>
				<ul class="list-disc list-inside">
					<li>
						{{
							$t(
								"The bonus rewards are distributed by our sales reps. If you are seeing this, means you indeed have a bonus code",
							)
						}}
					</li>
					<li>
						{{
							$t(
								"A bonus code allows you to add more sleep token rewards to your giveaway. Sleep tokens are claimed by participants who complete your quest.",
							)
						}}
					</li>
					<li>
						{{
							$t(
								"Please note that an empty sleep faucet doesnt stop your giveaway from progressing. so don't lose any sleep over zero sleep balance",
							)
						}}
					</li>
				</ul>
			</template>
		</DialogModal>
	</div>
</template>
