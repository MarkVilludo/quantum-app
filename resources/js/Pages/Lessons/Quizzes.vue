<script setup>
import { useForm } from "@inertiajs/vue3";
import { defineProps } from "vue";

defineProps({ lesson: Object, quizzes: Array });

const newQuiz = useForm({
  question: "",
  answer: "",
  success: false, // Ensure 'success' is initialized if you're checking it
});

const addQuiz = () => {
  newQuiz.post(route("lesson.quizzes.store", lesson.id), {
    onSuccess: () => newQuiz.reset(),
  });
};
</script>

<template>
  <div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Quizzes for {{ lesson.title }}</h2>

    <!-- Add Quiz Form -->
    <form @submit.prevent="addQuiz" class="mb-4">
      <input
        v-model="newQuiz.question"
        placeholder="Question"
        class="border p-2 rounded w-full mb-2"
      />
      <input
        v-model="newQuiz.answer"
        placeholder="Answer"
        class="border p-2 rounded w-full mb-2"
      />
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
        Add Quiz
      </button>
    </form>

    <!-- List Quizzes -->
    <div v-for="quiz in quizzes" :key="quiz.id" class="mb-6 p-4 border rounded">
      <p class="text-gray-600"><strong>Q:</strong> {{ quiz.question }}</p>
      <p class="text-gray-800"><strong>A:</strong> {{ quiz.answer }}</p>
    </div>

    <!-- <a :href="route('quizzes.index')" class="text-gray-500">Back to quizzes list</a> -->
  </div>
</template>
