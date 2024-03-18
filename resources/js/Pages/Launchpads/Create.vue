<script setup>
import {computed, reactive, watch} from "vue";

import {PlusIcon} from "@heroicons/vue/24/outline";
import {useForm} from "@inertiajs/vue3";
import {UseTimeAgo} from "@vueuse/components";
import {DateTime} from "luxon";
import {uid} from "uid";
import {useAccount} from "use-wagmi";
import {DatePicker} from "v-calendar";
import {formatUnits, parseUnits} from "viem";

import CoinSelect from "@/Components/CoinSelect.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import RadioCards from "@/Components/RadioCards.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import SelectAmms from "@/Components/SelectAmms.vue";
import {useDeployLaunchpad} from "@/hooks/launchpads/useDeployLaunchpad";
import {useBillions} from "@/hooks/useBillions";
import {useToken} from "@/hooks/useUpdateCoins";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import CreateLayout from "@/Pages/Launchpads/Create/CreateLayout.vue";
import LaunchPadConfirmationModal from "@/Pages/Launchpads/Create/LaunchPadConfirmationModal.vue";
import MerkleRoot from "@/Pages/Launchpads/Create/MerkleRoot.vue";
import {useToggle} from "@/utils/useToggle.js";
import {isAddress} from "@/Wagmi/utils/utils";
import UseTokenContract from "../Tokens/UseTokenContract.vue";
const {chainId, address: account} = useAccount();
const props = defineProps({
	coins: Array,
	amms: Array,
	tokens: Array,
	factory: Object,
	type: String,
	isFairLaunch: Boolean,
	isPrivatesale: Boolean,
	isFairLiquidity: Boolean,
	isPresale: Boolean,
});

const form = useForm({
	factory_id: props.factory.id,
	coin_id: null,
	amm_id: null,
	// token
	token: null,
	token_name: null,
	token_symbol: null,
	token_decimals: null,
	token_supply: null,
	token_balance: null,
	supply: null,
	logo_uri: null,
	upload_logo: null,
	// presale
	useVesting: false,
	baseToken: null,
	feeInBase: true,
	merkleRoot: 5,
	participants: [],
	presaleOwner: null,
	tokenPrice: "0",
	listingRate: "0",
	maxSpendPerBuyer: "5",
	minSpendPerBuyer: "3",
	hardcap: "3",
	softcap: "2",
	total: 0,
	router: null,
	liquidityPercent: "70",
	startTime: null,
	presalePeriod: "20",
	burn: true,
	timeLock: null,
	lockPeriod: 365,
	initialPercent: 10,
	cycle: 30,
	deploy: false,
	cyclePercent: 10,
	presale: null,
});
const tokenContract = computed(() => isAddress(form.token));
const launch = useDeployLaunchpad(props.factory, form, props.isFairLiquidity, props.type);
({
	name: form.token_name,
	symbol: form.token_supply,
	decimals: form.token_decimals,
	supply: form.token_supply,
	balance: form.token_balance,
} = useToken(tokenContract, props.factory.chainId, false));

const refundTypes = [
	{
		id: uid(),
		value: true,
		label: "Burn",
	},
	{
		id: uid(),
		value: false,
		label: "Refund",
	},
];

const {close, open, isOpen} = useToggle();
watch(
	[account, chainId],
	([account, chainId]) => {
		if (!account) return;
		form.presaleOwner = account;
		launch.update();
	},
	{immediate: true},
);

watch(
	() => form.baseToken,
	(baseToken) => {
		if (isAddress(baseToken)) launch.update();
	},
);

form.startTime = DateTime.now().plus({days: 2}).toJSDate();
if (props.isFairLiquidity) {
	form.total = form.token_supply / 2;
}

const deploy = () => {
	launch.deploy();
	close();
};

