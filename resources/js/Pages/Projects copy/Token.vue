<script setup>
import {computed, ref, watch} from "vue";

import {useForm, usePage} from "@inertiajs/vue3";
import {useAccount, useChainId, useChains, usePublicClient} from "use-wagmi";
import {formatEther, parseEther, parseEventLogs} from "viem";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import TxStatus from "@/Components/TxStatus.vue";
import UnsupportedFactory from "@/Components/UnsupportedFactory.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import {useBillions} from "@/hooks/useBillions";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
import DialogModal from "@/Jetstream/DialogModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TokenDetails from "@/Pages/Projects/Create/TokenDetails.vue";
const {chainId: uChainId} = useAccount();
const props = defineProps({
	factory: Object,
	NFPM: Object,
	WETH9: Object,
});
const showHowItWorks = ref(false);
const chainId = computed(() => {
	if (usePage().props.AuthCheck) return uChainId.value;
	return useChainId().value;
});
const chains = useChains();
const chain = computed(() => chains.value.find((c) => c.id === chainId.value));
const factoryAddress = computed(() => props.factory?.addresses?.[chainId.value]);
const nfpm = computed(() => props.NFPM?.[chainId.value]);
const weth9 = computed(() => props.WETH9?.[chainId.value]);
const state = useReactiveContractCall(props.factory.abi, factoryAddress);
const fees = ref(0n);
const feesFormatted = computed(() => formatEther(fees.value));
const publicClient = usePublicClient();
const loadFees = async () => {
	fees.value = await publicClient.value.readContract({
		abi: props.factory.abi,
		address: factoryAddress.value,
		functionName: "fees",
	});
};
watch(
	chainId,
	(chainId) => {
		if (!chainId) return;
		if (!factoryAddress.value) return;
		loadFees();
	},
	{immediate: true},
);
const form = useForm({
	// token info
	name: "Pepe",
	symbol: "PEPE",
	supply: "100000000000",
	chainId: null,
	address: null,
	contract: null,
	logo_uri: "https://static.sleep.finance/logos/mIQ5XzPK9GmOfTnxZwFF.png",
	logo_upload: false,
	logo_path: "logos/mIQ5XzPK9GmOfTnxZwFF.png",
	chart: null,
	description:
		"😎 🐸Yeah I know, I Know... there's too many pepes out there, BUT WHY NOT? 😿 The cats are outta control. Time to pepe back what is ours. So gather around pepedonians, Lets pepe this pepe at pepe speeds to pepedom. Don't wait, pepe now and lets reclaim the pepevolution",
	is_meme: true,
	is_game: false,
	launch_at: null,
	launchDone: true,
	type: "presale",
	// project info
	youtube: "",
	website: "https://sleep.finance",
	docs: "",
	whitepaper: "",
	telegram: "sleepfinance",
	linktree: "",
	snapchat: "",
	twitter: "sleepprotocol",
	tiktok: "",
	github: "",
	discord: "",
	facebook: "",
	instagram: "",
	medium: "",
	reddit: "",
});
const {t} = useI18n();
const deploy = async () => {
	form.clearErrors();
	if (!form.symbol) form.setError("symbol", t("Could not load Token Details"));
	if (!form.logo_uri) form.setError("logo_uri", t("Please upload a project logo"));
	if (!form.name) form.setError("name", t("Token needs a name"));
	if (!form.symbol) form.setError("name", t("Token needs a symbol"));
	if (!form.supply) form.setError("name", t("Token needs some supply"));
	if (!form.description) form.setError("name", t("At least say something... anything?"));
	if (form.hasErrors) {
		state.error = "It seems you missed you stuff we need. check the form!";
		return;
	}
	await state.call(
		"create",
		[
			{
				name: form.name,
				symbol: form.symbol,
				supply: parseEther(form.supply.toString()),
				weth: weth9.value,
				uniswap: nfpm.value,
			},
		],
		fees.value,
		t("Deploy Meme Sale"),
	);
	const logs = parseEventLogs({
		abi: props.factory.abi,
		logs: state.receipt.logs,
		eventName: ["CLONE"],
	});
	form.contract = logs?.[0]?.args?.fairlaunch;
	form.address = logs?.[0]?.args?.token;
	form.transform((data) => ({
		...data,
		chainId: chainId.value,
	})).post(window.route("projects.deploy.store"));
};
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div class="card mt-12 p-8 border dark:border-gray-600 border-gray-300">
					<div class="grid mx-auto">
						<div class="mb-4 flex space-x-3 items-center">
							<h3>Create Token Sale</h3>
							<a
								href="#"
								@click="showHowItWorks = true"
								class="hover:text-emerald-500 hover:underline transition-colors duration-200"
							>
								[How it works?]</a
							>
						</div>
						<div class="gap-3 w-full grid md:grid-cols-3">
							<FormInput
								:placeholder="$t('Token Name')"
								v-model="form.name"
								:error="form.errors.name"
							/>

							<FormInput
								:placeholder="$t('Token Symbol')"
								v-model="form.symbol"
								:error="form.errors.symbol"
							/>

							<FormInput
								:placeholder="$t('Token Supply')"
								v-model="form.supply"
								:error="form.errors.supply"
							>
								<template #trail>
									<span
										class="text-xs font-semibold text-emerald-600 dark:text-emerald-400"
										>{{ useBillions(form.supply) }}</span
									>
								</template>
							</FormInput>
						</div>
						<div class="gap-x-3 my-4 grid md:grid-cols-2">
							<FormInput
								v-model="form.logo_uri"
								:disabled="form.logo_upload"
								:placeholder="
									form.logo_upload
										? 'Use the uploader =>'
										: 'Enter a url to your logo'
								"
								:error="form.errors.logo_uri"
								:help="$t('Supports png, jpeg or svg')"
							>
								<template #label>
									<div class="flex">
										<span class="mr-3">{{ $t("Token Logo Link") }}</span>
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
						<TokenDetails
							:errors="form.errors"
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
						/>
						<UnsupportedFactory
							class="mt-4"
							v-if="!factoryAddress || !nfpm || !weth9"
						/>
						<div
							v-else
							class="mt-6 w-full flex space-x-3 items-center justify-end"
						>
							<TxStatus :state="state" />
							<ConnectWalletButton>
								<PrimaryButton
									@click.prevent="deploy"
									primary
									:disabled="state.busy || form.processing"
								>
									<Loading
										class="mr-2 -ml-1"
										v-if="state.busy || form.processing"
									/>
									{{ $t("Start Selling") }} [{{ feesFormatted
									}}{{ chain.nativeCurrency.symbol }}]
								</PrimaryButton>
							</ConnectWalletButton>
						</div>
						<div
							v-if="state.busy"
							class="text-right mt-2 text-red"
						>
							Dont leave this page. We need to save your tokens after deploy
						</div>
					</div>
				</div>
			</div>
		</div>
		<DialogModal
			:show="showHowItWorks"
			@close="showHowItWorks = false"
			closeable
		>
			<template #title> How it works </template>
			<template #content>
				<ul class="list-disc list-inside">
					<li>
						This is a prize fairlaunch, with 10% Team allocation and 85% liquidity Add.
					</li>
					<li>
						The launch starts as soon as you create and lasts for 10 days. You Cannot
						cancel or stop.
					</li>
					<li>
						10% of sales will be sent to your deploy address when liquidity is added.
					</li>
					<li>
						10% of of minted tokens will available for you to claim 25 days after
						liquidity is added.
					</li>
					<li>
						5% sales will added to your winner prize if prize more than 2000$ If not
						sleep eats it.
					</li>
					<li>
						Users withdraw is in stages. Every user can claim 10 days after they buy.
						This should give you enough time to build momentum for the pump stage. One
						way to buy time is to hide the listing for 5 days. Then start. This give you
						5 days after sale to get pumping stage rolling.
					</li>
					<li>Prize payout is after sale ends.</li>
					<li>
						Projects without running prizes or an influencer will be hidden to members
					</li>
					<li>Projects are ordered by total pumps. So pump your pumps to get up</li>
				</ul>
			</template>
		</DialogModal>
	</AppLayout>
</template>
