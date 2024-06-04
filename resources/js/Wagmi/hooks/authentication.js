

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
export const isSignining = ref(false);
export const signatureRejected = ref(false);
export const logout = ref(false);
const AuthCheck = computed(() => usePage().props.AuthCheck);
export const logOut = async () => {
    if (!AuthCheck.value || logout.value) return;
    logout.value = true;
    await axios.post(window.route("logout"));
    router.reload({ preserveScroll: false, preserveState: false });
    logout.value = false;
    busy.value = false;
};


export const useAuth = () => {
    const { address, connector, isConnected, isConnecting } = useAccount();
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
        console.log('SignIn');
        signatureRejected.value = false;
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
        isSignining.value = true;
        let signature;
        try {
            signature = await signMessageAsync({ message: data.nonce });
        } catch (err) {
            error.value = "Signature Rejected.";
            signatureRejected.value = true;
            busy.value = false;
            return;
        }
        const { data: response } = await axios.post(data.address, {
            address: get(address),
            signature,
        });
        closeWagmiModal();
        isSignining.value = false;
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
        console.log('activate');
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
        watch([isConnected, address], ([connected, address]) => {
            if (isConnecting.value) return;
            if (connected && address && connector.value) {
                connector?.value?.on?.("", (accounts) => {
                    activate();
                });
                if (AuthCheck.value) return;
                activate();
            }
            else {
                close();
                if (AuthCheck.value)
                    signOut();
            }
        }, { immediate: true });
        watch(address, (address) => {
            if (isConnecting.value) return;
            if (AuthCheck.value) {
                if (!address) return signOut();
                if (accounts.value.includes(address)) {
                    busy.value = false;
                    closeAddAdressModal();
                    return;
                }
                console.log('showing address modal');
                showAddAdressModal();
            }
        }, { immediate: true });
    };

    return {
        init,
        signOut,
        busy,
        error,
        SignIn
    };
};


