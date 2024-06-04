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
		<div
			v-tippy="$t('Vote to win')"
			class="text-xs flex items-center px-1 bg-white dark:bg-gray-900 border-green-500 text-green-500 border rounded-[2px] uppercase font-semibold"
		>
			{{ $t("VOTE") }}
		</div>
		<div
			v-tippy="$t('Pump to win')"
			class="text-xs px-1 flex items-center border-amber-500 text-amber-500 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
		>
			{{ $t("PUMP") }}
		</div>
		<div
			v-tippy="$t('Buy to Win')"
			class="text-xs flex items-center px-1 border-sky-500 text-sky-500 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
		>
			{{ $t("BUY") }}
		</div>
		<div
			v-tippy="$t('Follow to Win')"
			class="text-xs flex items-center px-1 border-gray-500 text-gray-500 bg-white dark:bg-gray-900 border rounded-[2px] uppercase font-semibold"
		>
			{{ $t("FOLLOW") }}
		</div>
	</div>
</template>
