<script setup>
import {useForm} from "@inertiajs/vue3";

import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import ConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import DangerButton from "@/Jetstream/DangerButton.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";

const props = defineProps({
	project: Object,
});
const emit = defineEmits(["close"]);

const form = useForm({
	delist: props.project.inactive,
	remove: false,
});
const deleteproject = async () => {
	form.delete(window.route("admin.projects.destroy", {project: props.project.uuid}), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => emit("close"),
	});
};
</script>
<template>
	<ConfirmationModal
		:show="true"
		@close="$emit('close')"
	>
		<template #title>
			{{ $t("Delete/Delist project") }}
		</template>

		<template #content>
			<div class="w-full grid mt-8 gap-3">
				<Switch v-model="form.delist">
					<div>
						<div>{{ $t("Delist the project") }}</div>
						<small>
							{{ $t("Delisting will simple disable the project listing") }}</small
						>
					</div>
				</Switch>
				<Switch v-model="form.remove">
					<div>
						<div>{{ $t("Delete the Project") }}</div>
						<small> {{ $t("Deleting will remove the project from the site") }}</small>
					</div>
				</Switch>
				<p>{{ $t("Are you sure you would like to proceed?") }}</p>
			</div>
		</template>
		<template #footer>
			<SecondaryButton @click="$emit('close')">
				{{ $t("Cancel") }}
			</SecondaryButton>

			<DangerButton
				class="ml-2"
				@click="deleteproject"
				:class="{'opacity-25': form.processing}"
				:disabled="form.processing"
			>
				<Loading
					class="mr-2 -ml-1 inline-flex"
					v-if="form.processing"
				/>
				{{ $t("Proceed") }}
			</DangerButton>
		</template>
	</ConfirmationModal>
</template>
