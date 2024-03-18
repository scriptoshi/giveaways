import { ref } from 'vue';
const bet = ref(null);
const market = ref(null);
export const useBet = () => bet;
export const useMarket = () => market;
