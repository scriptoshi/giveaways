<script setup>
import { ref } from "vue";

import { useForm } from "@inertiajs/vue3";

import Loading from "@/Components/Loading.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
const props = defineProps({
    galxe: Object,
});
const showConfirmation = ref(false);
const form = useForm({ id: props.galxe.id });
const deleteCredential = () => {
    form.delete(window.route("galxe.destroy", { galxe: props.galxe.id }),{
        preserveState:false
    });
};
</script>
<template>
    <div class="w-full">
        <button
            @click="showConfirmation = true"
            :disabled="showConfirmation"
            class="bg-white dark:bg-gray-700 hover:bg-gray-50 hover:dark:bg-gray-800 rounded-lg py-3 border border-red-500 dark:border-gray-600 w-full transition duration-200 text-red-600 dark:text-white font-semibold"
        >
            {{$t('Delete Galxe Credential')}}
        </button>
        <JetConfirmationModal
            :show="showConfirmation"
            @close="showConfirmation = false"
        >
            <template #title>
                {{$t('Delete Galxe Credential')}}
            </template>
            <template #content>
                <p>{{$t('This will Delete the connection to this credential. Participants will not longer be added to galxe credential')}}</p>
            </template>
            <template #footer>
                <JetSecondaryButton
                    :class="{ 'opacity-60 pointer-events-none': form.processing }"
                    :disabled="form.processing"
                    @click="showConfirmation = false"
                >
                    {{$t('Cancel')}}
                </JetSecondaryButton>
                <JetDangerButton
                    class="ml-2"
                    @click="deleteCredential()"
                    :class="{ 'opacity-60 pointer-events-none': form.processing }"
                    :disabled="form.processing"
                >
                    <Loading
                        v-if="form.processing"
                        class="mr-2 -ml-1 inline-block w-5 h-5"
                    /> {{form.processing?$t('Working...'):$t('Delete Credential')}}
                </JetDangerButton>
            </template>
        </JetConfirmationModal>
    </div>
</template>