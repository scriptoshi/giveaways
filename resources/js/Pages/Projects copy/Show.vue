<script setup>
import {ref} from "vue";

import {
	BiChatLeftTextFill,
	BiDiscord,
	BiTelegram,
	BiTwitter,
	MdLivetv,
	RiTelegramLine,
	RiTwitterLine,
} from "oh-vue-icons/icons";
import {uid} from "uid";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProjectChain from "@/Pages/Admin/Projects/ProjectChain.vue";
import BuyCard from "@/Pages/Projects/Show/BuyCard.vue";
import ComposeMessages from "@/Pages/Projects/Show/ComposeMessages.vue";
import Emoticons from "@/Pages/Projects/Show/Emoticons.vue";
import HowGiveAwaysWorkModal from "@/Pages/Projects/Show/HowGiveAwaysWorkModal.vue";
import Information from "@/Pages/Projects/Show/Information.vue";
import Leaderboard from "@/Pages/Projects/Show/Leaderboard.vue";
import LinkHeader from "@/Pages/Projects/Show/LinkHeader.vue";
import UpdateMessageCard from "@/Pages/Projects/Show/UpdateMessageCard.vue";
import VotersCard from "@/Pages/Projects/Show/VotersCard.vue";
import TwitterAccount from "./Show/Connect/TwitterAccount.vue";

defineProps({
	project: Object,
	votes: Object,
	isInfluencer: Boolean,
});

