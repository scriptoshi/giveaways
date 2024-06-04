<script setup>
import {MdSupervisedusercircleOutlined} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import WeCopySvg from "@/Components/WeCopySvg.vue";
defineProps({
	giveaway: Object,
	quester: Object,
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
				<h3
					v-if="quester?.qid"
					class="text-sm group"
				>
					<WeCopySvg
						after
						:text="quester?.qid"
						>Refer {{ giveaway.referrals ?? 1 }} users to boost with your
						<span class="group-hover:underline">BOOST ID</span>
					</WeCopySvg>
				</h3>
				<h3
					class="text-sm"
					v-else
				>
					Refer {{ giveaway.referrals ?? 1 }} users to boost with your BOOST ID
				</h3>
				<p class="text-sms">Earn 200 SLEEP each when someone uses your BOOST ID</p>
			</div>
		</div>

		<PrimaryButton
			secondary
			v-if="quester?.qid"
			class="!py-1 !pr-2 pl-4"
			><WeCopySvg
				after
				class="-mr-2"
				:text="quester?.qid"
				>BOOST ID</WeCopySvg
			>
		</PrimaryButton>
	</div>
</template>
