<script setup>
import {computed, reactive, ref} from "vue";

import {useForm} from "@inertiajs/vue3";
import {UseTimeAgo} from "@vueuse/components";
import upperFirst from "lodash/upperFirst";
import {DateTime} from "luxon";
import {AiCeur} from "oh-vue-icons/icons";
import {uid} from "uid";
import {DatePicker} from "v-calendar";
import {useI18n} from "vue-i18n";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import RadioCards from "@/Components/RadioCards.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TopUp from "@/Pages/Giveaways/Edit/TopUp.vue";
const props = defineProps({
	hasProject: Boolean,
	prizeClaim: Object,
	usdtContracts: Object,
	giveaway: {
		type: Object,
		default: () => ({}),
	},
});

const form = useForm({
	starts_at: DateTime.now().plus({hours: 4}).toJSDate(),
	duration: "12",
	period: "hours",
	type: "draw",
	brief: "Gas takeover 2024",
	...props.giveaway,
});

const routed = computed(() => window.route("giveaways.update", {giveaway: props.giveaway.uuid}));
const {t} = useI18n();
const draft = async () => {
	form.clearErrors();
	if (form.brief === "") form.setError("brief", t("Giveaway brief is required"));
	if (!form.duration || parseInt(form.duration) < 1)
		form.setError("duration", t("Giveaway duration is required"));
	if (form.hasErrors) return;
	form.put(routed.value, {
		preserveScroll: true,
	});
};

const togglePeriod = (d) => {
	if (form.period === "days") form.period = "hours";
	else form.period = "days";
};

