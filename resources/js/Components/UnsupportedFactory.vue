<script setup>
import {useChain} from "@/Wagmi/hooks/useChain";

const {chain} = useChain();
</script>
<template>
	<div>
		<div
			class="p-4 mb-4 text-sm border border-red-200 dark:border-red-600/30 text-red-800 rounded-sm bg-red-50 dark:bg-red-800/10 dark:text-red-400"
			role="alert"
		>
			<span class="font-semibold uppercase">Deployment is offline!</span>
			{{ chain?.name ?? "your current chain" }} is not yet supported
		</div>
	</div>
</template>
