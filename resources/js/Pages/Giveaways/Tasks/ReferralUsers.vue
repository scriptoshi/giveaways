<script setup>
import {MdSupervisedusercircleOutlined} from "oh-vue-icons/icons";

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
				:icon="MdSupervisedusercircleOutlined"
			/>
			<div>
				<h3 class="text-sm">
					Refer users to boost with with BOOST ID
					<span class="bg-emerald-500 text-xs text-white px-3 py-0.5 ml-2 rounded-xl">{{
						$t("On")
					}}</span>
				</h3>
				<p class="text-xs">
					Referral task is enabled by default. Both the referred and referrer earn 200
					SLEEP
				</p>
			</div>
		</div>
	</div>
</template>
