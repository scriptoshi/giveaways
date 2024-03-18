<script setup>
import {computed, watch} from "vue";

import {PlusIcon} from "@heroicons/vue/24/outline";
import {useForm} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";

import ApproveTokenButton from "@/Components/ApproveTokenButton.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ConnetWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import SelectAmms from "@/Components/SelectAmms.vue";
import UnsupportedFactory from "@/Components/UnsupportedFactory.vue";
import {useDeployTaxToken} from "@/hooks/token/useDeployTaxToken.js";
import {useBillions} from "@/hooks/useBillions";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
const props = defineProps({
	factory: Object,
});
const {address: account} = useAccount();

const form = useForm({
	/// tax
	factory_id: props.factory?.id,
	swapAndLiquifyEnabled: true,
	router: null,
	liquidityFee: 5,
	taxFee: 5,
	owner: account.value,
	///
	token_id: null,
	name: null,
	upload_logo: false,
	logo_uri: null,
	minter: account.value,
	symbol: null,
	contract: null,
	decimals: null,
	total_supply: null,
});
const deploy = useDeployTaxToken(props.factory, form);
const supply = computed(() => form.total_supply);
const shortSupply = computed(() => useBillions(supply));
watch(
	account,
	(account) => {
		form.minter = account;
		form.owner = account;
	},
	{immediate: true},
);
</script>
<template>
	<div class="md:col-span-6 space-y-6">
		<div class="gap-x-3 mt-6 mx-auto grid">
			<p
				class="mt-2 text-sm text-gray-600 dark:text-gray-300"
				id="email-error"
			>
				{{ $t("Tax Tokens Must exempt presale address from Taxes") }}
			</p>
		</div>
		<CollapseTransition>
			<div class="gap-x-3 mx-auto grid md:grid-cols-4">
				<SelectAmms
					class="md:col-span-2"
					v-model="form.router"
				/>
				<FormInput
					:label="$t('Token Tax Fee %')"
					v-model="form.taxFee"
					:placeholder="$t('Tax Fee')"
					:help="$t('The percentage of taxes for admin')"
					:error="form.errors.taxFee"
				>
					<template #trail>%</template>
				</FormInput>
				<FormInput
					:label="$t('Liquidity Fee %')"
					v-model="form.liquidityFee"
					:placeholder="$t('Percent')"
					:help="$t('The percentage of taxes for Dex Liquidity')"
					:error="form.errors.liquidityFee"
				>
					<template #trail>%</template>
				</FormInput>
			</div>
		</CollapseTransition>
		<div class="gap-x-3 mx-auto grid md:grid-cols-4">
			<FormInput
				:label="$t('Token Admin (Owner)')"
				v-model="form.minter"
				class="md:col-span-3"
				:placeholder="$t('Token supply destination address')"
				:error="form.errors.minter"
			/>
		</div>
		<div class="gap-x-3 mx-auto grid md:grid-cols-4">
			<FormInput
				:label="$t('Mint Token Supply to')"
				v-model="form.minter"
				class="md:col-span-3"
				:placeholder="$t('Token supply destination address')"
				:error="form.errors.minter"
			/>
		</div>
		<div class="gap-x-3 mx-auto grid md:grid-cols-2">
			<FormInput
				v-model="form.logo_uri"
				:disabled="form.upload_logo"
				placeholder="https://"
				:error="form.errors.logo_uri"
				:help="$t('Supports png, jpeg, svg or gif')"
			>
				<template #label>
					<div class="flex">
						<span class="mr-3">{{ $t("Token Logo Url") }}</span>
						<label class="inline-flex items-center space-x-2">
							<input
								v-model="form.upload_logo"
								class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
								type="checkbox"
							/>
							<span>{{ $t("Upload to server") }}</span>
						</label>
					</div>
				</template>
			</FormInput>
			<template v-if="form.upload_logo">
				<LogoInput
					v-if="$page.props.config.s3"
					v-model="form.logo_uri"
					:errors="form.errors.logo_uri"
					auto
				/>
				<LogoInputLocal
					v-else
					v-model="form.logo_uri"
					:errors="form.errors.logo_uri"
				/>
			</template>
			<img
				v-else
				class="w-12 h-12 my-auto rounded-full b-0"
				:src="form.logo_uri ?? fakeLogo"
			/>
		</div>
		<div class="gap-x-3 mx-auto grid md:grid-cols-4">
			<FormInput
				:label="$t('Token Name')"
				v-model="form.name"
				class="md:col-span-2"
				:placeholder="$t('Token Name')"
				:error="form.errors.name"
			/>
			<FormInput
				:label="$t('Token Symbol')"
				v-model="form.symbol"
				:placeholder="$t('Token Symbol')"
				:error="form.errors.symbol"
			/>
			<FormInput
				:label="$t('Token Decimals')"
				v-model="form.decimals"
				placeholder="18"
				:error="form.errors.decimals"
				:help="$t('This is Normaly 18!')"
			/>
		</div>
		<div class="gap-x-3 mx-auto grid md:grid-cols-2">
			<FormInput
				:label="$t('Total supply ({shortSupply})', {shortSupply})"
				v-model="form.total_supply"
				:placeholder="$t('Total supply')"
				:error="form.errors.total_supply"
				:help="$t('Current Total supply of tokens')"
			/>
		</div>
		<UnsupportedFactory v-if="!factory" />
		<div
			class="md:col-start-2"
			v-else
		>
			<ConnetWalletButton>
				<ApproveTokenButton
					:contract="deploy.fees.feeToken"
					:spender="factory.contract"
					:amount="deploy.fees.price"
				>
					<div class="gap-x-3 flex flex-col sm:flex-row sm:justify-end">
						<div class="w-full justify-end flex items-center">
							<h3 class="text-sm">
								<p
									v-if="deploy.error"
									class="col-span-3 text-red dark:text-red-400 text-xs font-semibold"
								>
									{{ deploy.error }}
								</p>
								<p
									v-else
									class="col-span-3 text-xs font-semibold"
								>
									{{ deploy.busy ? deploy.status : "Deploy token"
									}}<a
										:href="deploy.txlink"
										target="_blank"
										class="ml-2 text-emerald-500"
										v-if="deploy.busy && deploy.tx"
										>{{ deploy.tx }}</a
									>
								</p>
							</h3>
						</div>
						<PrimaryButton
							type="button"
							@click="deploy.deploy()"
							primary
							class="whitespace-nowrap"
						>
							<Loading
								v-if="deploy.busy"
								class="mr-2 -ml-1 inline-block w-5 h-5"
							/>
							<PlusIcon
								v-else
								class="mr-2 -ml-1 w-5 h-5 inline-block"
							/>
							{{
								$t("Deploy Token {price} {symbol}", {
									price: deploy.fees.priceFormatted,
									symbol: deploy.fees.symbol,
								})
							}}
						</PrimaryButton>
					</div>
				</ApproveTokenButton>
			</ConnetWalletButton>
		</div>
	</div>
</template>
