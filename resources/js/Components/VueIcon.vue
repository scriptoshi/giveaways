
<script setup>
import { addIcons } from "oh-vue-icons";
const props = defineProps({
    icon:Object
});
addIcons(props.icon);
</script>
<template>
    <OhVueIcon :name="icon.name"/>
</template>