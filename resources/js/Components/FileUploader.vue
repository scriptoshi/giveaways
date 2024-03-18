
<script setup>
import { parse } from "svg-parser";
import DOMPurify from 'dompurify';
defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});
const emit = defineEmits(["update:modelValue"]);
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file && file.type === "image/svg+xml") {
        const reader = new FileReader();
        reader.onload = () => {
            const svgString = reader.result;
            const svg = DOMPurify.sanitize(svgString);
            const hast = parse(svg);
            emit("update:modelValue", hast);
        };
        reader.readAsText(file);
    }
};

</script>
<template>
    <label class=" cursor-pointer">
        <slot>Upload SVG</slot>
        <input
            type="file"
            hidden
            accept="image/svg+xml"
            @change="handleFileChange"
        />
    </label>
</template> 
    