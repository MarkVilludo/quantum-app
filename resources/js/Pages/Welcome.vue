<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <!-- Header -->
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Log in</Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>

 

  <!-- Hero Section -->
  <section class="flex-1 flex items-center justify-center text-center px-4 py-24 bg-gradient-to-br from-indigo-200 to-white">
    <div class="max-w-3xl">
      <h2 class="text-4xl md:text-5xl font-extrabold text-indigo-700 leading-tight">
        Test Your Knowledge with Interactive Quizzes!
      </h2>
      <p class="mt-6 text-lg text-gray-700">
        Challenge yourself with engaging quizzes, improve your learning. 
      </p>
      <div class="mt-10 flex justify-center gap-4">
        <a href="/quizzes" class="bg-indigo-600 text-white px-6 py-3 rounded-xl shadow hover:bg-indigo-700 transition">
          Start Quiz
        </a>
        <a href="/register" class="bg-gray-200 text-indigo-700 px-6 py-3 rounded-xl shadow hover:bg-gray-300 transition">
          Sign Up
        </a>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h3 class="text-3xl font-bold text-gray-800 mb-12">Why Choose Quantum Quizzes App?</h3>
      <div class="grid md:grid-cols-2 gap-10">
        <div class="p-6 border rounded-xl shadow hover:shadow-md transition">
          <div class="text-indigo-600 text-4xl mb-4">🧠</div>
          <h4 class="text-xl font-semibold mb-2">Smart Questions</h4>
          <p class="text-gray-600">Get curated questions that adapt to your learning progress.</p>
        </div>
        <div class="p-6 border rounded-xl shadow hover:shadow-md transition">
          <div class="text-indigo-600 text-4xl mb-4">📊</div>
          <h4 class="text-xl font-semibold mb-2">Progress Tracking</h4>
          <p class="text-gray-600">Track your results, scores in each lessons</p>
        </div>
      </div>
    </div>
  </section>

    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
