<script setup>
import { useForm } from "@inertiajs/vue3";
import { defineProps } from "vue";

defineProps({ lesson: Object, activities: Array });

const newActivity = useForm({
    step: "",
});

const addActivity = () => {
    newActivity.post(route("lesson.activities.store", lesson.id), {
        onSuccess: () => newActivity.reset(),
    });
};
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Activities for {{ lesson.title }}</h2>

        <!-- Add Activity Form -->
        <form @submit.prevent="addActivity" class="mb-4">
            <input v-model="newActivity.step" placeholder="Activity Step" class="border p-2 rounded w-full mb-2" />
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Activity</button>
        </form>

        <!-- List Activities -->
        <div v-for="activity in activities" :key="activity.id" class="mb-6 p-4 border rounded">
            <h3 class="text-lg font-semibold">Step {{ activity.step }}</h3>
        </div>

        <a :href="route('lessons.index', 1)" class="text-gray-500">Back to Lessons</a>
    </div>
</template>
