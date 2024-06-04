<script setup>
import {useAccount} from "use-wagmi";

import {useAuth} from "@/Wagmi/hooks/authentication";
import {useWagmiModalToggle} from "@/Wagmi/hooks/useWagmiModalToggle";

const {toggle} = useWagmiModalToggle();
const {isDisconnected} = useAccount();
const {SignIn} = useAuth();
defineEmits(["clicked"]);
</script>
<template>
	<a
		href="#"
		type="button"
		@click.prevent="$emit('clicked')"
		v-bind="$attrs"
		v-if="$page.props.AuthCheck"
	>
		<slot></slot>
	</a>
	<template v-else>
		<a
			href="#"
			type="button"
			v-bind="$attrs"
			@click.prevent="toggle"
			v-if="isDisconnected"
		>
			<slot></slot>
		</a>
		<a
			href="#"
			v-bind="$attrs"
			@click.prevent="SignIn"
			v-else
		>
			<slot></slot>
		</a>
	</template>
</template>
