<script setup>
import {computed, watch} from "vue";

import {useForm} from "@inertiajs/vue3";

import AvatarUpload from "@/Components/AvatarUpload.vue";
import AvatarUploadLocal from "@/Components/AvatarUploadLocal.vue";
import WeCopy from "@/Components/WeCopy.vue";
import {useBillions} from "@/hooks/useBillions";
import {useAddress} from "@/Wagmi/hooks/useTxHash";

const props = defineProps({
	token: Object,
});
const form = useForm({
	logo_uri: props.token.logo_uri,
});
const logoUri = computed(() => form.logo_uri);
watch(
	logoUri,
	(uri) => {
		if (uri) {
			form.clearErrors();
			form.put(window.route("tokens.update.logo", props.token.id));
		}
	},
	{deep: true},
);
const [shortTx, txLink] = useAddress(props.token.contract);
const billion = useBillions(props.token.total_supply);
</script>
<template>
	<div class="col-span-12 lg:col-span-8">
		<div class="mt-3 flex flex-col justify-between px-4 sm:flex-row sm:items-center sm:px-5">
			<div class="flex flex-1 items-center justify-between space-x-2 sm:flex-initial">
				<h2 class="text-sm+ font-medium tracking-wide text-slate-700 dark:text-navy-100">
					{{ token.name }} | {{ token.contract_name }}
				</h2>
			</div>
		</div>
		<div
			class="mt-4 grid grid-cols-2 gap-3 px-4 sm:mt-5 sm:grid-cols-4 sm:gap-5 sm:px-5 lg:mt-6"
		>
			<div class="flex col-span-2 flex-col">
				<div class="flex">
					<AvatarUpload
						v-if="$page.props.config.s3"
						:disk="$page.props.config.uploadsDisk"
						v-model="form.logo_uri"
					/>
					<AvatarUploadLocal
						v-else
						v-model="form.logo_uri"
						:preview="token.logo_uri"
						:busy="form.processing"
					/>

					<div class="">
						<WeCopy
							class="flex-row-reverse"
							:text="token.contract"
						>
							<p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
								{{ shortTx }}
							</p>
						</WeCopy>
						<a
							:href="txLink"
							target="_blank"
							class="border-b border-dotted border-current pb-0.5 text-tiny font-medium uppercase text-gray-700 outline-none transition-colors duration-300 hover:text-primary/70 focus:text-primary/70 dark:text-accent-light dark:hover:text-accent-light/70 dark:focus:text-accent-light/70"
							>View on Etherscan</a
						>
					</div>
				</div>
			</div>
			<div class="rounded-sm bg-gray-100 p-4 dark:bg-gray-900">
				<div class="flex justify-between">
					<p class="text-xl font-semibold text-gray-800 dark:text-gray-200">
						{{ token.symbol }}
					</p>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						class="h-5 w-5 text-success"
						fill="none"
						viewBox="0 0 24 24"
						stroke="currentColor"
						stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
						></path>
					</svg>
				</div>
				<p class="mt-1 text-xs">{{ $t("Token Symbol") }}</p>
			</div>
			<div class="rounded-sm bg-gray-100 p-4 dark:bg-gray-900">
				<div class="flex justify-between">
					<p class="text-xl font-semibold text-gray-700 dark:text-gray-200">
						{{ billion }}
					</p>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						class="h-5 w-5 text-warning"
						fill="none"
						viewBox="0 0 24 24"
						stroke="currentColor"
						stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
						></path>
					</svg>
				</div>
				<p class="mt-1 text-xs">{{ $t("Total Supply") }}</p>
			</div>
		</div>
	</div>
</template>
