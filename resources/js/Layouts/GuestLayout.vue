<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-100 lg:flex-row">
        <div
            class="relative hidden flex-1 flex-col justify-between overflow-hidden bg-gradient-to-br from-quantum-bg-dark via-quantum-primary-dark to-quantum-bg-mid p-10 text-white lg:flex"
        >
            <div
                class="pointer-events-none absolute -right-20 top-1/4 h-64 w-64 rounded-full bg-quantum-accent/20 blur-3xl"
                aria-hidden="true"
            />
            <div>
                <Link href="/" class="inline-flex items-center gap-2">
                    <ApplicationLogo class="h-10 w-auto fill-current text-quantum-accent" />
                    <span class="text-lg font-semibold">Quantum Quizzes</span>
                </Link>
            </div>
            <div class="relative max-w-md">
                <h1 class="text-3xl font-bold leading-tight">Manage courses, lessons, and quiz results in one place.</h1>
                <p class="mt-4 text-quantum-secondary">
                    Same colors as the mobile app—indigo actions, cyan highlights, dark space theme.
                </p>
            </div>
            <p class="text-sm text-quantum-muted">© Quantum Realm Learning</p>
        </div>

        <div class="flex flex-1 flex-col justify-center px-6 py-10 sm:px-10">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 text-center lg:hidden">
                    <Link href="/" class="inline-flex flex-col items-center gap-2">
                        <ApplicationLogo class="h-12 w-auto fill-current text-quantum-primary" />
                        <span class="text-sm font-semibold text-gray-900">Quantum Quizzes</span>
                    </Link>
                </div>

                <div class="rounded-2xl border border-quantum-stroke/30 bg-white p-8 shadow-sm shadow-quantum-primary/5">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
