<script setup>
import {computed, ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {UseTimeAgo} from "@vueuse/components";
import axios from "axios";
import {DateTime} from "luxon";
import {
	FaGasPump,
	HiDotsHorizontal,
	HiRefresh,
	HiSolidGift,
	HiSolidPlus,
	LaUserCheckSolid,
	MdSwaphorizontalcircleRound,
	MdTimerRound,
	RiExchangeFill,
	RiExchangeFundsLine,
	RiTimerFlashFill,
} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import Verified from "@/Components/FilterNav/svg/verified.vue";
import Loading from "@/Components/Loading.vue";
import TxHash from "@/Components/TxHash.vue";
import TxStatus from "@/Components/TxStatus.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useReactiveContractCall} from "@/hooks/contracts/useContractCall";
import {useBillions} from "@/hooks/useBillions";
import UsdtLogo from "@/images/usdt.svg";
import AppLayout from "@/Layouts/AppLayout.vue";
import ApiTask from "@/Pages/Giveaways/Show/ApiTask.vue";
import ContributeCard from "@/Pages/Giveaways/Show/ContributeCard.vue";
import GiveawayWinners from "@/Pages/Giveaways/Show/GiveawayWinners.vue";
import JoinDiscord from "@/Pages/Giveaways/Show/JoinDiscord.vue";
import JoinTelegramGroup from "@/Pages/Giveaways/Show/JoinTelegramGroup.vue";
import Leaderboard from "@/Pages/Giveaways/Show/Leaderboard.vue";
import MinimumPumps from "@/Pages/Giveaways/Show/MinimumPumps.vue";
import NftBalance from "@/Pages/Giveaways/Show/NftBalance.vue";
import PumpBooster from "@/Pages/Giveaways/Show/PumpBooster.vue";
import ReferralUsers from "@/Pages/Giveaways/Show/ReferralUsers.vue";
import SleepCode from "@/Pages/Giveaways/Show/SleepCode.vue";
import TokenBalance from "@/Pages/Giveaways/Show/TokenBalance.vue";
import TweetTask from "@/Pages/Giveaways/Show/TweetTask.vue";
import TwitterFollow from "@/Pages/Giveaways/Show/TwitterFollow.vue";
import WinningInfoBox from "@/Pages/Giveaways/Show/WinningInfoBox.vue";

const props = defineProps({
	giveaway: Object,
	coin: Object,
	questers: Array,
	winners: Array,
	quester: Object,
	twitter: Object,
	telegram: Object,
	discord: Object,
	tweet: Object,
	contribute: Object,
	launchpad: Object,
	token: Object,
	nft: Object,
	api: Object,
	connections: Object,
	prizeClaim: Object,
});

const verifyForm = useForm({
	response: "",
});
const verifyAllForm = useForm({});
const verifying = ref();
const verify = (quest) => {
	verifying.value = quest.id;
	verifyForm.post(window.route("tasks.check", {quest: quest.id}), {
		preserveScroll: true,
	});
};

const verifyAll = (quest) => {
	verifyAllForm.post(window.route("tasks.check.all", {giveaway: props.giveaway.uuid}), {
		preserveScroll: true,
	});
};
const {address} = useAccount();
const claimForm = useForm({
	quester: "",
	address: address.value,
	txhash: null,
});
const {t} = useI18n();
const prizeContract = computed(() => props.prizeClaim.addresses[props.giveaway.chainId]);
const state = useReactiveContractCall(props.prizeClaim.abi, prizeContract);
const loading = ref(false);
const claim = async (quest) => {
	claimForm.clearErrors();
	if (!props.quester) claimForm.setError("quester", "Complete all quests first");
	if (claimForm.hasErrors) return;
	loading.value = true;
	claimForm.address = address.value;
	const {data} = await axios.post(window.route("questers.claim", {quester: props.quester.id}), {
		address: address.value,
	});
	loading.value = false;
	if (data.error) {
		claimForm.setError("quester", data.error);
		return;
	}
	await state.call("withdrawPrize", [data.claim, data.signature], null, t("Claim Prize"));
	if (state.error) return;
	claimForm.txhash = state.txhash;
	claimForm.post(window.route("questers.claimed", {quester: props.quester.id}));
};

const types = {
	draw: {name: "Raffle", icon: HiSolidGift, info: "Random Draw"},
	leaderboard: {name: "LB", icon: MdTimerRound, info: "Leaderboard"},
	draw_leaderboard: {name: "LB Raffle", icon: RiTimerFlashFill, info: "Leaderboard Raffle"},
	fcfs: {name: "FCFS", icon: RiExchangeFill, info: "First come, first serve"},
	fcfs_leaderboard: {
		name: "LB FCFS",
		icon: RiExchangeFundsLine,
		info: "Leaderboard - First come, first serve",
	},
	draw_fcfs: {
		name: "FCFS Raffle",
		icon: MdSwaphorizontalcircleRound,
		info: "First come, first serve.  Raffle",
	},
};
const type = computed(() => types[props.giveaway.type]);
</script>
<template>
	<AppLayout>
		<div class="min-h-full w-full bg-white dark:bg-gray-900">
			<div class="container px-4">
				<div class="grid mt-12 sm:grid-cols-2 gap-5">
					<div>
						<h3>{{ giveaway.summary }}</h3>
						<p># {{ giveaway.brief }}</p>
						<div class="flex mt-4 items-center">
							<div class="flex flex-wrap -space-x-2">
								<div class="avatar h-7 w-7 hover:z-10">
									<img
										class="rounded-full ring ring-white dark:ring-navy-700"
										src="https://lineone.piniastudio.com/images/avatar/avatar-16.jpg"
										alt="avatar"
									/>
								</div>
								<div class="avatar h-7 w-7 hover:z-10">
									<img
										class="rounded-full ring ring-white dark:ring-navy-700"
										src="https://lineone.piniastudio.com/images/avatar/avatar-20.jpg"
										alt="avatar"
									/>
								</div>
								<div class="avatar h-7 w-7 hover:z-10">
									<img
										class="rounded-full ring ring-white dark:ring-navy-700"
										src="https://lineone.piniastudio.com/images/avatar/avatar-8.jpg"
										alt="avatar"
									/>
								</div>
								<div class="avatar h-7 w-7 hover:z-10">
									<div
										class="is-initial rounded-full bg-info text-xs uppercase text-white ring ring-white dark:ring-navy-700"
									>
										<VueIcon :icon="HiDotsHorizontal" />
									</div>
								</div>
							</div>
							<h3 class="font-light ml-3 text-lg">
								{{ useBillions(giveaway.totalParticipants) }}+
							</h3>
							<h3 class="font-light mx-3 sm:mx-6 text-lg">|</h3>
							<UseTimeAgo
								v-slot="{timeAgo}"
								:time="DateTime.fromSeconds(giveaway.endTimeTs).toJSDate()"
							>
								<h3 class="font-light text-lg">
									Ends:
									<span class="text-green-600 font-semibold dark:text-green-400">
										{{ timeAgo }}</span
									>
								</h3>
							</UseTimeAgo>
						</div>
					</div>
					<div>
						<div class="flex items-center justify-end mb-1">
							<a
								href="#"
								class="flex items-center space-x-3 cursor-pointer"
							>
								<img
									class="w-8 h-8 rounded-full"
									:src="giveaway.project.logo.url"
								/>
								<h3 class="text-xl">{{ giveaway.project.name }}</h3>
								<Verified
									v-if="giveaway.project.isVerified"
									v-tippy="$t('Verified by Sleep Team')"
									class="w-5 h-5 cursor-pointer"
								/>
								<div
									class="border flex items-center border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-500 rounded-sm px-3 py-1"
								>
									<VueIcon
										class="w-4 h-4 mr-1"
										:icon="HiSolidPlus"
									/>
									<span
										class="font-semibold text-gray-900 dark:text-gray-200 dark:font-white"
										>Votes</span
									>
									<VueIcon
										class="w-4 h-4 ml-2 mr-1"
										:icon="LaUserCheckSolid"
									/>
									<span>{{ useBillions(giveaway.project.totalVotes) }}+</span>
								</div>
							</a>
							<a
								href="#"
								v-tippy="$t('Sleep Tokens Faucet Balance')"
								class="border ml-1 flex items-center border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-500 rounded-sm px-3 py-1"
							>
								<VueIcon
									class="w-5 h-5 mr-1 text-sky-500"
									:icon="FaGasPump"
								/>
								<span>{{ useBillions(giveaway.sleep_balance) }}+</span>
							</a>
						</div>
						<div class="flex items-center justify-end mb-3">
							<p class="max-w-lg truncate text-ellipsis">
								{{ giveaway.project.description }}
							</p>
						</div>
						<div
							v-if="giveaway.project.isOwner"
							class="flex items-end space-x-2 justify-end"
						>
							<PrimaryButton
								class="!py-1 !text-xs uppercase"
								secondary
								link
								:href="route('giveaways.edit', {giveaway: giveaway.uuid})"
							>
								{{ $t("Edit Giveaway") }}</PrimaryButton
							>
							<PrimaryButton
								class="!py-1 !text-xs uppercase"
								secondary
								link
								:href="route('projects.edit', {project: giveaway.project.uuid})"
							>
								{{ $t("Edit Project") }}</PrimaryButton
							>
						</div>
					</div>
				</div>

				<div class="mt-12 grid sm:grid-cols-12 gap-4 lg:gap-8">
					<div class="md:col-span-8 grid gap-4">
						<ContributeCard
							v-if="contribute"
							:quest="contribute"
							:giveaway="giveaway"
							:coin="coin"
							:launchpad="launchpad"
							@verify="verify(contribute)"
							:verifying="verifying === contribute.id && verifyForm.processing"
						/>
						<TwitterFollow
							v-if="twitter"
							:connected="connections.twitter"
							:giveaway="giveaway"
							:quest="twitter"
							@verify="verify(twitter)"
							:verifying="verifying === twitter.id && verifyForm.processing"
						/>
						<TweetTask
							v-if="tweet"
							:connected="connections.twitter"
							:giveaway="giveaway"
							:quest="tweet"
							@verify="verify(tweet)"
							v-model:comment="verifyForm.response"
							:errors="verifyForm.errors.response"
							:verifying="verifying === tweet.id && verifyForm.processing"
						/>
						<JoinTelegramGroup
							v-if="telegram"
							:connected="connections.telegram"
							:giveaway="giveaway"
							:quest="telegram"
							@verify="verify(telegram)"
							:verifying="verifying === telegram.id && verifyForm.processing"
						/>
						<JoinDiscord
							v-if="discord"
							:connected="connections.discord"
							:giveaway="giveaway"
							:quest="discord"
							@verify="verify(discord)"
							:verifying="verifying === discord.id && verifyForm.processing"
						/>
						<ApiTask
							v-if="api"
							:giveaway="giveaway"
							:quest="api"
							@verify="verify(api)"
							:verifying="verifying === api.id && verifyForm.processing"
						/>
						<TokenBalance
							v-if="token"
							:giveaway="giveaway"
							:quest="token"
							@verify="verify(token)"
							:verifying="verifying === token.id && verifyForm.processing"
						/>
						<NftBalance
							v-if="nft"
							:giveaway="giveaway"
							:quest="nft"
							@verify="verify(nft)"
							:verifying="verifying === nft.id && verifyForm.processing"
						/>
						<ReferralUsers
							:giveaway="giveaway"
							:quester="quester"
						/>
						<div
							class="flex mt-12 relative justify-between items-center p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
						>
							<div class="flex items-center">
								<div class="w-20">
									<UsdtLogo class="w-16 -top-8 h-16 absolute mr-4 text-sky-500" />
								</div>

								<div>
									<h3
										class="text-xl text-gray-900 dark:text-white font-Walsheim-Bold"
									>
										{{ giveaway.prize }} <span class="text-lg">USDT</span>
									</h3>
									<p
										v-if="quester?.isComplete"
										class="text-emerald-600"
									>
										{{ $t("You are Queued for the Draw") }}
									</p>
								</div>
								<div class="border"></div>
							</div>
							<div class="flex items-center space-x-3">
								<ConnectWalletLink
									:disabled="verifyAllForm.processing"
									v-tippy="$t('Verify task')"
									@clicked="verifyAll"
									class="flex items-center text-gray-500 hover:text-gray-700 justify-center -mr-1.5 h-8 w-8 rounded-full p-0 bg-gray-300/20 hover:border hover:border-gray-500/50 focus:bg-gray-400/20 active:border-gray-500/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25"
									href="#"
								>
									<VueIcon
										:class="{'animate-spin': verifyAllForm.processing}"
										:icon="HiRefresh"
									/>
								</ConnectWalletLink>
								<PrimaryButton
									secondary
									link
									:href="route('questers.sleep')"
									v-if="quester?.isComplete"
									class="!py-1 !px-2 !text-emerald-500"
									>{{ $t("CLAIM") }} {{ quester?.sleep * 1 }} SLEEP
								</PrimaryButton>
								<PrimaryButton
									@click.prevent="verifyAll"
									secondary
									v-else
									class="!py-1 !px-2"
								>
									{{ $t("VERIFY COMPLETION") }}
								</PrimaryButton>
							</div>
						</div>
						<p>
							We Only Verify Twitter tasks for selected winners to save on Elons
							expensive API. If you are selected and found not to have completed the
							twitter tasks, you will be disqualified and another winner drawn.
						</p>
					</div>
					<div class="md:col-span-4 gap-4">
						<SleepCode
							:giveaway="giveaway"
							:code="$page.props.code"
							v-if="giveaway.project.isOwner"
							class="mb-4"
						/>
						<div class="border rounded-sm dark:border-gray-600 p-4">
							<div class="flex flex-col gap-3 lg:flex-row justify-between">
								<div class="flex items-center justify-between space-x-2">
									<h3>{{ $t("Reward") }}</h3>
									<div
										class="rounded-2xl border dark:border-gray-700 px-3 py-1 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-100 text-md font-Walsheim-Bold"
									>
										{{ giveaway.num_winners - giveaway.num_claimed }} /
										{{ giveaway.num_winners }}
									</div>
								</div>
								<a
									v-tippy="type.info"
									href="#"
									@click.prevent="() => null"
									class="rounded-sm border dark:border-gray-700 px-3 py-1 text-gray-800 dark:text-gray-100 text-md font-Walsheim-Bold"
								>
									<VueIcon
										class="w-5 h-5"
										:icon="type.icon"
									/>

									{{ type.name }}
								</a>
							</div>
							<WinningInfoBox :giveaway="giveaway" />
							<div class="mt-8">
								<p v-if="quester?.status === 'claimed'">
									{{ $t("You claimed your prize") }}
								</p>
								<TxHash
									v-if="quester?.comment"
									:txhash="quester?.comment"
								/>
								<TxStatus
									v-else
									:state="state"
								/>
								<PrimaryButton
									secondary
									@click="claim"
									class="w-full mt-4 uppercase disabled:pointer-events-none"
								>
									<Loading
										class="mr-2 -ml-2"
										v-if="claimForm.processing || loading || state.busy"
									/>
									{{ $t("Claim prize") }}</PrimaryButton
								>
								<p
									v-if="claimForm.errors.quester"
									class="text-red mt-1 text-center"
								>
									{{ claimForm.errors.quester }}
								</p>
							</div>
							<GiveawayWinners
								v-if="giveaway.hasEnded || winners.length > 0"
								:winners="winners"
								:me="quester"
							/>
							<Leaderboard
								:questers="questers"
								:quester="quester"
							/>
						</div>
						<MinimumPumps
							:quester="quester"
							class="mt-4"
						/>
						<PumpBooster
							:quester="quester"
							class="mt-4"
						/>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
