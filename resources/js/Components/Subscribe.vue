<script setup>
import {addIcons} from "oh-vue-icons";
import {BiTelegram} from "oh-vue-icons/icons";
defineProps({
	links: Array,
	website: String,
});
addIcons(BiTelegram);
</script>
<template>
	<div
		class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5"
	>
		<div class="mb-1 text-center">
			<div class="-space-x-2 inline-flex">
				<img
					class="box-content block h-auto max-w-full rounded-full border-2 border-white align-middle"
					alt="Avatar 01"
					height="24"
					width="24"
					src="../images/avatar/avatar-1.jpg"
				/>
				<img
					class="box-content block h-auto max-w-full rounded-full border-2 border-white align-middle mr-[calc(-0.75rem_*_-0px)] ml-[calc(-0.75rem_*_calc(1_-_-0px))]"
					alt="Avatar 02"
					height="24"
					width="24"
					src="../images/avatar/avatar-2.jpg"
				/>
				<img
					class="box-content block h-auto max-w-full rounded-full border-2 border-white align-middle mr-[calc(-0.75rem_*_-0px)] ml-[calc(-0.75rem_*_calc(1_-_-0px))]"
					alt="Avatar 03"
					height="24"
					width="24"
					src="../images/avatar/avatar-3.jpg"
				/>
				<img
					class="box-content block h-auto max-w-full rounded-full border-2 border-white align-middle mr-[calc(-0.75rem_*_-0px)] ml-[calc(-0.75rem_*_calc(1_-_-0px))]"
					alt="Avatar 04"
					height="24"
					width="24"
					src="../images/avatar/avatar-5.jpg"
				/>
				<img
					class="box-content block h-auto max-w-full rounded-full border-2 border-white align-middle mr-[calc(-0.75rem_*_-0px)] ml-[calc(-0.75rem_*_calc(1_-_-0px))]"
					alt="Avatar 05"
					height="24"
					width="24"
					src="../images/avatar/avatar-5.jpg"
				/>
			</div>
		</div>
		<div class="mb-8 text-center">
			<div class="mb-1 font-[Aspekta,_sans-serif] font-[650]">Never miss a launch!</div>
			<p class="text-sm text-slate-500 leading-[1.5715]">
				Join 100K+ giveaways claimers in telelgram
			</p>
		</div>
		<form class="">
			<div class="mb-2 hidden">
				<label
					class="absolute h-px w-px overflow-hidden whitespace-nowrap border-0"
					for="newsletter"
					>Your email…</label
				>
				<input
					class="w-full rounded-full border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-1 text-sm text-inherit font-[inherit] leading-[1.5715]"
					placeholder="Your email…"
					type="email"
				/>
			</div>
			<button
				class="inline-flex py-1.5 w-full cursor-pointer items-center justify-center whitespace-nowrap rounded-full bg-sky-500 bg-none text-sm font-medium normal-case leading-5 text-gray-100 transition"
				type="submit"
			>
				<OhVueIcon
					name="bi-telegram"
					class="w-5 h-5 mr-2 -ml-1"
				/>Join Telegram
			</button>
		</form>
	</div>
</template>
