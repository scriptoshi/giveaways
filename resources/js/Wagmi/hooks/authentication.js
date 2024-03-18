

import { computed, ref, watch } from "vue";

import { router, usePage } from "@inertiajs/vue3";
import { get } from "@vueuse/core";
import axios from "axios";
import { useAccount, useDisconnect, useSignMessage } from 'use-wagmi';

import { useRegisterModal } from "@/Wagmi/hooks/RegisterModal";
import { close as closeAddAdressModal, open as showAddAdressModal } from "@/Wagmi/hooks/useAddAccountModalToggle";
import { useWagmiModalToggle } from "@/Wagmi/hooks/useWagmiModalToggle.js";
export const error = ref(null);
export const busy = ref(false);
export const logOut = async () => {
    await axios.post(window.route("logout"));
    router.reload({ preserveScroll: false, preserveState: false });
    busy.value = false;
};


export const useAuth = () => {
    const { address, connector, isConnected, isConnecting, isReconnecting } = useAccount();
    const AuthCheck = computed(() => usePage().props.AuthCheck);
    const accounts = computed(() => usePage().props.user?.accounts?.map(a => a.address));
    const { signMessageAsync } = useSignMessage();
    const { open, isEnabled: useModalToRegister, isOpen, close } = useRegisterModal();
    const { close: closeWagmiModal } = useWagmiModalToggle();
    const signOut = async () => {
        busy.value = true;
        disconnect();
        await logOut();
    };
    const { disconnect } = useDisconnect();
    const SignIn = async () => {
        // avoid redirect loop
        if (window.route().current('register')
            || isOpen.value
            || AuthCheck.value
        ) {
            busy.value = false;
            return;
        }
        busy.value = true;
        const { data } = await axios.post(window.route("nonce"), {
            address: get(address),
        });
        if (data.register) {
            if (useModalToRegister.value && !!address) return open();
            return router.visit(data.address);
        }
        let signature;
        try {
            signature = await signMessageAsync({ message: data.nonce });
        } catch (err) {
            error.value = "Signature Rejected.";
            busy.value = false;
            return;
        }
        const { data: response } = await axios.post(data.address, {
            address: get(address),
            signature,
        });
        closeWagmiModal();
        busy.value = false;
        if (window.route().current('login')) {
            router.visit(response.url ?? window.route('home'));
            return;
        }
        router.reload({
            onFinish() {
                busy.value = false;
            }
        });
    };

    const activate = async () => {
        busy.value = true;
        closeAddAdressModal();
        const { data } = await axios.post(window.route("status"), {
            address: get(address),
        });
        if (data.register) {
            busy.value = false;
            if (useModalToRegister.value) return open();
            if (!window.route().current("register"))
                return router.visit(window.route("register"));
        }
        SignIn();
    };
    const init = () => {
        watch([isConnected, address, isConnecting, isReconnecting], ([connected, address, isConnecting, isReconnecting]) => {
            if (isConnecting || isReconnecting) return;
            if (address) {
                if (AuthCheck.value) {
                    if (accounts.value.includes(address)) {
                        busy.value = false;
                        closeAddAdressModal();
                        return;
                    }
                    console.log('showing address modal');
                    showAddAdressModal();
                } else {
                    activate();
                }
            }
            if (connected) {
                connector?.value?.on?.("", (accounts) => {
                    activate();
                });
            } else {
                close();
                if (AuthCheck.value)
                    signOut();
            }
        }, { immediate: true });
    };

    return {
        init,
        signOut,
        busy,
        error,
        SignIn,
        activate
    };
};


