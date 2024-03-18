
import { ref } from "vue";
export const isOpen = ref(false);
export const useWagmiModalToggle = () => {
    const close = () => isOpen.value = false;
    const open = () => isOpen.value = true;
    const toggle = () => isOpen.value ? close() : open();
    return {
        isOpen,
        close,
        open,
        toggle
    };
};
