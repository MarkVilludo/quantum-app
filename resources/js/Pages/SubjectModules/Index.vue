<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
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
        router.get(route("subject-modules.index"), {
            search: searchQuery.value,
            subject_id: subjectFilter.value,
            page,
        }, {
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
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Subject modules</h1>
                <p class="text-sm text-gray-500">Group lessons within each course</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search modules…"
                    class="qr-input min-w-[180px] flex-1"
                />
                <select v-model="subjectFilter" class="qr-select">
                    <option value="">All subjects</option>
                    <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                        {{ subject.name }}
                    </option>
                </select>
                <a :href="route('subject-modules.create')" class="qr-btn-primary shrink-0">Create module</a>
            </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="qr-table">
                    <thead>
                        <tr>
                            <th>Module name</th>
                            <th>Subject</th>
                            <th>Year</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="module in modules.data" :key="module.id">
                            <td class="font-medium text-gray-900">{{ module.name }}</td>
                            <td>{{ module.subject.name }}</td>
                            <td>{{ module?.subject?.year?.name }}</td>
                            <td class="text-center">
                                <a :href="route('subject-modules.edit', module.id)" class="qr-btn-warning">Edit</a>
                                <button type="button" @click="deleteModule(module.id)" class="qr-btn-danger ml-3">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationBar
                :current-page="modules.current_page"
                :last-page="modules.last_page"
                @previous="goToPage(modules.current_page - 1)"
                @next="goToPage(modules.current_page + 1)"
            />
        </PageContainer>
    </AuthenticatedLayout>
</template>
