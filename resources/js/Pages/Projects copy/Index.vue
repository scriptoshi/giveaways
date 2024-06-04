<script setup>
import {ref} from "vue";

import {BiWindowDash, HiSolidPlus, HiSolidX} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import DialogModal from "@/Jetstream/DialogModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import FilterRow from "@/Pages/Projects/Index/FilterRow.vue";
import ProjectRow from "@/Pages/Projects/ProjectRow.vue";
defineProps({
	projects: [Array, Object],
});
const showHowItWorks = ref(false);
</script>
<template>
	<AppLayout search-route="projects.index">
		<div class="mt-8 mx-auto container-fluid px-4">
			<FilterRow />
			<div class="mt-4 mb-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0">
				<div
					v-if="!projects?.data?.length"
					class="h-64 p-8 w-full border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-sm flex items-center justify-center"
				>
					<div class="flex items-center flex-col">
						<VueIcon
							:icon="BiWindowDash"
							class="w-16 h-16 mb-6 text-gray-300 dark:text-gray-600"
						/>
						<h3 class="text-gray-400 dark:text-gray-500 text-sm">
							{{ $t("We could not find any projects that match your search") }}
						</h3>
						<p class="text-gray-400 dark:text-gray-500 text-sm">
							{{ $t("You can reset your search or add the project to our system") }}
						</p>
						<div
							class="flex flex-col mt-8 space-y-2 sm:space-y-0 sm:flex-row sm:space-x-2"
						>
							<PrimaryButton secondary>
								<VueIcon
									:icon="HiSolidX"
									class="-ml-1 mr-2 w-4 h-4"
								/>
								{{ $t("Reset Search") }}
							</PrimaryButton>
							<PrimaryButton secondary>
								<VueIcon
									:icon="HiSolidPlus"
									class="-ml-1 mr-2 w-4 h-4"
								/>
								{{ $t("Add Project") }}
							</PrimaryButton>
							<PrimaryButton secondary>
								<VueIcon
									:icon="HiSolidPlus"
									class="-ml-1 mr-2 w-4 h-4"
								/>
								{{ $t("Add Influencer") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
				<div
					v-else
					class="grid w-full gap-1"
				>
					<ProjectRow
						v-for="project in projects?.data ?? []"
						:key="project.id"
						:project="project"
						@show-howit-works="showHowItWorks = true"
					/>
				</div>
			</div>
		</div>
		<DialogModal
			:show="showHowItWorks"
			@close="showHowItWorks = false"
			closeable
		>
			<template #title> How it works </template>
			<template #content>
				<ul class="list-disc">
					<li>
						Prize fairlaunch: You Earn prizes daily, NO QUEST, NO QUEUE, simple Follow,
						Vote & Pump
					</li>
					<li>You support the project by following, voting, pumping and contributing.</li>
					<li>
						Project Team will distribute prize via sleep certified influencers on
						twitter.
					</li>
					<li>Sleep Team will give Launch fees and all voting rewards as prize</li>
					<li>
						Sleep Team will give 5% of collected sales as prize. So make sure to follow
						sleep finance.
					</li>
					<li>
						When you Contributed, Withdraw is after 10 Days. So early contribute, Early
						withdraw.
					</li>
					<li>When contribute, your voterID get 10 Pumps. Pump to claim the Pumps.</li>
					<li>
						First Five pump Leaderboard always get guaranteed prize from sleepfinance
						team
					</li>
					<li>If love project, PUMP pump PUMP contribute PUMP Pump everyday.</li>
					<p class="font-semibold">Tokenomics:</p>
					<li>
						85% LP, 10% Team, 5% Prize Rewards. ALL LP TOKEN is BURNED. so no rug here.
					</li>
				</ul>
			</template>
		</DialogModal>
	</AppLayout>
</template>
