<script setup>
import SkeletonBox from './SkeletonBox.vue';
import SkeletonText from './SkeletonText.vue';

defineProps({
    count: { type: Number, default: 3 },
});
</script>

<template>
    <div class="space-y-4" aria-hidden="true">
        <div
            v-for="n in count"
            :key="n"
            class="rounded-xl border border-quantum-stroke/30 bg-white p-5 shadow-sm"
        >
            <SkeletonBox class="mb-3 h-4 w-1/3" />
            <SkeletonText :lines="2" :last-short="true" />
            <div class="mt-4 grid grid-cols-2 gap-2">
                <SkeletonBox class="h-8 rounded-lg" />
                <SkeletonBox class="h-8 rounded-lg" />
                <SkeletonBox class="h-8 rounded-lg" />
                <SkeletonBox class="h-8 rounded-lg" />
            </div>
        </div>
    </div>
</template>
