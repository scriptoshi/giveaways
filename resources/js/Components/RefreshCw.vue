<script setup>
import {HiRefresh} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
defineProps({
	spin: {type: Boolean, default: false},
	pulse: {type: Boolean, default: false},
});
</script>
<template>
	<VueIcon
		:icon="HiRefresh"
		:class="{[$style.spin]: spin, [$style.pulse]: pulse}"
	/>
</template>
<style module>
.spin {
	animation: zb-spin 2s infinite linear;
}

.pulse {
	animation: zb-spin 1s infinite steps(8);
}

@keyframes zb-spin {
	0% {
		transform: rotate(0deg);
	}

	100% {
		transform: rotate(360deg);
	}
}
</style>
