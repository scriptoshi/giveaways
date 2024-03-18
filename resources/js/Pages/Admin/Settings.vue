<script setup>
import {ComputerDesktopIcon} from "@heroicons/vue/24/outline";

import Layout from "@/Layouts/AdminLayout.vue";
import General from "@/Pages/Admin/Settings/General.vue";
</script>
<template>
	<Layout>
		<main class="">
			<div class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-4 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Site Settings</h3>
							<p>Manage Site Environment Settings</p>
						</div>
						<div class="flex flex-col lg:flex-row lg:items-center gap-3">
							<button
								type="button"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-sm text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800"
							>
								<ComputerDesktopIcon class="w-4 h-4 -ml-2 mr-2 inline-block" />
								Enable Maintenance Mode
							</button>
						</div>
					</div>
					<General />
				</div>
			</div>
		</main>
	</Layout>
</template>
