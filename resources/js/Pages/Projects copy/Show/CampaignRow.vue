<script setup>
import {upperFirst} from "lodash";
defineProps({
	giveaway: Object,
	project: Boolean,
});
</script>
<template>
	<div
		class="p-4 rounded-sm shadow-sm dark:shadow-lg dark:text-gray-300 dark-text-emerald-400-text-dark bg-white dark:bg-gray-800 mt-4"
	>
		<div class="grid sm:grid-cols-2 gap-3">
			<div>
				<h3>{{ giveaway.prize }} {{ giveaway.prize_symbol }}</h3>
				<div class="w-full flex items-center">
					<span>
						({{ giveaway.winners }} Winners | ~ ${{
							giveaway.prize / giveaway.winners
						}}
						each| ) {{ giveaway.duration }}{{ upperFirst(giveaway.period) }}</span
					>
					<span
						v-if="giveaway.isLive"
						class="ml-3 px-2 border rounded-sm border-green-500 text-xs text-green-500 font-semibold uppercase"
					>
						LIVE
					</span>
					<span
						v-else-if="giveaway.isEnded"
						class="ml-3 px-2 border rounded-sm border-red-500 text-xs text-red-500 font-semibold uppercase"
					>
						ENDED
					</span>
					<span
						v-else
						class="ml-3 px-2 border rounded-sm border-gray-500 text-xs text-gray-500 font-semibold uppercase"
					>
						PENDING
					</span>
				</div>
			</div>
			<div
				v-if="project"
				class="sm:text-end"
			>
				<span>GIVEAWAY HOST</span>
				<div class="flex items-center sm:justify-end">
					<img
						v-if="giveaway.influencer?.logo?.url"
						class="w-7 h-7 mr-2 rounded-full border border-gray-200 dark:border-gray-600"
						:src="giveaway.influencer?.logo?.url"
					/>
					<h3 class="text-gray-400 dark:text-gray-500 uppercase">
						{{ giveaway.influencer?.name }}
					</h3>
				</div>
			</div>
			<div
				v-else
				class="sm:text-end"
			>
				<span>PROJECT</span>
				<div class="flex items-center sm:justify-end">
					<img
						v-if="giveaway.project?.logo?.url"
						class="w-7 h-7 mr-2 rounded-full border border-gray-200 dark:border-gray-600"
						:src="giveaway.project?.logo?.url"
					/>
					<h3 class="text-gray-400 dark:text-gray-500 uppercase">
						{{ giveaway.project?.name }}
					</h3>
				</div>
			</div>
		</div>
		<div class="flex mt-3 space-x-3 items-center justify-start">
			<span
				v-if="giveaway.pumps > 0"
				class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
				>PUMPS <span class="text-emerald-500 ml-0.5">{{ giveaway.pumps }}</span>
			</span>
			<span
				v-if="giveaway.contract"
				class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
			>
				BUY
				<span
					v-if="giveaway.contribute > 0"
					class="text-emerald-500 ml-0.5"
					>{{ giveaway.contribute }}$</span
				><span
					v-else
					class="text-emerald-500 ml-0.5"
				>
					ANY</span
				>
			</span>
			<span
				class="text-gray-500 dark:text-gray-400 hover:font-semibold hover:text-gray-700 dark:hover:text-gray-200 border-gray-300 text-xs flex items-center px-1 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
				><span class="hidden sm:flex">EVENT</span>
				<VueIcon
					v-if="
						giveaway.twitter_follow ||
						giveaway.twitter_broadcast ||
						giveaway.twitter_tweet
					"
					v-tippy="$t('Twitter Task')"
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
</template>
