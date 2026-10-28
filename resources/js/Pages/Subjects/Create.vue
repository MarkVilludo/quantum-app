<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
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
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Create subject</h1>
                <p class="text-sm text-gray-500">Add a new course for a year level</p>
            </div>
        </template>

        <PageContainer>
            <div v-if="successMessage" class="qr-alert-success">{{ successMessage }}</div>

            <form @submit.prevent="submitForm" class="max-w-xl space-y-5">
                <div>
                    <label for="name" class="qr-form-label">Subject name</label>
                    <input
                        type="text"
                        id="name"
                        v-model="form.name"
                        class="qr-input"
                        placeholder="Enter subject name"
                    />
                    <span v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</span>
                </div>

                <div>
                    <label for="year_id" class="qr-form-label">Year level</label>
                    <select id="year_id" v-model="form.year_id" class="qr-select w-full">
                        <option value="">Select year</option>
                        <option v-for="year in props.years" :key="year.id" :value="year.id">
                            {{ year.name }}
                        </option>
                    </select>
                    <span v-if="form.errors.year_id" class="mt-1 text-sm text-red-600">{{ form.errors.year_id }}</span>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button type="submit" class="qr-btn-primary">Create subject</button>
                    <button type="button" @click="goBack" class="qr-btn-secondary">Back</button>
                </div>
            </form>
        </PageContainer>
    </AuthenticatedLayout>
</template>
