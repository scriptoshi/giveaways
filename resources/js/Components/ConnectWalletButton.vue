<script setup>
import {WalletIcon} from "@heroicons/vue/24/outline";
import {useAccount} from "use-wagmi";

import {useAuth} from "@/Wagmi/hooks/authentication";
import {useWagmiModalToggle} from "@/Wagmi/hooks/useWagmiModalToggle";

const {toggle} = useWagmiModalToggle();
const {isDisconnected} = useAccount();
const {SignIn} = useAuth();
</script>
<template>
	<slot v-if="$page.props.AuthCheck"></slot>
	<template v-else>
		<button
			type="button"
			@click="toggle"
			v-if="isDisconnected"
			v-bind="$attrs"
			class="btn disabled:pointer-events-none disabled:bg-emerald-50/50 disabled:dark:bg-gray-800/50 bg-white dark:bg-gray-800 hover:bg-emerald-300/20 hover:dark:bg-emerald-900/20 rounded-sm py-3 border border-emerald-200 dark:border-emerald-500 transition duration-200 text-emerald-700 dark:text-emerald-400 font-semibold"
		>
			<WalletIcon class="w-5 h-5 -ml-1 mr-2 inline-block" />{{
				$t("Connect Wallet To Continue")
			}}
		</button>
		<button
			type="button"
			@click="SignIn"
			v-else
			v-bind="$attrs"
			class="btn disabled:pointer-events-none disabled:bg-emerald-50/50 disabled:dark:bg-gray-800/50 bg-white dark:bg-gray-800 hover:bg-emerald-300/20 hover:dark:bg-emerald-900/20 rounded-sm py-3 border border-emerald-200 dark:border-emerald-500 transition duration-200 text-emerald-700 dark:text-emerald-400 font-semibold"
		>
			<WalletIcon class="w-5 h-5 -ml-1 mr-2 inline-block" />{{ $t("Login To Continue") }}
		</button>
	</template>
</template>
