<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const features = [
    {
        title: 'Module lessons & slides',
        description: 'Teachers publish structured lessons with activities tied to each module.',
        icon: 'lesson',
    },
    {
        title: 'Module & final quizzes',
        description: 'Students practice with scored quizzes in the app; results sync to the CMS.',
        icon: 'quiz',
    },
    {
        title: 'Progress you can see',
        description: 'Track scores, pass status, and answers—students see their own; staff see class reports.',
        icon: 'chart',
    },
    {
        title: 'Role-based dashboard',
        description: 'Admins manage users, teachers manage content, students view their results.',
        icon: 'roles',
    },
];

const steps = [
    { n: '1', title: 'Sign in', text: 'Use your school account on the web CMS or mobile app.' },
    { n: '2', title: 'Learn', text: 'Work through module lessons and activities in Quantum Realm.' },
    { n: '3', title: 'Quiz', text: 'Complete module quizzes and final subject checks.' },
    { n: '4', title: 'Review', text: 'See scores and feedback on the dashboard instantly.' },
];
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-slate-100 text-gray-900 antialiased">
        <!-- Nav -->
        <header
            class="sticky top-0 z-50 border-b border-quantum-stroke/30 bg-quantum-bg-mid/95 backdrop-blur-md"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2.5">
                    <ApplicationLogo class="h-9 w-9 shrink-0" />
                    <span class="text-sm font-semibold tracking-tight text-quantum-text-primary sm:text-base">Quantum Quizzes</span>
                </Link>

                <nav v-if="canLogin" class="flex items-center gap-2 sm:gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="qr-btn-primary">Dashboard</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="qr-btn-secondary border-quantum-stroke bg-quantum-surface text-quantum-text-primary hover:bg-quantum-surface-up">Log in</Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="qr-btn-primary qr-animate-cta"
                        >
                            Get started
                        </Link>
                        <Link v-if="!canRegister" :href="route('login')" class="qr-btn-primary">Log in</Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-quantum-stroke bg-gradient-to-br from-quantum-bg-dark via-quantum-bg-mid to-quantum-surface text-quantum-text-primary">
            <div
                class="pointer-events-none absolute -left-32 top-20 h-72 w-72 rounded-full bg-quantum-primary/30 blur-3xl"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-quantum-accent/20 blur-3xl"
                aria-hidden="true"
            />

            <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-16 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
                <div class="qr-animate-fade-up text-center lg:text-left">
                    <p
                        class="mb-4 inline-flex items-center rounded-full border border-quantum-accent/40 bg-quantum-accent/10 px-3 py-1 text-xs font-medium text-quantum-accent"
                    >
                        Quantum Realm · CMS & mobile learning
                    </p>
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-[3.25rem] lg:leading-tight">
                        Learn in the app.
                        <span class="text-quantum-accent">Manage on the web.</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-xl text-lg text-quantum-secondary lg:mx-0">
                        Interactive module lessons, quizzes, and live progress—built for students, teachers, and admins in one connected platform.
                    </p>
                    <div
                        v-if="canLogin && !$page.props.auth.user"
                        class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row lg:justify-start"
                    >
                        <Link :href="route('login')" class="qr-btn-primary qr-animate-cta w-full sm:w-auto px-8">
                            Sign in to CMS
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="qr-btn-secondary w-full sm:w-auto px-8"
                        >
                            Create account
                        </Link>
                    </div>
                    <Link
                        v-else-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="qr-btn-primary qr-animate-cta mt-8 inline-flex"
                    >
                        Open dashboard
                    </Link>
                </div>

                <!-- Animated hero visual -->
                <div class="relative mx-auto flex h-[320px] w-full max-w-md items-center justify-center sm:h-[360px] lg:max-w-none">
                    <div class="qr-orbit-ring absolute h-56 w-56 rounded-full border border-quantum-accent/40 sm:h-64 sm:w-64" aria-hidden="true">
                        <span class="qr-orbit-dot qr-orbit-dot-a" />
                        <span class="qr-orbit-dot qr-orbit-dot-b" />
                        <span class="qr-orbit-dot qr-orbit-dot-c" />
                    </div>
                    <div
                        class="qr-animate-float relative z-10 w-[min(100%,280px)] rounded-2xl border border-quantum-stroke bg-quantum-surface-up p-5 shadow-lg shadow-black/30"
                    >
                        <div class="mb-3 flex items-center justify-between text-xs font-medium text-quantum-secondary">
                            <span>Module quiz</span>
                            <span class="rounded-full bg-quantum-success/20 px-2 py-0.5 text-quantum-success">Live</span>
                        </div>
                        <p class="text-sm font-semibold text-quantum-text-primary">Which particle fits the quantum model?</p>
                        <ul class="mt-3 space-y-2 text-sm text-quantum-secondary">
                            <li class="rounded-lg border border-quantum-stroke bg-quantum-surface px-3 py-2">A · Photon</li>
                            <li class="rounded-lg border-2 border-quantum-accent bg-quantum-accent/10 px-3 py-2 font-medium text-quantum-accent">
                                B · Electron ✓
                            </li>
                            <li class="rounded-lg border border-quantum-stroke px-3 py-2">C · Neutron</li>
                        </ul>
                        <div class="mt-4">
                            <div class="mb-1 flex justify-between text-xs text-quantum-muted">
                                <span>Score</span>
                                <span class="font-semibold text-quantum-gold">12 / 15</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-quantum-stroke">
                                <div class="qr-animate-progress h-full rounded-full bg-gradient-to-r from-quantum-primary to-quantum-accent" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="qr-animate-float-delay absolute -right-2 top-4 hidden rounded-xl border border-quantum-stroke bg-quantum-surface-up px-3 py-2 shadow-md sm:block"
                        aria-hidden="true"
                    >
                        <p class="text-xs text-quantum-muted">Lesson complete</p>
                        <p class="text-sm font-bold text-quantum-accent">+1 module</p>
                    </div>
                    <div
                        class="qr-animate-float-slow absolute -left-2 bottom-8 hidden rounded-xl border border-quantum-stroke bg-quantum-surface-up px-3 py-2 shadow-md sm:block"
                        aria-hidden="true"
                    >
                        <p class="text-xs text-quantum-muted">Final quiz</p>
                        <p class="text-sm font-bold text-quantum-success">Passed</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Everything your class needs
                    </h2>
                    <p class="mt-4 text-gray-600">
                        Matches the mobile app palette—indigo actions, cyan highlights, and green quiz success.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <article
                        v-for="(item, index) in features"
                        :key="item.title"
                        class="qr-animate-fade-up rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-quantum-accent/40 hover:shadow-md"
                        :style="{ animationDelay: `${index * 0.1}s` }"
                    >
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-quantum-primary/10 text-quantum-primary"
                        >
                            <svg v-if="item.icon === 'lesson'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <svg v-else-if="item.icon === 'quiz'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <svg v-else-if="item.icon === 'chart'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ item.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ item.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="border-y border-quantum-stroke bg-gradient-to-br from-quantum-primary-dark via-quantum-bg-mid to-quantum-bg-dark py-16 text-white sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-center text-3xl font-bold sm:text-4xl">How it works</h2>
                <p class="mx-auto mt-4 max-w-2xl text-center text-quantum-secondary">
                    From first login to quiz results—four steps across mobile and web.
                </p>
                <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <li
                        v-for="(step, index) in steps"
                        :key="step.n"
                        class="qr-animate-fade-up rounded-xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm"
                        :style="{ animationDelay: `${0.15 + index * 0.12}s` }"
                    >
                        <span
                            class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-quantum-accent text-sm font-bold text-quantum-bg-dark"
                        >
                            {{ step.n }}
                        </span>
                        <h3 class="text-lg font-semibold">{{ step.title }}</h3>
                        <p class="mt-2 text-sm text-quantum-secondary">{{ step.text }}</p>
                    </li>
                </ol>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
                <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Ready to open the CMS?</h2>
                <p class="mt-3 text-gray-600">Sign in with your teacher, student, or admin account.</p>
                <div v-if="canLogin" class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <Link
                        :href="$page.props.auth.user ? route('dashboard') : route('login')"
                        class="qr-btn-primary qr-animate-cta px-8"
                    >
                        {{ $page.props.auth.user ? 'Go to dashboard' : 'Sign in' }}
                    </Link>
                </div>
            </div>
        </section>

        <footer class="border-t border-quantum-stroke/30 bg-quantum-bg-mid py-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 text-sm text-quantum-secondary sm:flex-row sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <ApplicationLogo class="h-6 w-6 shrink-0" />
                    <span>Quantum Quizzes · Learning CMS</span>
                </div>
                <p>© {{ new Date().getFullYear() }} Quantum Realm</p>
            </div>
        </footer>
    </div>
</template>
