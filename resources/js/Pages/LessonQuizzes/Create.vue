<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue"; // Correct import for ref
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    lesson: Object, // Passed from the controller
    errors: Object  // Validation errors from Laravel
});
const successMessage = ref(null);

const form = useForm({
    lesson_id: props.lesson.id,
    question: '',
    answer1: '',
    answer2: '',
    answer3: '',
    answer4: '',
    correct: 'answer1'
});


const submit = () => {
    form.post(route('lesson-quizzes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = "Lesson created successfully!";
            setTimeout(() => {
                successMessage.value = null;
            }, 3000); // Clear message after 3 seconds
            form.reset();
        }
    });
};

const goBack = () => {
    window.history.back();
}
</script>

<template>
    <Head title="Create Quiz" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Quiz for Lesson: {{ props.lesson.name }}
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded shadow">
                    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
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

                            <button type="submit"
                                    class="px-4 py-2 text-white bg-blue-500 hover:bg-green-600 rounded">
                                Save Quiz
                            </button>
                            <button type="button" @click="goBack" class="bg-gray-500 text-white px-4 py-2 rounded">
                                ← Back to list
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
