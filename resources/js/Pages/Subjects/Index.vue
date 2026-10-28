<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    subjects: Object,
    filters: Object,
});

const searchQuery = ref(props.filters.search || "");

const goToPage = (page) => {
    if (page >= 1 && page <= props.subjects.last_page) {
        router.get(route("subjects.index"), { search: searchQuery.value, page }, {
            preserveState: true,
            replace: true,
        });
    }
};

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
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Subjects</h1>
                <p class="text-sm text-gray-500">Courses organized by year level</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search subjects…"
                    class="qr-input min-w-[200px] flex-1"
                />
                <a :href="route('subjects.create')" class="qr-btn-primary shrink-0">Create subject</a>
            </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="qr-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Year</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="subject in subjects.data" :key="subject.id">
                            <td class="font-medium text-gray-900">{{ subject.name }}</td>
                            <td>{{ subject.year.name }}</td>
                            <td class="text-center">
                                <a :href="route('subjects.edit', subject.id)" class="qr-btn-warning">Edit</a>
                                <button type="button" @click="deleteSubject(subject.id)" class="qr-btn-danger ml-3">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationBar
                :current-page="subjects.current_page"
                :last-page="subjects.last_page"
                @previous="goToPage(subjects.current_page - 1)"
                @next="goToPage(subjects.current_page + 1)"
            />
        </PageContainer>
    </AuthenticatedLayout>
</template>
