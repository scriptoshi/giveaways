import { ref } from "vue";
export const isOpen = ref(false);
export const close = () => isOpen.value = false;
export const open = () => isOpen.value = true;
const toggle = () => isOpen.value ? close() : open();
export const useEmailVerificationModal = () => {
    return {
        isOpen,
        close,
        open,
        toggle,
    };
};