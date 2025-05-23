<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    lesson: Object,
    quizzes: Object, // Paginated response: { data, links, meta }
    flash: Object
});

const successMessage = ref(null);

const deleteQuiz = (id) => {
    if (confirm("Are you sure you want to delete this quiz?")) {
        router.delete(route('lesson-quizzes.destroy', id), {
            preserveScroll: true
        });
        successMessage.value = "Quiz deleted successfully!";
        setTimeout(() => {
            successMessage.value = null;
        }, 3000);
    }
};
</script>

<template>
    <Head title="Lesson Quizzes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quizzes for Lesson: {{ props.lesson.name }}
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Success -->
                <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ successMessage }}</span>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-700">Quiz List</h3>
                        <a :href="route('lesson-quizzes.create', { lesson: props.lesson.id })" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            + Add Quiz
                        </a>
                    </div>

                    <div v-if="quizzes.data.length">
                        <div class="space-y-6">
                            <div v-for="quiz in quizzes.data" :key="quiz.id" class="border p-4 rounded shadow">
                                <h4 class="font-medium text-gray-800">Q: {{ quiz.question }}</h4>
                                <ul class="mt-2 text-gray-700 list-disc list-inside">
                                    <li :class="{ 'font-bold text-green-700': quiz.correct === 'answer1' }">A. {{ quiz.answer1 }}</li>
                                    <li :class="{ 'font-bold text-green-700': quiz.correct === 'answer2' }">B. {{ quiz.answer2 }}</li>
                                    <li :class="{ 'font-bold text-green-700': quiz.correct === 'answer3' }">C. {{ quiz.answer3 }}</li>
                                    <li :class="{ 'font-bold text-green-700': quiz.correct === 'answer4' }">D. {{ quiz.answer4 }}</li>
                                </ul>
                                <div class="mt-4 flex gap-4">
                                    <a :href="route('lesson-quizzes.edit', quiz.id)" class="text-blue-600 hover:underline">Edit</a>
                                    <button @click="() => deleteQuiz(quiz.id)" class="text-red-600 hover:underline">Delete</button>
                                </div>
                            </div>
                        </div>

                         <!-- Pagination Links -->
                        <div class="mt-6 flex flex-wrap gap-2">
                            <template v-for="link in quizzes.links" :key="link.label">
                                <button
                                    v-if="link.url"
                                    @click="router.visit(link.url, { preserveScroll: true })"
                                    v-html="link.label"
                                    :class="[
                                        'px-3 py-1 border rounded',
                                        link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
                                    ]"
                                ></button>
                            </template>
                        </div>
                    </div>
                    <div v-else class="text-gray-500">No quizzes added for this lesson yet.</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
