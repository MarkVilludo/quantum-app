<script setup>
import { ref, watch, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PageContainer from '@/Components/PageContainer.vue';
import PaginationBar from '@/Components/PaginationBar.vue';
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    users: Object,
    filters: Object,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

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
    if (!confirm('Permanently delete this user? This cannot be undone.')) {
        return;
    }

    router.post(route('users.destroy.post', userId), {}, {
        preserveScroll: true,
    });
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
            <div
                v-if="flashSuccess"
                class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            >
                {{ flashSuccess }}
            </div>
            <div
                v-if="flashError"
                class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
            >
                {{ flashError }}
            </div>

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
                            <td class="text-center whitespace-nowrap">
                                <a :href="route('users.edit', user.id)" class="qr-btn-warning">Edit</a>
                                <button type="button" class="qr-btn-danger ml-3" @click="deleteUser(user.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="py-8 text-center text-sm text-gray-500">
                                No users match this search.
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
