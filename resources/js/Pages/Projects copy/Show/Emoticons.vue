<script setup>
import {useForm} from "@inertiajs/vue3";

import Loading from "@/Components/Loading.vue";
import {useBillions} from "@/hooks/useBillions";
import EmojiButton from "@/Pages/Projects/Index/EmojiButton.vue";
const props = defineProps({
	project: Object,
});

const form = useForm({
	uuid: props.project.uuid,
	type: "project",
	reaction: null,
});
const vote = (type) => {
	form.reaction = type;
	form.post(window.route("votes.react"), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div class="grid grid-cols-5 mt-5 gap-4">
		<div
			v-for="emoji in [
				{type: 'sleep', action: 'sleeped', tip: 'Sweet Slumber'},
				{type: 'moon', action: 'mooned', tip: 'Big Dreams'},
				{type: 'love', action: 'loved', tip: 'Lullaby'},
				{type: 'bad', action: 'baded', tip: 'Insomniac'},
				{type: 'poo', action: 'pooed', tip: 'Nightmare'},
			]"
			:key="emoji.type"
			class="flex flex-col items-center"
		>
			<Loading
				class="w-8 h-8"
				v-if="form.processing && form.reaction == emoji.type"
			/>
			<EmojiButton
				class="w-8 h-8 animate-shake"
				v-else
				:type="emoji.type"
				v-tippy="emoji.tip"
				@click="vote(emoji.type)"
			/>
			<h3
				:class="{
					'px-1 bg-green-500 text-white': project.reactioned && project[emoji.action],
				}"
				class="text-xs font-semibold mt-0.5"
			>
				{{ useBillions(project[emoji.type] ?? 0) }}
			</h3>
		</div>
	</div>
</template>
