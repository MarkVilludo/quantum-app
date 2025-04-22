<script setup>
import { usePage, router, useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// const props = defineProps({ lessons: Object, search: String });

const props = defineProps({
    lessons: Object,
    filters: Object,
    years: Array,
    subjects: Array,
    modules: Array
});

const searchQuery = ref(props.filters.search || "");
const selectedYear = ref(props.filters.year_id || "");
const selectedSubject = ref(props.filters.subject_id || "");
const selectedModule = ref(props.filters.module_id || "");

const subjectOptions = ref([]);
const moduleOptions = ref([]);

const currentPage = ref(props.lessons.current_page); // Track current page

const goToPage = (page) => {
    if (page >= 1 && page <= props.lessons.last_page) {
        // Get the current URL and append the page parameter
        const currentUrl = new URL(window.location.href); // Get current URL
        currentUrl.searchParams.set('page', page); // Update the page parameter

        // Navigate to the new URL with the page parameter
        router.get(currentUrl.toString());
    }
};

// Fetch subjects when year changes
const fetchSubjects = async () => {
    if (selectedYear.value) {
        try {
            const response = await axios.get(route('subjects.get', { yearId: selectedYear.value }));
            subjectOptions.value = response.data;
            selectedSubject.value = "";
            selectedModule.value = "";
            moduleOptions.value = [];
        } catch (error) {
            console.error("Error fetching subjects:", error);
            subjectOptions.value = [];
        }
    } else {
        subjectOptions.value = [];
        selectedSubject.value = "";
        selectedModule.value = "";
        moduleOptions.value = [];
    }
};

// Fetch modules when subject changes
const fetchModules = async () => {
    if (selectedSubject.value) {
        try {
            const response = await axios.get(route('modules.get', { subject_id: selectedSubject.value }));
            moduleOptions.value = response.data;
            selectedModule.value = "";
        } catch (error) {
            console.error("Error fetching modules:", error);
        }
    } else {
        moduleOptions.value = [];
        selectedModule.value = "";
    }
};

// Watch for changes and update results dynamically
watch([searchQuery, selectedYear, selectedSubject, selectedModule], () => {
    router.get(route("lessons.index"), {
        search: searchQuery.value,
        year_id: selectedYear.value,
        subject_id: selectedSubject.value,
        module_id: selectedModule.value
    }, {
        preserveState: true,
        replace: true
    });
});


onMounted(() => {
    if (props.filters.year_id) {
        fetchSubjects();
    }
    if (props.filters.subject_id) {
        fetchModules();
    }
});

// Edit Lesson
const editLesson = (lessonId) => {
    router.get(route("lessons.edit", lessonId));
};

const form = useForm({});

const deleteLesson = (lessonId) => {
    if (confirm("Are you sure you want to delete this lesson?")) {
        form.delete(route("lessons.destroy", lessonId), {
            onSuccess: () => {
                location.reload();
            },
        });
    }
};
</script>


<template>
   <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lessons</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                    <div class="p-6 bg-white rounded-lg shadow">
                      <div class="mb-4 flex gap-4">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search lessons..."
                            class="border rounded"
                        />

                        <select v-model="selectedSubject" @change="fetchModules" class="border p-2 rounded">
                            <option value="">All Subjects</option>
                            <option v-for="subject in subjectOptions" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>

                        <select v-model="selectedModule" class="border p-2 rounded">
                            <option value="">All Modules</option>
                            <option v-for="module in moduleOptions" :key="module.id" :value="module.id">
                                {{ module.name }}
                            </option>
                        </select>

                        <a :href="route('lessons.create')" class="bg-blue-500 text-white px-4 py-4 rounded inline-block">Create New Lesson</a>
                    </div>


                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">Year</th>
                                <th class="border border-gray-300 px-4 py-2">Subject</th>
                                <th class="border border-gray-300 px-4 py-2">Module</th>
                                <th class="border border-gray-300 px-4 py-2">Name</th>
                                <th class="border border-gray-300 px-4 py-2">Content</th>
                                <th class="border border-gray-300 px-4 py-2">Activity</th>
                                <th class="border border-gray-300 px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in lessons.data" :key="lesson.id">
                                <td class="border border-gray-300 px-4 py-2">{{ lesson.year.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ lesson.subject?.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ lesson.module?.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ lesson.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <ul>
                                        <li v-for="(contentItem, keyT) in lesson.data" :key="contentItem.content">
                                            {{ keyT + 1 + '. ' + contentItem.content }}
                                        </li>
                                    </ul>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <ul>
                                        <li v-for="activity in lesson.activity" :key="activity.activity">
                                            <ul>
                                                <li><strong>Title:</strong> {{ activity.title }}</li>
                                                <li><strong>Question:</strong> {{ activity.question }}</li>
                                                <li><strong>Difficulty:</strong> {{ activity.difficulty }}</li>
                                                <li><strong>Correct Answer:</strong> {{ activity.correct_answer }}</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <a :href="route('lesson-quizzes.index', lesson.id)" class="text-green-500 ml-4">Quizzes</a>
                                    <button @click="editLesson(lesson.id)" class="text-yellow-500 ml-4">Edit</button>
                                    <button @click="deleteLesson(lesson.id)" class="text-red-500 ml-4">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-between">
                        <button @click="goToPage(lessons.current_page - 1)" :disabled="lessons.current_page === 1" class="px-4 py-2 bg-gray-300 rounded">
                            Previous
                        </button>

                        <span class="px-4 py-2">Page {{ lessons.current_page }} of {{ lessons.last_page }}</span>

                        <button @click="goToPage(lessons.current_page + 1)" :disabled="lessons.current_page === lessons.last_page" class="px-4 py-2 bg-gray-300 rounded">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>