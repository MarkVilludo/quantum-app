<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    results: Object,
    filters: Object,
    filterOptions: Object,
    viewerRole: String,
});

const searchQuery = ref(props.filters.search || "");
const userFilter = ref(props.filters.user_id || "");
const yearFilter = ref(props.filters.year_id || "");
const subjectFilter = ref(props.filters.subject_id || "");
const moduleFilter = ref(props.filters.module_id || "");
const quizTypeFilter = ref(props.filters.quiz_type || "");

const isAdmin = computed(() => props.viewerRole === 'admin');

const filterParams = () => ({
    search: searchQuery.value,
    user_id: userFilter.value,
    year_id: yearFilter.value,
    subject_id: subjectFilter.value,
    module_id: moduleFilter.value,
    quiz_type: quizTypeFilter.value,
});

const goToPage = (page) => {
    if (page >= 1 && page <= props.results.last_page) {
        router.get(route("quiz-results.index"), { ...filterParams(), page }, {
            preserveState: true,
            replace: true,
        });
    }
};

watch([searchQuery, userFilter, yearFilter, subjectFilter, moduleFilter, quizTypeFilter], () => {
    router.get(route("quiz-results.index"), filterParams(), {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="Student Quiz Results" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Student quiz results</h1>
                <p class="text-sm text-gray-500">Module and final quiz scores from the mobile app</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <div class="grid w-full grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search student, subject, module…"
                    class="qr-input md:col-span-2 lg:col-span-3"
                />

                <select v-model="userFilter" class="qr-select w-full">
                            <option value="">All students</option>
                            <option v-for="student in filterOptions.students" :key="student.id" :value="String(student.id)">
                                {{ student.name }}
                            </option>
                        </select>

                <select v-if="isAdmin" v-model="yearFilter" class="qr-select w-full">
                            <option value="">All year levels</option>
                            <option v-for="year in filterOptions.years" :key="year.id" :value="String(year.id)">
                                {{ year.name }}
                            </option>
                        </select>

                <select v-model="subjectFilter" class="qr-select w-full">
                            <option value="">All subjects</option>
                            <option v-for="subject in filterOptions.subjects" :key="subject.id" :value="String(subject.id)">
                                {{ subject.name }}
                            </option>
                        </select>

                <select v-model="moduleFilter" class="qr-select w-full">
                            <option value="">All modules</option>
                            <option v-for="mod in filterOptions.modules" :key="mod.id" :value="String(mod.id)">
                                {{ mod.name }}
                            </option>
                        </select>

                <select v-model="quizTypeFilter" class="qr-select w-full">
                    <option value="">All quiz types</option>
                    <option value="module">Module quiz</option>
                    <option value="subject_final">Final subject quiz</option>
                    <option value="lesson_legacy">Lesson quiz (legacy)</option>
                </select>
                </div>
            </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="qr-table text-sm">
                        <thead>
                            <tr>
                                <th>Student</th>
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
                            <tr v-if="!results.data.length">
                                <td colspan="8" class="py-8 text-center text-gray-500">
                                    No quiz results match your filters yet.
                                </td>
                            </tr>
                            <tr v-for="result in results.data" :key="result.id">
                                <td class="font-medium text-gray-900">{{ result.user.name }}</td>
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
                                    <details v-if="result.answers && result.answers.length" class="text-xs">
                                        <summary class="cursor-pointer text-quantum-primary">View answers</summary>
                                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto">
                                            <div
                                                v-for="(answer, index) in result.answers"
                                                :key="answer.quiz_id || index"
                                                class="p-2 rounded border bg-gray-50"
                                            >
                                                <div class="font-semibold text-gray-700">
                                                    Q{{ index + 1 }}: {{ answer.question }}
                                                </div>
                                                <p><strong>Selected:</strong> {{ answer.selected || '—' }}</p>
                                                <p><strong>Correct:</strong> {{ answer.correct }}</p>
                                                <p :class="answer.is_correct ? 'text-green-600' : 'text-red-600'">
                                                    {{ answer.is_correct ? '✔ Correct' : '✘ Incorrect' }}
                                                </p>
                                            </div>
                                        </div>
                                    </details>
                                    <span v-else class="text-gray-400">—</span>
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
        </PageContainer>
    </AuthenticatedLayout>
</template>
