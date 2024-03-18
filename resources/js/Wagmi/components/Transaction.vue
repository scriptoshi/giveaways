<script setup>
import {computed} from "vue";

import {CheckIcon, PlusIcon, XMarkIcon} from "@heroicons/vue/24/solid";
import {UseTimeAgo} from "@vueuse/components";
import {DateTime} from "luxon";

import Loading from "@/Components/Loading.vue";
import {useTxHash} from "@/Wagmi/hooks/useTxHash";

const props = defineProps({
	tx: Object,
});

const [shortTx, etherScanLink] = useTxHash(props.tx.hash, undefined, 8);
const time = computed(() => DateTime.fromMillis(parseInt(props.tx.addedTime)).toJSDate());
</script>
<template>
	<div className="relative hover:opacity-80">
		<a
			target="_blank"
			:href="etherScanLink"
			className="!no-underline"
		>
			<div class="pr-4 relative cursor-pointer flex items-center gap-5 rounded-2xl px-4 py-3">
				<div
					className=" bg-gray-100 rounded-full h-[36px] w-[36px] flex justify-center items-center"
				>
					<CheckIcon
						v-if="tx.success || !!tx.receipt"
						class="w-5 h-5 text-emerald-500"
					/>

					<XMarkIcon
						v-else-if="tx.error"
						class="text-red-500 dark:text-red-400 w-5 h-5"
					/>
					<Loading
						class="!text-emerald-500 !m-0"
						v-else-if="tx.loading"
					/>
					<PlusIcon
						v-else
						class="w-5 h-5"
					/>
				</div>
				<div className="flex flex-col gap-0.5">
					<div className="flex items-center gap-2">
						<span
							class="text-sm font-medium items-center whitespace-normal text-gray-600"
						>
							{{ tx.summary ? tx.summary : shortTx }}
						</span>
					</div>
					<div class="flex items-center space-x-3">
						<div>{{ shortTx }}</div>
						<UseTimeAgo
							:time="time"
							v-slot="{timeAgo}"
						>
							<div class="text-xs text-slate-500">
								{{ timeAgo }}
							</div>
						</UseTimeAgo>
					</div>
				</div>
			</div>
		</a>
	</div>
</template>
