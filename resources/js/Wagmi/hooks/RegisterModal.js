import { ref } from "vue";
export const isOpen = ref(false);
export const isEnabled = ref(true);
export const active = ref(true);
export const useRegisterModal = () => {
    return {
        isEnabled,
        isOpen,
        active,
        close: () => isOpen.value = false,
        open: () => isOpen.value = true,
        toggle: () => isOpen.value ? close() : open(),
    };
};
