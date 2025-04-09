<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    subjectModule: Object,
    subjects: Array,
});

const form = useForm({
    name: props.subjectModule.name,
    subject_id: props.subjectModule.subject_id,
});

const successMessage = ref('');

const updateModule = () => {
    form.put(route('subject-modules.update', props.subjectModule.id), {
        onSuccess: () => {
            successMessage.value = 'Module updated successfully!';
            setTimeout(() => successMessage.value = '', 3000);
        },
    });
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Edit Subject Module" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Subject Module</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                        {{ successMessage }}
                    </div>

                    <form @submit.prevent="updateModule">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Module Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md"
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
                                Update Module
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
