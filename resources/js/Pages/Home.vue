<script setup>
import {BiWindowDash, HiSolidPlus, HiSolidX} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProjectRow from "@/Pages/Projects/ProjectRow.vue";
defineProps({
	projects: Array,
});
</script>
<template>
	<AppLayout>
		<div class="mt-8 mx-auto container">
			<div class="mt-4 mb-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0">
				<div
					v-if="projects?.data?.length ?? 0 === 0"
					class="h-64 p-8 w-full border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-sm flex items-center justify-center"
				>
					<div class="flex items-center flex-col">
						<VueIcon
							:icon="BiWindowDash"
							class="w-16 h-16 mb-6 text-gray-300 dark:text-gray-600"
						/>
						<h3 class="text-gray-400 dark:text-gray-500 text-sm">
							{{ $t("We could not find any GA-s that match your search") }}
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
								{{ $t("Add Give away") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
				<div
					v-else
					class="grid 2xl:grid-cols-4 xl:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-4"
				>
					<ProjectRow
						v-for="project in projects?.data ?? []"
						:key="project.id"
						:project="project"
					/>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
