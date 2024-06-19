<script setup>
import {computed, reactive, ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {UseTimeAgo} from "@vueuse/components";
import {debouncedWatch, useLocalStorage} from "@vueuse/core";
import axios from "axios";
import upperFirst from "lodash/upperFirst";
import {DateTime} from "luxon";
import {AiCeur, HiSolidCheck} from "oh-vue-icons/icons";
import {uid} from "uid";
import {useAccount, usePublicClient} from "use-wagmi";
import {DatePicker} from "v-calendar";
import {erc20Abi, parseUnits} from "viem";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
import RadioCards from "@/Components/RadioCards.vue";
import SocialIcon from "@/Components/SocialIcon.vue";
import Switch from "@/Components/Switch.vue";
import SwitchChainButton from "@/Components/SwitchChainButton.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import {useChainId} from "@/hooks/useChainId";
import {useFormUploadParams} from "@/hooks/useFormUploadParams";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
import AppLayout from "@/Layouts/AppLayout.vue";
import DiscordInvite from "@/Pages/Giveaways/Create/DiscordInvite.vue";
import TelegramGroup from "@/Pages/Giveaways/Create/TelegramGroup.vue";

const props = defineProps({
	hasProject: Boolean,
	prizeClaim: Object,
	giveaway: {
		type: Object,
		default: () => ({}),
	},
	usdtContracts: Object,
	gasChainId: Number,
});

const {chainId} = useChainId();
const prizeContract = computed(() => props.prizeClaim.addresses[props.gasChainId]);
const usdtContract = computed(() => props.usdtContracts[props.gasChainId]);
const usdt = reactive({
	address: usdtContract,
	abi: erc20Abi,
	decimals: 6,
	symbol: "USDT",
});
const publicClient = usePublicClient();
const updateUsdt = async () => {
	usdt.decimals = await publicClient.value.readContract({
		address: usdt.address,
		abi: usdt.abi,
		functionName: "decimals",
	});
};
watch(
	usdtContract,
	(usdtContract) => {
		if (!usdtContract) return;
		updateUsdt();
	},
	{immediate: true},
);
const usdtAddress = computed(() => usdt.address);
const state = useReactiveContractCall(usdt.abi, usdtAddress);
const formdata = {
	// token brief
	starts_at: DateTime.now().plus({hours: 4}).toJSDate(),
	duration: "12",
	period: "hours",
	prize: "0",
	fee: 0,
	gas: 0,
	symbol: "USDT",
	num_winners: "1",
	type: "draw",
	brief: "",
	hash: null,
	customize: false,
	name: "",
	description: "",
	...useFormUploadParams("logo"),
	// basic tasks

	telegram: "",
	twitter: "",
	discord: "",
};
const memo = useLocalStorage("give-away-form", {});
const {address} = useAccount();
const form = useForm({
	...formdata,
	...memo.value,
	...props.giveaway,
});
watch(form, (form) => {
	memo.value = form;
});
const reset = () => {
	form.defaults({...formdata});
	memo.value = {};
	form.reset();
};
const route = computed(() =>
	props.giveaway?.id
		? window.route("giveaways.update", {giveaway: props.giveaway.id})
		: window.route("giveaways.store"),
);
const saveLive = ref(false);
const {t} = useI18n();
const draft = async () => {
	saveLive.value = false;
	form.clearErrors();
	if (form.brief === "") form.setError("brief", t("Giveaway brief is required"));
	if (!form.duration || parseInt(form.duration) < 1)
		form.setError("duration", t("Giveaway duration is required"));
	if (!form.prize || form.prize === "" || parseFloat(form.prize) === 0)
		form.setError("prize", t("Please provide a prize to give to participants"));
	if (!form.num_winners || parseInt(form.num_winners) === 0)
		form.setError("num_winners", t("Minimum is 1 winner"));
	if (!props.hasProject) {
		if (!form.name || form.name === "") form.setError("name", t("Project name is required"));
		if (!form.description || form.description === "")
			form.setError("description", t("Giveaway description is required"));
		if (!form.logo_uri || form.logo_uri === "")
			form.setError("logo_uri", t("You need to provide project logo"));
	}
	if (form.hasErrors) return;
	await updateUsdt();
	const fee = parseUnits(totalPrize.value.toString(), usdt.decimals);
	await state.call("transfer", [prizeContract.value, fee], null, t("Deposit Prize"));
	if (state.error) return;
	form.transform((data) => ({
		...data,
		live: false,
		hash: state.txhash,
		account: address.value,
		chainId: chainId.value,
		token: usdtAddress.value,
	})).post(route.value, {
		preserveScroll: true,
		onSuccess() {
			reset();
		},
	});
};

const togglePeriod = (d) => {
	if (form.period === "days") form.period = "hours";
	else form.period = "days";
};
const totalPrize = computed(() => {
	if (form.prize === "" || !form.prize || !form.num_winners || form.num_winners === "") return 0;
	return (parseFloat(form.prize) * parseFloat(form.num_winners)).toFixed(2) * 2;
});
const totalFee = computed(() => {
	if (form.prize === "" || !form.prize || !form.num_winners || form.num_winners === "") return 0;
	return (parseFloat(form.prize) * parseFloat(form.num_winners)).toFixed(2);
});
watch(
	() => form.prize,
	(prize) => {
		if (prize === "" || !prize) return (form.fee = 0);
		form.fee = prize;
	},
);

const winOptions = reactive([
	{
		key: uid(),
		value: "draw",
		title: "Random Draw",
		subtitle: "Select Winner randomly from participants",
	},
	{
		key: uid(),
		value: "leaderboard",
		title: "GAS Leaderboard",
		subtitle: "Select the users who claimed the most GAS token",
	},
	{
		key: uid(),
		value: "draw_leaderboard",
		title: "Draw Leaderboard",
		subtitle: "Draw from 100 users who claimed the most GAS",
	},
	{
		key: uid(),
		value: "fcfs",
		title: "First come , First serve",
		subtitle: "First to complete tasks will get prize",
	},
]);

const loading = ref(false);
const verified = ref(false);
const error = ref();

const checkUsername = async (username) => {
	form.clearErrors();
	error.value = null;
	verified.value = false;
	loading.value = true;
	const {data} = await axios.post(window.route("projects.check.username"), {
		username,
	});
	error.value = data.error;
	verified.value = data.verified;
	loading.value = false;
};
debouncedWatch(
	() => form.name,
	(username) => {
		checkUsername(username);
	},
	{debounce: 800},
);
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div class="card mt-12 p-8 border dark:border-gray-600 border-gray-300">
					<div class="grid mx-auto max-w-4xl">
						<div class="mb-4 flex space-x-3 items-center">
							<h3>Host a giveaway</h3>
						</div>

						<div class="w-full mt-2 mb-6 grid md:grid-cols-5 gap-4">
							<FormInput
								:label="$t('Giveaway Brief ')"
								v-model="form.brief"
								class="col-span-3"
								:error="form.errors.brief"
								:placeholder="$t('Giveaways Finance Takeover 2024')"
							/>
							<div>
								<label
									for="name"
									class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-300"
									>{{ $t("Start Time") }}</label
								>
								<DatePicker
									v-model="form.starts_at"
									mode="dateTime"
									:disabled="giveaway?.live"
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
									v-if="form.errors.starts_at"
									class="text-red-500"
								>
									{{ form.errors.starts_at }}
								</p>
								<UseTimeAgo
									v-else
									v-slot="{timeAgo}"
									:time="form.starts_at"
								>
									<p class="text-sx font-semibold text-emerald-500">
										{{ timeAgo }}
									</p>
								</UseTimeAgo>
							</div>
							<FormInput
								:label="$t('Duration')"
								v-model="form.duration"
								:disabled="giveaway?.live"
								:placeholder="$t('24')"
								:error="form.errors.duration"
							>
								<template #trail>
									<a
										href="#"
										class="hover:text-emerald-500 px-1 text-xs font-semibold border border-gray-300 dark:border-gray-600 rounded-sm"
										@click.prevent="togglePeriod"
										>{{ upperFirst(form.period) }}</a
									>
								</template>
								<template #help>
									<div
										class="flex mt-1 text-xs font-semibold items-start space-x-1"
									>
										<a
											v-for="item in ['hours', 'days', 'weeks']"
											:key="item"
											:class="
												form.period == item
													? 'text-emerald-500 dark:text-emerald-400'
													: ''
											"
											class="px-1 bg-white hover:bg-gray-100 dark:hover:text-white dark:bg-gray-900 border rounded-sm border-gray-300 dark:border-gray-600"
											href="#"
											@click.prevent="form.period = item"
										>
											{{ item }}
										</a>
									</div>
								</template>
							</FormInput>
						</div>

						<div>
							<div class="grid w-full p-5 my-6 border">
								<div>
									<h3 class="text-base text-emerald-500 mb-2">
										<VueIcon :icon="AiCeur" /> {{ $t("Prize Details") }}
									</h3>
									<p>
										We dont give XPS, Instead we distribute GAS token which
										participants can trade later.
									</p>
								</div>
								<div class="gap-3 my-5 w-full grid md:grid-cols-4">
									<FormInput
										:placeholder="$t('20')"
										:label="$t('Prize Per Winner')"
										:disabled="giveaway?.live"
										help="Only USDT prize allowed"
										v-model="form.prize"
										:error="form.errors.prize"
									>
										<template #trail>USDT</template>
									</FormInput>
									<div class="md:col-span-3 md:grid gap-3 md:grid-cols-4">
										<FormInput
											:label="$t('Number of Winners')"
											:disabled="giveaway?.live"
											:placeholder="$t('Eg 10')"
											v-model="form.num_winners"
											:error="form.errors.num_winners"
										/>
										<FormInput
											:label="$t('Participant GAS Tokens')"
											:help="$t('Distributed to all participants')"
											v-model="totalFee"
											:error="form.errors.fee"
											class="md:col-span-2"
											disabled
										>
											<template #lead>$</template>
											<template #trail
												><span class="text-emerald-500"
													>{{ (totalPrize * 1000) / 2 }} GAS</span
												></template
											>
										</FormInput>
										<FormInput
											:label="$t('Total Spend')"
											v-model="totalPrize"
											disabled
										>
											<template #trail>
												<span class="text-xs font-semibold"> USDT</span>
											</template>
										</FormInput>
									</div>
								</div>
								<Switch
									class="mb-2"
									v-model="form.customize"
								>
									<h3 class="text-sm">
										{{ $t("Customize method to get winner") }}?
									</h3>
								</Switch>
								<CollapseTransition>
									<div v-show="form.customize">
										<RadioCards
											v-model="form.type"
											:options="winOptions"
											:disabled="giveaway?.live"
											:grid="2"
										/>
									</div>
								</CollapseTransition>
							</div>
							<div class="mt-8">
								<h3 class="text-base">Base Tasks</h3>
								<p class="text-xs">You will be able to add more tasks later</p>
							</div>
							<div class="gap-8 mt-5 w-full grid sm:grid-cols-3">
								<FormInput
									:disabled="giveaway?.live"
									:label="$t('Twitter account to follow')"
									:placeholder="$t('Enter Twitter Profile url')"
									:help="$t('Eg https://x.com/giveawaypot')"
									v-model="form.twitter"
									:error="form.errors.twitter"
									class="mt-1"
								>
									<template #trail>
										<SocialIcon icon="twitter" />
									</template>
								</FormInput>
								<TelegramGroup
									:disabled="giveaway?.live"
									:label="$t('Telegram Group to Join')"
									:placeholder="$t('Enter Telegram Group url')"
									:help="$t('Eg https://t.me/giveawaysfinance')"
									v-model="form.telegram"
									:error="form.errors.telegram"
									class="mt-1"
								/>
								<DiscordInvite
									:disabled="giveaway?.live"
									:label="$t('Discord Channel to Join')"
									:placeholder="$t('Provide channel invite')"
									:help="$t('Eg https://discord.gg/shXVDx78BQ')"
									v-model="form.discord"
									:error="form.errors.discord"
									class="mt-1"
								/>
							</div>
							<div
								class="p-6 border mt-8 border-gray-200 dark:border-gray-600"
								v-if="!$page.props.hasProject"
							>
								<div>
									<h3 class="text-base">Project Information</h3>
									<p class="text-xs">
										It seems you havent created a project yet.
									</p>
								</div>
								<div class="gap-8 mt-5 w-full grid sm:grid-cols-3">
									<FormInput
										class="max-w-xs"
										inputClasses="!pl-32"
										v-model="form.name"
										:label="$t('Create a Username / Gas unique url')"
										:error="error ?? form.errors.name"
										:help="
											form.name
												? `https://giveaways.finance/@${form.name}`
												: 'Cannot be changed later'
										"
									>
										<template #lead>
											<span class="text-xs font-semibold text-green-600"
												>giveaways.finance/@</span
											>
										</template>
										<template #trail>
											<Loading
												class="!w-4 !h-4 text-gray-500"
												v-if="loading"
											/>
											<VueIcon
												class="w-5 h-5 text-green-600"
												:icon="HiSolidCheck"
												v-else-if="verified"
											/>
										</template>
									</FormInput>
									<div class="gap-x-3 sm:col-span-2 grid md:grid-cols-2">
										<FormInput
											v-model="form.logo_uri"
											:disabled="form.logo_upload"
											placeholder="https://"
											:error="form.errors.logo_uri"
											:help="$t('Supports png, jpeg or svg')"
										>
											<template #label>
												<div class="flex">
													<span class="mr-3">{{
														$t("Token Logo Url")
													}}</span>
													<label
														class="inline-flex items-center space-x-2"
													>
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
								</div>
								<div class="w-full grid mt-6">
									<label
										class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
										>{{ $t("Brief Description") }}</label
									>
									<ProjectDescriptionTextArea
										v-model="form.description"
										:rows="2"
										:placeholder="
											$t(
												'Do not copy and paste from another site or use AI content. Write a brief description. We optimize for seo. We shall delist projects with duplicate or AI content',
											)
										"
									/>
									<p
										v-if="form.errors?.description"
										class="text-red"
									>
										{{ form.errors?.description }}.
									</p>
								</div>
							</div>
						</div>
						<div class="mt-10 w-full flex items-center justify-end">
							<TxStatus :state="state" />
						</div>
						<div class="mt-3 w-full flex space-x-3 items-center justify-end">
							<p
								v-if="state.busy"
								class="text-red-500"
							>
								Dont leave this page! We need to save your giveaway after TX
								confirms.
							</p>
							<PrimaryButton
								@click.prevent="reset"
								secondary
							>
								{{ $t("Reset") }}
							</PrimaryButton>
							<ConnectWalletButton>
								<SwitchChainButton
									v-if="gasChainId"
									:to-chain="gasChainId"
								>
									<PrimaryButton
										@click.prevent="draft"
										secondary
										:disabled="state.busy || form.processing"
									>
										<Loading
											class="mr-2 -ml-1"
											v-if="state.busy || form.processing"
										/>
										{{ $t("Create") }} ({{ totalPrize }} USDT)
									</PrimaryButton>
								</SwitchChainButton>
							</ConnectWalletButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
