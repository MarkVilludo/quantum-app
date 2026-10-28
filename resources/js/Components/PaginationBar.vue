<script setup>
defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    lastPage: {
        type: Number,
        required: true,
    },
});

defineEmits(['previous', 'next']);
</script>

<template>
    <div class="mt-6 flex flex-col items-center justify-between gap-3 border-t border-gray-100 pt-4 sm:flex-row">
        <button
            type="button"
            class="qr-btn-secondary w-full sm:w-auto"
            :disabled="currentPage <= 1"
            @click="$emit('previous')"
        >
            Previous
        </button>

        <span class="text-sm text-gray-600">
            Page <span class="font-semibold text-gray-900">{{ currentPage }}</span> of
            {{ lastPage }}
        </span>

        <button
            type="button"
            class="qr-btn-secondary w-full sm:w-auto"
            :disabled="currentPage >= lastPage"
            @click="$emit('next')"
        >
            Next
        </button>
    </div>
</template>
