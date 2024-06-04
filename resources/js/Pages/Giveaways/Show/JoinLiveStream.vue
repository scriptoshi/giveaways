<script setup>
import {MdLivetvOutlined} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ExternalLink from "@/Components/ExternalLink.vue";
import VueIcon from "@/Components/VueIcon.vue";
defineProps({
	giveaway: Object,
});
</script>
<template>
	<div
		class="flex justify-between items-center p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex items-center">
			<VueIcon
				class="w-10 h-10 mr-4 text-emerald-500"
				:icon="MdLivetvOutlined"
			/>
			<h3 class="text-sm">Join {{ giveaway.project.name }} live stream</h3>
		</div>
		<ExternalLink :href="giveaway.live_stream">
			<PrimaryButton
				secondary
				class="!py-1 !px-2"
				>[ Go live ]
			</PrimaryButton></ExternalLink
		>
	</div>
</template>
