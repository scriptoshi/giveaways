<script setup>
import {computed} from "vue";

import Verified from "@/Components/FilterNav/svg/verified.vue";
const props = defineProps({
	src: {
		type: String,
		default:
			"https://s3.ap-northeast-1.amazonaws.com/quest3.xyz/community/714666054149394512.jpeg",
	},
	verified: Boolean,
	sm: Boolean,
});
const icon = computed(() => (props.sm ? "w-5 h-5 -right-1 " : "w-7 h-7 -right-3"));
</script>
<template>
	<div
		:class="size"
		class="relative rounded-full"
	>
		<div
			class="rounded-full border border-gray-500 overflow-hidden flex justify-center items-center"
		>
			<img
				class="block object-cover bg-white"
				:class="size"
				:src="src"
			/>
		</div>
		<Verified
			v-if="verified"
			v-tippy="$t('Verified by Gas Team')"
			:class="icon"
			class="absolute bg-white border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-full bottom-0"
		/>
	</div>
</template>
