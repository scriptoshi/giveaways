<script setup>
import DeployFactory from "@/Factories/DeployFactory.vue";
import DeployLaunchpadFactory from "@/Factories/DeployLaunchpadFactory.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
defineProps({
	foundryInfo: Object,
	name: String,
	isLaunchPad: Boolean,
	locks: Array,
});
</script>
<template>
	<AdminLayout>
		<main class="min-h-screen">
			<div
				class="relative h-full flex container mx-auto flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8"
			>
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center mt-6 mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="text-lg text-emerald-600">Deploy {{ name }} Factory</h3>
							<p v-if="name === 'Fair launch'">
								The Fair launch contract supports fair Liqiudity
							</p>
						</div>
					</div>
					<div class="h-full border-0">
						<div class="px-0 card-gutterless h-full">
							<DeployLaunchpadFactory
								v-if="isLaunchPad"
								:locks="locks"
								:name="name"
								:foundryInfo="foundryInfo"
							/>
							<DeployFactory
								v-else
								:locks="locks"
								:name="name"
								:foundryInfo="foundryInfo"
							/>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