const winOptions = reactive([
	{
		key: uid(),
		value: "draw",
		title: "Random Draw",
		subtitle: "Select Winner randomly from participants",
	},
	{
		key: uid(),
		value: "leaderboard",
		title: "GAS Leaderboard",
		subtitle: "Select the users who claimed the most GAS token",
	},
	{
		key: uid(),
		value: "draw_leaderboard",
		title: "Draw Leaderboard",
		subtitle: "Draw from 100 users who claimed the most GAS",
	},
	{
		key: uid(),
		value: "fcfs",
		title: "First come , First serve",
		subtitle: "First to complete tasks will get prize",
	},
]);
const stopGiveAway = ref(false);
const endform = useForm({
	live: false,
});
const endGiveWay = () => {
	endform.post(window.route("giveaways.stop", {giveaway: props.giveaway.uuid}));
};
const top = ref(false);
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div class="card mt-12 p-8 border dark:border-gray-600 border-gray-300">
					<div class="grid mx-auto max-w-4xl">
						<div class="mb-8">
							<h3>{{ $t("Update Giveaway") }} #{{ giveaway.brief }}</h3>
							<p
								class="text-red-500"
								v-if="giveaway.hasStarted"
							>
								{{
									$t(
										"This G.A has started. We only accept few changes to running Giveways!",
									)
								}}
							</p>
							<div class="flex mt-4 items-end space-x-2">
								<PrimaryButton
									class="!py-1 !text-xs uppercase"
									secondary
									link
									:href="route('giveaways.tasks', {giveaway: giveaway.uuid})"
								>
									Edit Tasks</PrimaryButton
								>
								<PrimaryButton
									class="!py-1 !text-xs uppercase"
									secondary
									link
									:href="route('projects.edit', {project: giveaway.project.uuid})"
								>
									Edit Project</PrimaryButton
								>
								<PrimaryButton
									class="!py-1 !text-xs uppercase"
									secondary
									link
									:href="route('giveaways.show', {giveaway: giveaway.slug})"
								>
									View</PrimaryButton
								>
							</div>
						</div>

						<div class="w-full mt-2 mb-6 grid sm:grid-cols-5 gap-4">
							<FormInput
								:label="$t('Giveaway Brief ')"
								v-model="form.brief"
								class="col-span-3"
								:error="form.errors.brief"
								:placeholder="$t('Giveaways Finance Takeover 2024')"
							/>
							<div>
								<label
									for="name"
									class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-300"
									>{{ $t("Start Time") }}</label
								>
								<DatePicker
									v-model="form.starts_at"
									mode="dateTime"
									:disabled="giveaway?.live"
									is24hr
								>
									<template v-slot="{inputValue, inputEvents}">
										<input
											class="bg-white border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white border block w-full focus:outline-none focus:ring-1 appearance-none py-2 text-sm pl-2 pr-2"
											:value="inputValue"
											v-on="inputEvents"
										/>
									</template>
								</DatePicker>
								<p
									v-if="form.errors.starts_at"
									class="text-red-500"
								>
									{{ form.errors.starts_at }}
								</p>
								<UseTimeAgo
									v-else
									v-slot="{timeAgo}"
									:time="form.starts_at"
								>
									<p class="text-sx font-semibold text-emerald-500">
										{{ timeAgo }}
									</p>
								</UseTimeAgo>
							</div>
							<FormInput
								:label="$t('Duration')"
								v-model="form.duration"
								:disabled="giveaway?.live"
								:placeholder="$t('24')"
								:error="form.errors.duration"
							>
								<template #trail>
									<a
										href="#"
										class="hover:text-emerald-500 px-1 text-xs font-semibold border border-gray-300 dark:border-gray-600 rounded-sm"
										@click.prevent="togglePeriod"
										>{{ upperFirst(form.period) }}</a
									>
								</template>
								<template #help>
									<div
										class="flex mt-1 text-xs font-semibold items-start space-x-1"
									>
										<a
											v-for="item in ['hours', 'days', 'weeks']"
											:key="item"
											:class="
												form.period == item
													? 'text-emerald-500 dark:text-emerald-400'
													: ''
											"
											class="px-1 bg-white hover:bg-gray-100 dark:hover:text-white dark:bg-gray-900 border rounded-sm border-gray-300 dark:border-gray-600"
											href="#"
											@click.prevent="form.period = item"
										>
											{{ item }}
										</a>
									</div>
								</template>
							</FormInput>
						</div>

						<div>
							<div class="grid w-full p-5 my-6 border dark:border-gray-700">
								<div>
									<h3 class="text-base text-emerald-500 mb-2">
										<VueIcon :icon="AiCeur" /> {{ $t("Prize Details") }}
									</h3>
									<p>{{ $t("Customize winner selection") }}</p>
								</div>
								<RadioCards
									v-model="form.type"
									:options="winOptions"
									:disabled="giveaway?.live"
									:grid="2"
								/>
							</div>
						</div>
						<CollapseTransition>
							<TopUp
								v-show="top"
								:prize-claim="prizeClaim"
								:giveaway="giveaway"
								:usdtContracts="usdtContracts"
							/>
						</CollapseTransition>
						<CollapseTransition>
							<div
								v-show="top"
								class="mt-3 w-full flex space-x-3 items-center justify-end"
							>
								<Switch v-model="top"> Adjust Prize</Switch>
							</div>
						</CollapseTransition>
						<CollapseTransition>
							<div
								v-show="!top"
								class="mt-3 w-full flex space-x-3 items-center justify-end"
							>
								<Switch v-model="top"> Adjust Prize</Switch>
								<PrimaryButton
									@click.prevent="stopGiveAway = true"
									error
								>
									STOP GIVEAWAY
								</PrimaryButton>
								<PrimaryButton
									@click.prevent="draft"
									secondary
									:disabled="form.processing"
								>
									<Loading
										class="mr-2 -ml-1"
										v-if="form.processing"
									/>
									{{ $t("Update Giveaway") }}
								</PrimaryButton>
							</div>
						</CollapseTransition>
					</div>
				</div>
			</div>
		</div>
		<JetConfirmationModal
			:show="stopGiveAway"
			@close="stopGiveAway = false"
		>
			<template #title>
				{{ $t("Stop Give Away") }}
			</template>
			<template #content>
				<p>
					{{
						$t(
							"This will end the giveaway quests and force select winners. If no winners can be derived using selected methods, random Draw will be used.",
						)
					}}
				</p>
				<p class="mt-3">
					{{
						$t(
							"PLEASE NOTE: To avoid sending members on a goose chase, giveways cannot be refunded",
						)
					}}
				</p>
			</template>
			<template #footer>
				<JetSecondaryButton
					:class="{'opacity-60 pointer-events-none': endform.processing}"
					:disabled="endform.processing"
					@click="stopGiveAway = false"
				>
					{{ $t("Cancel") }}
				</JetSecondaryButton>
				<JetDangerButton
					class="ml-2"
					@click="endGiveWay()"
					:class="{'opacity-60 pointer-events-none': endform.processing}"
					:disabled="endform.processing"
				>
					<Loading
						v-if="endform.processing"
						class="mr-2 -ml-1 inline-block w-5 h-5"
					/>
					{{ endform.processing ? $t("Working...") : $t("Stop Giveaway") }}
				</JetDangerButton>
			</template>
		</JetConfirmationModal>
	</AppLayout>
</template>
