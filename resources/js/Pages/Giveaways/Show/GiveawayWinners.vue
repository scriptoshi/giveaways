<script setup>
import {shortenAddress} from "@/Wagmi/utils/utils";
defineProps({
	winners: Array,
	me: Object,
});
</script>
<template>
	<div>
		<div class="flex items-center justify-between">
			<h3 class="text-xl mt-8">{{ $t("Winners") }}</h3>
		</div>
		<div class="grid gap-4 mt-4">
			<div
				v-if="me?.status == 'winner' || me?.status == 'claimed'"
				class="w-full text-emerald-500 flex items-center justify-between font-semibold space-x-3 p-1 rounded-sm bg-gray-100 dark:bg-gray-800"
			>
				<div class="flex items-center space-x-3">
					<img
						class="border border-gray-200 dark:border-gray-600 w-5 h-5 rounded-full"
						:src="me.user.profile_photo_url"
					/>
					<span class="hidden lg:flex">
						{{ shortenAddress(me.user.account.address, 6) }}
					</span>
					<span class="lg:hidden">
						{{ shortenAddress(me.user.account.address, 3) }}
					</span>
				</div>
				<div class="justify-self-end uppercase">
					{{ me.status == "claimed" ? $t("You Claimed") : $t("You won") }}
				</div>
			</div>
			<template
				v-for="quester in winners"
				:key="quester.id"
			>
				<div
					v-if="!quester.isMe"
					class="w-full flex items-center font-semibold space-x-3 p-1 rounded-sm bg-gray-100 dark:bg-gray-800"
				>
					<img
						class="border border-gray-200 dark:border-gray-600 w-5 h-5 rounded-full"
						:src="quester.user.profile_photo_url"
					/>
					<span class="hidden lg:flex">
						{{ shortenAddress(quester.user.account.address, 12) }}
					</span>
					<span class="lg:hidden">
						{{ shortenAddress(quester.user.account.address, 6) }}
					</span>
				</div>
			</template>
		</div>
	</div>
</template>
