<script setup>
import {inject, ref, watch} from "vue";

import {HiSolidPlus} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";

const params = inject("filter");
const mine = ref(null);
watch(mine, (mine) => {
	params.q = mine ? "mine" : null;
});

const filter = (find) => (params.q = params?.q === find ? null : find);
</script>
<template>
	<div class="grid grid-cols-2">
		<div class="flex -mt-3 space-x-3">
			<PrimaryButton
				secondary=""
				link
				:href="route('projects.create', {type: 'token'})"
				class="py-1 px-2 !text-xs"
			>
				<VueIcon
					class="!w-4 !h-4 mr-1 -ml-1"
					:icon="HiSolidPlus"
				/>

				SUBMIT</PrimaryButton
			>
			<PrimaryButton
				secondary
				link
				:href="route('projects.deploy')"
				class="py-1 px-2 !text-xs !text-emerald-500"
			>
				<VueIcon
					class="!w-4 !h-4 mr-1 -ml-1"
					:icon="HiSolidPlus"
				/>

				LAUNCH</PrimaryButton
			>
		</div>
		<div class="flex -mt-3 space-x-3 justify-end">
			<PrimaryButton
				:secondary="params?.q != 'top'"
				:primary="params?.q == 'top'"
				@click.prevent="filter('top')"
				class="py-1 px-2 !text-xs"
			>
				TOP</PrimaryButton
			>
			<PrimaryButton
				:secondary="params?.q != 'sleep'"
				:primary="params?.q == 'sleep'"
				@click.prevent="filter('sleep')"
				class="py-1 px-2 !text-xs"
			>
				PUMPS</PrimaryButton
			>
			<PrimaryButton
				:secondary="params?.q != 'votes'"
				:primary="params?.q == 'votes'"
				@click.prevent="filter('votes')"
				class="py-1 px-2 !text-xs"
			>
				VOTE</PrimaryButton
			>
			<Switch v-model="mine">Mine</Switch>
		</div>
	</div>
</template>
