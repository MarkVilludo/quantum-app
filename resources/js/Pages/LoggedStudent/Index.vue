<script setup>
import { usePage, router, useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    results: Object,
    filters: Object,
    lessonId: Number, // Assuming lessonId is passed in the props
});

// The logged-in user's ID (assuming it's available in the props or the page context)
const userId = usePage().props.auth.user.id; // Access the authenticated user’s ID

const searchQuery = ref(props.filters.search || "");
const quizResult = ref(null); // Store the quiz result fetched from API

// Pagination logic
const goToPage = (page) => {
    if (page >= 1 && page <= props.results.last_page) {
        const params = {
            search: searchQuery.value,
            page,
            user_id: userId, // Filter only for the logged-in student
        };
        router.get(route("quiz-results.index"), params, {
            preserveState: true,
            replace: true,
        });
    }
};

watch([searchQuery], () => {
    router.get(route("quiz-results.index"), {
        search: searchQuery.value,
        user_id: userId, // Filter results for the logged-in student
    }, {
        preserveState: true,
        replace: true,
    });
});

onMounted(() => {
    // You can also fetch the results using an axios call if needed
    // Example: fetchQuizResults(userId);
});

// You can add a function here to fetch quiz results using axios if you want
// const fetchQuizResults = async (userId) => {
//     const response = await axios.get(`/api/quiz-results?user_id=${userId}`);
//     quizResult.value = response.data;
// };
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
                    <div class="mb-4 flex gap-4">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by lesson name"
                            class="border rounded p-2 w-full"
                        />
                    </div>

                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2 text-left">Lesson</th>
                                <th class="border px-4 py-2 text-center">Score</th>
                                <th class="border px-4 py-2 text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr v-for="result in results.data" :key="result.id">
                                <td class="border px-4 py-2">{{ result.lesson.name }}</td>
                                <td class="border px-4 py-2 text-center">{{ result.score }}/{{ result.total }}</td>
                                <td class="border px-4 py-2">
                                    <div v-if="result.answers" class="space-y-4">
                                        <div
                                            v-for="(answer, index) in result.answers"
                                            :key="answer.quiz_id"
                                            class="p-3 rounded border bg-gray-50"
                                        >
                                            <div class="font-semibold text-gray-700">
                                                Q{{ index + 1 }}: {{ answer.question_title }}
                                            </div>
                                            <div class="ml-4 text-sm">
                                                <p><strong>Your Answer:</strong> {{ answer.selected }}</p>
                                                <p><strong>Correct Answer:</strong> {{ answer.correct }}</p>
                                                <p>
                                                    <span :class="answer.is_correct ? 'text-green-600' : 'text-red-600'">
                                                        <strong>{{ answer.is_correct ? '✔ Correct' : '✘ Incorrect' }}</strong>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-between">
                        <button
                            @click="goToPage(results.current_page - 1)"
                            :disabled="results.current_page === 1"
                            class="px-4 py-2 bg-gray-300 rounded"
                        >
                            Previous
                        </button>

                        <span class="px-4 py-2">Page {{ results.current_page }} of {{ results.last_page }}</span>

                        <button
                            @click="goToPage(results.current_page + 1)"
                            :disabled="results.current_page === results.last_page"
                            class="px-4 py-2 bg-gray-300 rounded"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
