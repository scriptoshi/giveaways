<script setup>
import {Link, useForm} from "@inertiajs/vue3";
import {useI18n} from "vue-i18n";

import LoadingButton from "@/Components/LoadingButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const {t: $t} = useI18n();
defineProps({
	title: {required: false, type: String},
});
const form = useForm({
	avatar: "",
	username: "",
	address: "",
	rank: "",
	points: "",
});
const save = () => form.post(window.route("posts.create"));
</script>
<template>
	<Head :title="title ?? `New Leaderboard`" />
	<AdminLayout>
		<template #header>
			<template v-if="title">{{ title }}</template>
			<template v-else>{{ $t("Create New") }} Leaderboard</template>
		</template>
		<div class="bg-white">
			<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
				<div class="max-w-3xl mx-auto">
					<form
						class="space-y-8 divide-y divide-gray-200"
						@submit.prevent="save()"
					>
						<div class="sm:col-span-6">
							<label
								for="avatar"
								:class="form.error.avatar ? 'text-gray-700' : ' text-red-700'"
								class="block text-sm font-medium"
							>
								{{ __("leaderboards.avatar") }}</label
							>
							<div class="mt-1">
								<input
									id="avatar"
									v-model="form.avatar"
									type="text"
									:class="
										form.error.avatar
											? 'bg-red-50 text-red-900 border-red-500 placeholder-red-700 focus:ring-red-500 focus:border-red-500'
											: 'focus:ring-amber-500 focus:border-amber-500 border-gray-300'
									"
									class="border shadow-sm block w-full sm:text-sm rounded-md"
									required
								/>
							</div>
							<p
								v-if="form.error.avatar"
								class="mt-1 text-sm text-red-600 dark:text-red-500"
							>
								<span class="font-medium">Oops!</span> {{ form.error.avatar }}
							</p>
						</div>
						<div class="sm:col-span-6">
							<label
								for="username"
								:class="form.error.username ? 'text-gray-700' : ' text-red-700'"
								class="block text-sm font-medium"
							>
								{{ __("leaderboards.username") }}</label
							>
							<div class="mt-1">
								<input
									id="username"
									v-model="form.username"
									type="text"
									:class="
										form.error.username
											? 'bg-red-50 text-red-900 border-red-500 placeholder-red-700 focus:ring-red-500 focus:border-red-500'
											: 'focus:ring-amber-500 focus:border-amber-500 border-gray-300'
									"
									class="border shadow-sm block w-full sm:text-sm rounded-md"
									required
								/>
							</div>
							<p
								v-if="form.error.username"
								class="mt-1 text-sm text-red-600 dark:text-red-500"
							>
								<span class="font-medium">Oops!</span> {{ form.error.username }}
							</p>
						</div>
						<div class="sm:col-span-6">
							<label
								for="address"
								:class="form.error.address ? 'text-gray-700' : ' text-red-700'"
								class="block text-sm font-medium"
							>
								{{ __("leaderboards.address") }}</label
							>
							<div class="mt-1">
								<input
									id="address"
									v-model="form.address"
									type="text"
									:class="
										form.error.address
											? 'bg-red-50 text-red-900 border-red-500 placeholder-red-700 focus:ring-red-500 focus:border-red-500'
											: 'focus:ring-amber-500 focus:border-amber-500 border-gray-300'
									"
									class="border shadow-sm block w-full sm:text-sm rounded-md"
									required
								/>
							</div>
							<p
								v-if="form.error.address"
								class="mt-1 text-sm text-red-600 dark:text-red-500"
							>
								<span class="font-medium">Oops!</span> {{ form.error.address }}
							</p>
						</div>
						<div class="sm:col-span-6">
							<label
								for="rank"
								:class="form.error.rank ? 'text-gray-700' : ' text-red-700'"
								class="block text-sm font-medium"
							>
								{{ __("leaderboards.rank") }}</label
							>
							<div class="mt-1">
								<input
									id="rank"
									v-model="form.rank"
									type="number"
									:class="
										form.error.rank
											? 'bg-red-50 text-red-900 border-red-500 placeholder-red-700 focus:ring-red-500 focus:border-red-500'
											: 'focus:ring-amber-500 focus:border-amber-500 border-gray-300'
									"
									class="border shadow-sm block w-full sm:text-sm rounded-md"
									required
								/>
							</div>
							<p
								v-if="form.error.rank"
								class="mt-1 text-sm text-red-600 dark:text-red-500"
							>
								<span class="font-medium">Oops!</span> {{ form.error.rank }}
							</p>
						</div>
						<div class="sm:col-span-6">
							<label
								for="points"
								:class="form.error.points ? 'text-gray-700' : ' text-red-700'"
								class="block text-sm font-medium"
							>
								{{ __("leaderboards.points") }}</label
							>
							<div class="mt-1">
								<input
									id="points"
									v-model="form.points"
									type="number"
									:class="
										form.error.points
											? 'bg-red-50 text-red-900 border-red-500 placeholder-red-700 focus:ring-red-500 focus:border-red-500'
											: 'focus:ring-amber-500 focus:border-amber-500 border-gray-300'
									"
									class="border shadow-sm block w-full sm:text-sm rounded-md"
									required
								/>
							</div>
							<p
								v-if="form.error.points"
								class="mt-1 text-sm text-red-600 dark:text-red-500"
							>
								<span class="font-medium">Oops!</span> {{ form.error.points }}
							</p>
						</div>
						<div class="pt-5">
							<div class="flex justify-end">
								<Link
									as="button"
									:href="route('admin.leaderboards.index')"
									type="button"
									class="bg-white py-2 px-4 border border-gray-300 rounded-sm shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
								>
									{{ $t("Cancel") }}
								</Link>
								<LoadingButton
									type="submit"
									:busy="form.processing"
									class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
								>
									{{ $t("Update") }} Leaderboard
								</LoadingButton>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</AdminLayout>
</template>
