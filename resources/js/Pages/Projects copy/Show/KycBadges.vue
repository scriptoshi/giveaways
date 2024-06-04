<script setup>
import {BiEye} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";

defineProps({
	project: Object,
});
</script>
<template>
	<div class="flex items-center space-x-1">
		<span
			v-tippy="$t('Unique Views')"
			class="text-xs px-1 bg-white dark:bg-gray-900 border border-gray-300 text-gray-500 rounded-[2px] uppercase font-semibold"
		>
			<VueIcon
				class="w-4 h-4"
				:icon="BiEye"
			/>
			{{ project.views ?? 0 }}
		</span>
	</div>
</template>
