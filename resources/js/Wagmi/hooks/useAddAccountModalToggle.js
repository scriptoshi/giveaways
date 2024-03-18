import { ref } from "vue";
export const isOpen = ref(false);
export const close = () => isOpen.value = false;
export const open = () => isOpen.value = true;
const toggle = () => isOpen.value ? close() : open();
export const useAddAccountModalToggle = () => {
    return {
        isOpen,
        close,
        open,
        toggle,
    };
};
