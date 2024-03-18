<script setup>
import { h, useAttrs, useSlots } from "vue";

const WEIGHTS = {
    100: "font-thin",
    200: "font-extralight",
    300: "font-light",
    400: "font-normal",
    500: "font-medium",
    600: "font-semibold",
    700: "font-bold",
    800: "font-extrabold",
    900: "font-black",
};

const VARIANTS = {
    hero: "text-5xl ",
    h1: "text-4xl",
    h2: "text-3xl ",
    h3: "text-2xl ",
    xl: "text-xl  ",
    lg: "text-lg ",
    base: "text-base leading-5",
    sm: "text-sm leading-5",
    xs: "text-xs leading-5",
    xxs: "text-[0.625rem] leading-[1.2]",
};
const props = defineProps({
    variant: {
        required: false,
        type: String,
        default: "base",
        validator(value) {
            return [
                "hero",
                "h1",
                "h2",
                "h3",
                "lg",
                "base",
                "sm",
                "xs",
                "xxs",
            ].includes(value);
        },
    },
    weight: {
        required: false,
        type: [Number,String],
        default: 400,
        validator(value) {
            return [100, 200, 300, 400, 500, 600, 700].includes(parseInt(value));
        },
    },
    as: {
        required: false,
        type: String,
        default: "div",
    },
    clickable: { type: Boolean, default: false },
});
const slots = useSlots();
const attrs = useAttrs();
const Node = h(
    props.as,
    {
        class: [
            VARIANTS[props.variant],
            WEIGHTS[props.weight],
            props.clickable ? "cursor-pointer select-none" : "",
        ],
        ...attrs,
    },
    slots.default()
);
</script>
<template>
    <Node />
</template>