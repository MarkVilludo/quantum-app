<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
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

// Delete user functionality
const deleteUser = (userId) => {
    if (confirm('Are you sure you want to delete this user?')) {
        const form = useForm({});
        form.delete(route('users.destroy', userId), {
            onSuccess: () => {
                alert('User deleted successfully!');
            },
        });
    }
};

</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              
                    <div class="p-6">
                        <div class="mb-4 flex gap-6">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search users..."
                                class="border rounded p-2"
                            />
                            <a :href="route('users.create')" class="bg-blue-500 text-white px-4 py-2 rounded inline-block">Create New User</a>
                        </div>

                        <table class="w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 border">Name</th>
                                    <th class="px-4 py-2 border">Email</th>
                                    <th class="px-4 py-2 border">Role</th>
                                    <th class="px-4 py-2 border">Year / Year Handled for Teacher</th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-4 py-2 border">{{ user.name }}</td>
                                    <td class="px-4 py-2 border">{{ user.email }}</td>
                                    <td class="px-4 py-2 border capitalize">{{ user.role }}</td>
                                    <td class="px-4 py-2 border capitalize">{{ user.year?.name }}</td>
                                    <td class="px-4 py-2 border capitalize">
                                        <a :href="route('users.edit', user.id)" class="text-blue-500 hover:text-blue-700">Edit</a>
                                        <!-- Delete Button -->
                                        <button 
                                            type="button"
                                            @click="deleteUser(user.id)"
                                            class="ml-2 text-red-500 hover:text-red-700">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-4 flex justify-between items-center">
                            <button
                                @click="router.get(route('users.index', { page: users.current_page - 1 }))"
                                :disabled="users.current_page === 1"
                                class="bg-gray-200 px-4 py-2 rounded"
                            >
                                Previous
                            </button>

                            <span>Page {{ users.current_page }} of {{ users.last_page }}</span>

                            <button
                                @click="router.get(route('users.index', { page: users.current_page + 1 }))"
                                :disabled="users.current_page === users.last_page"
                                class="bg-gray-200 px-4 py-2 rounded"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
