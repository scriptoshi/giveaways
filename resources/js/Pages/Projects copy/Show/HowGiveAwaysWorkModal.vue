<script setup>
import {ref} from "vue";

import DialogModal from "@/Jetstream/DialogModal.vue";
const modal = ref(false);
</script>
<template>
	<a
		@click.prevent="modal = true"
		href="#"
		>[How it works?]</a
	>
	<DialogModal
		closeable="true"
		@close="modal = false"
		:show="modal"
	>
		<template #title> How Give Aways Work </template>
		<template #content>
			<ul class="list-disc list-inside">
				<li>Giveaways are hosted by sleep finance or a partner influencer.</li>
				<li>In order to qualify for the draw, you will perform tasks</li>
				<li>
					Influencers will post proof of payment (Tx hash of the payout to you, if you
					win)
				</li>
				<li>
					Sleep finance hosted giveaways will require you to claim from a contract when
					you win
				</li>
				<li>
					Some giveaways require contributing to the projects fairlaunch. If you miss the
					prize, you can always claim you allocated tokens after sale ends.
				</li>
				<li>
					Please follow the instructions of the influencers carefully, if you smell
					something wrong, please contact the sleep team.
				</li>
				<li>
					Project Owners cannot host giveway, If you need to run a promotion , contact our
					certified influencers for help.
				</li>
				<li>
					"Hi", "hey", donot count as comments. you must provide at least 20 meaningfull
					words about the project.
				</li>
			</ul>
		</template>
	</DialogModal>
</template>
