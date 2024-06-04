
import { get } from "@vueuse/core";

export const useBillions = (billion, precision = 2) => {
    const num = get(billion);
    const map = [
        { suffix: 'T', threshold: 1e12 },
        { suffix: 'B', threshold: 1e9 },
        { suffix: 'M', threshold: 1e6 },
        { suffix: 'K', threshold: 1e3 },
        { suffix: '', threshold: 1 },
    ];
    const found = map.find((x) => Math.abs(num) >= x.threshold);
    if (found) {
        const formatted = ((num / found.threshold).toFixed(precision) * 1) + found.suffix;
        return formatted;
    }
    return num;
};
