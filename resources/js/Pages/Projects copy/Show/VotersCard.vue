<script setup>
import {ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
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
	code: "",
});
watch(address, (address) => {
	if (address) form.address = address;
});
const voting = ref(false);
const pumping = ref(false);
const boosting = ref(false);
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

const boost = () => {
	boosting.value = true;
	form.post(window.route("votes.boost"), {
		preserveScroll: true,
		onSuccess() {
			boosting.value = false;
		},
	});
};
</script>
<template>
	<div
		class="flex items-center justify-between border rounded-sm border-gray-200 dark:border-gray-600 p-5"
	>
		<div>
			<h3 class="text-center text-3xl">{{ project.totalVotes }}</h3>
			<p class="text-center">Votes</p>
		</div>
		<div>
			<div class="w-full flex items-center justify-end">
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
					primary
				>
					<Loading
						v-if="voting && form.processing"
						class="mr-2 -ml-1 !text-white"
					/>
					{{ $t("VOTE") }}
				</PrimaryButton>
			</div>
			<p
				v-if="project.voted"
				class="text-center text-green-600 text-xs mt-3"
			>
				{{ $t("You already voted") }}
			</p>
			<p
				v-else
				class="text-center text-xs mt-3"
			>
				{{ $t("You can only vote once") }}
			</p>
		</div>
	</div>
	<div
		class="mt-3 flex items-center justify-between border rounded-sm border-gray-200 dark:border-gray-600 p-5"
	>
		<div>
			<h3 class="text-center text-3xl">{{ project.totalPumps ?? 0 }}</h3>
			<p class="text-center">Pumps</p>
		</div>
		<div>
			<div class="w-full flex items-center justify-end">
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
			<p class="text-center text-xs mt-3">
				{{ $t("Once per hour") }}
			</p>
		</div>
	</div>
	<div class="my-5 flex items-start">
		<FormInput
			size="xs"
			v-model="form.code"
			:error="form.errors.code"
			placeholder="Enter VOTEID or BOOST CODE"
			class="w-full mr-3"
		/>
		<div class="flex items-center justify-end">
			<PrimaryButton
				@click.prevent="boost"
				secondary
				class="py-0.5"
			>
				<Loading
					v-if="boosting && form.processing"
					class="mr-2 -ml-1 !text-white"
				/>
				{{ $t("BOOST") }}
			</PrimaryButton>
		</div>
	</div>
</template>
