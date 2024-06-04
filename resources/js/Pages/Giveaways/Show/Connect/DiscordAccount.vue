<script setup>
import {useForm} from "@inertiajs/vue3";
import {BiDiscord} from "oh-vue-icons/icons";

import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
import providers from "@/Pages/Projects/Show/Connect/providers";

const props = defineProps({
	action: Object,
});
const href = window.route("airdrops.show", {
	chainId: props.airdrop.chainId,
	airdrop: props.airdrop.contract,
	action: props.action.id,
	admin: false,
});
const form = useForm({
	redirect: href,
});
const connect = () => {
	form.post(window.route("connections.connect", providers.DISCORD));
};
</script>
<template>
	<button
		type="button"
		class="absolute top-0 w-full h-full left-0 backdrop-blur-sm bg-white/60 dark:bg-gray-900/30 rounded-md"
	>
		<div class="flex w-full justify-center space-x-1 flex-row mb-3">
			<VueIcon
				:icon="BiDiscord"
				class="text-[#4285F4] w-12 h-12 group-hover:text-[#3b77d8] dark:group-hover:text-gray-400"
			/>
		</div>
		<h2 class="text-gray-800 dark:text-gray-100 font-semibold text-center">
			{{ $t("Connect your Discord account") }}
		</h2>
		<h2 class="text-gray-800 dark:text-gray-100 font-semibold text-center">
			{{ $t("to claim rewards") }}
		</h2>
		<button
			@click="connect"
			rel="noopener nofollow noreferrer"
			class="text-white bg-[#4285F4] hover:bg-[#4285F4]/90 focus:ring-4 focus:outline-none focus:ring-[#4285F4]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#4285F4]/55 mr-2 mb-2"
		>
			<Loading
				class="!w-4 !h-4 !mr-2 !-ml-1"
				v-if="form.processing"
			/>
			<OhVueIcon
				name="bi-discord"
				v-else
				class="w-4 h-4 mr-2 -ml-1"
			/>
			{{ $t("Connect discord account") }}
		</button>
	</button>
</template>
