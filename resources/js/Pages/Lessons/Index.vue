<script setup>
import { usePage, router, useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
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
   <Head title="Module Lessons" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Module lessons</h1>
                <p class="text-sm text-gray-500">Slides, activities, and linked quizzes</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search lessons…"
                    class="qr-input min-w-[160px] flex-1"
                />
                <select v-model="selectedSubject" @change="fetchModules" class="qr-select">
                    <option value="">All subjects</option>
                    <option v-for="subject in subjectOptions" :key="subject.id" :value="subject.id">
                        {{ subject.name }}
                    </option>
                </select>
                <select v-model="selectedModule" class="qr-select">
                    <option value="">All modules</option>
                    <option v-for="module in moduleOptions" :key="module.id" :value="module.id">
                        {{ module.name }}
                    </option>
                </select>
                <a :href="route('lessons.create')" class="qr-btn-primary shrink-0">Create lesson</a>
            </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="qr-table text-sm">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Subject</th>
                                <th>Module</th>
                                <th>Name</th>
                                <th>Content</th>
                                <th>Activity</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lesson in lessons.data" :key="lesson.id">
                                <td>{{ lesson.year.name }}</td>
                                <td>{{ lesson.subject?.name }}</td>
                                <td>{{ lesson.module?.name }}</td>
                                <td class="font-medium text-gray-900">{{ lesson.name }}</td>
                                <td>
                                    <ul>
                                        <li v-for="(contentItem, keyT) in lesson.data" :key="contentItem.content">
                                            {{ keyT + 1 + '. ' + contentItem.content }}
                                        </li>
                                    </ul>
                                </td>
                                <td>
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
                                <td class="text-center whitespace-nowrap">
                                    <a :href="route('lesson-quizzes.index', lesson.id)" class="qr-btn-success">Quizzes</a>
                                    <button type="button" @click="editLesson(lesson.id)" class="qr-btn-warning ml-3">Edit</button>
                                    <button type="button" @click="deleteLesson(lesson.id)" class="qr-btn-danger ml-3">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>

            <PaginationBar
                :current-page="lessons.current_page"
                :last-page="lessons.last_page"
                @previous="goToPage(lessons.current_page - 1)"
                @next="goToPage(lessons.current_page + 1)"
            />
        </PageContainer>
    </AuthenticatedLayout>
</template>