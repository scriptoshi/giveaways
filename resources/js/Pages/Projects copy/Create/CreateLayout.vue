<script setup>
import {breakpointsTailwind, useBreakpoints} from "@vueuse/core";
import {useI18n} from "vue-i18n";

import Steps from "@/Components/Steps/Steps.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
	current: Number,
	errors: Object,
});
const breakpoints = useBreakpoints(breakpointsTailwind);
const {t} = useI18n();
const steps = [
	{
		title: t(t("Token Information")),
		description: t("Provide and Verify your Token"),
		current: 1,
	},
	{
		title: t("Save Token Information"),
		description: t("Project information and links"),
		current: 2,
	},
];
const isLg = breakpoints.greater("sm");
</script>
<template>
	<AppLayout>
		<div class="h-full max-h-full">
			<div class="container-fluid sm:container">
				<div class="hero-content container">
					<div class="intro mb-8">
						<h1
							class="mt-4 mb-2 !text-emerald-700 dark:!text-emerald-400 font-normal text-4xl"
						>
							{{ $t("Submit Crypto Coin / Token") }}
						</h1>
					</div>
				</div>
				<div class="flex flex-row container mx-auto">
					<div class="card border dark:border-gray-600 border-gray-300 px-0 pb-8">
						<div class="grid mt-8 gap-y-8 p-4 md:grid-cols-9">
							<div class="md:col-span-3 md:ml-3">
								<Steps
									:items="steps"
									:current="current"
									:vertical="isLg"
									:errors="errors"
								/>
								<slot name="navigation" />
							</div>
							<slot />
						</div>
					</div>
				</div>
				<slot name="footnote" />
			</div>
		</div>
	</AppLayout>
</template>
