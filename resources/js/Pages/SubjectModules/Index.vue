<script setup>
import { usePage, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    modules: Object,
    filters: Object,
    subjects: Array,
});

const searchQuery = ref(props.filters.search || "");
const subjectFilter = ref(props.filters.subject_id || "");

const goToPage = (page) => {
    if (page >= 1 && page <= props.modules.last_page) {
        const params = {
            search: searchQuery.value,
            subject_id: subjectFilter.value,
            page,
        };
        router.get(route("subject-modules.index"), params, {
            preserveState: true,
            replace: true
        });
    }
};

watch([searchQuery, subjectFilter], () => {
    router.get(route("subject-modules.index"), {
        search: searchQuery.value,
        subject_id: subjectFilter.value,
    }, {
        preserveState: true,
        replace: true
    });
});

const form = useForm({});
const deleteModule = (moduleId) => {
    if (confirm("Are you sure you want to delete this module?")) {
        form.delete(route("subject-modules.destroy", moduleId), {
            onSuccess: () => {
                location.reload();
            },
        });
    }
};
</script>

<template>
    <Head title="Subject Modules" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subject Modules</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="mb-4 flex gap-4">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search modules..."
                            class="border rounded p-2 w-full"
                        />

                        <select v-model="subjectFilter" class="border rounded p-2">
                            <option value="">All Subjects</option>
                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>

                        <a :href="route('subject-modules.create')" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Create New Module
                        </a>
                    </div>

                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2 text-left">Module Name</th>
                                <th class="border px-4 py-2 text-left">Subject</th>
                                <th class="border px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="module in modules.data" :key="module.id">
                                <td class="border px-4 py-2">{{ module.name }}</td>
                                <td class="border px-4 py-2">{{ module.subject.name }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a :href="route('subject-modules.edit', module.id)" class="text-yellow-500">Edit</a>
                                    <button @click="deleteModule(module.id)" class="text-red-500 ml-4">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-between">
                        <button
                            @click="goToPage(modules.current_page - 1)"
                            :disabled="modules.current_page === 1"
                            class="px-4 py-2 bg-gray-300 rounded"
                        >
                            Previous
                        </button>

                        <span class="px-4 py-2">Page {{ modules.current_page }} of {{ modules.last_page }}</span>

                        <button
                            @click="goToPage(modules.current_page + 1)"
                            :disabled="modules.current_page === modules.last_page"
                            class="px-4 py-2 bg-gray-300 rounded"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>