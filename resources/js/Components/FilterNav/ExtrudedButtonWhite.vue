<script setup>
import {computed} from "vue";

const props = defineProps({
	bg: [Array, String],
	btn: [Array, String],
	icon: Boolean,
	disabled: Boolean,
	sm: Boolean,
});
const btnclass = computed(() => {
	if (props.sm) return "px-1 py-0.5";
	if (props.icon) return "p-1.5";
	return "px-4 py-1.5";
});
</script>
<template>
	<div
		:class="disabled ? 'pointer-events-none cursor-not-allowed' : ''"
		class="pt-1 pr-0 pb-0 group"
	>
		<div class="relative z-0 cursor-pointer">
			<div
				:class="btn"
				class="group-hover:left-0 group-hover:top-0 text-emerald-600 bg-white rounded-sm -left-1 -top-1 focus-visible:ring-0 flex items-center border border-emerald-600 z-2 overflow-hidden transition-corner ease-in-out duration-100 relative"
			>
				<div
					:class="btnclass"
					class="flex w-full items-center justify-center text-base"
				>
					<slot />
				</div>
			</div>
			<span
				:class="bg"
				class="bg-white border-b-2 border-r-2 border-emerald-600 rounded-[0.75rem] box-border w-full h-full absolute left-0 top-0 transition-opacity duration-500 opacity-100 group-hover:opacity-50"
			></span>
		</div>
	</div>
</template>
