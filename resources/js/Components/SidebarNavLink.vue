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
        ? 'relative flex items-center gap-3 rounded-lg border border-quantum-accent/25 bg-quantum-surface px-3 py-2.5 text-sm font-medium text-quantum-accent shadow-sm shadow-quantum-accent/10 before:absolute before:inset-y-2 before:left-0 before:w-1 before:rounded-full before:bg-quantum-accent'
        : 'flex min-h-[44px] items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-quantum-secondary hover:bg-quantum-surface hover:text-quantum-text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-quantum-accent'
);
</script>

<template>
    <Link :href="href" :class="classes" :aria-current="active ? 'page' : undefined">
        <slot name="icon" />
        <span><slot /></span>
    </Link>
</template>
