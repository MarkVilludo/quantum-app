<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Quiz Results</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <p class="text-sm text-gray-600 mb-4">
                        Module and final subject quiz scores from the mobile app appear here automatically.
                    </p>

                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search student, subject, module…"
                            class="border rounded p-2 w-full"
                        />

                        <select v-model="userFilter" class="border rounded p-2 w-full">
                            <option value="">All students</option>
                            <option v-for="student in filterOptions.students" :key="student.id" :value="String(student.id)">
                                {{ student.name }}
                            </option>
                        </select>

                        <select v-if="isAdmin" v-model="yearFilter" class="border rounded p-2 w-full">
                            <option value="">All year levels</option>
                            <option v-for="year in filterOptions.years" :key="year.id" :value="String(year.id)">
                                {{ year.name }}
                            </option>
                        </select>

                        <select v-model="subjectFilter" class="border rounded p-2 w-full">
                            <option value="">All subjects</option>
                            <option v-for="subject in filterOptions.subjects" :key="subject.id" :value="String(subject.id)">
                                {{ subject.name }}
                            </option>
                        </select>

                        <select v-model="moduleFilter" class="border rounded p-2 w-full">
                            <option value="">All modules</option>
                            <option v-for="mod in filterOptions.modules" :key="mod.id" :value="String(mod.id)">
                                {{ mod.name }}
                            </option>
                        </select>

                        <select v-model="quizTypeFilter" class="border rounded p-2 w-full">
                            <option value="">All quiz types</option>
                            <option value="module">Module quiz</option>
                            <option value="subject_final">Final subject quiz</option>
                            <option value="lesson_legacy">Lesson quiz (legacy)</option>
                        </select>
                    </div>

                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-3 py-2 text-left">Student</th>
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
                            <tr v-if="!results.data.length">
                                <td colspan="8" class="border px-4 py-6 text-center text-gray-500">
                                    No quiz results match your filters yet.
                                </td>
                            </tr>
                            <tr v-for="result in results.data" :key="result.id">
                                <td class="border px-3 py-2">{{ result.user.name }}</td>
                                <td class="border px-3 py-2">{{ result.year_name }}</td>
                                <td class="border px-3 py-2">{{ result.subject_name }}</td>
                                <td class="border px-3 py-2">{{ result.module_name }}</td>
                                <td class="border px-3 py-2">{{ result.quiz_type_label }}</td>
                                <td class="border px-3 py-2 text-center">
                                    {{ result.score }}/{{ result.total }} ({{ result.percent }}%)
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <span
                                        v-if="result.passed === true"
                                        class="text-green-700 font-semibold"
                                    >Passed</span>
                                    <span
                                        v-else-if="result.passed === false"
                                        class="text-red-600 font-semibold"
                                    >Not passed</span>
                                    <span v-else class="text-gray-500">—</span>
                                </td>
                                <td class="border px-3 py-2">
                                    <details v-if="result.answers && result.answers.length" class="text-xs">
                                        <summary class="cursor-pointer text-blue-600">View answers</summary>
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
