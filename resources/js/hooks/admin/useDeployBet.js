
import { computed } from 'vue';

import {
    useContractWrite,
    usePrepareContractWrite,
    useWaitForTransaction,
} from 'wagmi';

import { useTxHash } from '@/Wagmi/hooks/useTxHash';
export const useDeployBet = () => {
    const {
        config,
        error: prepareError,
        isError: isPrepareError,
    } = usePrepareContractWrite({
        address: '0xFBA3912Ca04dd458c843e2EE08967fC04f3579c2',
        abi: [
            {
                name: 'mint',
                type: 'function',
                stateMutability: 'nonpayable',
                inputs: [],
                outputs: [],
            },
        ],
        functionName: 'mint',
    });
    const { data, error, isError, write } = useContractWrite(config);
    const { isLoading, isSuccess } = useWaitForTransaction({
        hash: data?.hash,
    });
    const hash = computed(() => data?.hash);
    const [shortTx, etherScanLink] = useTxHash(hash);
    return {
        isLoading,
        isSuccess,
        shortTx,
        etherScanLink,
        isPrepareError,
        prepareError,
        data, error,
        isError,
        write
    };


};
