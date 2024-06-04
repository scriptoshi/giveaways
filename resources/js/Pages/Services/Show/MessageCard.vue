<script setup>
import {useForm} from "@inertiajs/vue3";

import Loading from "@/Components/Loading.vue";

const props = defineProps({
	service: Object,
	message: Object,
	isAdmin: Boolean,
});
const form = useForm({action: "delete", message: "*****"});
const destroy = () => {
	form.post(
		window.route("services.messages.save", {
			service: props.service.uuid,
			uuid: props.message.uuid,
		}),
		{
			preserveScroll: true,
		},
	);
};
</script>
<template>
	<div class="card tip-details flex flex-col items-start p-0">
		<div
			class="w-full flex mt-3 flex-col items-start pb-4 px-4 md:px-5 border-b border-dashed border-gray-200 dark:border-gray-600"
		>
			<div class="w-full flex justify-between">
				<div class="text-left font-semibold text-base truncate w-full flex items-center">
					<img
						:src="message.user?.profile_photo_url"
						class="w-3 h-3 mr-2 rounded-full border border-gray-300 dark:border-e-gray-600"
					/>{{ message.title }}
				</div>
				<a
					v-if="isAdmin || message.user_id === $page.props.user?.id"
					class="hover:text-emerald-500 flex items-center font-semibold"
					href="#"
					@click.prevent="destroy"
				>
					[{{ $t("Delete")
					}}<Loading
						v-if="form.processing"
						class="mx-2 !w-4 !h-4"
					/>]</a
				>
			</div>

			<div class="text-md mt-1 text-gray-subtitle truncate w-full text-left text-xs">
				<time
					:datetime="message.date"
					class="flex items-center"
				>
					<div class="font-semibold text-gray-400 dark:text-gray-500">
						{{ message.date }}
					</div>
					<div
						class="border text-emerald-600 dark:text-emerald-400 rounded-[3px] border-emerald-600 dark:border-emerald-400 px-2 ml-3"
					>
						{{ message.created_at }}
					</div>
				</time>
			</div>
			<p class="mt-1">{{ message.message }}</p>
		</div>
	</div>
</template>
