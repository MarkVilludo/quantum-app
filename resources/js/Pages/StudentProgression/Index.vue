<script setup>
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
import AsyncContent from '@/Components/AsyncContent.vue';
import SkeletonTable from '@/Components/Skeleton/SkeletonTable.vue';
import { useInertiaLoading } from '@/composables/useInertiaLoading';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    students: Object,
    filters: Object,
    filterOptions: Object,
    viewerRole: String,
    devProgressionToolsEnabled: Boolean,
});

const searchQuery = ref(props.filters?.search || '');
const yearFilter = ref(props.filters?.year_id || '');

const { navigating } = useInertiaLoading();
const isAdmin = computed(() => props.viewerRole === 'admin');
const studentRows = computed(() => props.students?.data ?? []);

const filterParams = () => ({
    search: searchQuery.value,
    year_id: yearFilter.value,
});

const goToPage = (page) => {
    if (page >= 1 && page <= props.students.last_page) {
        router.get(route('student-progression.index'), { ...filterParams(), page }, {
            preserveState: true,
            replace: true,
        });
    }
};

watch([searchQuery, yearFilter], () => {
    router.get(route('student-progression.index'), filterParams(), {
        preserveState: true,
        replace: true,
    });
});

const graduateDev = (student) => {
    if (
        !props.devProgressionToolsEnabled ||
        !confirm(
            `Dev tool: mark ${student.email} as fully graduated across all years in the CMS? This overwrites quiz/progress records.`,
        )
    ) {
        return;
    }
    router.post(route('student-progression.complete-all', student.id), {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Student Progression" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Student levels &amp; EXP</h1>
                <p class="text-sm text-gray-500">Live progression from the mobile app (Quantum XP)</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-2">
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by student name or email…"
                        class="qr-input md:col-span-2"
                    />
                    <select v-if="isAdmin" v-model="yearFilter" class="qr-select w-full">
                        <option value="">All year levels</option>
                        <option v-for="year in filterOptions.years" :key="year.id" :value="String(year.id)">
                            {{ year.name }}
                        </option>
                    </select>
                </div>
            </template>

            <AsyncContent :loading="navigating">
                <template #skeleton>
                    <SkeletonTable :rows="8" :columns="7" />
                </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="qr-table text-sm">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Year level</th>
                            <th class="text-center">Level</th>
                            <th>Title tier</th>
                            <th class="text-center">Quantum XP</th>
                            <th>Progress to next level</th>
                            <th v-if="devProgressionToolsEnabled" class="text-right">Dev</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!studentRows.length">
                            <td :colspan="devProgressionToolsEnabled ? 8 : 7" class="py-8 text-center text-gray-500">
                                No students match your filters yet.
                            </td>
                        </tr>
                        <tr v-for="student in studentRows" :key="student.id">
                            <td class="font-medium text-gray-900">{{ student.name }}</td>
                            <td class="text-gray-600">{{ student.email }}</td>
                            <td>{{ student.year_name }}</td>
                            <td class="text-center font-semibold text-quantum-primary">{{ student.level }}</td>
                            <td>
                                <span class="inline-flex rounded-full bg-quantum-primary/10 px-2.5 py-0.5 text-xs font-medium text-quantum-primary-dark">
                                    {{ student.title }}
                                </span>
                            </td>
                            <td class="text-center font-medium">{{ student.quantum_xp }}</td>
                            <td class="min-w-[140px]">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-quantum-primary to-quantum-accent transition-all"
                                            :style="{
                                                width: `${Math.min(100, Math.round((student.xp_in_level / Math.max(1, student.xp_for_next_level)) * 100))}%`,
                                            }"
                                        />
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                        {{ student.xp_in_level }}/{{ student.xp_for_next_level }}
                                    </span>
                                </div>
                            </td>
                            <td v-if="devProgressionToolsEnabled" class="text-right">
                                <button
                                    type="button"
                                    class="rounded-md border border-amber-300 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-900 hover:bg-amber-100"
                                    @click="graduateDev(student)"
                                >
                                    Complete all
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationBar
                :current-page="students.current_page"
                :last-page="students.last_page"
                @previous="goToPage(students.current_page - 1)"
                @next="goToPage(students.current_page + 1)"
            />
            </AsyncContent>
        </PageContainer>
    </AuthenticatedLayout>
</template>
