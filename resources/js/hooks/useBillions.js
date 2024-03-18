
import { get } from "@vueuse/core";
export const useBillions = (billion) => {
    let newValue = parseInt(get(billion)) ?? 0;
    if (!newValue) return 0;
    if (newValue < 1000) return newValue;
    const suffixes = ["", "K", "M", "B", "T"];
    let suffixNum = 0;
    while (newValue >= 1000) {
        newValue /= 1000;
        suffixNum++;
    }
    newValue =
        newValue.toString().length > 2
            ? newValue.toPrecision(3)
            : newValue.toPrecision();
    newValue += suffixes[suffixNum];
    return newValue;
};
