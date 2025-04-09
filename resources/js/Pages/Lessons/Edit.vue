<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';


const props = defineProps({
    years: Array,
    subjects: Array,
    modules: Array,
    lesson: Object, // Assuming you're passing the lesson data
});

const selectedYear = ref(props.lesson ? props.lesson.year_id : null);
const selectedSubject = ref(props.lesson ? props.lesson.subject_id : null);
const selectedModule = ref(props.lesson ? props.lesson.module_id : null);
const moduleOptions = ref([]);
const subjectOptions = ref([]);
const successMessage = ref(null);

const editLesson = useForm({
    year_id: props.lesson ? props.lesson.year_id : null,
    subject_id: props.lesson ? props.lesson.subject_id : null,
    module_id: props.lesson ? props.lesson.module_id : null,
    name: props.lesson ? props.lesson.name : "",
    datax: props.lesson ? props.lesson.data : [{ content: "" }],
    activity: props.lesson ? props.lesson.activity : [{ title: "", question: "", difficulty: "", correct_answer: "" }],
});

const addContentSection = () => {
    editLesson.datax.push({ content: "" });
};

const removeContentSection = (index) => {
    if (editLesson.datax.length > 1) {
        editLesson.datax.splice(index, 1);
    }
};

const addActivitySection = () => {
    editLesson.activity.push({ title: "", question: "", difficulty: "", correct_answer: "" });
};

const removeActivitySection = (index) => {
    if (editLesson.activity.length > 1) {
        editLesson.activity.splice(index, 1);
    }
};

const updateLesson = () => {
    editLesson.put(route("lessons.update", props.lesson.id), { // Use PUT for updates
        onSuccess: () => {
            successMessage.value = "Lesson updated successfully!";
            setTimeout(() => {
                successMessage.value = null;
            }, 3000);
        },
    });
};

const fetchSubjects = () => {
    if (editLesson.year_id) {
        axios.get(route('subjects.get', { yearId: editLesson.year_id }))
            .then(response => {
                subjectOptions.value = response.data;
                selectedSubject.value = null;
                moduleOptions.value = [];
                editLesson.module_id = null;
            })
            .catch(error => {
                console.error('Error fetching subjects:', error);
                subjectOptions.value = [];
            });
    } else {
        subjectOptions.value = [];
        selectedSubject.value = null;
        moduleOptions.value = [];
        editLesson.module_id = null;
    }
};

const fetchModules = () => {
    console.log(editLesson)
    if (editLesson.subject_id) {
        axios.get(route('modules.get', { subject_id: editLesson.subject_id }))
            .then(response => {
                moduleOptions.value = response.data;
            })
            .catch(error => {
                console.error('Error fetching modules:', error);
                moduleOptions.value = [];
            });
    } else {
        moduleOptions.value = [];
    }
    
};

const goBack = () => {
    window.history.back();
}

onMounted(() => {
    fetchSubjects();
});

// Computed property to get the selected module name
const selectedModuleName = computed(() => {
    const selectedModule = moduleOptions.value.find(module => module.id === editLesson.module_id);
    return selectedModule ? selectedModule.name : 'No module selected';
});

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
                    <h2 class="text-xl font-semibold mb-4">Edit Lesson</h2>
                    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ successMessage }}</span>
                    </div>

                    <form @submit.prevent="updateLesson" class="mb-6">
                        <h1 class="text-xl font-semibold">Select Year:</h1>
                        <select v-model="editLesson.year_id" @change="fetchSubjects" class="border p-2 rounded w-full mb-2">
                            <option :value="null">Select a Year</option>
                            <option v-for="year in years" :key="year.id" :value="year.id">
                                {{ year.name }}
                            </option>
                        </select>

                        <h1 class="text-xl font-semibold">Select Subject:</h1>
                        <select v-model="editLesson.subject_id" @change="fetchModules" class="border p-2 rounded w-full mb-2">
                            <option :value="null">Select a Subject</option>
                            <option v-for="subject in subjectOptions" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>

                        <h1 class="text-xl font-semibold">Select Module:</h1>
                        <select v-model="editLesson.module_id" id="module" class="border p-2 rounded w-full mb-2">
                            <option :value="null">Select a Module</option>
                            <option v-for="module in moduleOptions" :key="module.id" :value="module.id">
                                {{ module.name }}
                            </option>
                        </select>

                        <h1 class="text-xl font-semibold">Name:</h1>
                        <input v-model="editLesson.name" placeholder="Lesson Name" class="border p-2 rounded w-full mb-2" required />

                        <h1 class="text-xl font-semibold">Contents:</h1>
                        <div v-for="(section, index) in editLesson.datax" :key="index" class="mb-2 p-2 border rounded">
                            <textarea v-model="section.content" placeholder="Content" class="border p-2 rounded w-full mb-1" required></textarea>
                            <button type="button" @click="removeContentSection(index)" class="bg-red-500 text-white px-3 py-1 rounded" v-if="editLesson.datax.length > 1">
                                Remove
                            </button>
                        </div>

                        <button type="button" @click="addContentSection" class="bg-gray-500 text-white px-4 py-2 rounded mb-2">
                            + Add Content Section
                        </button>

                        <h1 class="text-xl font-semibold">Activities:</h1>
                        <div v-for="(activity, index) in editLesson.activity" :key="index" class="mb-2 p-2 border rounded">
                            <input v-model="activity.title" placeholder="Activity Title" class="border p-2 rounded w-full mb-1" required />
                            <textarea v-model="activity.question" placeholder="Question" class="border p-2 rounded w-full mb-1" required />
                            <select v-model="activity.difficulty" id="difficulty" class="border p-2 rounded w-full mb-1">
                                <option value="" disabled>Select</option>
                                <option value="Novice">Novice</option>
                                <option value="Expert">Expert</option>
                            </select>
                            <input v-model="activity.correct_answer" placeholder="Correct Answer" class="border p-2 rounded w-full mb-1" required />
                            <button type="button" @click="removeActivitySection(index)" class="bg-red-500 text-white px-3 py-1 rounded" v-if="editLesson.activity.length > 1">
                                Remove
                            </button>
                        </div>

                        <button type="button" @click="addActivitySection" class="bg-gray-500 text-white px-4 py-2 rounded mb-2">
                            + Add Activity Section
                        </button>

                        <div class="flex items-center gap-2 mt-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">
                                Update Lesson
                            </button>
                            <button @click="goBack" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                ← Back
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>