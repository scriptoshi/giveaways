<script setup>
import {HiSolidArrowLeft} from "oh-vue-icons/icons";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import ManageFactory from "@/Factories/ManageFactory.vue";
import ManageLaunchpadFacory from "@/Factories/ManageLaunchpadFactory.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";

defineProps({
	factory: Object,
	deployments: Object,
});
</script>
<template>
	<AdminLayout>
		<main class="min-h-screen">
			<div
				class="relative h-full flex container mx-auto flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8"
			>
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex justify-between items-center mt-6 mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="text-xl text-emerald-600 font-[Walsheim-Light]">
								Manage {{ factory.name }} Factory
							</h3>
						</div>
						<PrimaryButton
							secondary
							link
							:href="route('admin.factories.index')"
						>
							<VueIcon
								:icon="HiSolidArrowLeft"
								class="-ml-1 mr-2 w-4 h-4"
							/>
							Go back
						</PrimaryButton>
					</div>
					<div class="h-full border-0">
						<div class="px-0 card-gutterless h-full">
							<ManageLaunchpadFacory
								v-if="factory.isLaunchPad"
								:factory="factory"
							/>
							<ManageFactory
								v-else
								:factory="factory"
							/>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
