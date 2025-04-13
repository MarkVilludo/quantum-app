<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    subjects: Array, // Passed from controller
    error : String
});

const form = useForm({
    name: '',
    subject_id: '',
});

const successMessage = ref('');

// Handle form submission
const submitForm = () => {
    form.post(route('subject-modules.store'), {
        onSuccess: () => {
        
            successMessage.value = 'Subject module created successfully!';
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};

// Go back
const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Create Subject Module" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Subject Module</h2>
        </template>

         <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <!-- Success Message -->
                    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                        {{ successMessage }}
                    </div>

                    <form @submit.prevent="submitForm">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Module Name</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md"
                                placeholder="Enter module name"
                            />
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>

                        <div class="mb-4">
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Select Subject</label>
                            <select
                                id="subject_id"
                                v-model="form.subject_id"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md"
                            >
                                <option value="">Select Subject</option>
                                <option v-for="subject in props.subjects" :key="subject.id" :value="subject.id">
                                    {{ subject.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.subject_id" class="text-red-500 text-sm">{{ form.errors.subject_id }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                                Create Module
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

<style scoped>
/* Add any custom styles if needed */
</style>
