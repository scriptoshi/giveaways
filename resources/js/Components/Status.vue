<script setup>
defineProps({
    status: String,
});
</script>
<template>
    <div
        v-if="status=='cancelled'"
        class="badge space-x-2.5 text-[0.625rem] font-semibold rounded-full bg-gray-500/10 text-gray-700 dark:bg-gray-300/15 dark:text-gray-200"
    >
        <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
        <span>{{$t('Cancelled')}}</span>
    </div>
    <div
        v-if="status=='vesting'"
        class=" badge space-x-2.5 text-[0.625rem] font-semibold rounded-full bg-info/10 text-info dark:bg-info/15"
    >
        <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
        <span>{{$t('Vesting')}}</span>
    </div>
    <div
        v-if="status=='live'"
        class=" badge space-x-2.5 text-[0.625rem] font-semibold rounded-full bg-success/10 text-success dark:bg-success/15"
    >
        <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
        <span>{{$t('Live')}}</span>
    </div>
    <div
        v-if="status=='pending'"
        class="badge space-x-2.5 rounded-full text-[0.625rem] font-semibold bg-warning/10 text-warning dark:bg-warning/15"
    >
        <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
        <span>{{$t('Pending')}}</span>
    </div>
    <div
        v-if="status=='ended'"
        class="badge space-x-2.5 rounded-full text-[0.625rem] font-semibold bg-error/10 text-error dark:bg-error/15"
    >
        <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
        <span>{{$t('Ended')}}</span>
    </div>
</template>