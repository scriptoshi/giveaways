<script setup>
import {useForm} from "@inertiajs/vue3";

import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import Loading from "@/Components/Loading.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
const props = defineProps({
	service: Object,
	message: Object,
});
const emit = defineEmits(["done"]);
const form = useForm({
	message: props.message?.message ?? "",
});

const message = () => {
	form.post(
		window.route("services.messages.save", {
			service: props.service.uuid,
			action: "create",
		}),
		{
			preserveScroll: true,
			onSuccess() {
				form.reset();
				emit("done");
			},
		},
	);
};
</script>
<template>
	<div>
		<div class="w-full max-w-lg grid">
			<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{
				$t("Comment")
			}}</label>
			<ProjectDescriptionTextArea
				v-model="form.message"
				:rows="2"
				:placeholder="$t('Enter your comment')"
			>
				<template #clear>
					<ConnectWalletButton class="!px-3 !py-1 !text-sm !leading-4 !font-medium">
						<button
							type="button"
							@click.prevent="message"
							class="ease-in-out duration-150 inline-flex items-center px-4 py-1 border border-transparent text-sm leading-4 font-medium rounded-sm shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
						>
							<Loading
								v-if="form.processing"
								class="text-white !w-4 !h-4 mr-2 -ml-1"
							/>
							Save
						</button>
					</ConnectWalletButton>
				</template>
			</ProjectDescriptionTextArea>
			<p
				v-if="form.errors?.message"
				class="text-red"
			>
				{{ form.errors?.message }}.
			</p>
			<p v-else>{{ $t("DO NOT share BOOST IDs here. you will be blocked!.") }}</p>
		</div>
	</div>
</template>
