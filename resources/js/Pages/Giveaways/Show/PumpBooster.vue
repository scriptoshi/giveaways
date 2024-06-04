<script setup>
import {useForm} from "@inertiajs/vue3";
import {HiSolidCheck, MdOfflineboltOutlined} from "oh-vue-icons/icons";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import VueIcon from "@/Components/VueIcon.vue";

const props = defineProps({
	quester: Object,
});

const form = useForm({
	boostId: "",
});

const {t} = useI18n();
const pump = () => {
	form.clearErrors();
	if (!props.quester?.id) form.setError("code", t("Complete tasks first"));
	if (form.hasErrors) return;
	form.post(window.route("questers.boost", {quester: props.quester?.id}), {
		preserveScroll: true,
	});
};
</script>
<template>
	<div
		class="flex justify-between space-x-3 items-start p-2 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border rounded-sm border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
	>
		<div class="flex flex-1">
			<VueIcon
				class="w-6 h-6 mr-3 text-gray-500"
				:icon="MdOfflineboltOutlined"
				:class="{'animate-shake': form.recentlySuccessful}"
			/>
			<FormInput
				size="xs"
				placeholder="Enter Boost ID"
				class="w-full"
				v-model="form.boostId"
				:error="form.errors.boostId"
			/>
		</div>

		<PrimaryButton
			secondary
			@click.prevent="pump"
			:class="{'!text-emerald-500': form.recentlySuccessful}"
			class="!py-1 !px-2 !text-xs uppercase"
			><Loading
				v-if="form.processing"
				class="mr-2 -ml-1 !w-4 !h-4"
			/>
			<VueIcon
				:icon="HiSolidCheck"
				v-if="form.recentlySuccessful"
				class="mr-2 -ml-1 !w-4 !h-4"
			/>BOOST
		</PrimaryButton>
	</div>
</template>
