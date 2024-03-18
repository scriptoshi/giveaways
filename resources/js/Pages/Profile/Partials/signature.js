import { ref } from 'vue';

import axios from 'axios';
import { useAccount, useSignMessage } from "use-wagmi";

export const useSignature = () => {
    const { signMessageAsync } = useSignMessage();
    const { address } = useAccount();
    const error = ref();
    const signMessage = async () => {
        const { data } = await axios.post(window.route("nonce"), {
            address: address.value,
        });
        let signature;
        try {
            signature = await signMessageAsync({ message: data.nonce });
        } catch (e) {
            error.value = 'Signature Failed';
        }
        return signature;
    };
    return {
        signMessage,
        error
    };
};
