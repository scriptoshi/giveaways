<script setup>
import {reactive, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {UseTimeAgo} from "@vueuse/components";
import {useLocalStorage} from "@vueuse/core";
import axios from "axios";
import upperFirst from "lodash/upperFirst";
import {DateTime} from "luxon";
import {
	BiCircleSquare,
	HiSolidChevronLeft,
	HiSolidChevronRight,
	HiSolidX,
	MdTokenSharp,
} from "oh-vue-icons/icons";
import {uid} from "uid";
import {DatePicker} from "v-calendar";
import isURL from "validator/lib/isURL";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
import chains from "@/Pages/Projects/Create/ChainSelect/chains.json";
import ChainSelect from "@/Pages/Projects/Create/ChainSelect/ChainSelect.vue";
import CreateLayout from "@/Pages/Projects/Create/CreateLayout.vue";
import ProjectDetails from "@/Pages/Projects/Create/ProjectDetails.vue";

const props = defineProps({
	type: String,
	formId: String,
});
const data = useLocalStorage(`my-project-form`, {});
const formFields = {
	// token info
	name: null,
	title: null,
	symbol: null,
	chainId: null,
	address: null,
	presale_page: null,
	logo_uri: null,
	logo_upload: false,
	logo_path: null,
	chart: null,
	description: "",
	is_meme: false,
	is_game: false,
	launch_at: null,
	launchDone: false,
	type: props.type,
	// project info
	youtube: "",
	website: "",
	docs: "",
	whitepaper: "",
	telegram: "",
	linktree: "",
	snapchat: "",
	twitter: "",
	tiktok: "",
	github: "",
	discord: "",
	facebook: "",
	instagram: "",
	medium: "",
	reddit: "",

	// for user verfication of token details
	decimals: "",
	balance: "",
	supply: "",
};
const form = useForm({...formFields, ...(data.value ?? {})});
watch(form, (form) => {
	data.value = form;
});
const step = useLocalStorage(`airdrop-step-${props.type}-${props.formId}`, 0);
const stepErrors = useLocalStorage(`airdrop-stepErrors-${props.type}-${props.formId}`, {
	0: null,
	1: null,
	2: null,
});
const verified = useLocalStorage(`airdrop-verified-${props.type}-${props.formId}`, {
	0: false,
	1: false,
	2: false,
});
const {t} = useI18n();
const goNext = () => (step.value += 1);
const goBack = () => (step.value -= 1);
const reset = () => {
	data.value = {};
	form.defaults({...formFields});
	form.reset();
	step.value = 0;
};
/**
 * Check token and advance form to step One
 */
const goToStepOne = () => {
	// some basic validation
	form.clearErrors("name", "symbol", "logo_uri", "address", "launch_at", "chainId");
	stepErrors.value[0] = null;
	if (!form.address) form.setError("address", t("Please provide a token Address"));
	if (!form.chainId?.name) form.setError("chainId", t("Please Select a chain"));
	else if (!form.symbol) form.setError("symbol", t("Could not load Token Details"));
	if (!form.logo_uri) form.setError("logo_uri", t("Please upload a project logo"));
	if (!form.launch_at) form.setError("launch_at", t("Please Set a launch date"));
	if (form.hasErrors) {
		stepErrors[0] = "Token verification failed";
		return;
	}
	verified.value[0] = true;
	step.value = 1;
};

const saveProject = async () => {
	form.clearErrors("title", "description", "website", "telegram", "twitter");
	stepErrors.value[1] = null;
	["title", "description", "website", "telegram", "twitter"].forEach((item) => {
		if (!form[item]) form.setError(item, t("{item} is required", {item: upperFirst(item)}));
		else if (["website", "telegram", "twitter"].includes(item) && !isURL(form[item]))
			form.setError(item, t("{item} link should be a valid url", {item: upperFirst(item)}));
	});
	if (form.hasErrors) {
		stepErrors[1] = "Project info failed validation";
		return;
	}
	form.transform((data) => {
		return {
			...data,
			chainId: data.chainId.id,
		};
	}).post(window.route("projects.store"), {
		onSuccess() {
			reset();
		},
	});
};

const typeOptions = reactive([
	{
		id: uid(),
		value: "token",
		label: "Tokens",
	},
	{
		id: uid(),
		value: "airdrop",
		label: "Airdrops",
	},
	{
		id: uid(),
		value: "presale",
		label: "Presale",
	},
	{
		id: uid(),
		name: "nft",
		label: "Nft",
	},
]);
const showSalesPage = ref(false);
watch(showSalesPage, (show) => {
	if (!show && form.presale_page) form.presale_page = null;
});
const load = ref(false);
const price = ref(0);
const loadToken = async () => {
	if (!form.address) return;
	load.value = true;
	const {data} = await axios.post(window.route("projects.token.info"), {
		contract: form.address,
	});
	load.value = false;
	if (!data) return;
	form.chainId = chains.find((c) => c.id === data.chainId);
	form.name = data.name;
	form.symbol = data.symbol;
	price.value = data.priceUsd;
	form.launchDone = true;
	form.logo_uri = data.logo;
	form.chart = data.chart;
	form.launch_at = DateTime.fromMillis(data.launch_at);
	if (!data.hasInfo) return;
	form.youtube = data.info.youtube ?? "";
	form.website = data.info.website ?? "";
	form.docs = data.info.docs ?? "";
	form.whitepaper = data.info.whitepaper ?? "";
	form.telegram = data.info.telegram ?? "";
	form.linktree = data.info.linktree ?? "";
	form.snapchat = data.info.snapchat ?? "";
	form.twitter = data.info.twitter ?? "";
	form.tiktok = data.info.tiktok ?? "";
	form.github = data.info.github ?? "";
	form.discord = data.info.discord ?? "";
	form.facebook = data.info.facebook ?? "";
	form.instagram = data.info.instagram ?? "";
	form.medium = data.info.medium ?? "";
	form.reddit = data.info.reddit ?? "";
};
watch(
	() => form.address,
	() => loadToken(),
);
form.launch_at = DateTime.now().plus({days: 2}).toJSDate();
</script>
<template>
	<CreateLayout
		:current="step"
		:errors="stepErrors"
	>
		<template #navigation>
			<div class="flex mt-12 space-x-3 items-center">
				<PrimaryButton
					:disabled="step === 0"
					secondary
					@click="goBack"
				>
					<VueIcon :icon="HiSolidChevronLeft" />
				</PrimaryButton>
				<PrimaryButton
					secondary
					:disabled="!verified[step] || step === 3"
					@click="goNext"
				>
					<VueIcon :icon="HiSolidChevronRight" />
				</PrimaryButton>
				<PrimaryButton
					primary
					@click="reset"
				>
					{{ $t("Clear") }}
				</PrimaryButton>
			</div>
		</template>
		<div class="md:col-span-6 space-y-6 md:pr-8">
			<CollapseTransition>
				<div
					v-show="step === 0"
					class="w-full border border-gray-200 p-5 dark:border-gray-600"
				>
					<div class="w-full mb-6 flex items-center">
						<VueIcon
							:icon="MdTokenSharp"
							class="mr-2 h-7 w-7 text-emerald-600 dark:text-emerald-400"
						/>
						<h3 class="text-base uppercase text-emerald-600 dark:text-emerald-400">
							{{ $t("Token Information") }}
						</h3>
					</div>
					<div class="grid w-full mb-6">
						<div
							class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-sm bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800"
							role="alert"
						>
							<svg
								class="flex-shrink-0 inline w-4 h-4 me-3"
								aria-hidden="true"
								xmlns="http://www.w3.org/2000/svg"
								fill="currentColor"
								viewBox="0 0 20 20"
							>
								<path
									d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
								/>
							</svg>
							<span class="sr-only">Info</span>
							<div>
								<strong class="font-semibold">100% Free Listings</strong> All our
								listings are <strong>free</strong> and will remain
								<strong>free forever</strong>. Do not pay anyone to list, activate,
								review, advertise or verify your project on sleep.finance. Spread
								the word
							</div>
						</div>
					</div>
					<div class="grid w-full mb-6">
						<h3 class="text-sm mb-2">{{ $t("Category") }}</h3>
						<RadioSelect
							v-model="form.type"
							:list="typeOptions"
						/>
					</div>
					<CollapseTransition>
						<div
							v-show="['airdrop', 'presale'].includes(type)"
							class="gap-3 mb-6 w-full grid"
						>
							<div>
								<Switch v-model="showSalesPage">Send me participants</Switch>
								<CollapseTransition>
									<div
										class="mt-3"
										v-show="showSalesPage"
									>
										<FormInput
											v-model="form.presale_page"
											:error="form.errors.presale_page"
											:placeholder="$t('https://yoursales.page')"
										/>
										<p class="text-xs mt-1">
											{{
												$t(
													"We shall ask users to go to this link instead of your website",
												)
											}}
										</p>
									</div>
								</CollapseTransition>
							</div>
						</div>
					</CollapseTransition>

					<div class="gap-3 mb-6 w-full grid md:grid-cols-2">
						<div>
							<Switch v-model="form.is_meme">{{ $t("This is a Meme Token") }}</Switch>
						</div>
						<div>
							<Switch v-model="form.is_game">{{ $t("This is a Game Token") }}</Switch>
						</div>
					</div>

					<div class="gap-3 mb-5 w-full grid md:grid-cols-3">
						<FormInput
							:label="$t('Enter a Token address')"
							v-model="form.address"
							:error="form.errors.address"
							class="md:col-span-2"
						>
							<template #trail>
								<Loading
									v-if="load"
									class="w-4 h-4"
								/>
								<a
									href="#"
									v-else
									@click.prevent="form.address = null"
									class="p-2 group"
								>
									<VueIcon
										:icon="HiSolidX"
										class="w-4 h-5 group-hover:text-red"
									/>
								</a>
							</template>
						</FormInput>
						<div>
							<label
								for="name"
								class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-300"
								>{{ $t("Select a chain") }}</label
							>
							<ChainSelect v-model="form.chainId" />
							<p
								v-if="form.errors.chainId"
								class="text-red-500"
							>
								{{ form.errors.chainId }}
							</p>
						</div>
					</div>

					<CollapseTransition>
						<div
							v-show="form.address"
							class="p-6 mb-6 border rounded-sm border-gray-200 dark:border-gray-600"
						>
							<div class="gap-3 w-full grid md:grid-cols-3">
								<FormInput
									:label="$t('Token Name')"
									v-model="form.name"
									:error="form.errors.name"
									:disabled="load"
									class="md:col-span-2"
								>
									<template #trail>
										<Loading
											v-if="load"
											class="w-4 h-4"
										/>
									</template>
								</FormInput>
								<FormInput
									:disabled="load"
									:label="$t('Token Symbol')"
									v-model="form.symbol"
									:error="form.errors.supply"
								>
									<template #trail>
										<Loading
											v-if="load"
											class="w-4 h-4"
										/>
									</template>
								</FormInput>
							</div>
						</div>
					</CollapseTransition>
					<div class="gap-3 mb-6 w-full grid">
						<div class="flex items-end">
							<Switch
								v-model="form.launchDone"
								class="mb-2"
							>
								Token Already Launched</Switch
							>
						</div>
					</div>
					<CollapseTransition>
						<div
							v-show="form.launchDone"
							class="gap-3 mb-6 w-full grid"
						>
							<FormInput
								:disabled="load"
								:label="$t('Trading Chart')"
								v-model="form.chart"
								help="Dextools Dexview etc"
							>
								<template #trail>
									<Loading
										v-if="load"
										class="w-4 h-4"
									/>
								</template>
							</FormInput>
						</div>
					</CollapseTransition>
					<CollapseTransition>
						<div
							v-show="!form.launchDone"
							class="gap-3 mb-6 w-full grid md:grid-cols-3"
						>
							<div>
								<label
									for="name"
									class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-300"
									>{{ $t("Launch / Start Date") }}</label
								>
								<DatePicker
									v-model="form.launch_at"
									mode="dateTime"
									is24hr
								>
									<template v-slot="{inputValue, inputEvents}">
										<input
											class="bg-white border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white border block w-full focus:outline-none focus:ring-1 appearance-none py-2 text-sm pl-2 pr-2"
											:value="inputValue"
											v-on="inputEvents"
										/>
									</template>
								</DatePicker>
								<p
									v-if="form.errors.launch_at"
									class="text-red-500"
								>
									{{ form.errors.launch_at }}
								</p>
								<UseTimeAgo
									v-else
									v-slot="{timeAgo}"
									:time="form.launch_at"
								>
									<p class="text-sx font-semibold text-emerald-500">
										{{ timeAgo }}
									</p>
								</UseTimeAgo>
							</div>
						</div>
					</CollapseTransition>
					<div class="gap-x-3 mt-6 mx-auto grid md:grid-cols-2">
						<FormInput
							v-model="form.logo_uri"
							:disabled="form.logo_upload"
							placeholder="https://"
							:error="form.errors.logo_uri"
							:help="$t('Supports png, jpeg or svg')"
						>
							<template #label>
								<div class="flex">
									<span class="mr-3">{{ $t("Token Logo Url") }}</span>
									<label class="inline-flex items-center space-x-2">
										<input
											v-model="form.logo_upload"
											class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
											type="checkbox"
										/>
										<span>{{ $t("Upload to server") }}</span>
									</label>
								</div>
							</template>
						</FormInput>
						<template v-if="form.logo_upload">
							<LogoInput
								v-if="$page.props.config.s3"
								v-model="form.logo_uri"
								v-model:file="form.logo_path"
								auto
							/>
							<LogoInputLocal
								v-else
								v-model="form.logo_uri"
							/>
						</template>
						<img
							v-else
							class="w-12 h-12 my-auto rounded-full b-0"
							:src="form.logo_uri ?? fakeLogo"
						/>
					</div>
					<div class="mt-6 w-full flex justify-end">
						<PrimaryButton
							@click.prevent="goToStepOne"
							primary
						>
							{{ $t("Continue") }}
						</PrimaryButton>
					</div>
				</div>
			</CollapseTransition>
			<CollapseTransition>
				<ProjectDetails
					v-show="step === 1"
					:errors="form.errors"
					v-model:title="form.title"
					v-model:description="form.description"
					v-model:discord="form.discord"
					v-model:facebook="form.facebook"
					v-model:github="form.github"
					v-model:instagram="form.instagram"
					v-model:medium="form.medium"
					v-model:reddit="form.reddit"
					v-model:twitter="form.twitter"
					v-model:tiktok="form.tiktok"
					v-model:telegram="form.telegram"
					v-model:youtube="form.youtube"
					v-model:youtube_trailer="form.youtube_trailer"
					v-model:website="form.website"
					v-model:docs="form.docs"
					v-model:whitepaper="form.whitepaper"
				>
					<template #title>
						<div class="w-full mb-6 flex items-center">
							<VueIcon
								:icon="BiCircleSquare"
								class="mr-2 h-7 w-7 text-emerald-600 dark:text-emerald-400"
							/>
							<h3 class="text-base uppercase text-emerald-600 dark:text-emerald-400">
								{{ $t("Project details") }}
							</h3>
						</div>
					</template>
					<template #footer>
						<div class="mt-6 w-full flex justify-end">
							<ConnectWalletButton>
								<PrimaryButton
									@click.prevent="saveProject"
									primary
								>
									{{ $t("Submit Token Info") }}
								</PrimaryButton>
							</ConnectWalletButton>
						</div>
					</template>
				</ProjectDetails>
			</CollapseTransition>
		</div>
	</CreateLayout>
</template>
