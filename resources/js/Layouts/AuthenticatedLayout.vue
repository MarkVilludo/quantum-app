<script setup>
import { computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarNavLink from '@/Components/SidebarNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useAppSidebar } from '@/composables/useAppSidebar';

const { sidebarOpen, closeSidebar, toggleSidebar } = useAppSidebar();
const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role ?? '');
const roleLabel = computed(() => {
    const labels = { admin: 'Administrator', teacher: 'Teacher', student: 'Student' };
    return labels[role.value] ?? role.value;
});
const androidApp = computed(() => page.props.androidApp);
</script>

<template>
    <div class="min-h-screen bg-slate-100 antialiased">
        <!-- Mobile backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <button
                v-show="sidebarOpen"
                type="button"
                class="fixed inset-0 z-30 bg-gray-900/60 lg:hidden"
                aria-label="Close menu"
                @click="closeSidebar"
            />
        </Transition>

        <!-- Sidebar -->
        <aside
            id="app-sidebar"
            class="fixed top-0 left-0 z-40 flex h-[100dvh] w-72 max-w-[85vw] flex-col border-r border-quantum-stroke bg-quantum-bg-mid shadow-xl transition-transform duration-200 ease-out lg:w-64 lg:max-w-none lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-quantum-stroke px-4">
                <Link :href="route('dashboard')" class="flex min-w-0 items-center gap-2" @click="closeSidebar">
                    <ApplicationLogo class="h-8 w-8 shrink-0" />
                    <span class="truncate text-sm font-semibold text-quantum-text-primary">Quantum CMS</span>
                </Link>
                <button
                    type="button"
                    class="inline-flex shrink-0 rounded-lg p-2 text-quantum-secondary hover:bg-quantum-surface hover:text-quantum-text-primary lg:hidden"
                    aria-label="Close menu"
                    @click="closeSidebar"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav
                class="flex-1 space-y-1 overflow-y-auto overscroll-contain p-4"
                aria-label="Main navigation"
            >
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-quantum-muted">Main</p>

                <SidebarNavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                    @click="closeSidebar"
                >
                    <template #icon>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </template>
                    Dashboard
                </SidebarNavLink>

                <template v-if="role === 'admin'">
                    <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wide text-quantum-muted">Administration</p>
                    <SidebarNavLink
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        Users
                    </SidebarNavLink>
                    <SidebarNavLink
                        :href="route('admin.mobile-app.edit')"
                        :active="route().current('admin.mobile-app.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </template>
                        Android app
                    </SidebarNavLink>
                </template>

                <a
                    v-if="androidApp?.available"
                    href="/download/android"
                    class="flex min-h-[44px] items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-quantum-secondary hover:bg-quantum-surface hover:text-quantum-text-primary"
                    @click="closeSidebar"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download APK</span>
                </a>

                <template v-if="role === 'teacher'">
                    <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wide text-quantum-muted">Content</p>
                    <SidebarNavLink
                        :href="route('subjects.index')"
                        :active="route().current('subjects.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </template>
                        Courses
                    </SidebarNavLink>
                    <SidebarNavLink
                        :href="route('subject-modules.index')"
                        :active="route().current('subject-modules.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </template>
                        Subject Modules
                    </SidebarNavLink>
                    <SidebarNavLink
                        :href="route('lessons.index')"
                        :active="route().current('lessons.*') || route().current('lesson-quizzes.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </template>
                        Module Lessons
                    </SidebarNavLink>
                </template>

                <template v-if="role === 'teacher' || role === 'admin'">
                    <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wide text-quantum-muted">Reports</p>
                    <SidebarNavLink
                        :href="route('quiz-results.index')"
                        :active="route().current('quiz-results.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </template>
                        Student Quiz Results
                    </SidebarNavLink>
                    <SidebarNavLink
                        :href="route('student-progression.index')"
                        :active="route().current('student-progression.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </template>
                        Student Levels &amp; EXP
                    </SidebarNavLink>
                </template>

                <template v-if="role === 'student'">
                    <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wide text-quantum-muted">Learning</p>
                    <SidebarNavLink
                        :href="route('student-quiz-results.index')"
                        :active="route().current('student-quiz-results.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </template>
                        My Quiz Results
                    </SidebarNavLink>
                    <SidebarNavLink
                        :href="route('my-progression.index')"
                        :active="route().current('my-progression.*')"
                        @click="closeSidebar"
                    >
                        <template #icon>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </template>
                        My Levels &amp; Tiers
                    </SidebarNavLink>
                </template>
            </nav>

            <div class="shrink-0 border-t border-quantum-stroke p-4">
                <div class="mb-3 rounded-lg bg-quantum-surface/80 px-3 py-2.5">
                    <p class="truncate text-sm font-medium text-quantum-text-primary">{{ user.name }}</p>
                    <p class="truncate text-xs text-quantum-muted">{{ user.email }}</p>
                    <p class="mt-1 text-xs text-quantum-accent">{{ roleLabel }}</p>
                </div>
                <SidebarNavLink
                    :href="route('profile.edit')"
                    :active="route().current('profile.*')"
                    @click="closeSidebar"
                >
                    <template #icon>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </template>
                    Profile settings
                </SidebarNavLink>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-quantum-secondary hover:bg-quantum-surface hover:text-quantum-text-primary"
                    @click="closeSidebar"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log out
                </Link>
            </div>
        </aside>

        <!-- Main column -->
        <div class="lg:ml-64">
            <header class="sticky top-0 z-20 border-b border-quantum-stroke/20 bg-white/95 backdrop-blur-sm">
                <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex shrink-0 items-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden"
                            aria-controls="app-sidebar"
                            :aria-expanded="sidebarOpen ? 'true' : 'false'"
                            @click="toggleSidebar"
                        >
                            <span class="sr-only">{{ sidebarOpen ? 'Close menu' : 'Open menu' }}</span>
                            <svg v-if="!sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div v-if="$slots.header" class="min-w-0 flex-1">
                            <slot name="header" />
                        </div>
                        <p v-else class="truncate text-sm font-semibold text-gray-900 lg:hidden">Quantum CMS</p>
                    </div>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <span class="hidden sm:inline max-w-[10rem] truncate">{{ user.name }}</span>
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-quantum-primary/15 text-xs font-semibold text-quantum-primary">
                                    {{ user.name?.charAt(0)?.toUpperCase() }}
                                </span>
                            </button>
                        </template>
                        <template #content>
                            <div class="border-b border-gray-100 px-4 py-2">
                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ user.email }}</p>
                                <p class="mt-1 text-xs text-quantum-accent">{{ roleLabel }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <main class="min-h-[20rem]">
                <div :key="page.url" class="qr-page-content">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
