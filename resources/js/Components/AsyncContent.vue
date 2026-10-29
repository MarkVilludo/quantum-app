<script setup>
defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="relative min-h-[12rem]">
        <Transition name="qr-fade">
            <div v-if="loading" key="loading" class="absolute inset-0 z-10">
                <slot name="skeleton" />
            </div>
        </Transition>
        <div
            class="transition-opacity duration-300 ease-out"
            :class="loading ? 'pointer-events-none opacity-0' : 'opacity-100'"
            :aria-busy="loading ? 'true' : 'false'"
        >
            <slot />
        </div>
    </div>
</template>
