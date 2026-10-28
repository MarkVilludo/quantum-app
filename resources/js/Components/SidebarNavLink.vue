<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center gap-3 rounded-lg border border-quantum-accent/25 bg-quantum-surface px-3 py-2.5 text-sm font-medium text-quantum-accent shadow-sm shadow-quantum-accent/10'
        : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-quantum-secondary hover:bg-quantum-surface hover:text-quantum-text-primary'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot name="icon" />
        <span><slot /></span>
    </Link>
</template>
