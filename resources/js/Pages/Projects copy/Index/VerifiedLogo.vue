<script setup>
import {computed} from "vue";

const props = defineProps({
	src: String,
	chainSrc: String,
	chain: String,
	name: String,
	sm: Boolean,
});
const size = computed(() =>
	props.sm ? "!w-[40px] !h-[40px] !rounded-sm" : "!w-[72px] !h-[72px] !rounded-sm",
);
const icon = computed(() => (props.sm ? "!w-5 !h-5 -right-1 " : "w-7 h-7 -right-3"));
</script>
<template>
	<div
		:class="size"
		class="relative rounded-2xl"
	>
		<div
			class="rounded-full border border-emerald-500 overflow-hidden flex justify-center items-center"
		>
			<img
				class="block object-cover bg-white"
				:class="size"
				:src="src"
				:title="name"
				:alt="name"
			/>
		</div>
		<img
			:class="icon"
			class="absolute cursor-pointer w-7 h-7 bg-white border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded-full bottom-0"
			:src="chainSrc"
			v-tippy="chain"
			:alt="chain"
		/>
	</div>
</template>
