<script setup>
    import { usePage, router, useForm } from "@inertiajs/vue3";
    import { ref } from "vue";

    const props = defineProps({ lessons: Object }); // Paginated lessons
    const currentPage = ref(props.lessons.current_page); // Track current page

    const newLesson = useForm({
        title: "",
        content: [{ content: "" }],
    });

    // Add a new content section dynamically
    const addContentSection = () => {
        newLesson.content.push({ content: "" });
    };

    // Remove a specific content section
    const removeContentSection = (index) => {
        if (newLesson.content.length > 1) {
            newLesson.content.splice(index, 1);
        }
    };

    // Submit new lesson
    const addLesson = () => {
        newLesson.post(route("lessons.store"), {
            onSuccess: () => newLesson.reset(), // Reset form on success
        });
    };

    // Pagination logic
    const goToPage = (page) => {
        if (page >= 1 && page <= props.lessons.last_page) {
            router.get(route("lessons.index", { page }));
        }
    };
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Lessons</h2>

        <!-- Add Lesson Form -->
        <form @submit.prevent="addLesson" class="mb-6">
            <input v-model="newLesson.title" placeholder="Lesson Title" class="border p-2 rounded w-full mb-2"
                required />

            <!-- Content Sections -->
            <div v-for="(section, index) in newLesson.content" :key="index" class="mb-2 p-2 border rounded">
                <textarea v-model="section.content" placeholder="Lesson Content" class="border p-2 rounded w-full mb-1"
                    required></textarea>
                <button type="button" @click="removeContentSection(index)"
                    class="bg-red-500 text-white px-3 py-1 rounded" v-if="newLesson.content.length > 1">
                    Remove
                </button>
            </div>

            <button type="button" @click="addContentSection" class="bg-gray-500 text-white px-4 py-2 rounded mb-2">
                + Add Content Section
            </button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">
                Add Lesson
            </button>
        </form>

        <!-- Lessons Table -->
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Title</th>
                    <th class="border border-gray-300 px-4 py-2">Content</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="lesson in lessons.data" :key="lesson.id">
                    <td class="border border-gray-300 px-4 py-2">{{ lesson.title }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <ul>
                            <li v-for="contentItem in JSON.parse(lesson.content)" :key="contentItem.content">
                                {{ contentItem.content }}
                            </li>
                        </ul>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a :href="route('lesson.activities.index', lesson.id)" class="text-blue-500">Activities</a>
                        <a :href="route('lesson.quizzes.index', lesson.id)" class="text-green-500 ml-4">Quizzes</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="mt-4 flex justify-between">
            <button @click="goToPage(lessons.current_page - 1)" :disabled="lessons.current_page === 1"
                class="px-4 py-2 bg-gray-300 rounded">
                Previous
            </button>

            <span class="px-4 py-2">Page {{ lessons.current_page }} of {{ lessons.last_page }}</span>

            <button @click="goToPage(lessons.current_page + 1)" :disabled="lessons.current_page === lessons.last_page"
                class="px-4 py-2 bg-gray-300 rounded">
                Next
            </button>
        </div>
    </div>
</template>