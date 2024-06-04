<script setup>
import {ref} from "vue";

import {Link, router, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import {upperFirst} from "lodash";
import {GiDiamonds, HiSearch, HiSolidStar} from "oh-vue-icons/icons";
import {uid} from "uid";
import TextClamp from "vue3-text-clamp";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import FormInput from "@/Components/FormInput.vue";
import VueIcon from "@/Components/VueIcon.vue";
import {useBillions} from "@/hooks/useBillions";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import Layout from "@/Layouts/AppLayout.vue";
import PopularEvents from "@/Pages/Services/Index/PopularEvents.vue";
import ComposeMessages from "@/Pages/Services/Show/ComposeMessages.vue";
import MessageCard from "@/Pages/Services/Show/MessageCard.vue";
import {shortenAddress} from "@/Wagmi/utils/utils";

const props = defineProps({
	service: Object,
	popular: Array,
	categories: Array,
	tags: Object,
});
const params = useUrlSearchParams("history");
debouncedWatch(
	[() => params.search],
	([searchBy]) => {
		const search = searchBy === "" ? null : searchBy;

		router.visit(window.route("services.index", {search}), {
			preserveScroll: true,
		});
	},
	{debounce: 500},
);
const serviceBeingDeleted = ref(false);
const deleteServiceForm = useForm({});
const deleteService = () => {
	deleteServiceForm.delete(window.route("services.destroy", {ad: props.service.uuid}), {
		preserveScroll: true,
		preserveState: true,
	});
};
const messagesBeingEdited = ref();
const adminMessages = ref(false);
const done = () => {
	messagesBeingEdited.value = null;
	adminMessages.value = false;
};
const uuid = uid();
</script>

<template>
	<Layout white>
		<main class="w-full relative container">
			<div
				class="grid relative grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6"
			>
				<div class="lg:col-span-2 mt-8">
					<div class="grid sm:grid-cols-2 gap-3 w-full mb-4 lg:mb-2 -mt-2">
						<FormInput
							class="max-w-xs"
							input-classes="!pl-7"
							placeholder="project name"
							v-model="params.search"
							size="xs"
						>
							<template #lead>
								<VueIcon
									:icon="HiSearch"
									class="w-4 h-4"
								/>
							</template>
						</FormInput>
						<div
							v-if="service.isOwner"
							class="flex items-center lg:justify-end space-x-2"
						>
							<Link
								class="text-xs font-semibold hover:underline"
								:href="route('services.edit', {ad: service.uuid})"
							>
								[ Edit ]</Link
							>
							<a
								class="text-xs text-red-500 z-2 font-semibold hover:underline"
								href="#"
								@click.prevent="serviceBeingDeleted = true"
								>[ Delete ]</a
							>
						</div>
					</div>
					<div class="grid gap-y-3 w-full">
						<h3 class="my-5">{{ service.title }}</h3>
						<div class="flex items-center space-x-3">
							<img
								class="w-16 h-16 rounded-full border dark:border-gray-600"
								:src="service.user.profile_photo_url"
							/>
							<div>
								<div class="flex items-center">
									<h3 class="text-base">{{ upperFirst(service.user.name) }}</h3>
									<h3 class="text-base mx-4 text-gray-400 dark:text-gray-600">
										|
									</h3>
									<div class="flex-shrink-0">
										<div
											v-if="service.isPartner"
											class="flex items-center gap-1 text-xs font-semibold text-white bg-blue-800 py-0.5 px-2 rounded-md"
										>
											<div>Verified</div>
											<div class="text-green-500">Partner</div>
										</div>
										<div
											v-else-if="service.isTopRated"
											class="flex items-center gap-1 text-xs font-semibold text-amber-900 bg-amber-400/50 py-0.5 px-2 rounded-md"
										>
											<div>Top Rated</div>
											<div class="flex items-center">
												<VueIcon
													class="w-4 h-4"
													:icon="GiDiamonds"
												/><VueIcon
													class="w-4 h-4"
													:icon="GiDiamonds"
												/><VueIcon
													class="w-4 h-4"
													:icon="GiDiamonds"
												/>
											</div>
										</div>
									</div>
								</div>
								<h3 class="flex mt-2 items-center text-base">
									<span class="text-xs inline-flex"
										>[
										{{ shortenAddress(service.user.account.address) }} ]</span
									>
									<VueIcon
										class="w-5 h-5"
										:icon="HiSolidStar"
									/>
									<strong class="text-semibold">{{ service.rating }}</strong>
									<span>({{ useBillions(service.comments) }})</span>
								</h3>
							</div>
						</div>
						<div class="mt-6">
							<div
								class="flex justify-between items-center p-3 border dark:border-gray-600"
							>
								<h3 class="text-sm">Contact service provider</h3>
								<div class="flex items-center space-x-3">
									<PrimaryButton
										v-if="service.url"
										:href="service.url"
										url
										external
										secondary
									>
										Website</PrimaryButton
									>
									<PrimaryButton
										url
										external
										:href="service.telegram"
									>
										Telegram</PrimaryButton
									>
								</div>
							</div>
						</div>
						<div class="mt-6">
							<p>{{ service.description }}</p>
						</div>
						<MessageCard
							v-for="update in service.messages"
							class="mt-6"
							:key="update.uuid"
							:message="update"
							:service="service"
							:isAdmin="service.isOwner"
							@edit="messagesBeingEdited = update"
						/>
						<ComposeMessages
							class="mt-6"
							:service="service"
							:message="messagesBeingEdited"
							@done="done"
							:key="messagesBeingEdited?.uuid ?? uuid"
						/>
					</div>
				</div>
				<div class="dark:bg-gray-800 h-[calc(100vh-40px)] bg-white/90 sticky top-16">
					<div
						class="bg-gray-300/40 dark:bg-gray-700/40 text-gray-900 dark:text-white p-3 text-sm font-semibold"
					>
						{{ $t("Popular Services") }}
					</div>
					<PopularEvents :services="popular" />
				</div>
			</div>
		</main>
		<jet-confirmation-modal
			:show="serviceBeingDeleted"
			@close="serviceBeingDeleted = null"
		>
			<template #title>
				{{ $t("Delete Service") }}
			</template>

			<template #content>
				{{ $t("Are you sure you would like to delete this Service?") }}
				<p class="font-semibold mt-3">{{ service.title }}</p>
				<p>
					<TextClamp
						:text="service.description"
						:max-lines="2"
					/>
				</p>
			</template>

			<template #footer>
				<jet-secondary-button @click="serviceBeingDeleted = null">
					{{ $t("Cancel") }}
				</jet-secondary-button>

				<jet-danger-button
					class="ml-2"
					@click="deleteService"
					:class="{'opacity-25': deleteServiceForm.processing}"
					:disabled="deleteServiceForm.processing"
				>
					{{ $t("Delete") }}
				</jet-danger-button>
			</template>
		</jet-confirmation-modal>
	</Layout>
</template>