const getString = (num) => (isNaN(parseFloat(num)) ? "0" : parseFloat(num).toString());
const info = computed(() => {
	const formListingRate = getString(form.listingRate);
	const formTokenPrice = getString(form.tokenPrice);
	const formLiquidityPercent = getString(form.liquidityPercent);
	const listingRate = parseUnits(formListingRate, launch.feeToken.decimals);
	const tokenPrice = parseUnits(formTokenPrice, launch.feeToken.decimals);
	const liquidityPercent = parseUnits(formLiquidityPercent, 2);
	const baseFee = form.feeInBase
		? BigInt(launch.baseOnlyFee.toString())
		: BigInt(launch.baseFee.toString());
	const tokenFee = form.feeInBase ? 0n : BigInt(launch.tokenFee.toString());
	const rate = tokenPrice === 0n ? tokenPrice : (listingRate * 10000n) / tokenPrice;
	const referralFee = BigInt(launch.referralFee.toString());
	const amount = props.isFairLaunch
		? parseUnits(getString(form.total), form.token_decimals)
		: parseUnits(
				parseFloat(getString(form.hardcap) * formTokenPrice).toString(),
				form.token_decimals,
		  );
	const feeAmount = (amount * tokenFee) / 10000n;
	const referralAmount = (amount * referralFee) / 10000n;
	const totalFee = feeAmount + referralAmount;
	const amountMinusFee = props.isPrivatesale ? amount - totalFee : amount;
	const liquidityRequired = props.isFairLaunch
		? (amountMinusFee * (liquidityPercent - baseFee)) / 10000n
		: (amountMinusFee * (liquidityPercent - baseFee) * rate) / 100000000n;

	const totalTokens = amount + liquidityRequired + totalFee;
	return {
		amount: formatUnits(amount, form.token_decimals),
		totalTokens: formatUnits(totalTokens, form.token_decimals),
		liquidity: formatUnits(liquidityRequired, form.token_decimals),
		referral: formatUnits(referralAmount, form.token_decimals),
		fee: formatUnits(feeAmount, form.token_decimals),
		refPercent: formatUnits(referralFee, 2),
		feePercent: formatUnits(tokenFee, 2),
		basePercent: formatUnits(baseFee, 2),
	};
});

