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
const showDeleted = ref(Boolean(props.filters.show_deleted));

const listParams = () => ({
    search: searchQuery.value || undefined,
    show_deleted: showDeleted.value ? 1 : undefined,
});

watch(searchQuery, () => {
    router.get(route("users.index"), listParams(), {
        preserveState: true,
        replace: true,
    });
});

watch(showDeleted, () => {
    router.get(route("users.index"), listParams(), {
        preserveState: true,
        replace: true,
    });
});

const deleteUser = (userId, permanent = false) => {
    const message = permanent
        ? 'Permanently delete this user? This cannot be undone.'
        : 'Remove this user from the active list? You can restore them under “Show deleted users”.';
    if (!confirm(message)) {
        return;
    }

    router.post(route('users.destroy.post', userId), listParams(), {
        preserveScroll: true,
    });
};

const restoreUser = (userId) => {
    router.post(route('users.restore', userId), {}, {
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
                <label class="flex shrink-0 cursor-pointer items-center gap-2 text-sm text-gray-600">
                    <input v-model="showDeleted" type="checkbox" class="rounded border-gray-300" />
                    Show deleted users
                </label>
                <a :href="route('users.create')" class="qr-btn-primary shrink-0">Create user</a>
            </template>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="qr-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th v-if="showDeleted">Status</th>
                            <th>Year / year handled</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            :class="{ 'bg-red-50/80': user.deleted_at }"
                        >
                            <td class="font-medium text-gray-900">{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td class="capitalize">{{ user.role }}</td>
                            <td v-if="showDeleted">
                                <span
                                    v-if="user.deleted_at"
                                    class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800"
                                >
                                    Deleted
                                </span>
                                <span v-else class="text-xs text-gray-500">Active</span>
                            </td>
                            <td class="capitalize">{{ user.year?.name ?? '—' }}</td>
                            <td class="text-center whitespace-nowrap">
                                <template v-if="user.deleted_at">
                                    <button type="button" class="qr-btn-primary" @click="restoreUser(user.id)">
                                        Restore
                                    </button>
                                    <button type="button" class="qr-btn-danger ml-2" @click="deleteUser(user.id, true)">
                                        Delete forever
                                    </button>
                                </template>
                                <template v-else>
                                    <a :href="route('users.edit', user.id)" class="qr-btn-warning">Edit</a>
                                    <button type="button" class="qr-btn-danger ml-3" @click="deleteUser(user.id)">
                                        Delete
                                    </button>
                                </template>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td :colspan="showDeleted ? 6 : 5" class="py-8 text-center text-sm text-gray-500">
                                No users match this view.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <PaginationBar
                :current-page="users.current_page"
                :last-page="users.last_page"
                @previous="router.get(route('users.index', { ...listParams(), page: users.current_page - 1 }))"
                @next="router.get(route('users.index', { ...listParams(), page: users.current_page + 1 }))"
            />
        </PageContainer>
    </AuthenticatedLayout>
</template>
