<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    subject: Object,
    years: Array,
});

// Initialize form data with the subject values
const form = useForm({
    name: props.subject.name,
    year_id: props.subject.year_id,
});

const successMessage = ref(''); // Success message state

const submit = () => {
    // Perform PUT request to update the subject
    form.put(route('subjects.update', props.subject.id), {
        onSuccess: () => {
            successMessage.value = 'Subject updated successfully!'; // Set success message on successful update
            setTimeout(() => {
                successMessage.value = ''; // Clear success message after a few seconds
            }, 3000);
            router.push(route('subjects.index'));
        },
    });
};


const goBack = () => {
    window.history.back();
}


</script>

<template>
    <Head title="Edit Subject" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Subject</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <!-- Success Message -->
                    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                        {{ successMessage }}
                    </div>

                    <form @submit.prevent="submit">
                        <!-- Subject Name Input -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                id="name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                :class="{'border-red-500': form.errors.name}"
                                placeholder="Enter subject name"
                            />
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>

                        <!-- Year Select Dropdown -->
                        <div class="mb-4">
                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                            <select
                                v-model="form.year_id"
                                id="year"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                :class="{'border-red-500': form.errors.year_id}"
                            >
                                <option value="" disabled>Select Year</option>
                                <option v-for="year in props.years" :key="year.id" :value="year.id">
                                    {{ year.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.year_id" class="text-red-500 text-sm">{{ form.errors.year_id }}</span>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
                                Update Subject
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
