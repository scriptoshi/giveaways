import { computed, reactive } from "vue";
export const txs = reactive({});
export const useTxs = () => txs;
export const clearAllTransactions = () => {
    for (const hash in txs) delete txs[hash];
};
export const clearTx = (hash) => {
    delete txs[hash];
};

export const addTransaction = (tx) => {
    if (txs[tx.hash]) {
        throw Error("Attempted to add existing transaction.");
    }
    txs[tx.hash] = {
        ...tx,
        addedTime: new Date().getTime(),
        receipt: null,
        error: null,
        loading: true,
    };
};

export const txReceipt = (hash, receipt) => {
    txs[hash] = {
        ...txs[hash],
        receipt,
        loading: false,
    };
};

export const txError = (hash, error) => {
    txs[hash] = {
        ...txs[hash],
        error,
        loading: false,
    };
};

export const pendingTxs = computed(() => {
    return Object.fromEntries(
        Object.entries(txs).filter(([, tx]) => !tx.receipt),
    );
});
