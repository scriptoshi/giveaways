<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CrossChainToken from "@/Pages/Tokens/Show/CrossChainToken.vue";
import StandardToken from "@/Pages/Tokens/Show/StandardToken.vue";
import TaxToken from "@/Pages/Tokens/Show/TaxToken.vue";
defineProps({
	token: Object,
});
</script>
<template>
	<AppLayout>
		<div class="mx-auto w-full container px-4">
			<TaxToken
				v-if="token.isTax"
				:token="token"
			/>
			<StandardToken
				v-if="token.isStandard"
				:token="token"
			/>
			<CrossChainToken
				v-if="token.isCrossChain"
				:token="token"
			/>
		</div>
	</AppLayout>
</template>
