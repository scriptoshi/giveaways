<script setup>
import {computed, reactive, ref} from "vue";

import {Cog6ToothIcon, ComputerDesktopIcon} from "@heroicons/vue/24/outline";
import {useForm, usePage} from "@inertiajs/vue3";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import vueFilePond from "vue-filepond";

import FormInput from "@/Components/FormInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Loading from "@/Components/Loading.vue";
import DescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import Switch from "@/Components/Switch.vue";
import MailSettings from "@/Pages/Admin/Settings/MailSettings.vue";

const form = useForm({
	...usePage().props.general,
	file: [],
});
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);
const server = reactive({
	url: "/filepond/api",
	process: "/process",
	revert: "/process",
	patch: "?patch=",
	headers: {
		"X-CSRF-TOKEN": computed(() => usePage().props.csrf_token),
	},
});
const handleProcessFile = (error, file) => {
	if (error) console.log(error);
	form.LOGO = file.serverId;
};
const type = ref(null);
const save = (s) => {
	type.value = s;
	form.post(window.route("admin.settings.update"), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
		<div class="lg:col-span-2">
			<div class="card card-border">
				<div>
					<div class="flex card-body items-center justify-between mb-6">
						<h4>Database Settings</h4>
					</div>
					<div class="max-w-xl mx-auto space-y-4 mb-6">
						<div class="grid lg:grid-cols-3 gap-x-3">
							<FormInput
								class="lg:col-span-2"
								:label="$t('Database Host')"
								v-model="form.DB_HOST"
								:error="form.errors.DB_HOST"
							/>
							<FormInput
								:label="$t('Database Port')"
								v-model="form.DB_PORT"
								:error="form.errors.DB_PORT"
							/>
						</div>

						<FormInput
							:label="$t('Database Name')"
							v-model="form.DB_DATABASE"
							:error="form.errors.DB_DATABASE"
						/>
						<FormInput
							:label="$t('Database User')"
							v-model="form.DB_USERNAME"
							:error="form.errors.DB_USERNAME"
						/>
						<FormInput
							:label="$t('Database Password')"
							v-model="form.DB_USERNAME"
							:error="form.errors.DB_USERNAME"
						/>

						<div class="flex justify-end">
							<button
								@click.prevent="save('settings')"
								class="text-gray-600 my-4 bg-white-100 hover:bg-gray-100 hover:text-blue-600 border border-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:border-gray-600 font-medium rounded-sm text-base px-6 py-2.5 text-center inline-flex justify-center items-center"
							>
								<Loading v-if="form.processing && busy == 'settings'" />
								<Cog6ToothIcon
									v-else
									class="w-5 h-5"
								/>
								<span class="ml-2">Save Database Settings</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="card mt-8 card-border">
				<div class="card-body">
					<div class="block">
						<MailSettings />
					</div>
				</div>
			</div>
		</div>
		<div>
			<div class="card card-border">
				<div class="card-body">
					<div class="flex items-center justify-between mb-4">
						<h4>Site Config</h4>
					</div>
					<div class="grid gap-y-4">
						<FormInput
							:label="$t('Site Name')"
							v-model="form.APP_NAME"
							:error="form.errors.APP_NAME"
						/>
						<div>
							<InputLabel>
								{{ $t("Site Description") }}
							</InputLabel>
							<DescriptionTextArea
								:rows="3"
								v-model="form.DESCRIPTION"
							/>
							<p class="text-xs font-semibold text-red">
								{{ form.errors.DESCRIPTION }}
							</p>
						</div>
						<div>
							<InputLabel>
								{{ $t("Admin address") }}
							</InputLabel>
							<p class="text-xs mb-1">
								{{ $t("Enter Admin Addresses separated by a comma") }}
							</p>
							<DescriptionTextArea
								v-model="form.ADMIN"
								:emoji="false"
							/>
							<p class="text-xs font-semibold text-red">
								{{
									form.errors.ADMIN ??
									$t("Changing this could lock you out of admin area")
								}}
							</p>
						</div>

						<FormInput
							:label="$t('Site URL')"
							v-model="form.APP_URL"
							:help="$t('Change this to your site URL')"
							:error="form.errors.APP_URL"
						/>
						<div class="flex items-center">
							<div>
								<FilePond
									name="filepond"
									ref="pond"
									class-name="logo filepond fp-bordered label-icon w-18 "
									label-idle="Upload Logo"
									:allow-multiple="false"
									:allowImagePreview="true"
									stylePanelAspectRatio="1:1"
									stylePanelLayout="compact circle"
									labelIdle="<svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewbox='0 0 24 24' stroke='currentColor'>
                                      <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'></path>
                                    </svg>"
									accepted-file-types="image/jpeg, image/png"
									:server="server"
									v-model="form.LOGO"
									:files="form.files"
									@processfile="handleProcessFile"
								/>
							</div>
							<p
								class="text-sm mb-4 ml-4 font-medium text-gray-900 dark:text-gray-300"
							>
								Site Logo
							</p>
						</div>
						<FormInput
							:label="$t('Wallet Connect ProjectID')"
							v-model="form.WALLETCONNET_PROJECT_ID"
							:help="$t('Change this Immediately')"
							:error="form.errors.WALLETCONNET_PROJECT_ID"
						/>
						<FormInput
							:label="$t('Airdrop Signatures EIP712 name')"
							v-model="form.EIP712"
							:help="$t('Change this only before deploying')"
							:error="form.errors.EIP712"
						/>

						<Switch v-model="form.APP_DEBUG">Enable App Debug</Switch>
						<button
							@click.prevent="save('config')"
							class="text-gray-600 w-full my-4 bg-white-100 hover:bg-gray-100 hover:text-blue-600 border border-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:border-gray-600 font-medium rounded-sm text-base px-6 py-2.5 text-center inline-flex justify-center items-center"
						>
							<Loading v-if="form.processing && busy == 'config'" />
							<ComputerDesktopIcon
								v-else
								class="w-5 h-5"
							/>
							<span class="ml-2">Save Configuration</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style>
.filepond.filepond--root[data-style-panel-layout~="circle"] .filepond--file [data-align*="right"] {
	right: calc(50% - 1em);
	top: calc(50% - 1em);
}
</style>
