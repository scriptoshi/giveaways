<script setup>
import {computed, watch} from "vue";

import {ArrowTopRightOnSquareIcon, PlusIcon} from "@heroicons/vue/24/outline";
import {useForm} from "@inertiajs/vue3";
import upperFirst from "lodash/upperFirst";
import {useAccount} from "use-wagmi";

import ApproveTokenButton from "@/Components/ApproveTokenButton.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import ConnetWalletButton from "@/Components/ConnectWalletButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import UnsupportedFactory from "@/Components/UnsupportedFactory.vue";
import {useDeployStandardToken} from "@/hooks/token/useDeployStandardToken.js";
import {useBillions} from "@/hooks/useBillions";
import fakeLogo from "@/images/no-image-available-icon.jpeg?url";
const props = defineProps({
	factory: Object,
});
const {address: account} = useAccount();
const form = useForm({
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
const deploy = useDeployStandardToken(props.factory, form);
const links = {
	antibot: "#",
	burnable: "https://docs.openzeppelin.com/contracts/4.x/api/token/erc20#ERC20Burnable",
	pausable: "https://docs.openzeppelin.com/contracts/4.x/api/token/erc20#ERC20Pausable",
	capped: "https://docs.openzeppelin.com/contracts/4.x/api/token/erc20#ERC20Capped",
	votes: "https://docs.openzeppelin.com/contracts/4.x/api/token/erc20#ERC20Votes",
	permit: "https://docs.openzeppelin.com/contracts/4.x/api/token/erc20#ERC20Permit",
};
const supply = computed(() => form.total_supply);
const shortSupply = computed(() => useBillions(supply));
watch(account, (account) => (form.minter = account), {immediate: true});
</script>
<template>
	<div class="space-y-6 mb-6">
		<h3 class="text-base my-4">
			{{ $t("Select ERC20 Extensions") }}
		</h3>
		<div class="p-8 border rounded-sm border-gray-200 dark:border-gray-700">
			<div class="grid grid-cols-2 place-items-start gap-6 sm:grid-cols-3">
				<label
					v-for="ext in deploy.extensions"
					:key="ext"
					class="inline-flex items-center gap-4"
				>
					<input
						class="form-checkbox is-basic h-5 w-5 rounded-sm border-slate-400/70checked:!border-emerald-600 checked:bg-emerald-600 hover:!border-emerald-600 focus:!border-emerald-600 dark:border-navy-400"
						type="checkbox"
						v-model="deploy.plugins"
						:value="ext"
					/>
					<span class="font-semibold text-base">{{ upperFirst(ext) }}</span>
					<a
						target="_blank"
						v-tippy="$t('View Contract Details')"
						class="text-gray-600 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100"
						:href="links[ext]"
					>
						<ArrowTopRightOnSquareIcon class="w-4 h-4 inline-block" />
					</a>
				</label>
			</div>
			<h3 class="mt-4 text-xl text-emerald-500">
				{{ upperFirst(deploy.contractName) }}
			</h3>
		</div>
		<CollapseTransition>
			<div
				v-show="!deploy.noMinter"
				class="gap-x-3 mx-auto grid md:grid-cols-4"
			>
				<FormInput
					:label="$t('Mint Token Supply to')"
					v-model="form.minter"
					class="md:col-span-3"
					:placeholder="$t('Token supply destination address')"
					:error="form.errors.minter"
				/>
			</div>
		</CollapseTransition>
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
		<div class="gap-x-3 mx-auto grid md:grid-cols-3">
			<FormInput
				:label="$t('Token Name')"
				v-model="form.name"
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
		<ConnetWalletButton
			v-else
			class="md:col-start-2"
		>
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
</template>
