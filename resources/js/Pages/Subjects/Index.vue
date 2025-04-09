<script setup>
import { usePage, router, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    subjects: Array,
    filters: Object,
});


const searchQuery = ref(props.filters.search || "");

const currentPage = ref(props.subjects.current_page); // Track current page

const goToPage = (page) => {
    if (page >= 1 && page <= props.subjects.last_page) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('page', page);
        router.get(currentUrl.toString());
    }
};

// Watch for search query change
watch(searchQuery, () => {
    router.get(route("subjects.index"), {
        search: searchQuery.value
    }, {
        preserveState: true,
        replace: true
    });
});

const form = useForm({});
const deleteSubject = (subjectId) => {
     if (confirm("Are you sure you want to delete this subject?")) {
        form.delete(route("subjects.destroy", subjectId), {
            onSuccess: () => {
                location.reload();
            },
        });
    }
}

</script>

<template>
    <Head title="Subjects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subjects</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="mb-4 flex gap-6">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search subjects..."
                            class="border rounded p-2"
                        />
                      <a :href="route('subjects.create')" class="bg-blue-500 text-white px-4 py-4 rounded inline-block">Create New Subject</a>

                    </div>

                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                                <th class="border border-gray-300 px-4 py-2">Year</th>
                                <th class="border border-gray-300 px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="subject in subjects.data" :key="subject.id">
                                <td class="border border-gray-300 px-4 py-2">{{ subject.name }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ subject.year.name }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <a :href="route('subjects.edit', subject.id)" class="text-yellow-500 ml-4">Edit</a>
                                    <button @click="deleteSubject(subject.id)" class="text-red-500 ml-4">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-between">
                        <button @click="goToPage(subjects.current_page - 1)" :disabled="subjects.current_page === 1" class="px-4 py-2 bg-gray-300 rounded">
                            Previous
                        </button>

                        <span class="px-4 py-2">Page {{ subjects.current_page }} of {{ subjects.last_page }}</span>

                        <button @click="goToPage(subjects.current_page + 1)" :disabled="subjects.current_page === subjects.last_page" class="px-4 py-2 bg-gray-300 rounded">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
