<script setup>
import {ref, watch} from "vue";

import {useForm} from "@inertiajs/vue3";
import {useAccount} from "use-wagmi";

import Loading from "@/Components/Loading.vue";

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

const pumping = ref(false);
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
	<a
		href="#"
		@click.prevent="pump"
		class="border text-center group transition-colors duration-200 border-gray-200 dark:border-gray-600 px-2.5 rounded-[3px]"
	>
		<span
			class="flex items-center justify-center my-1.5 w-full"
			v-if="pumping"
		>
			<Loading class="!m" />
		</span>

		<h3
			v-else
			class="group-hover:text-emerald-500"
		>
			{{ project.totalPumps ?? 0 }}
		</h3>
		<small class="text-xs group-hover:text-emerald-500">PUMPS</small>
	</a>
</template>
