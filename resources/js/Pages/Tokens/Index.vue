<script setup>
import {ref} from "vue";

import {Link} from "@inertiajs/vue3";
import {BiWindowDash, HiSolidPlus, MdContentcopy} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";
import {useI18n} from "vue-i18n";

import Address from "@/Components/Address.vue";
import BreadCrumbs from "@/Components/BreadCrumbs.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ChainInfo from "@/Components/ChainInfo.vue";
import PaginationSquare from "@/Components/PaginationSquare.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import {formatMoney} from "@/hooks/money";
import AppLayout from "@/Layouts/AppLayout.vue";
defineProps({
	tokens: Array,
});
const {t} = useI18n();
const crumbs = ref([
	{
		name: t("My Tokens"),
	},
]);
const {chainId} = useAccount();
</script>
<template>
	<AppLayout>
		<div class="mt-8 mx-auto container px-4">
			<BreadCrumbs :crumbs="crumbs" />
			<div class="mt-4 mb-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0">
				<h3 class="text-xl">{{ $t("Manage Tokens") }}</h3>
				<div
					v-if="chainId"
					class="flex flex-col space-y-2 sm:space-y-0 sm:flex-row sm:space-x-2"
				>
					<PrimaryButton
						secondary
						link
						:href="route('tokens.create', {type: 'standard', chainId})"
					>
						<VueIcon
							:icon="HiSolidPlus"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Standard Token") }}
					</PrimaryButton>
					<PrimaryButton
						secondary
						link
						:href="route('tokens.create', {type: 'taxtoken', chainId})"
					>
						<VueIcon
							:icon="HiSolidPlus"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Tax Token") }}
					</PrimaryButton>
					<PrimaryButton
						secondary
						link
						:href="route('tokens.create', {type: 'crosschain', chainId})"
					>
						<VueIcon
							:icon="HiSolidPlus"
							class="-ml-1 mr-2 w-4 h-4"
						/>
						{{ $t("Crosschain Token") }}
					</PrimaryButton>
				</div>
			</div>
			<div
				v-if="tokens.data.length === 0"
				class="h-64 p-8 w-full border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-sm flex items-center justify-center"
			>
				<div class="flex items-center flex-col">
					<VueIcon
						:icon="BiWindowDash"
						class="w-16 h-16 mb-6 text-gray-300 dark:text-gray-600"
					/>
					<h3 class="text-gray-400 dark:text-gray-500 text-sm">
						{{ $t("It seems you have not deployed any tokens") }}
					</h3>
					<p class="text-gray-400 dark:text-gray-500 text-sm">
						{{ $t("Use the links below to create your own unique token") }}
					</p>
					<div
						v-if="chainId"
						class="flex flex-col mt-8 space-y-2 sm:space-y-0 sm:flex-row sm:space-x-2"
					>
						<PrimaryButton
							secondary
							link
							:href="route('tokens.create', {type: 'standard', chainId})"
						>
							<VueIcon
								:icon="HiSolidPlus"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							{{ $t("Standard Token") }}
						</PrimaryButton>
						<PrimaryButton
							secondary
							link
							:href="route('tokens.create', {type: 'taxtoken', chainId})"
						>
							<VueIcon
								:icon="HiSolidPlus"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							{{ $t("Tax Token") }}
						</PrimaryButton>
						<PrimaryButton
							secondary
							link
							:href="route('tokens.create', {type: 'crosschain', chainId})"
						>
							<VueIcon
								:icon="HiSolidPlus"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							{{ $t("Crosschain Token") }}
						</PrimaryButton>
					</div>
				</div>
			</div>
			<div
				v-else
				class="rounded-sm p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600"
			>
				<div class="my-8">
					<table class="table-fluid w-full">
						<thead>
							<tr>
								<th
									class="text-left pb-5 md:pl-8 md:pr-8 text-gray-900 dark:text-white"
								>
									{{ $t("Name") }}
								</th>
								<th
									class="text-left pb-5 md:pl-8 md:pr-8 text-gray-900 dark:text-white"
								>
									{{ $t("Chain") }}
								</th>
								<th
									class="text-left pb-5 md:pl-8 md:pr-8 text-gray-900 dark:text-white"
								>
									{{ $t("Contract") }}
								</th>
								<th
									class="text-left hidden sm:table-cell pb-5 md:pl-8 md:pr-8 text-gray-900 dark:text-white"
								>
									{{ $t("dec") }}
								</th>

								<th
									class="text-left hidden sm:table-cell pb-5 md:pl-8 md:pr-8 text-gray-900 dark:text-white"
								>
									{{ $t("Supply") }}
								</th>
								<th
									class="text-left hidden sm:table-cell pb-5 md:pl-8 md:pr-8 text-gray-900 dark:text-white"
								>
									{{ $t("symbol") }}
								</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="token in tokens.data"
								:key="token.id"
							>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600"
								>
									<div class="flex space-x-2 md:space-x-4">
										<img
											:src="token.logo_uri"
											v-if="token.logo_uri"
											class="w-5 h-5 md:h-10 md:w-10"
											:alt="token.symbol"
										/>
										<div
											v-else
											class="avatar w-5 h-5 md:h-10 md:w-10 hover:z-10"
										>
											<div
												class="is-initial rounded-full font-bold bg-success text-xs text-[10px] uppercase text-white ring ring-white dark:ring-navy-700"
											>
												{{ token.symbol }}
											</div>
										</div>
										<div>
											<h2
												class="text-gray-900 dark:text-white font-semibold text-sm"
											>
												{{ token.name }}
											</h2>
											<p class="text-gray-500 dark:text-gray-400 text-xs">
												{{ token.type }}
											</p>
										</div>
									</div>
								</td>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600"
								>
									<ChainInfo :chain-id="token.chainId" />
								</td>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600"
								>
									<div class="flex space-x-1">
										<Address
											:address="token.contract"
											:len="4"
										/>
										<WeCopy
											after
											:text="token.contract"
										>
											<VueIcon :icon="MdContentcopy" />
										</WeCopy>
									</div>
								</td>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600"
								>
									{{ token.decimals }}
								</td>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600 hidden sm:table-cell"
								>
									{{ formatMoney(token.total_supply) }}
								</td>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600 hidden sm:table-cell"
								>
									<h2 class="text-gray-900 dark:text-white text-xs md:text-sm">
										<span class="uppercase font-semibold">{{
											token.symbol
										}}</span>
									</h2>
								</td>
								<td
									class="md:pl-8 pt-4 pb-4 md:pr-8 border-b border-gray-200 dark:border-gray-600 text-right"
								>
									<Link
										v-if="chainId"
										:href="
											route('tokens.show', {
												chainId: token.chainId,
												token: token.contract,
											})
										"
										class="bg-emerald-500 dark:bg-emerald-500 hover:bg-emerald-600 transition duration-150 px-2 py-1 text-sm rounded-sm text-white"
										>{{ $t("Admin") }}</Link
									>
								</td>
							</tr>
						</tbody>
					</table>
					<PaginationSquare :meta="tokens.meta" />
				</div>
			</div>
		</div>
	</AppLayout>
</template>