const feeOptions = reactive([
	{
		key: uid(),
		value: true,
		title: computed(() => `Fees in only ${form.baseToken?.symbol}`),
		subtitle: computed(
			() => `${launch.baseOnlyFee}% ${form.baseToken?.symbol} Raised (Recommended)`,
		),
	},
	{
		key: uid(),
		value: false,
		title: computed(
			() => `Fees in Both ${form.baseToken?.symbol} &&  ${form.token_symbol ?? "token"}`,
		),
		subtitle: computed(
			() =>
				`${launch.baseFee}% ${form.baseToken?.symbol} raised + ${launch.tokenFee}% ${
					form.token_symbol ?? "token"
				} sold`,
		),
	},
]);
</script>
<template>
	<CreateLayout
		:current="1"
		:isFairLaunch="isFairLaunch"
		:isFairLiquidity="isFairLiquidity"
		:isPrivateSale="isPrivatesale"
	>
		<CollapseTransition>
			<div
				v-show="loaded && (isLocked || selected)"
				class="gap-x-3 mx-auto"
			>
				<div class="grid w-full">
					<h3 class="text-sm mb-2">Fee Options</h3>
					<RadioCards
						v-model="form.feeInBase"
						:list="feeOptions"
					/>
				</div>
				<div class="grid w-full mt-6">
					<div class="grid w-full mt-6">
						<h3 class="text-sm mb-2">Currency</h3>
						<CoinSelect
							v-model="form.baseToken"
							:list="coins"
						/>
					</div>
				</div>
			</div>
		</CollapseTransition>
		<div class="md:col-span-6 space-y-6">
			<UseTokenContract
				class="w-full"
				v-model="form.token_contract"
				v-model:logo_uri="form.logo_uri"
				v-model:upload_logo="form.upload_logo"
				:name="form.token_name"
				:symbol="form.token_symbol"
				:decimals="form.token_decimals"
				:supply="form.token_supply"
				:balance="form.token_balance"
			>
				<SecondaryButton
					link
					:href="route('tokens.create', {type: 'standard', chainId: factory.chainId})"
					class="!self-end"
					>{{ $t("Create New Token") }}</SecondaryButton
				>
			</UseTokenContract>
			<MerkleRoot
				v-model:participants="form.participants"
				v-model:merkleRoot="form.merkleRoot"
				v-if="isPrivatesale"
			/>
			<div class="gap-x-3 mx-auto grid md:grid-cols-3">
				<div>
					<label
						for="name"
						class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
						>{{ $t("Presale Start Time") }}</label
					>
					<DatePicker
						v-model="form.startTime"
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
						v-if="form.errors.startTime"
						class="text-red-500"
					>
						{{ form.errors.startTime }}
					</p>
					<UseTimeAgo
						v-else
						v-slot="{timeAgo}"
						:time="form.startTime"
					>
						<p class="text-sx font-semibold text-emerald-500">{{ timeAgo }}</p>
					</UseTimeAgo>
				</div>
				<FormInput
					:label="$t('Presale Duration in days')"
					v-model="form.presalePeriod"
					:placeholder="$t('Duration')"
					:help="$t('Max {max} Days', {max: launch.maxPresalePeriod})"
					:error="form.errors.presalePeriod"
				>
					<template #trail>Days</template>
				</FormInput>
			</div>
			<div class="gap-x-3 mx-auto grid md:grid-cols-3">
				<FormInput
					:label="$t('Presale Contract Owner previlage')"
					v-model="form.presaleOwner"
					class="md:col-span-2"
					:placeholder="$t('Owner Address')"
					:error="form.errors.presaleOwner"
				/>
			</div>
			<div class="gap-x-3 mx-auto grid md:grid-cols-5">
				<FormInput
					:label="
						isFairLiquidity
							? $t('Amount of {name} ({billions}). Minimum {min}{symbol} ', {
									symbol: form.token_supply,
									min: (form.token_supply / 2).toFixed(6) * 1,
									name: form.token_name,
									billions: useBillions(form.total),
							  })
							: $t('Amount of {name} ({billions})', {
									name: form.token_name,
									billions: useBillions(form.total),
							  })
					"
					v-model="form.total"
					v-if="isFairLaunch"
					:class="isFairLaunch ? 'md:col-span-3' : 'md:col-span-2'"
					:help="$t('Num of {tokens} amount to sell', {tokens: form.token_symbol})"
					:placeholder="$t('Total Tokens')"
					:error="form.errors.total"
				>
					<template #trail>{{ form?.symbol }}</template>
				</FormInput>
				<FormInput
					:label="$t('Hardcap')"
					v-model="form.hardcap"
					v-else
					class="md:col-span-2"
					:help="$t('Max {eth} amount to raise', {eth: baseToken?.symbol})"
					:placeholder="$t('Hardcap')"
					:error="form.errors.hardcap"
				>
					<template #trail>{{ baseToken?.symbol }}</template>
				</FormInput>
				<FormInput
					:label="$t('Softcap') + ` (${baseToken?.symbol})`"
					v-model="form.softcap"
					class="md:col-span-2"
					:placeholder="$t('Softcap')"
					:help="$t('Launch fails if softcap not reached')"
					:error="form.errors.softcap"
				>
					<template #trail>{{ baseToken?.symbol }}</template>
				</FormInput>
			</div>
			<div class="gap-x-3 mx-auto grid md:grid-cols-2">
				<FormInput
					:label="$t('Minimum Purchase Amount')"
					v-model="form.minSpendPerBuyer"
					:placeholder="$t('Min purchase amount')"
					:error="form.errors.minSpendPerBuyer"
				>
					<template #trail>{{ baseToken?.symbol }}</template>
				</FormInput>
				<FormInput
					:label="$t('Max Total Purchase per Address')"
					v-model="form.maxSpendPerBuyer"
					:placeholder="$t('Max Total Amount per Address')"
					:error="form.errors.maxSpendPerBuyer"
				>
					<template #trail>{{ baseToken?.symbol }}</template>
				</FormInput>
			</div>
			<div class="gap-x-3 mx-auto grid md:grid-cols-2">
				<SelectAmms
					:label="$t('Amm Exchange')"
					v-model="form.router"
				/>

				<div>
					<label
						id="headlessui-listbox-label-83"
						class="block text-sm font-medium text-gray-700 dark:text-gray-300"
						>{{ $t("Unsold Tokens Action") }}</label
					>
					<div class="mt-2 relative">
						<RadioSelect
							v-model="form.burn"
							:list="refundTypes"
						/>
					</div>
					<p
						v-if="form.burn"
						class="text-red"
					>
						{{ $t("Ensure your token contract allows burning to address zero") }}
					</p>
				</div>
			</div>
			<div
				:class="isFairLaunch ? 'md:grid-cols-2' : 'md:grid-cols-3'"
				class="gap-x-3 mx-auto grid"
			>
				<FormInput
					:label="$t('Token Price')"
					v-model="form.tokenPrice"
					v-if="!isFairLaunch"
					:placeholder="$t('Sale Price')"
					:help="
						form.tokenPrice
							? `1 ${baseToken?.symbol} = ${form.tokenPrice}${form?.symbol}`
							: $t('Number of tokens in 1 {symbol}', {symbol: baseToken?.symbol})
					"
					:error="form.errors.tokenPrice"
				>
					<template #trail>{{ form?.symbol }}</template>
				</FormInput>
				<FormInput
					:label="$t('{swap} Listing Price', {swap: form.router?.name ?? ''})"
					v-model="form.listingRate"
					v-if="!isFairLaunch"
					:placeholder="$t('Listing Price')"
					:help="
						form.listingRate
							? `1 ${baseToken?.symbol} = ${form.listingRate}${form?.symbol}`
							: $t('Number of tokens in 1 {symbol}', {symbol: baseToken?.symbol})
					"
					:error="form.errors.listingRate"
				>
					<template #trail>{{ form?.symbol }}</template>
				</FormInput>
				<FormInput
					:label="$t('{swap} Liquidity %', {swap: form.router?.name ?? ''})"
					v-model="form.liquidityPercent"
					:placeholder="$t('% {symbol} for Liquidity', {symbol: baseToken?.symbol})"
					:help="$t('Minimum is 60%')"
					:error="form.errors.liquidityPercent"
				>
					<template #trail>%</template>
				</FormInput>
			</div>

			<div class="gap-x-3 mx-auto align-middle grid md:grid-cols-2">
				<FormInput
					:label="
						isFairLiquidity
							? $t('Liquidity Lock Days. (Max {maxLockPeriod} days)', launch)
							: $t('Liquidity Lock Days')
					"
					v-model="form.lockPeriod"
					:placeholder="$t('Lock LP token days')"
					:help="$t('minimum {min} days', {min: launch.minLockPeriod})"
					:error="form.errors.lockPeriod"
				/>
				<div class="">
					<label class="inline-flex mt-9 items-center space-x-2">
						<input
							class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-success checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
							type="checkbox"
							v-model="form.useVesting"
						/>
						<span>{{ $t("Use Vesting Release") }}</span>
					</label>
				</div>
			</div>
			<CollapseTransition>
				<div v-show="form.useVesting">
					<h3 class="grid text-base">
						{{ $t("Token Release Vesting Schedule") }}
					</h3>
					<p
						v-if="isFairLiquidity"
						class="mb-6"
					>
						{{ $t("This release schedule is after users lockup period expires") }}
					</p>
					<p
						v-else
						class="mb-6"
					>
						{{ $t("This release schedule is after your lockup period expires") }}
					</p>
					<div class="gap-x-3 mx-auto grid md:grid-cols-3">
						<FormInput
							:label="$t('First Release after lockup expiry')"
							v-model="form.initialPercent"
							:placeholder="$t('Initial Percent')"
							:help="$t('% initial amount')"
							:error="form.errors.initialPercent"
						>
							<template #trail>%</template>
						</FormInput>
						<FormInput
							:label="
								isFairLiquidity
									? $t('Release cycle (max {maxLockCycle} days)', launch)
									: $t('Release cycle')
							"
							v-model="form.cycle"
							:placeholder="$t('Release cycle')"
							:help="$t('Days between releases')"
							:error="form.errors.cycle"
						>
							<template #trail>{{ $t("Days") }}</template>
						</FormInput>
						<FormInput
							:label="
								isFairLiquidity
									? $t('Percent per cycle (Min {minCyclePercent}%)', launch)
									: $t('Percent per cycle')
							"
							v-model="form.cyclePercent"
							:placeholder="$t('Cycle Amount')"
							:help="
								$t('Release % every {cycle} Days', {cycle: form.cycle ?? 'cycle'})
							"
							:error="form.errors.cyclePercent"
						>
							<template #trail>%</template>
						</FormInput>
					</div>
				</div>
			</CollapseTransition>
			<div class="gap-3 mx-auto grid md:grid-cols-3">
				<div class="h-full w-full justify-end flex items-center col-span-1">
					<h3 class="text-sm">
						<p
							v-if="launch.error"
							class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
						>
							{{ launch.error }}
						</p>
						<p
							v-else
							class="col-span-3 text-xs font-semibold"
						>
							{{ launch.busy ? launch.status : "Deploy token"
							}}<a
								:href="launch.txlink"
								target="_blank"
								class="ml-2 text-emerald-500"
								v-if="launch.busy && launch.tx"
								>{{ launch.tx }}</a
							>
						</p>
					</h3>
				</div>

				<div
					class="flex flex-col md:flex-row space-y-2 md:space-y-0 col-span-2 md:ml-auto lg:m-auto"
				>
					<button
						@click="previous"
						type="button"
						class="py-2.5 px-5 mr-3 text-base font-medium text-gray-900 focus:outline-none bg-white rounded-sm border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
					>
						{{ $t("Go Back") }}
					</button>
					<button
						type="button"
						@click="open()"
						class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none font-medium focus:ring-emerald-300 rounded-sm text-sm px-5 py-2.5 text-center inline-flex items-center justify-center mr-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
					>
						<Loading
							v-if="launch.busy"
							class="mr-2 -ml-1 inline-block w-5 h-5"
						/>
						<PlusIcon
							v-else
							class="mr-2 -ml-1 w-5 h-5 inline-block"
						/>
						{{ $t("Deploy") }} ({{ fees }}{{ feeSymbol }})
					</button>
				</div>
			</div>
			<LaunchPadConfirmationModal
				:baseToken="baseToken"
				:balance="balance"
				:info="info"
				:isOpen="isOpen"
				:busy="launch.busy"
				@close="close"
				:form="form"
				@deploy="deploy()"
			/>
		</div>
	</CreateLayout>
</template>
