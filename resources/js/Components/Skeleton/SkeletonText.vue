<script setup>
import SkeletonBox from './SkeletonBox.vue';

defineProps({
    lines: {
        type: Number,
        default: 3,
    },
    lastShort: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="space-y-2.5" aria-hidden="true">
        <SkeletonBox
            v-for="n in lines"
            :key="n"
            class="h-3"
            :class="lastShort && n === lines ? 'w-2/3' : 'w-full'"
        />
    </div>
</template>
