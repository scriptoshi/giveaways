<script setup>
import {computed} from "vue";

import ConnectWalletLink from "@/Components/ConnectWalletLink.vue";
import bad from "@/Pages/Projects/emojis/bad.png?url";
import blank from "@/Pages/Projects/emojis/blank.png?url";
import moon from "@/Pages/Projects/emojis/great.png?url";
import love from "@/Pages/Projects/emojis/love.png?url";
import poo from "@/Pages/Projects/emojis/poo.png?url";
import sleep from "@/Pages/Projects/emojis/sleep.png?url";
const props = defineProps({
	type: String,
});
const emoji = computed(() => {
	switch (props.type) {
		case "sleep":
			return sleep;
		case "moon":
			return moon;
		case "love":
			return love;
		case "bad":
			return bad;
		case "poo":
			return poo;
		default:
			return blank;
	}
});
</script>
<script>
export default {inheritAttrs: false};
</script>
<template>
	<ConnectWalletLink class="cursor-pointer">
		<img
			:src="emoji"
			v-bind="$attrs"
			class="transition duration-200 hover:-translate-y-1"
		/>
	</ConnectWalletLink>
</template>
