
<script setup>
	import { computed, useCssModule } from "vue";
	const props = defineProps({
        src:String,
        alt:String,
		xxl: Boolean,
		xl: Boolean,
		lg: Boolean,
		md: Boolean,
		sm: Boolean,
		xs: Boolean,
	});
	const style = useCssModule();
	const classNames = computed(() => [
		style.avatar,
		{
			[style.xxl]: props.xxl,
			[style.xl]: props.xl,
			[style.lg]: props.lg,
			[style.md]: props.md,
			[style.sm]: props.sm,
			[style.xs]: props.xs,
		},
	]);
</script>
<template>
	<div :class="classNames" class="relative flex-shrink-0">
		<div class=" rounded-full w-full h-full flex items-center justify-center relative overflow-hidden bg-gray-300">
            <img :src="src" alt="">
            <slot/>
       </div>
	</div>
</template>
<style module>
	.avatar.xxl {
		height: 96px;
		width: 96px;
	}

	.avatar.xl {
		height: 72px;
		width: 72px;
	}

	.avatar.lg {
		height: 56px;
		width: 56px;
	}

	.avatar.md {
		height: 40px;
		width: 40px;
	}

	.avatar.sm {
		height: 32px;
		width: 32px;
	}

	.avatar.xs {
		height: 24px;
		width: 24px;
	}

	.avatar img {
		display: block;
		height: 100%;
		left: 0;
		top: 0;
		position: absolute;
		width: 100%;
	}
</style>