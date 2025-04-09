<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Define props passed from the backend (years data)
const props = defineProps({
    years: Array,
});

const form = useForm({
    name: '',
    year_id: '',
});

const successMessage = ref(''); // Success message state

// Handle form submission
const submitForm = () => {
    form.post(route('subjects.store'), {
        onSuccess: () => {
            successMessage.value = 'Subject created successfully!'; // Set success message on successful creation
            setTimeout(() => {
                successMessage.value = ''; // Clear success message after a few seconds
            }, 3000);
        },
        onError: (errors) => {
            console.log('Validation errors:', errors); // Log validation errors
        },
    });
};

// Handle going back to the previous page
const goBack = () => {
    window.history.back();
}
</script>

<template>
    <Head title="Create Subject" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Subject</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <!-- Success Message -->
                    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                        {{ successMessage }}
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitForm">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md"
                                placeholder="Enter subject name"
                            />
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>

                        <div class="mb-4">
                            <label for="year_id" class="block text-sm font-medium text-gray-700">Select Year</label>
                            <select
                                id="year_id"
                                v-model="form.year_id"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md"
                            >
                                <option value="">Select Year</option>
                                <option v-for="year in props.years" :key="year.id" :value="year.id">
                                    {{ year.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.year_id" class="text-red-500 text-sm">{{ form.errors.year_id }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                                Create Subject
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
