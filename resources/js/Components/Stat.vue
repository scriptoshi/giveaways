<script setup>
import {computed} from "vue";
const props = defineProps({
	title: {
		type: String,
		required: true,
	},
	home: {
		type: [Number, String],
		required: true,
	},
	away: {
		type: [Number, String],
		required: true,
	},
	active: {
		type: Boolean,
		default: false,
	},
});
const homePercentage = computed(() => {
	return (parseFloat(props.home) / (parseFloat(props.home) + parseFloat(props.away))) * 100;
});
const awayPercentage = computed(() => {
	return (parseFloat(props.away) / (parseFloat(props.home) + parseFloat(props.away))) * 100;
});
</script>
<template>
	<div class="w-full">
		<div class="flex justify-center">
			<p class="text-xs uppercase font-bold">{{ title }}</p>
		</div>
		<div class="flex items-center">
			<div class="text-xs mr-3 uppercase font-bold">
				{{ home }}
			</div>
			<div
				class="w-full progress reversed flex justify-end bg-gray-200 rounded-l-full h-1.5 dark:bg-gray-700"
			>
				<div
					:class="{'is-active': active}"
					class="bg-sky-500 h-1.5 rounded-l-full dark:bg-sky-400"
					:style="{width: homePercentage + '%'}"
				></div>
			</div>
			<div class="w-full progress flex bg-gray-200 rounded-r-full h-1.5 dark:bg-gray-700">
				<div
					:class="{'is-active': active}"
					class="bg-green-500 h-1.5 rounded-r-full dark:bg-green-400"
					:style="{width: awayPercentage + '%'}"
				></div>
			</div>
			<div class="text-xs ml-3 uppercase font-bold">
				{{ away }}
			</div>
		</div>
	</div>
</template>
