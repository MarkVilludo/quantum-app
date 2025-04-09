<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    quiz: Object,    // Quiz data passed from the controller
    lesson: Object,  // Lesson info
    errors: Object
});

const successMessage = ref(null);

const form = useForm({
    question: props.quiz.question,
    answer1: props.quiz.answer1,
    answer2: props.quiz.answer2,
    answer3: props.quiz.answer3,
    answer4: props.quiz.answer4,
    correct: props.quiz.correct,
    lesson_id: props.lesson.id
});

const submit = () => {
    form.post(route('lesson-quizzes.update', props.quiz.id), {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = "Quiz updated successfully!";
            setTimeout(() => {
                successMessage.value = null;
            }, 3000);
        }
    });
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Edit Quiz" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Quiz for Lesson: {{ props.lesson.name }}
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded shadow">
                    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ successMessage }}</span>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Question</label>
                            <input v-model="form.question" type="text" class="mt-1 block w-full border rounded px-3 py-2" />
                            <div v-if="form.errors.question" class="text-red-600 text-sm">{{ form.errors.question }}</div>
                        </div>

                        <div v-for="n in 4" :key="n">
                            <label :for="'answer' + n" class="block text-sm font-medium text-gray-700">
                                Answer {{ String.fromCharCode(64 + n) }}
                            </label>
                            <input :id="'answer' + n" v-model="form['answer' + n]" type="text"
                                   class="mt-1 block w-full border rounded px-3 py-2" />
                            <div v-if="form.errors['answer' + n]" class="text-red-600 text-sm">{{ form.errors['answer' + n] }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                            <select v-model="form.correct" class="mt-1 block w-full border rounded px-3 py-2">
                                <option value="answer1">A</option>
                                <option value="answer2">B</option>
                                <option value="answer3">C</option>
                                <option value="answer4">D</option>
                            </select>
                            <div v-if="form.errors.correct" class="text-red-600 text-sm">{{ form.errors.correct }}</div>
                        </div>

                        <div class="flex items-center gap-6 mt-6">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-500 hover:bg-green-600 rounded">
                                Update Quiz
                            </button>
                            <button type="button" @click="goBack" class="bg-gray-500 text-white px-4 py-2 rounded">
                                ← Back
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
