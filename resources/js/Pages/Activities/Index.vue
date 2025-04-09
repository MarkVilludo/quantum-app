<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

// Define props from Inertia
const props = defineProps({ lesson: Object });

// Reactive activities list to update UI dynamically
const activities = ref(props.lesson?.activities || []);

// Form for creating a new activity
const newActivity = useForm({
    lesson_id: props.lesson?.id,
    step: "",
});

// Function to add a new activity
const addActivity = () => {
    newActivity.post(route("lesson.activities.store", { lesson: props.lesson?.id }), {
        onSuccess: (response) => {
            activities.value.push(response.props.activity);
            newActivity.reset();
        },
    });
};

// Form for adding a question (specific to an activity)
const newQuestion = useForm({
    activity_id: null,
    question: "",
    answer: "true", // Default to 'true' to comply with enum type
});

// Function to add a question to an activity
const addQuestion = (activity) => {
    newQuestion.activity_id = activity.id;

    newQuestion.post(route("activities.questions.store", { activity: activity.id }), {
        onSuccess: (response) => {
            activity.questions.push(response.props.question);
            newQuestion.reset();
        },
    });
};
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Manage Activities for {{ props.lesson?.title }}</h2>

        <!-- Add New Activity -->
        <form @submit.prevent="addActivity" class="mb-4 bg-gray-100 p-4 rounded">
            <h3 class="text-lg font-semibold mb-2">Add New Activity</h3>
            <input v-model="newActivity.step" placeholder="Step Number" type="number" class="border p-2 rounded w-full mb-2" required />
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Add Activity</button>
        </form>

        <!-- List Activities and their Questions -->
        <div v-for="activity in activities" :key="activity.id" class="mb-6 p-4 border rounded bg-white">
            <h3 class="text-lg font-semibold">Step {{ activity.step }}</h3>

            <!-- List Questions for this Activity -->
            <ul v-if="activity.questions.length" class="list-disc list-inside mt-2">
                <li v-for="question in activity.questions" :key="question.id">
                    {{ question.question }} - <strong>{{ question.answer }}</strong>
                </li>
            </ul>
            <p v-else class="text-gray-500">No questions added yet.</p>

            <!-- Add New Question Form -->
            <form @submit.prevent="addQuestion(activity)" class="mt-4 bg-gray-50 p-3 rounded">
                <h4 class="text-md font-semibold mb-2">Add Question</h4>
                <input v-model="newQuestion.question" placeholder="Question" class="border p-2 rounded w-full mb-2" required />

                <!-- Dropdown for True/False Answer -->
                <select v-model="newQuestion.answer" class="border p-2 rounded w-full mb-2">
                    <option value="true">True</option>
                    <option value="false">False</option>
                </select>

                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded w-full">Add Question</button>
            </form>
        </div>
    </div>
</template>
