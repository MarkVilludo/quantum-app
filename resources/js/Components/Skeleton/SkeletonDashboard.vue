<script setup>
import SkeletonBox from './SkeletonBox.vue';
</script>

<template>
    <div class="px-4 py-6 sm:px-6 lg:px-8" aria-hidden="true">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="n in 3" :key="n" class="rounded-xl border border-quantum-stroke/20 bg-white p-6 shadow-sm">
                <SkeletonBox class="h-3 w-24" />
                <SkeletonBox class="mt-3 h-7 w-2/3" />
            </div>
        </div>
    </div>
</template>
