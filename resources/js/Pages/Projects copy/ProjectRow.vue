<script setup>
import {computed} from "vue";

import {Link, usePage} from "@inertiajs/vue3";
import {BiChatDots, BiTelegram} from "oh-vue-icons/icons";

import Verified from "@/Components/FilterNav/svg/verified.vue";
import ProjectBadge from "@/Components/ProjectBadge.vue";
import VueIcon from "@/Components/VueIcon.vue";
import chains from "@/Pages/Projects/Create/ChainSelect/chains.json";
import VerifiedLogo from "@/Pages/Projects/Index/VerifiedLogo.vue";
import VoteButton from "@/Pages/Projects/Index/VoteButton.vue";
import ActionBadges from "@/Pages/Projects/Show/ActionBadges.vue";
import Emoticons from "@/Pages/Projects/Show/Emoticons.vue";
import KycBadges from "@/Pages/Projects/Show/KycBadges.vue";
import PumpButton from "@/Pages/Projects/Show/PumpButton.vue";
import VoteButtonBlock from "@/Pages/Projects/Show/VoteButtonBlock.vue";
const props = defineProps({
	project: Object,
});
defineEmits(["showHowitWorks"]);
const chainIds = computed(() => usePage().props.chainIds);
const chain = computed(() =>
	chains.find((c) => {
		const indx = chainIds.value[parseInt(props.project.chainId)];
		if (indx) return c.id === indx.toLowerCase();
		return c.id === props.project.chainId.toLowerCase();
	}),
);
</script>
<template>
	<div
		:class="
			project.contract
				? 'border-emerald-300 bg-emerald-100 dark:border-emerald-700'
				: 'border-gray-200 dark:border-gray-700'
		"
		class="bg-white dark:bg-gray-800 border"
	>
		<div
			v-if="project.contract"
			class="w-full px-3 pt-3 mb-5 sm:mb-0 gap-3 flex flex-col sm:flex-row items-start justify-between"
		>
			<Link
				:href="route('projects.show', {project: project.slug})"
				class="flex-1 hover:underline"
			>
				<p>
					<strong class="font-semibold text-base mr-2">{{ project.name }}:</strong
					>{{ project.description }}
				</p>
			</Link>
			<div class="flex mt-1 items-center space-x-2">
				<a
					class="text-xs font-semibold hover:underline"
					href="#"
					>[<VueIcon
						class="w-4 h-4 mr-0.5"
						:icon="BiChatDots"
					/>
					53 Resplies]</a
				>
				<a
					class="text-xs font-semibold hover:underline"
					href="#"
					@click="$emit('showHowitWorks')"
					>[How it works]</a
				>
			</div>
		</div>
		<div
			:class="{'!pt-0': project.contract}"
			class="w-full p-3 grid gap-5 sm:gap-3 sm:grid-cols-2 md:grid-cols-3"
		>
			<div class="flex items-center space-x-3">
				<VerifiedLogo
					sm
					:chain-src="chain?.logo"
					:src="project.logo?.url"
					:name="project.name"
					:chain="chain?.name"
				/>
				<div>
					<a
						:href="route('projects.show', {project: project.slug})"
						class="flex hover:underline underline-offset-1 items-center space-x-3"
					>
						<template v-if="project.contract">
							<h3 class="text-lg">2000$ + 5% + [4.3M PEPE] 10 DAYS</h3>
						</template>
						<template v-else>
							<h3 class="text-lg">{{ project.name }}</h3>
							<div class="text-emerald-500">{{ project.symbol }}</div>
						</template>
					</a>
					<div class="flex mt-1 items-center space-x-3">
						<div class="flex items-center space-x-1">
							<Verified
								v-tippy="$t('Verified by Sleep Team')"
								class="w-5 h-5 cursor-pointer"
							/>
							<div class="flex items-center space-x-1">
								<VueIcon
									class="text-blue-500"
									:icon="BiTelegram"
								/>
							</div>
							<template
								v-for="badge in project?.badges"
								:key="badge.id"
							>
								<ProjectBadge
									v-tippy="badge.description"
									v-bind="{[badge.badge]: true}"
								/>
							</template>
							<ActionBadges
								v-if="project.contract"
								:project="project"
							/>
							<KycBadges
								v-else
								:project="project"
							/>
						</div>
					</div>
				</div>
			</div>
			<Emoticons :project="project" />
			<div class="flex items-center justify-between">
				<div class="flex items-center space-x-1">
					<VoteButtonBlock :project="project" />
					<PumpButton :project="project" />
				</div>
				<VoteButton :project="project" />
			</div>
		</div>
	</div>
</template>
