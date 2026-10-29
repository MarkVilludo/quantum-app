<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
import AsyncContent from '@/Components/AsyncContent.vue';
import SkeletonTable from '@/Components/Skeleton/SkeletonTable.vue';
import { useInertiaLoading } from '@/composables/useInertiaLoading';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    results: {
        type: Object,
        default: () => ({ data: [], current_page: 1, last_page: 1 }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    filterOptions: {
        type: Object,
        default: () => ({ subjects: [], years: [], modules: [], students: [] }),
    },
});

const searchQuery = ref(props.filters?.search || "");
const subjectFilter = ref(props.filters?.subject_id || "");
const quizTypeFilter = ref(props.filters?.quiz_type || "");

const { navigating } = useInertiaLoading();
const subjectOptions = computed(() => props.filterOptions?.subjects ?? []);
const resultRows = computed(() => props.results?.data ?? []);

const filterParams = () => ({
    search: searchQuery.value,
    subject_id: subjectFilter.value,
    quiz_type: quizTypeFilter.value,
});

const goToPage = (page) => {
    if (page >= 1 && page <= props.results.last_page) {
        router.get(route("student-quiz-results.index"), { ...filterParams(), page }, {
            preserveState: true,
            replace: true,
        });
    }
};

watch([searchQuery, subjectFilter, quizTypeFilter], () => {
    router.get(route("student-quiz-results.index"), filterParams(), {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="My Quiz Results" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">My quiz results</h1>
                <p class="text-sm text-gray-500">Scores from module and final quizzes in the app</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search subject or module…"
                    class="qr-input min-w-[200px] flex-1"
                />
                <select v-model="subjectFilter" class="qr-select">
                    <option value="">All subjects</option>
                    <option v-for="subject in subjectOptions" :key="subject.id" :value="String(subject.id)">
                        {{ subject?.name ?? 'Subject' }}
                    </option>
                </select>
                <select v-model="quizTypeFilter" class="qr-select">
                    <option value="">All quiz types</option>
                    <option value="module">Module quiz</option>
                    <option value="subject_final">Final subject quiz</option>
                </select>
            </template>

            <AsyncContent :loading="navigating">
                <template #skeleton>
                    <SkeletonTable :rows="6" :columns="7" />
                </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="qr-table text-sm">
                        <thead>
                            <tr>
                                <th>Year level</th>
                                <th>Subject</th>
                                <th>Module / quiz</th>
                                <th>Type</th>
                                <th class="text-center">Score</th>
                                <th class="text-center">Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!resultRows.length">
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    No quiz results yet. Complete module quizzes in the app to see scores here.
                                </td>
                            </tr>
                            <tr v-for="result in resultRows" :key="result.id">
                                <td>{{ result.year_name }}</td>
                                <td>{{ result.subject_name }}</td>
                                <td>{{ result.module_name }}</td>
                                <td>{{ result.quiz_type_label }}</td>
                                <td class="text-center">
                                    {{ result.score }}/{{ result.total }} ({{ result.percent }}%)
                                </td>
                                <td class="text-center">
                                    <span v-if="result.passed === true" class="font-semibold text-green-700">Passed</span>
                                    <span v-else-if="result.passed === false" class="font-semibold text-red-600">Not passed</span>
                                    <span v-else class="text-gray-500">—</span>
                                </td>
                                <td>
                                    <details v-if="result.answers && result.answers.length">
                                        <summary class="cursor-pointer text-xs text-quantum-primary">View answers</summary>
                                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto text-xs">
                                            <div
                                                v-for="(answer, index) in result.answers"
                                                :key="answer.quiz_id || index"
                                                class="p-2 rounded border bg-gray-50"
                                            >
                                                <div class="font-semibold">Q{{ index + 1 }}: {{ answer.question }}</div>
                                                <p>Your answer: {{ answer.selected || '—' }}</p>
                                                <p>Correct: {{ answer.correct }}</p>
                                            </div>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>

            <PaginationBar
                :current-page="results.current_page"
                :last-page="results.last_page"
                @previous="goToPage(results.current_page - 1)"
                @next="goToPage(results.current_page + 1)"
            />
            </AsyncContent>
        </PageContainer>
    </AuthenticatedLayout>
</template>
