<script setup>
defineProps({
    tag: {
        type: String,
        default: 'div',
    },
});
</script>

<template>
    <component :is="tag" class="qr-skeleton" aria-hidden="true">
        <slot />
    </component>
</template>
