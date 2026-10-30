<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GraduationCertificatesChart from '@/Components/Dashboard/GraduationCertificatesChart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    graduationAnalytics: {
        type: Object,
        default: null,
    },
    analyticsFilters: {
        type: Object,
        default: null,
    },
});

const role = computed(() => usePage().props.auth.user?.role);
const showGraduationAnalytics = computed(
    () =>
        (role.value === 'admin' || role.value === 'teacher') &&
        props.graduationAnalytics &&
        props.analyticsFilters,
);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500">Welcome back, {{ $page.props.auth.user.name }}</p>
            </div>
        </template>

        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-quantum-stroke/30 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Signed in as</p>
                    <p class="mt-1 text-xl font-semibold capitalize text-gray-900">{{ role }}</p>
                </div>

                <Link
                    v-if="role === 'teacher'"
                    :href="route('lessons.index')"
                    class="rounded-xl border border-quantum-accent/30 bg-gradient-to-br from-quantum-bg-mid to-quantum-surface p-6 text-white shadow-sm transition hover:border-quantum-accent/60"
                >
                    <p class="text-sm font-medium text-quantum-accent">Quick action</p>
                    <p class="mt-1 text-lg font-semibold">Manage module lessons</p>
                </Link>

                <Link
                    v-if="role === 'admin'"
                    :href="route('users.index')"
                    class="rounded-xl border border-quantum-accent/30 bg-gradient-to-br from-quantum-bg-mid to-quantum-surface p-6 text-white shadow-sm transition hover:border-quantum-accent/60"
                >
                    <p class="text-sm font-medium text-quantum-accent">Quick action</p>
                    <p class="mt-1 text-lg font-semibold">Manage users</p>
                </Link>

                <Link
                    v-if="role === 'student'"
                    :href="route('student-quiz-results.index')"
                    class="rounded-xl border border-quantum-accent/30 bg-gradient-to-br from-quantum-bg-mid to-quantum-surface p-6 text-white shadow-sm transition hover:border-quantum-accent/60"
                >
                    <p class="text-sm font-medium text-quantum-accent">Quick action</p>
                    <p class="mt-1 text-lg font-semibold">View my quiz results</p>
                </Link>

                <Link
                    v-if="role === 'student'"
                    :href="route('my-progression.index')"
                    class="rounded-xl border border-quantum-gold/40 bg-gradient-to-br from-quantum-bg-mid to-quantum-surface p-6 text-white shadow-sm transition hover:border-quantum-gold/70"
                >
                    <p class="text-sm font-medium text-quantum-gold">Progression</p>
                    <p class="mt-1 text-lg font-semibold">My levels &amp; title tiers</p>
                </Link>

                <Link
                    v-if="role === 'teacher' || role === 'admin'"
                    :href="route('quiz-results.index')"
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-quantum-primary/30 hover:bg-slate-50"
                >
                    <p class="text-sm font-medium text-gray-500">Reports</p>
                    <p class="mt-1 text-lg font-semibold text-quantum-primary">Student quiz results</p>
                </Link>

                <Link
                    v-if="role === 'teacher' || role === 'admin'"
                    :href="route('student-progression.index')"
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-quantum-accent/40 hover:bg-slate-50"
                >
                    <p class="text-sm font-medium text-gray-500">Reports</p>
                    <p class="mt-1 text-lg font-semibold text-quantum-primary">Student levels &amp; EXP</p>
                </Link>
            </div>

            <GraduationCertificatesChart
                v-if="showGraduationAnalytics"
                :analytics="graduationAnalytics"
                :filters="analyticsFilters"
            />
        </div>
    </AuthenticatedLayout>
</template>
