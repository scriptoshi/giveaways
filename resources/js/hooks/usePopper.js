import { nextTick, ref, watch } from "vue";

import { createPopper } from "@popperjs/core";
import { breakpointsTailwind, onClickOutside, useBreakpoints } from "@vueuse/core";
export const usePopper = (userOptions = {
    placement: "bottom-end",
    offset: 4,
}) => {
    const { smaller } = useBreakpoints(breakpointsTailwind);
    const sm = smaller('md');
    const md = smaller('lg');
    const lg = smaller('xl');
    const xl = smaller('2xl');
    const popperInstance = ref(null);
    const popperRef = ref(null);
    const popperRoot = ref(null);
    const outside = ref(null);
    const options = buildOptions(userOptions);
    const showPopper = ref(false);
    onClickOutside(outside, () => {
        if (showPopper.value) showPopper.value = false;
    });
    nextTick(() => {
        popperInstance.value = createPopper(
            popperRef.value,
            popperRoot.value,
            options
        );
    });
    watch(showPopper, () => popperInstance?.value?.update?.());
    watch([sm, md, lg, xl], () => showPopper.value = false);
    return {
        popperRef,
        popperRoot,
        showPopper,
        outside
    };

};
function setWidth({ instance: { reference, popper } }) {
    console.log(reference.offsetWidth);
    popper.style.width = `${reference.offsetWidth}px`;
}
const buildOptions = (options) => {
    const config = {
        placement: options.placement ?? "auto",
        strategy: options.strategy ?? "fixed",
        onCreate: setWidth,
        onUpdate: setWidth,
        onFirstUpdate: options.onFirstUpdate ?? function () { },
        modifiers: [
            {
                name: "offset",
                options: {
                    offset: [0, options.offset ?? 0],
                },
            },
            {
                name: "sameWidth",
                enabled: true,
                fn: ({ state }) => {
                    state.styles.popper.width = `${state.rects.reference.width}px`;
                },
                phase: "beforeWrite",
                requires: ["computeStyles"],
                effect: ({ state }) => {
                    state.elements.popper.style.width = `${state.elements.reference.clientWidth
                        }px`;
                }
            }
        ]
    };
    if (options.modifiers) config.modifiers.push(...options.modifiers);
    return config;
};
