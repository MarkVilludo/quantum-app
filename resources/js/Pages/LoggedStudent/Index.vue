<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Quiz Results</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="mb-4 flex flex-wrap gap-3">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search subject or module…"
                            class="border rounded p-2 flex-1 min-w-[200px]"
                        />
                        <select v-model="subjectFilter" class="border rounded p-2">
                            <option value="">All subjects</option>
                            <option v-for="subject in subjectOptions" :key="subject.id" :value="String(subject.id)">
                                {{ subject?.name ?? 'Subject' }}
                            </option>
                        </select>
                        <select v-model="quizTypeFilter" class="border rounded p-2">
                            <option value="">All quiz types</option>
                            <option value="module">Module quiz</option>
                            <option value="subject_final">Final subject quiz</option>
                        </select>
                    </div>

                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-3 py-2 text-left">Year level</th>
                                <th class="border px-3 py-2 text-left">Subject</th>
                                <th class="border px-3 py-2 text-left">Module / quiz</th>
                                <th class="border px-3 py-2 text-left">Type</th>
                                <th class="border px-3 py-2 text-center">Score</th>
                                <th class="border px-3 py-2 text-center">Status</th>
                                <th class="border px-3 py-2 text-left">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!resultRows.length">
                                <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                                    No quiz results yet. Complete module quizzes in the app to see scores here.
                                </td>
                            </tr>
                            <tr v-for="result in resultRows" :key="result.id">
                                <td class="border px-3 py-2">{{ result.year_name }}</td>
                                <td class="border px-3 py-2">{{ result.subject_name }}</td>
                                <td class="border px-3 py-2">{{ result.module_name }}</td>
                                <td class="border px-3 py-2">{{ result.quiz_type_label }}</td>
                                <td class="border px-3 py-2 text-center">
                                    {{ result.score }}/{{ result.total }} ({{ result.percent }}%)
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <span v-if="result.passed === true" class="text-green-700 font-semibold">Passed</span>
                                    <span v-else-if="result.passed === false" class="text-red-600 font-semibold">Not passed</span>
                                    <span v-else class="text-gray-500">—</span>
                                </td>
                                <td class="border px-3 py-2">
                                    <details v-if="result.answers && result.answers.length">
                                        <summary class="cursor-pointer text-blue-600 text-xs">View answers</summary>
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

                    <div class="mt-4 flex justify-between">
                        <button
                            @click="goToPage(results.current_page - 1)"
                            :disabled="results.current_page === 1"
                            class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <span class="px-4 py-2">Page {{ results.current_page }} of {{ results.last_page }}</span>
                        <button
                            @click="goToPage(results.current_page + 1)"
                            :disabled="results.current_page === results.last_page"
                            class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
