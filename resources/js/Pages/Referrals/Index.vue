<script setup>
import {HiCheck, MdContentcopy} from "oh-vue-icons/icons";

import Pagination from "@/Components/PaginationSquare.vue";
import TxHash from "@/Components/TxHash.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopy from "@/Components/WeCopy.vue";
import WeCopySvg from "@/Components/WeCopySvg.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
	referrals: Array,
	refLinks: Array,
	total: Number,
	totalGiveways: Number,
	sumGiveways: Number,
});
</script>
<template>
	<AppLayout>
		<div class="mx-auto container px-4">
			<div
				class="rounded-sm p-5 mt-8 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600"
			>
				<div>
					<div>
						<h2
							class="text-gray-900 dark:text-white mb-5 font-semibold pb-2 border-b border-gray-200 dark:border-gray-600"
						>
							{{ $t("Referrals") }}
						</h2>
						<p class="mb-3">We have increased referral amount from 5% to 10%</p>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
						<div
							class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 rounded-sm border border-gray-200 dark:border-gray-600 p-4"
						>
							<div>
								<h4
									class="text-gray-600 dark:text-gray-300 uppercase text-xs font-semibold"
								>
									{{ $t("Your Total Referrals") }}
								</h4>
								<h2 class="font-bold text-2xl text-gray-900 dark:text-white mt-3">
									<span class="font-mono">{{ total ?? 0 }}</span>
									{{ $t("Projects") }}
								</h2>
							</div>
						</div>
						<div
							class="bg-gray-100 dark:bg-gray-700 flex justify-between items-center rounded-sm border border-gray-200 dark:border-gray-600 p-4"
						>
							<div>
								<h4
									class="text-gray-600 dark:text-gray-300 uppercase text-xs font-semibold"
								>
									{{ $t("Giveaways created by your referrals") }}
								</h4>
								<h2 class="font-bold text-2xl text-gray-900 dark:text-white mt-3">
									<span class="font-mono">{{ totalGiveways ?? 0 }}</span>
									{{ $t("Giveaways") }} ({{ sumGiveways ?? 0 }} USDT)
								</h2>
							</div>
						</div>
					</div>
					<div class="mt-10">
						<ul class="text-sm">
							<li
								class="grid md:grid-cols-4 gap-3 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
							>
								<h3 class="text-gray-900 text-sm md:col-span-2 dark:text-white">
									{{ $t("Referral Links") }}
								</h3>
								<h3 class="text-gray-900 text-sm dark:text-white">
									{{ $t("25K Gas Bonus Code") }}
								</h3>
								<h3 class="text-gray-900 text-sm dark:text-white">
									{{ $t("Number of referrals") }}
								</h3>
							</li>
							<li
								v-for="link in refLinks"
								:key="link.referral"
								class="grid md:grid-cols-4 gap-3 mb-3 pb-3 border-b border-gray-100 dark:border-gray-700"
							>
								<div
									class="text-xs md:col-span-2 md:text-sm flex flex-col md:space-x-2"
								>
									<div class="flex items-center space-x-2">
										<WeCopy
											class="self-center items-center order-last mr-2"
											:text="link.refLink"
											after
										>
											<span
												class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
												>{{ link.refLink }}
											</span>
											<VueIcon
												:icon="MdContentcopy"
												class="w-4 h-4"
											/>
										</WeCopy>
									</div>
								</div>
								<WeCopySvg
									class="self-center items-center mr-2"
									:text="link.code"
									after
								>
									<span
										class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
										>{{ link.code }}
									</span>
								</WeCopySvg>
								<h2 class="text-gray-800 dark:text-gray-200 font-semibold">
									{{ link.count }}
								</h2>
							</li>
						</ul>
					</div>
					<div class="mt-10">
						<table class="table-fixed w-full">
							<thead>
								<tr>
									<th
										class="text-left pb-5 md:px-4 text-gray-900 dark:text-white"
									>
										{{ $t("Project") }}
									</th>
									<th
										class="text-left pb-5 md:px-4 text-gray-900 dark:text-white"
									>
										{{ $t("Giveaway") }}
									</th>
									<th
										class="text-left pb-5 md:px-4 text-gray-900 dark:text-white"
									>
										{{ $t("Commission") }}
									</th>
									<th
										class="text-left pb-5 md:px-4 text-gray-900 dark:text-white"
									>
										{{ $t("status") }}
									</th>
									<th class="pb-5 text-end md:px-4 text-gray-900 dark:text-white">
										{{ $t("Date") }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="ga in referrals.data"
									:key="ga.uuid"
								>
									<td
										class="pt-4 pb-4 md:px-4 border-b border-gray-200 dark:border-gray-600"
									>
										<a
											:href="addressLink"
											target="_blank"
											rel="nofollow noreferrer"
											>{{ ga.project.name }}</a
										>
									</td>
									<td
										class="pt-4 pb-4 md:px-4 border-b border-gray-200 dark:border-gray-600"
									>
										<h2
											class="text-gray-900 dark:text-white text-xs md:text-sm"
										>
											{{ ga.short_summary }}
										</h2>
									</td>
									<td
										class="pt-4 pb-4 md:px-4 border-b border-gray-200 dark:border-gray-600"
									>
										<h2
											class="text-gray-900 dark:text-white text-xs md:text-sm"
										>
											{{ ga.paid / 10 }} USDT
										</h2>
									</td>
									<td
										class="pt-4 pb-4 md:px-4 border-b border-gray-200 dark:border-gray-600"
									>
										<TxHash
											:chain-id="ga.chainId"
											:txhash="ga.ref_paid"
											v-if="ga.ref_paid"
											class="text-emerald-500 text-xs md:text-sm"
										>
											<VueIcon :icon="HiCheck" />

											PAID</TxHash
										>
										<h2
											v-else
											class="text-gray-900 dark:text-white text-xs md:text-sm"
										>
											UNPAID
										</h2>
									</td>
									<td
										class="pt-4 pb-4 md:px-4 text-end border-b border-gray-200 dark:border-gray-600"
									>
										<h2
											class="text-gray-900 dark:text-white text-xs md:text-sm"
										>
											{{ ga.created_at }}
										</h2>
									</td>
								</tr>
							</tbody>
						</table>
						<!-- pagination buttons -->
						<Pagination :meta="referrals.meta" />
						<!-- ./ Token table -->
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
