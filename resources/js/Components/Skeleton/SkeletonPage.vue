<script setup>
import SkeletonDashboard from './SkeletonDashboard.vue';
import SkeletonTable from './SkeletonTable.vue';
import SkeletonProfileProgress from './SkeletonProfileProgress.vue';

defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (v) => ['default', 'table', 'progression'].includes(v),
    },
});
</script>

<template>
    <SkeletonProfileProgress v-if="variant === 'progression'" />
    <div v-else-if="variant === 'table'" class="px-4 py-6 sm:px-6 lg:px-8">
        <SkeletonTable :rows="6" :columns="5" />
    </div>
    <SkeletonDashboard v-else />
</template>
