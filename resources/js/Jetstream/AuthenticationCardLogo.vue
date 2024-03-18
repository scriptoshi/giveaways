<script setup>
import {Link} from "@inertiajs/vue3";

import SiteLogo from "@/Components/SiteLogo.vue";
</script>
<template>
	<Link :href="'/'">
		<SiteLogo class="w-28" />
	</Link>
</template>
