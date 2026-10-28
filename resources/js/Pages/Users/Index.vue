<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    users: Object,
    filters: Object,
});

const searchQuery = ref(props.filters.search || "");

watch(searchQuery, () => {
    router.get(route("users.index"), {
        search: searchQuery.value,
    }, {
        preserveState: true,
        replace: true,
    });
});

const deleteUser = (userId) => {
    if (confirm('Are you sure you want to delete this user?')) {
        const form = useForm({});
        form.delete(route('users.destroy', userId));
    }
};

</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Users</h1>
                <p class="text-sm text-gray-500">Admins, teachers, and students</p>
            </div>
        </template>

        <PageContainer>
            <template #toolbar>
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search users…"
                    class="qr-input min-w-[200px] flex-1"
                />
                <a :href="route('users.create')" class="qr-btn-primary shrink-0">Create user</a>
            </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="qr-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Year / year handled</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="font-medium text-gray-900">{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td class="capitalize">{{ user.role }}</td>
                            <td class="capitalize">{{ user.year?.name ?? '—' }}</td>
                            <td class="text-center">
                                <a :href="route('users.edit', user.id)" class="qr-btn-warning">Edit</a>
                                <button type="button" @click="deleteUser(user.id)" class="qr-btn-danger ml-3">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationBar
                :current-page="users.current_page"
                :last-page="users.last_page"
                @previous="router.get(route('users.index', { page: users.current_page - 1, search: searchQuery }))"
                @next="router.get(route('users.index', { page: users.current_page + 1, search: searchQuery }))"
            />
        </PageContainer>
    </AuthenticatedLayout>
</template>
