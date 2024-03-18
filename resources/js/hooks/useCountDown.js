import { ref, watch } from "vue";

import { get } from "@vueuse/core";

export const useCountDown = (eventCountDown, up = false) => {
    const days = ref(null);
    const hours = ref(null);
    const minutes = ref(null);
    const seconds = ref(null);
    const timeout = ref(null);
    // Run myfunc every second
    const stop = () => {
        if (timeout.value)
            clearInterval(timeout.value);
        timeout.value = null;
        days.value = null;
        hours.value = null;
        minutes.value = null;
        seconds.value = null;
    };
    const start = () => {
        if (!get(eventCountDown)) {
            return;
        }
        stop();
        timeout.value = setInterval(function () {
            const now = new Date().getTime();
            const timeleft = up
                ? Math.abs(now - get(eventCountDown))
                : get(eventCountDown) - now;
            if (timeleft < 0) stop();
            // Calculating the days, hours, minutes and seconds left
            days.value = Math.floor(timeleft / (1000 * 60 * 60 * 24));
            hours.value = Math.floor((timeleft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            minutes.value = Math.floor((timeleft % (1000 * 60 * 60)) / (1000 * 60));
            seconds.value = Math.floor((timeleft % (1000 * 60)) / 1000);
        }, 1000);
    };
    start();
    watch(eventCountDown, start);
    return {
        days,
        hours,
        minutes,
        seconds
    };
};

