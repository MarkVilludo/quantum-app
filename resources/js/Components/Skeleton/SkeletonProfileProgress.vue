<script setup>
import SkeletonBox from './SkeletonBox.vue';
import SkeletonText from './SkeletonText.vue';

defineProps({
    tierCount: { type: Number, default: 6 },
});
</script>

<template>
    <div class="space-y-8" aria-hidden="true">
        <section class="overflow-hidden rounded-2xl border border-quantum-stroke/30 bg-gradient-to-br from-quantum-bg-mid via-quantum-surface to-quantum-bg-dark p-6 sm:p-8">
            <SkeletonBox class="h-3 w-24 bg-quantum-surface-up/80" />
            <SkeletonBox class="mt-3 h-8 w-3/4 max-w-md bg-quantum-surface-up/80" />
            <SkeletonBox class="mt-2 h-4 w-40 bg-quantum-surface-up/60" />
            <SkeletonBox class="mt-6 h-3 w-full rounded-full bg-quantum-bg-dark/80" />
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="n in tierCount"
                :key="n"
                class="rounded-xl border border-quantum-stroke/30 bg-white p-5"
            >
                <SkeletonBox class="mb-2 h-6 w-6 rounded-full" />
                <SkeletonBox class="h-4 w-2/3" />
                <SkeletonText class="mt-3" :lines="2" />
            </div>
        </section>
    </div>
</template>
