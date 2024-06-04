<script setup>
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ApiTask from "@/Pages/Giveaways/Tasks/ApiTask.vue";
import ContributeCard from "@/Pages/Giveaways/Tasks/ContributeCard.vue";
import JoinDiscord from "@/Pages/Giveaways/Tasks/JoinDiscord.vue";
import JoinTelegramGroup from "@/Pages/Giveaways/Tasks/JoinTelegramGroup.vue";
import NftBalance from "@/Pages/Giveaways/Tasks/NftBalance.vue";
import ReferralUsers from "@/Pages/Giveaways/Tasks/ReferralUsers.vue";
import TokenBalance from "@/Pages/Giveaways/Tasks/TokenBalance.vue";
import TweetTask from "@/Pages/Giveaways/Tasks/TweetTask.vue";
import TwitterFollow from "@/Pages/Giveaways/Tasks/TwitterFollow.vue";

defineProps({
	giveaway: Object,
	twitter: Object,
	telegram: Object,
	discord: Object,
	tweet: Object,
	mint: Object,
	swap: Object,
	contribute: Object,
	launchpad: Object,
	token: Object,
	api: Object,
	nft: Object,
});
</script>
<template>
	<AppLayout>
		<div class="min-h-full w-full bg-white dark:bg-gray-900">
			<div class="container px-4">
				<div class="grid mt-12 sm:grid-cols-2 gap-5">
					<div>
						<h3>Manage Tasks : #{{ giveaway.brief }}</h3>
						<div class="flex mt-4 items-end space-x-2">
							<PrimaryButton
								class="!py-1 !text-xs uppercase"
								secondary
								link
								:href="route('giveaways.edit', {giveaway: giveaway.uuid})"
							>
								Edit Giveaway</PrimaryButton
							>
							<PrimaryButton
								class="!py-1 !text-xs uppercase"
								secondary
								link
								:href="route('projects.edit', {project: giveaway.project.uuid})"
							>
								Edit Project</PrimaryButton
							>
						</div>
					</div>
				</div>

				<div class="mt-12 w-full">
					<div class="max-w-3xl mx-auto grid gap-6">
						<TwitterFollow
							:quest="twitter"
							:giveaway="giveaway"
						/>
						<TweetTask
							:quest="tweet"
							:giveaway="giveaway"
						/>
						<JoinTelegramGroup
							:quest="telegram"
							:giveaway="giveaway"
						/>
						<JoinDiscord
							:quest="discord"
							:giveaway="giveaway"
						/>
						<ContributeCard
							:launchpad="launchpad"
							:quest="contribute"
							:giveaway="giveaway"
						/>

						<TokenBalance
							:quest="token"
							:giveaway="giveaway"
						/>
						<NftBalance
							:quest="nft"
							:giveaway="giveaway"
						/>
						<ApiTask
							:quest="api"
							:giveaway="giveaway"
						/>

						<ReferralUsers :giveaway="giveaway" />
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
