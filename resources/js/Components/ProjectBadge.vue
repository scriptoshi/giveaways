<script setup>
import {computed} from "vue";

const props = defineProps({
	HOT: Boolean,
	BIG: Boolean,
	TOP: Boolean,
	MAD: Boolean,
	VAT: Boolean,
	TAX: Boolean,
	CAT: Boolean,
	DOG: Boolean,
});
const blank = computed(() => {
	return (
		!props.HOT &&
		!props.BIG &&
		!props.TOP &&
		!props.MAD &&
		!props.VAT &&
		!props.TAX &&
		!props.CAT &&
		!props.DOG
	);
});
const badge = computed(() => {
	if (props.HOT) return "HOT";
	if (props.BIG) return "BIG";
	if (props.TOP) return "TOP";
	if (props.MAD) return "MAD";
	if (props.VAT) return "VAT";
	if (props.TAX) return "TAX";
	if (props.CAT) return "CAT";
	if (props.DOG) return "DOG";
	return "GAS";
});
</script>
<template>
	<span
		:class="{
			'bg-teal-600': HOT,
			'bg-yellow-600': BIG,
			'bg-purple-600': TOP,
			'bg-sky-600': MAD,
			'bg-green-600': VAT,
			'bg-red-600': TAX,
			'bg-lime-600': CAT,
			'bg-pink-600': DOG,
			'bg-gray-600': blank,
		}"
		class="text-xs px-1 py-0.5 rounded-sm text-white font-semibold"
	>
		{{ badge }}
	</span>
</template>
