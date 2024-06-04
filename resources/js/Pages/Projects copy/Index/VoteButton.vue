<script setup>
import {ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Loading from "@/Components/Loading.vue";
import WeCopySvg from "@/Components/WeCopySvg.vue";
const props = defineProps({
	project: Object,
});
const {address} = useAccount();
const form = useForm({
	uuid: props.project.uuid,
	type: "project",
	address: address.value,
});
watch(address, (address) => {
	if (address) form.address = address;
});
const voting = ref(false);
const pumping = ref(false);
const vote = () => {
	voting.value = true;
	form.post(window.route("votes.store"), {
		preserveScroll: true,
		onSuccess() {
			voting.value = false;
		},
	});
};

const pump = () => {
	pumping.value = true;
	form.post(window.route("votes.pump"), {
		preserveScroll: true,
		onSuccess() {
			pumping.value = false;
		},
	});
};
</script>
<template>
	<div class="flex items-center space-x-2 justify-end">
		<PrimaryButton
			v-if="project.voted"
			secondary
		>
			<WeCopySvg
				class="-mr-4"
				:text="project.votes?.[0]?.voteId"
				after
				>{{ $t("VOTE ID") }}
			</WeCopySvg>
		</PrimaryButton>
		<PrimaryButton
			v-else
			@click.prevent="vote"
			:primary="!form.recentlySuccessful || $page.props.flash?.error"
			:success="form.recentlySuccessful && !$page.props.flash?.error"
		>
			<Loading
				v-if="form.processing && voting"
				class="mr-2 -ml-1 !text-white"
			/>
			{{
				project.voted
					? $t("VOTED")
					: form.recentlySuccessful && !$page.props.flash?.error
					  ? $t("SAVED")
					  : $t("VOTE")
			}}
		</PrimaryButton>
		<PrimaryButton
			@click.prevent="pump"
			primary
		>
			<Loading
				v-if="pumping && form.processing"
				class="mr-2 -ml-1 !text-white"
			/>
			{{ $t("PUMP") }}
		</PrimaryButton>
	</div>
</template>
