<script setup>
import {watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {BiArrowUpCircle} from "oh-vue-icons/icons";
import {useAccount} from "use-wagmi";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";
const props = defineProps({
	project: Object,
	giveaway: Object,
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

const pump = () => {
	form.post(window.route("votes.pump"), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div
		class="flex justify-between items-center p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex">
			<VueIcon
				class="w-10 h-10 mr-4 text-gray-500"
				:icon="BiArrowUpCircle"
			/>
			<div>
				<h3
					:class="{'text-green-500': $page.props.vote?.pump > giveaway.pumps}"
					class="text-sm"
				>
					Pump to claim 100 GAS
				</h3>
				<p>You can pump once every hour</p>
			</div>
		</div>

		<PrimaryButton
			primary
			@click.prevent="pump"
			class="!py-1 !px-2"
			><Loading
				v-if="form.processing"
				class="mr-2 -ml-1 !text-white"
			/>[ PUMP ]
		</PrimaryButton>
	</div>
</template>
