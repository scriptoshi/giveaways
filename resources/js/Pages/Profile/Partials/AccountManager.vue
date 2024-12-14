<script setup>
import {TrashIcon} from "@heroicons/vue/24/solid";
import {useAccount} from "use-wagmi";

import WeCopy from "@/Components/WeCopy.vue";
defineProps({
	account: Object,
});
const {address} = useAccount();
const emit = defineEmits(["remove"]);
const remove = (val) => emit("remove", val);
</script>
<template>
	<tr class="border-y border-transparent border-b-gray-300 dark:border-b-gray-600">
		<td class="whitespace-nowrap p-2 sm:px-6">
			{{ account.created_at }}
		</td>
		<td class="whitespace-nowrap p-2 sm:px-6">
			{{ account.provider ?? "Web3" }}
		</td>
		<td class="whitespace-nowrap p-2 sm:px-6">
			<div class="w-full">
				<WeCopy :text="account.address">
					<p
						:class="address !== account.address ? 'text-success' : 'text-gray-500'"
						class="font-medium"
					>
						{{ account.address }}
					</p>
				</WeCopy>
			</div>
		</td>
		<td class="flex justify-between items-center p-2 sm:px-6 text-right">
			<button
				as="button"
				type="button"
				@click.prevent="remove(account)"
				:disabled="account.is_primary || address == account.address"
				class="btn py-1 border border-gray-300 font-medium text-gray-800 hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-100/80 dark:border-gray-450 dark:text-gray-50 dark:hover:bg-gray-500 dark:focus:bg-gray-500 dark:active:bg-gray-500/90"
			>
				<TrashIcon class="h-5 w-5 mr-2 -ml-1" /> {{ $t("Disconnect") }}
			</button>
		</td>
	</tr>
</template>
