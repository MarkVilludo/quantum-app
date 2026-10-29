<script setup>
import SkeletonBox from './SkeletonBox.vue';
import SkeletonText from './SkeletonText.vue';

defineProps({
    rows: { type: Number, default: 5 },
    columns: { type: Number, default: 4 },
});
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-quantum-stroke/20 bg-white" aria-hidden="true">
        <div class="border-b border-gray-100 bg-slate-50/80 px-4 py-3">
            <div class="flex gap-4">
                <SkeletonBox v-for="c in columns" :key="'h' + c" class="h-3 flex-1" />
            </div>
        </div>
        <div v-for="r in rows" :key="r" class="flex gap-4 border-t border-gray-100 px-4 py-4">
            <SkeletonBox v-for="c in columns" :key="r + '-' + c" class="h-3 flex-1" />
        </div>
    </div>
</template>