const messagesBeingEdited = ref();
const adminMessages = ref(false);
const done = () => {
	messagesBeingEdited.value = null;
	adminMessages.value = false;
};
const uuid = uid();
</script>
<template>
	<AppLayout>
		<ProjectChain
			:chain-id="project.chainId"
			v-slot="{chain}"
		>
			<div class="mx-auto py-4 sm:py-6 md:py-12 container">
				<div class="flex-1 lg:flex-[2] w-full relative">
					<div class="flex items-center justify-between gap-2 ml-[20%] md:ml-[17%]">
						<h2
							class="text-[26px] font-semibold text-gray-600 dark:text-emerald-600 dark-text-emerald-400-text-dark capitalize leading-relaxed"
						>
							{{ project.title }}
						</h2>
						<div class="mb-2 p-2 flex justify-end space-x-2">
							<LinkHeader :project="project" />
						</div>
					</div>
					<div
						class="absolute -mt-10 sm:-mt-12 md:-mt-18 left-[10%] -translate-x-[50%] z-10"
					>
						<figure
							class="inline-block bg-white dark:bg-gray-900 relative shadow-xl rounded-full"
						>
							<img
								:src="project.logo?.url"
								alt=""
								class="rounded-full p-[2px] border border-gray-200 dark:border-gray-700 w-[80px] h-[80px] sm:w-[92px] sm:h-[92px] md:w-[128px] md:h-[128px]"
								loading="lazy"
							/>
							<img
								:src="chain.logo"
								class="rounded-full p-[1px] border border-gray-200 w-6 h-6 absolute right-0 bottom-0 sm:right-1 sm:bottom-1 md:right-2 md:bottom-2"
							/>
						</figure>
					</div>
					<div
						class="shadow-sm dark:shadow-lg text-sm dark:text-gray-300 dark-text-emerald-400-text-dark p-4 py-6 relative overflow-hidden rounded-t-none rounded-b-md bg-white dark:bg-gray-800"
					>
						<div class="relative">
							<div class="flex items-center justify-end">
								<div class="font-bold mr-6 font-Walsheim-Bold text-2xl">
									2400$ In Rewards
								</div>
								<div class="">
									<PrimaryButton
										link
										v-if="project.isOwner"
										class="!py-1"
										:href="route('giveaways.create', {project: project.uuid})"
										>Create giveaway</PrimaryButton
									>
									<PrimaryButton
										link
										class="!py-1"
										:href="route('giveaways.create', {project: project.uuid})"
										>Join</PrimaryButton
									>
								</div>
							</div>
							<div
								class="border-b w-full dark:border-gray-600 my-4 border-dashed"
							></div>
							<div class="flex justify-between items-center">
								<div class="text-md font-bold">About</div>
							</div>
							<div class="my-4 w-full overflow-hidden">
								<div class="flex flex-col lg:flex-row gap-6">
									<div
										class="whitespace-pre-line break-words text-sm leading-relaxed"
									>
										{{ project.description }}
									</div>
									<div class="w-full flex gap-4 justify-between items-center">
										<ul
											class="inline-flex py-4 px-6 border border-gray-200 dark:border-gray-700 rounded-[4px] max-w-fit"
										>
											<li class="list-none inline-flex flex-col h-16">
												<div
													class="inline-flex space-x-4 items-center justify-between text-gray-500 dark:text-gray-400 font-normal text-sm"
												>
													<span class="flex whitespace-nowrap"
														>Members</span
													>
													<span
														class="inline-block text-xs leading-5 border border-gray-200 dark:border-gray-600 rounded-sm px-2"
														>#356</span
													>
												</div>
												<div
													class="font-bold font-Walsheim-Bold text-2xl mt-4"
												>
													456,000
												</div>
											</li>
											<div
												class="w-[1px] mx-4 sm:mx-8 bg-gray-200 dark:bg-gray-600"
											></div>
											<li class="list-none inline-flex flex-col h-16">
												<div
													class="inline-flex space-x-4 items-center justify-between text-gray-500 dark:text-gray-400 font-normal text-sm"
												>
													<span class="flex whitespace-nowrap"
														>Pumps</span
													>
												</div>
												<div
													class="font-bold font-Walsheim-Bold text-2xl mt-4"
												>
													456,000
												</div>
											</li>
											<div
												class="w-[1px] mx-4 sm:mx-8 bg-gray-200 dark:bg-gray-600"
											></div>
											<li class="list-none inline-flex flex-col h-16">
												<div
													class="inline-flex space-x-4 items-center justify-between text-gray-500 dark:text-gray-400 font-normal text-sm"
												>
													<span class="flex whitespace-nowrap"
														>Avg Pumps</span
													>
												</div>
												<div
													class="font-bold font-Walsheim-Bold text-2xl mt-4"
												>
													234
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="mx-auto py-4 sm:py-6 container relative">
				<TwitterAccount />
				<div class="flex items-start gap-4 flex-col lg:flex-row">
					<div class="flex-1 lg:flex-[2] w-full">
						<div class="px-4 mt-4 flex justify-between">
							<span>Give Aways</span> <HowGiveAwaysWorkModal />
						</div>
						<div
							class="p-4 rounded-sm shadow-sm dark:shadow-lg dark:text-gray-300 dark-text-emerald-400-text-dark bg-white dark:bg-gray-800 mt-4"
						>
							<div class="grid sm:grid-cols-2 gap-3">
								<div>
									<h3>2000$ USDT</h3>
									<div class="w-full flex items-center">
										<span> (~10 Winners) 10Days</span>
										<span
											class="ml-3 px-2 border rounded-sm border-green-500 text-xs text-green-500 font-semibold uppercase"
										>
											LIVE
										</span>
									</div>
								</div>
								<div class="sm:text-end">
									<span>GIVEAWAY HOST</span>
									<div class="flex items-center sm:justify-end">
										<img
											class="w-7 h-7 mr-2 rounded-full border border-gray-200 dark:border-gray-600"
											src="https://static.sleep.finance/profile-photos/r6t6skiM4P0rLEZLvlCj.jpg"
										/>
										<h3 class="text-gray-400 dark:text-gray-500">
											SLEEP FINANCE
										</h3>
									</div>
								</div>
							</div>
							<div class="flex mt-3 space-x-3 items-center justify-start">
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									>PUMPS <span class="text-emerald-500 ml-0.5">500</span>
								</span>
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									>BUY <span class="text-emerald-500 ml-0.5">20$</span>
								</span>
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									>TASK
									<VueIcon
										v-tippy="$t('Follow Twitter')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiTwitter"
									/>
									<VueIcon
										v-tippy="$t('Join Telegram')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiTelegram"
									/>
									<VueIcon
										v-tippy="$t('Join Discord')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiDiscord"
									/>
									<VueIcon
										v-tippy="$t('Comment')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiChatLeftTextFill"
									/>
								</span>
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									>COMMENT
								</span>
							</div>
						</div>

						<div
							class="p-4 rounded-sm shadow-sm dark:shadow-lg dark:text-gray-300 dark-text-emerald-400-text-dark bg-white dark:bg-gray-800 mt-4"
						>
							<div class="grid sm:grid-cols-2 gap-3">
								<div>
									<h3>23,00000 PEPE</h3>
									<div class="w-full flex items-center">
										<span> (~$800 | 200 Winners) 25Days</span>
										<span
											class="ml-3 px-2 border rounded-sm border-green-500 text-xs text-green-500 font-semibold uppercase"
										>
											LIVE
										</span>
									</div>
								</div>
								<div class="sm:text-end">
									<span>GIVEAWAY HOST</span>
									<div class="flex items-center sm:justify-end">
										<img
											class="w-7 h-7 mr-2 rounded-full border border-gray-200 dark:border-gray-600"
											src="https://static.sleep.finance/profile-photos/r6t6skiM4P0rLEZLvlCj.jpg"
										/>
										<h3 class="text-gray-400 dark:text-gray-500">
											SLEEP FINANCE
										</h3>
									</div>
								</div>
							</div>
							<div class="flex mt-3 space-x-3 items-center justify-start">
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									>PUMPS <span class="text-emerald-500 ml-0.5">500</span>
								</span>
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									>BUY <span class="text-emerald-500 ml-0.5">20$</span>
								</span>
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									><span class="hidden sm:flex">EVENT</span>
									<VueIcon
										v-tippy="$t('Follow Twitter')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiTwitter"
									/>
									<VueIcon
										v-tippy="$t('Join Telegram')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiTelegram"
									/>
									<VueIcon
										v-tippy="$t('Join Discord')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiDiscord"
									/>
									<VueIcon
										v-tippy="$t('Comment')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="BiChatLeftTextFill"
									/>
								</span>
								<span
									class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
									><span class="hidden sm:flex">EVENT</span>
									<VueIcon
										v-tippy="$t('Twitter Broadcast')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="RiTwitterLine"
									/>
									<VueIcon
										v-tippy="$t('Telegram AMA')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="RiTelegramLine"
									/>
									<VueIcon
										v-tippy="$t('Live Stream')"
										class="ml-0.5 !w-4 !h-4 text-emerald-500 cursor-pointer"
										:icon="MdLivetv"
									/>
								</span>
							</div>
						</div>
						<Leaderboard
							:url="route('projects.show', {project: project.slug})"
							:votes="votes"
						/>
						<div
							class="rounded-sm shadow-sm dark:shadow-lg dark:text-gray-300 dark-text-emerald-400-text-dark bg-white dark:bg-gray-800 mt-4"
						>
							<div
								class="border-b flex items-center justify-between border-dashed dark:border-gray-600 p-4"
							>
								<div>
									<h2
										class="capitalize font-semibold leading-relaxed text-md flex-1"
									>
										Shout backs
									</h2>
									<p class="text-xs">
										{{
											$t(
												"Information here is not moderated. Please take caution",
											)
										}}
									</p>
								</div>
							</div>

							<UpdateMessageCard
								v-for="update in project.updates"
								:key="update.uuid"
								:update="update"
								:project="project"
								:isAdmin="project.isAdmin"
								@edit="messagesBeingEdited = update"
							/>
							<ComposeMessages
								class="p-6"
								:project="project"
								:update="messagesBeingEdited"
								@done="done"
								:key="messagesBeingEdited?.uuid ?? uuid"
							/>
						</div>
					</div>
					<div class="flex-1 w-full">
						<div class="lg:block">
							<div class="mb-4 p-2 flex justify-end space-x-2">
								<h3 class="text-sm">You have 20 pump bonus</h3>
							</div>
							<div
								class="shadow-sm mb-6 dark:shadow-lg text-sm dark:text-gray-300 dark-text-emerald-400-text-dark p-4 rounded-md bg-white dark:bg-gray-800"
							>
								<VotersCard :project="project" />
								<Emoticons :project="project" />
							</div>

							<BuyCard :project="project" />
							<Information
								class="mt-6"
								:chain="chain"
								:project="project"
							/>
						</div>
					</div>
				</div>
			</div>
		</ProjectChain>
	</AppLayout>
</template>
