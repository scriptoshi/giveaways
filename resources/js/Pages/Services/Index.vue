<script setup>
import {computed, ref} from "vue";

import {router, useForm} from "@inertiajs/vue3";
import {debouncedWatch, useUrlSearchParams} from "@vueuse/core";
import upperFirst from "lodash/upperFirst";
import {HiSearch, HiSolidArrowUp, HiSolidCubeTransparent} from "oh-vue-icons/icons";
import TextClamp from "vue3-text-clamp";

import FormInput from "@/Components/FormInput.vue";
import Multiselect from "@/Components/Multiselect/Multiselect.vue";
import PaginationSquare from "@/Components/PaginationSquare.vue";
import VueIcon from "@/Components/VueIcon.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import Layout from "@/Layouts/AppLayout.vue";
import PopularEvents from "@/Pages/Services/Index/PopularEvents.vue";
import ServiceCard from "@/Pages/Services/Index/ServiceCard.vue";
const props = defineProps({
	services: Object,
	popular: Array,
	categories: Array,
	tags: Object,
});
const params = useUrlSearchParams("history");
debouncedWatch(
	[
		() => params.search,
		() => params.by,
		() => params.order,
		() => params.show,
		() => params.cat,
		() => params.tag,
	],
	([searchBy, orderBy, orderDir, showMine, showCat, showTag]) => {
		const search = searchBy === "" ? null : searchBy;
		const show = showMine === "" ? null : showMine;
		const by = orderBy === "" ? null : orderBy;
		const cat = showCat === "" ? null : showCat;
		const tag = cat === null ? null : showTag === "" ? null : showTag;
		const order = orderDir === "" || orderDir === "latest" ? null : orderDir;
		router.visit(window.route("services.index", {search, by, order, show, tag, cat}), {
			preserveScroll: true,
		});
	},
	{debounce: 500},
);
const setLatest = () => {
	params.by = !params.order ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = null;
};
const setPrice = () => {
	params.by = params.order === "price" ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = "price";
};

const setRatings = () => {
	params.by = params.order === "ratings" ? (params.by === "oldest" ? null : "oldest") : null;
	params.order = "ratings";
};

const setMine = () => {
	params.show = params.show === "mine" ? null : "mine";
};
const order = computed(() => params.order);
const show = computed(() => params.show);
const serviceBeingDeleted = ref(null);
const deleteServiceForm = useForm({});
const deleteService = () => {
	deleteServiceForm.delete(
		window.route("services.destroy", {ad: serviceBeingDeleted.value.uuid}),
		{
			preserveScroll: true,
			preserveState: true,
			onSuccess: () => (serviceBeingDeleted.value = null),
		},
	);
};
const liveTags = computed(() => (params.cat ? props.tags[params.cat] ?? [] : []));
</script>

<template>
	<Layout>
		<main class="w-full relative container">
			<div
				class="grid relative grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6"
			>
				<div class="lg:col-span-2">
					<div class="flex flex-col lg:flex-row lg:justify-between gap-3 mt-8 mb-5">
						<div class="max-w-xs w-full">
							<Multiselect
								v-model="params.cat"
								:options="categories"
								:searchable="false"
								:close-on-select="true"
								:show-labels="false"
								placeholder="Categories"
								class="sm"
							>
								<template #option="{option}">
									<div>
										<div>{{ upperFirst(option.value) }}</div>
										<div class="text-xs">{{ option.label }}</div>
									</div>
								</template>
							</Multiselect>
						</div>
						<div class="flex items-center space-x-2">
							<a
								v-for="tag in liveTags"
								:key="tag"
								class="text-xs font-semibold hover:underline"
								:class="{'text-emerald-500': params.tag === tag}"
								href="#"
								@click.prevent="params.tag = params.tag === tag ? '' : tag"
								>[ {{ upperFirst(tag) }} ]</a
							>
						</div>
					</div>

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
						<div class="flex items-center lg:justify-end space-x-2">
							<a
								v-if="$page.props.AuthCheck"
								class="text-xs font-semibold hover:underline"
								:class="{'text-sky-500': show === 'mine'}"
								href="#"
								@click.prevent="setMine"
								>[ Mine ]</a
							>
							<a
								class="text-xs font-semibold hover:underline"
								:class="{'text-emerald-500': !order}"
								href="#"
								@click.prevent="setLatest"
								>[ Latest
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && !order"
									class="w-3 h-3"
								/>
								]</a
							>
							<a
								class="text-xs font-semibold hover:underline"
								href="#"
								:class="{'text-emerald-500': order === 'price'}"
								@click.prevent="setPrice"
								>[ Price
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && order === 'price'"
									class="w-3 h-3"
								/>]</a
							>

							<a
								class="text-xs font-semibold hover:underline"
								:class="{'text-emerald-500': order === 'ratings'}"
								@click.prevent="setRatings"
								href="#"
								>[ Rating
								<VueIcon
									:icon="HiSolidArrowUp"
									v-if="params.by === 'oldest' && order === 'ratings'"
									class="w-3 h-3"
								/>
								]</a
							>
						</div>
					</div>
					<div
						v-if="services.data?.length"
						class="grid gap-y-3 w-full"
					>
						<ServiceCard
							v-for="service in services.data"
							:key="service.id"
							:service="service"
							@destroy="serviceBeingDeleted = service"
						/>
						<PaginationSquare :meta="services.meta" />
					</div>
					<div
						v-else
						class="flex shadow-sm items-center border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-md justify-center w-full h-64"
					>
						<div class="flex flex-col items-center">
							<VueIcon
								class="w-12 h-12 text-gray-400"
								:icon="HiSolidCubeTransparent"
							/>
							<div class="text-gray-400 text-2xl font-semibold">
								{{ $t("No Services found") }}
							</div>
							<p class="mt-5">
								If you need
								<strong class="text-emerald-500">to host your service</strong>
							</p>
							<p class="mt-1">
								Please contact our
								<a
									target="_blank"
									class="text-sky-600 dark:text-sky-500 hover:text-sky-800 dark:hover:text-sky-400"
									href="https://t.me/betnfinance"
									>Telegram</a
								>
								for more information
							</p>
						</div>
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
				<p class="font-semibold mt-3">{{ serviceBeingDeleted.title }}</p>
				<p>
					<TextClamp
						:text="serviceBeingDeleted.description"
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
