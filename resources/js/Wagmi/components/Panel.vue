<script setup>
import {ref} from "vue";

import {PopoverPanel} from "@headlessui/vue";

import Profile from "@/Wagmi/components/Profile.vue";
import Transactions from "@/Wagmi/components/Transactions.vue";
const showTransations = ref(false);
</script>

<template>
	<transition
		enter-active-class="transition duration-100 ease-out"
		enter-from-class="transform scale-95 opacity-0"
		enter-to-class="transform scale-100 opacity-100"
		leave-active-class="transition duration-75 ease-in"
		leave-from-class="transform scale-100 opacity-100"
		leave-to-class="transform scale-95 opacity-0"
	>
		<PopoverPanel
			class="w-full bg-white dark:bg-gray-800 sm:w-[320px] fixed top-[58px] h-screen sm:h-[unset] shadow-md left-0 right-0 sm:absolute sm:bottom-[unset] sm:left-[unset] mt-2 sm:rounded-xl rounded-b-none border-t sm:border border-emerald-500 z-[100]"
		>
			<Transactions
				:showTxs="showTransations"
				v-if="showTransations"
				@hideTransactions="showTransations = false"
			/>
			<Profile
				v-else
				:showTxs="showTransations"
				@showTransactions="showTransations = true"
			>
				<slot />
			</Profile>
		</PopoverPanel>
	</transition>
</template>
