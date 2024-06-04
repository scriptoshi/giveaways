<script setup>
import {useForm} from "@inertiajs/vue3";

import ConnectWalletButton from "@/Components/ConnectWalletButton.vue";
import Loading from "@/Components/Loading.vue";
import ProjectDescriptionTextArea from "@/Components/ProjectDescriptionTextArea.vue";
const props = defineProps({
	project: Object,
	update: Object,
});
const emit = defineEmits(["done"]);
const form = useForm({
	message: props.update?.message ?? "",
});

const update = () => {
	form.post(
		window.route("projects.updates.save", {
			project: props.project.uuid,
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
		<div class="w-full grid">
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
							@click.prevent="update"
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
			<p v-else>{{ $t("DO NOT share BOOST CODE or VOTE ID here. you will be blocked!.") }}</p>
		</div>
	</div>
</template>
