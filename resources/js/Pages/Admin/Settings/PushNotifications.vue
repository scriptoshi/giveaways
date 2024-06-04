<script setup>
import {BellIcon} from "@heroicons/vue/24/outline";
import {useForm, usePage} from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
const pushSettings = usePage().props.notification ?? {};
const form = useForm(pushSettings);
const send = (s) => {
	form.post(window.route("admin.settings.update"), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div class="py-4 px-6 lg:px-8">
		<h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">
			Manage Notification Setting
		</h3>

		<form
			class="space-y-6"
			action="#"
			@submit.prevent="send"
		>
			<div class="grid lg:grid-cols-2 gap-3">
				<div class="col-span-2">
					<h3 class="text-base text-emerald-500">
						{{ $t("Telegram Channel Notification") }}
					</h3>
					<p>This Allows your BOT to update a channel on new Presales.</p>
				</div>
				<FormInput
					:label="$t('Telegram Channel/Group name')"
					v-model="form.TELEGRAM_CHANNEL"
					:help="$t('You must add your bot as admin to the group')"
				/>
				<FormInput
					:label="$t('Telegram BOT Token')"
					v-model="form.TELEGRAM_BOT_TOKEN"
					class="col-span-2"
					:help="$t('Get this from Bot father')"
				/>
			</div>
			<hr />

			<div class="grid gap-4">
				<div>
					<h3 class="text-base text-emerald-500">{{ $t("Webpush Notification") }}</h3>
					<p>This Allows Users to get notification on chrome</p>
				</div>
				<FormInput
					:label="$t('Pusher Beams Instance Id')"
					v-model="form.BEAMS_INSTANCE_ID"
					:help="$t('Get one from pusher.com')"
				/>
				<FormInput
					:label="$t('Pusher beams secrec')"
					v-model="form.BEAMS_SECRET_KEY"
					:help="$t('Get this from Bot father')"
				/>
			</div>
			<div class="flex justify-end">
				<button
					class="text-gray-600 mt-3 bg-white-100 hover:bg-gray-100 hover:text-blue-600 border border-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:border-gray-600 font-medium rounded-sm text-base px-6 py-2.5 text-center inline-flex justify-center items-center"
				>
					<BellIcon class="w-5 h-5" />
					<span class="ml-2">Save Notification Settings</span>
				</button>
			</div>
		</form>
	</div>
</template>
