<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue"; // Correct import for ref
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    years: Array,
    subjects: Array,
    modules: Array,
});
const selectedYear = ref(null);
const selectedSubject = ref(null);
const moduleOptions = ref([]);
const subjectOptions = ref([]); // Store filtered subjects
const successMessage = ref(null);

const newLesson = useForm({
    year_id: null,
    subject_id: null,
    module_id: null,
    name: "",
    datax: [{ content: "" }], // Ensure this is an array
    activity: [{ title: "", question: "", difficulty: "", correct_answer: "" }]
});

const addContentSection = () => {
    if (newLesson.datax) {
        newLesson.datax.push({ content: "" });
    } else {
        console.error("newLesson.datax is not an array");
    }
};

const removeContentSection = (index) => {
    if (newLesson.datax.length > 1) {
        newLesson.datax.splice(index, 1);
    }
};

const addActivitySection = () => {
    newLesson.activity.push({ title: "", question: "", difficulty: "", correct_answer: "" });
};

const removeActivitySection = (index) => {
    if (newLesson.activity.length > 1) {
        newLesson.activity.splice(index, 1);
    }
};

const addLesson = () => {
    newLesson.post(route("lessons.store"), {
        onSuccess: () => {
            newLesson.reset();
            successMessage.value = "Lesson created successfully!";
            setTimeout(() => {
                successMessage.value = null;
            }, 3000); // Clear message after 3 seconds
        },
    });
};

const fetchSubjects = () => {
    if (newLesson.year_id) {
        axios.get(route('subjects.get', { yearId: newLesson.year_id }))
            .then(response => {
                subjectOptions.value = response.data;
                selectedSubject.value = null;
                moduleOptions.value = [];
                newLesson.module_id = null;
            })
            .catch(error => {
                console.error('Error fetching subjects:', error);
                subjectOptions.value = [];
            });
    } else {
        subjectOptions.value = [];
        selectedSubject.value = null;
        moduleOptions.value = [];
        newLesson.module_id = null;
    }
};


const fetchModules = async () => {
    if (newLesson.subject_id) {
        try {
            const response = await axios.get(route('modules.get', { subject_id: newLesson.subject_id }));
            moduleOptions.value = response.data;
        } catch (error) {
            console.error('Error fetching modules:', error);
        }
    } else {
        moduleOptions.value = [];
    }
};
onMounted(() => {
    if (props.subjects.length > 0) {
        selectedSubject.value = props.subjects[0].id;
        fetchSubjects();
        fetchModules();
    }
});

const goBack = () => {
    window.history.back();
}

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
                    <h2 class="text-xl font-semibold mb-4">Create a New Lesson</h2>
                    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ successMessage }}</span>
                    </div>

                    <form @submit.prevent="addLesson" class="mb-6">
                        <h1 class="text-xl font-semibold">Select Year:</h1>

                        <select v-model="newLesson.year_id" @change="fetchSubjects" class="border p-2 rounded w-full mb-2">
                            <option :value="null">Select a Year</option>
                            <option v-for="year in years" :key="year.id" :value="year.id">
                                {{ year.name }}
                            </option>
                        </select>


                        <h1 class="text-xl font-semibold">Select Subject:</h1>

                        
                        <select v-model="newLesson.subject_id" @change="fetchModules" class="border p-2 rounded w-full mb-2">
                            <option :value="null">Select a Subject</option>
                            <option v-for="subject in subjectOptions" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>

                        <h1 class="text-xl font-semibold">Select Module:</h1>

                        <select v-model="newLesson.module_id" id="module" class="border p-2 rounded w-full mb-2">
                            <option :value="null">Select a Module</option>
                            <option v-for="module in moduleOptions" :key="module.id" :value="module.id">
                                {{ module.name }}
                            </option>
                        </select>
                        <h1 class="text-xl font-semibold">Name:</h1>
                        <input v-model="newLesson.name" placeholder="Lesson Name" class="border p-2 rounded w-full mb-2" required />

                        <h1 class="text-xl font-semibold">Contents:</h1>
                        <div v-for="(section, index) in newLesson.datax" :key="index" class="mb-2 p-2 border rounded">
                            <textarea v-model="section.content" placeholder="Content" class="border p-2 rounded w-full mb-1" required></textarea>
                            <button type="button" @click="removeContentSection(index)" class="bg-red-500 text-white px-3 py-1 rounded" v-if="newLesson.datax.length > 1">
                                Remove
                            </button>
                        </div>

                        <button type="button" @click="addContentSection" class="bg-gray-500 text-white px-4 py-2 rounded mb-2">
                            + Add Content Section
                        </button>

                        <h1 class="text-xl font-semibold">Activities:</h1>
                        <div v-for="(activity, index) in newLesson.activity" :key="index" class="mb-2 p-2 border rounded">
                            <input v-model="activity.title" placeholder="Activity Title" class="border p-2 rounded w-full mb-1" required />
                            <textarea v-model="activity.question" placeholder="Question" class="border p-2 rounded w-full mb-1" required></textarea>
                            <select v-model="activity.difficulty" id="difficulty" class="border p-2 rounded w-full mb-1">
                                <option value="" disabled>Select</option>
                                <option value="Novice">Novice</option>
                                <option value="Expert">Expert</option>
                            </select>

                            <input v-model="activity.correct_answer" placeholder="Correct Answer" class="border p-2 rounded w-full mb-1" required />
                            <button type="button" @click="removeActivitySection(index)" class="bg-red-500 text-white px-3 py-1 rounded" v-if="newLesson.activity.length > 1">
                                Remove
                            </button>
                        </div>

                        <button type="button" @click="addActivitySection" class="bg-gray-500 text-white px-4 py-2 rounded mb-2">
                            + Add Activity Section
                        </button>

                        <div class="flex items-center gap-6 mt-6">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Save Lesson
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