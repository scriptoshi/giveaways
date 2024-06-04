<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import {MdSettings} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";

const form = useForm({
	...usePage().props.services,
	file: [],
});
const save = () => {
	form.post(window.route("admin.settings.update"), {
		preserveScroll: true,
	});
};
</script>
<template>
	<form @submit.prevent="save">
		<div class="grid gap-y-4">
			<FormInput
				:label="$t('Coin Layer Key')"
				v-model="form.COINLAYER_APIKEY"
				:help="$t('Used to display usd prices of coins')"
				:error="form.errors.COINLAYER_APIKEY"
			/>
			<FormInput
				:label="$t('Ankr Api  Key')"
				v-model="form.ANKR_KEY"
				:help="$t('Used to display USD price of custom coin')"
				:error="form.errors.ANKR_KEY"
			/>
			<FormInput
				:label="$t('Moralis Api Key')"
				v-model="form.MORALIS_API_KEY"
				:help="$t('Alternative for Ankr API Above')"
				:error="form.errors.MORALIS_API_KEY"
			/>
			<FormInput
				:label="$t('Pinata Api Key')"
				v-model="form.PINATA_API_KEY"
				:help="$t('Used for NFT launchpad')"
				:error="form.errors.PINATA_API_KEY"
			/>
			<FormInput
				:label="$t('Pinata Api Secret')"
				v-model="form.PINATA_API_SECRET"
				:help="$t('Used for NFT launchpad')"
				:error="form.errors.PINATA_API_SECRET"
			/>
			<button
				@click.prevent="save('config')"
				class="text-gray-600 w-full my-4 bg-white-100 hover:bg-gray-100 hover:text-blue-600 border border-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:border-gray-600 font-medium rounded-sm text-base px-6 py-2.5 text-center inline-flex justify-center items-center"
			>
				<Loading v-if="form.processing" />
				<VueIcon
					:icon="MdSettings"
					v-else
					class="w-5 h-5"
				/>
				<span class="ml-2">Save Services Settings</span>
			</button>
		</div>
	</form>
</template>
