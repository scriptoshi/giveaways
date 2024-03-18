<script setup>
import { useCountDown } from "@/hooks/useCountDown";
import { computed } from "vue";
const props = defineProps({
    timestamp: Number,
    time: Number,
});
const time = computed(() => parseInt(props.timestamp??props.time) * 1000);
const { days, hours, minutes, seconds } = useCountDown(time);
function pad(n) {
    return (parseInt(n) < 10) ? ("0" + n) : n;
}
</script>
<template>
    <div>
        <slot
            :days="pad(days)??'00'"
            :hours="pad(hours)??'00'"
            :minutes="pad(minutes)??'00'"
            :seconds="pad(seconds)??'00'"
        />
    </div>
</template>
    