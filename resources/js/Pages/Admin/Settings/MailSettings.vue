<script setup>
import {EnvelopeIcon} from "@heroicons/vue/24/outline";
import {useForm, usePage} from "@inertiajs/vue3";
import {uid} from "uid";

import FormInput from "@/Components/FormInput.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
const mailSettings = usePage().props.mail ?? {};
const form = useForm(mailSettings);
const mailTypes = [
	{
		id: uid(),
		label: "SMTP",
		value: "smtp",
		url: null,
	},
	{
		id: uid(),
		label: "Mailgun",
		value: "mailgun",
		url: "https://www.mailgun.com/",
	},
	{
		id: uid(),
		label: "Amazon",
		value: "ses",
		url: "https://aws.amazon.com/ses/",
	},
	{
		id: uid(),
		label: "Postmark",
		value: "postmark",
		url: "https://postmarkapp.com/",
	},
];
const send = (s) => {
	form.post(window.route("admin.settings.update"), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div class="py-4 px-6 lg:px-8">
		<h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Manage Email Setting</h3>

		<form
			class="space-y-6"
			action="#"
			@submit.prevent="send"
		>
			<div class="grid lg:grid-cols-2 gap-x-3">
				<FormInput
					:label="$t('Mail from Address')"
					v-model="form.MAIL_FROM_ADDRESS"
				/>
				<FormInput
					:label="$t('Mail from Name')"
					v-model="form.MAIL_FROM_NAME"
				/>
			</div>
			<div>
				<label
					for="email"
					class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
					>Email Provider</label
				>
				<RadioSelect
					v-model="form.MAIL_MAILER"
					:list="mailTypes"
				/>
			</div>
			<template v-if="form.MAIL_MAILER === 'smtp'">
				<div>
					<h4>SMTP Setting</h4>
				</div>
				<div class="grid lg:grid-cols-3 lg:gap-x-3">
					<FormInput
						:label="$t('SMTP Host')"
						:error="form.errors.MAIL_HOST"
						v-model="form.MAIL_HOST"
						class="lg:col-span-2"
					/>
					<FormInput
						:label="$t('SMTP Port')"
						:error="form.errors.MAIL_PORT"
						v-model="form.MAIL_PORT"
					/>
				</div>
				<div class="grid lg:grid-cols-2 lg:gap-x-3">
					<FormInput
						:label="$t('SMTP Username')"
						:error="form.errors.MAIL_USERNAME"
						v-model="form.MAIL_USERNAME"
					/>
					<FormInput
						:label="$t('SMTP Password')"
						:error="form.errors.MAIL_PASSWORD"
						v-model="form.MAIL_PASSWORD"
					/>
				</div>
				<div>
					<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
						{{ $t("Mail Encryption") }}
					</label>
					<div class="flex gap-x-3">
						<label class="inline-flex items-center space-x-2">
							<input
								class="form-radio is-basic h-5 w-5 rounded-full border-slate-400/70 bg-slate-100 checked:!border-success checked:!bg-emerald-500 hover:!border-success focus:!border-success dark:border-navy-500 dark:bg-navy-900"
								name="encryption"
								v-model="form.MAIL_ENCRYPTION"
								value="tls"
								type="radio"
							/>
							<span>TLS</span>
						</label>
						<label class="inline-flex items-center space-x-2">
							<input
								class="form-radio is-basic h-5 w-5 rounded-full border-slate-400/70 bg-slate-100 checked:!border-success checked:!bg-emerald-500 hover:!border-success focus:!border-success dark:border-navy-500 dark:bg-navy-900"
								name="encryption"
								v-model="form.MAIL_ENCRYPTION"
								value="ssl"
								type="radio"
							/>
							<span>SLL</span>
						</label>
						<label class="inline-flex items-center space-x-2">
							<input
								class="form-radio is-basic h-5 w-5 rounded-full border-slate-400/70 bg-slate-100 checked:!border-success checked:!bg-emerald-500 hover:!border-success focus:!border-success dark:border-navy-500 dark:bg-navy-900"
								name="encryption"
								v-model="form.MAIL_ENCRYPTION"
								value=""
								type="radio"
							/>
							<span>None</span>
						</label>
					</div>
				</div>
			</template>
			<template v-if="form.MAIL_MAILER === 'mailgun'">
				<div>
					<h4>MailGun Settings</h4>
					<a href="https://www.mailgun.com/">https://www.mailgun.com</a>
				</div>
				<div>
					<FormInput
						:label="$t('Mailgun Secret')"
						:error="form.errors.MAILGUN_SECRET"
						v-model="form.MAILGUN_SECRET"
					/>
				</div>
				<div>
					<FormInput
						:label="$t('Mailgun Domain')"
						:error="form.errors.MAILGUN_DOMAIN"
						v-model="form.MAILGUN_DOMAIN"
					/>
				</div>
			</template>
			<template v-if="form.MAIL_MAILER === 'ses'">
				<div>
					<h4>Amazon SES</h4>
					<a href="https://aws.amazon.com/ses/">https://aws.amazon.com/ses/</a>
				</div>
				<div class="grid lg:grid-cols-2 gap-x-3">
					<FormInput
						:label="$t('AWS ACCESS KEY ID')"
						v-model="form.AWS_ACCESS_KEY_ID"
						:error="form.errors.AWS_ACCESS_KEY_ID"
					/>
					<FormInput
						:label="$t('AWS SECRET ACCESS KEY')"
						v-model="form.AWS_SECRET_ACCESS_KEY"
						:error="form.errors.AWS_SECRET_ACCESS_KEY"
					/>
				</div>
				<div>
					<FormInput
						:label="$t('AWS DEFAULT REGION')"
						:error="form.errors.AWS_DEFAULT_REGION"
						v-model="form.AWS_DEFAULT_REGION"
					/>
				</div>
			</template>
			<template v-if="form.MAIL_MAILER === 'postmark'">
				<div>
					<h4>Postmark Mail</h4>
					<a href="https://postmarkapp.com/">https://postmarkapp.com/</a>
				</div>
				<div>
					<FormInput
						:label="$t('Postmark Token')"
						:error="form.errors.POSTMARK_TOKEN"
						v-model="form.POSTMARK_TOKEN"
					/>
				</div>
			</template>
			<div class="flex justify-end">
				<button
					class="text-gray-600 mt-3 bg-white-100 hover:bg-gray-100 hover:text-blue-600 border border-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:border-gray-600 font-medium rounded-sm text-base px-6 py-2.5 text-center inline-flex justify-center items-center"
				>
					<EnvelopeIcon class="w-5 h-5" />
					<span class="ml-2">Save Mail Settings</span>
				</button>
			</div>
		</form>
	</div>
</template>
