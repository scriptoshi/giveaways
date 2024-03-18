<script setup>
import {ArrowLeftIcon} from "@heroicons/vue/24/outline";
import {Link} from "@inertiajs/vue3";

import AdminLayout from "@/Layouts/AdminLayout.vue";
import Token from "./Show/Token.vue";
defineProps({
	badge: Object,
	project: Object,
	badgeId: String,
	projectId: Number,
	config: Object,
});
</script>
<template>
	<AdminLayout>
		<main class="h-full">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ $t("Badge request") }}</h3>
							<a
								target="_blank"
								class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition duration-200"
								:href="route('projects.show', project.uid)"
								>Project: {{ project.uid }}</a
							>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								type="button"
								v-if="chainId"
								:href="route('admin.badges.index', {chainId})"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<ArrowLeftIcon class="mr-2 -ml-1 w-5 h-5 inline" />
								{{ $t("Go Back") }}
							</Link>
						</div>
					</div>
					<div class="2xl:col-span-4 lg:col-span-3 xl:col-span-2">
						<div class="card card-border">
							<div class="card-body pb-4">
								<div class="px-8 mx-auto">
									<div class="container my-12 mx-auto vertical">
										<ul class="mt-5">
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Project Name</p>
												<p>{{ project.name }}</p>
											</li>
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Requested Badge</p>
												<div
													v-if="badge.badge == 'SAFE'"
													class="badge space-x-2.5 text-info"
												>
													<div
														class="h-2 w-2 rounded-full bg-current"
													></div>
													<span>{{ badge.badge }}</span>
												</div>
												<div
													v-else-if="badge.badge == 'KYC'"
													class="badge space-x-2.5 text-success"
												>
													<div
														class="h-2 w-2 rounded-full bg-current"
													></div>
													<span>{{ badge.badge }}</span>
												</div>
												<div
													v-else-if="badge.badge == 'FEATURE'"
													class="badge space-x-2.5 text-warning"
												>
													<div
														class="h-2 w-2 rounded-full bg-current"
													></div>
													<span>{{ badge.badge }}</span>
												</div>
												<div
													v-else-if="badge.badge == 'AUDIT'"
													class="badge space-x-2.5 text-error"
												>
													<div
														class="h-2 w-2 rounded-full bg-current"
													></div>
													<span>{{ badge.badge }}</span>
												</div>
												<div
													v-else
													class="badge space-x-2.5 text-slate-800 dark:text-navy-100"
												>
													<div
														class="h-2 w-2 rounded-full bg-current"
													></div>
													<span>{{ badge.badge }}</span>
												</div>
											</li>
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Launchpads Count</p>
												<p>{{ project.launchpads_count }}</p>
											</li>
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Staking Count</p>
												<p>{{ project.stakings_count }}</p>
											</li>
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Airdrops Count</p>
												<p>{{ project.airdrops_count }}</p>
											</li>
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Governors Count</p>
												<p>{{ project.governors_count }}</p>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="card card-border mt-5">
							<div class="card-body pb-4">
								<div class="px-8 mx-auto">
									<div class="container my-12 mx-auto vertical">
										<ul class="mt-5">
											<li
												class="flex flex-col md:flex-row space-y-2 justify-between md:space-y-0 mb-3 pb-2 border-b border-gray-100 dark:border-gray-600"
											>
												<p>Tokens</p>
												<p>Type</p>
											</li>
											<Token
												v-for="token in project.tokens"
												:key="token.id"
												:token="token"
												:appName="config?.appName"
											/>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
