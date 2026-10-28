<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarNavLink from '@/Components/SidebarNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role ?? '');

const closeSidebar = () => {
    sidebarOpen.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 antialiased">
        <!-- Mobile backdrop -->
        <div
            v-show="sidebarOpen"
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            @click="closeSidebar"
        />

        <!-- Sidebar -->
        <aside
            class="fixed top-0 left-0 z-40 h-screen w-64 border-r border-quantum-stroke bg-quantum-bg-mid transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="flex h-16 items-center border-b border-quantum-stroke px-4">
                <Link :href="route('dashboard')" class="flex items-center gap-2" @click="closeSidebar">
                    <ApplicationLogo class="h-8 w-auto fill-current text-quantum-accent" />
                    <span class="text-sm font-semibold text-quantum-text-primary">Quantum CMS</span>
                </Link>
            </div>

            <nav class="space-y-1 p-4">
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
                </template>

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
                </template>
            </nav>
        </aside>

        <!-- Main column -->
        <div class="lg:ml-64">
            <header class="sticky top-0 z-20 border-b border-quantum-stroke/20 bg-white/95 backdrop-blur-sm">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden"
                            @click="sidebarOpen = !sidebarOpen"
                        >
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div v-if="$slots.header" class="min-w-0">
                            <slot name="header" />
                        </div>
                    </div>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <span class="hidden sm:inline">{{ user.name }}</span>
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-quantum-primary/15 text-xs font-semibold text-quantum-primary">
                                    {{ user.name?.charAt(0)?.toUpperCase() }}
                                </span>
                            </button>
                        </template>
                        <template #content>
                            <div class="border-b border-gray-100 px-4 py-2">
                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ user.email }}</p>
                                <p class="mt-1 text-xs capitalize text-quantum-accent">{{ role }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
