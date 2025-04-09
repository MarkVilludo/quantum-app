<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const { user } = usePage().props // Access the user data passed from the controller
const form = useForm({
    name: user.name,
    email: user.email,
    password: '', // password can be optional when editing
    role: user.role,
})

const successMessage = ref(''); // Success message state

// Load user data if needed, can be set on mounted if necessary
onMounted(() => {
    // Additional logic can be added here, if needed.
})

const submit = () => {
    form.put(route('users.update', user.id), {
        onSuccess: () => {
            successMessage.value = 'User updated successfully!';
            setTimeout(() => {
                successMessage.value = ''; // Clear success message after a few seconds
            }, 3000);
        }
    })
}

const goBack = () => {
    window.history.back();
}
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User Account</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow rounded-lg">
                    <!-- Success Message -->
                    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                        {{ successMessage }}
                    </div>
                    <form @submit.prevent="submit" class="space-y-4">

                        <div>
                            <label class="block text-gray-700">Name</label>
                            <input type="text" v-model="form.name" class="w-full border p-2 rounded" />
                            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-700">Email</label>
                            <input type="email" v-model="form.email" class="w-full border p-2 rounded" />
                            <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-700">Password (Leave blank to keep current)</label>
                            <input type="password" v-model="form.password" class="w-full border p-2 rounded" />
                            <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</div>
                        </div>

                        <div>
                            <label class="block text-gray-700">Role</label>
                            <select v-model="form.role" class="w-full border p-2 rounded">
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                            </select>
                            <div v-if="form.errors.role" class="text-red-500 text-sm">{{ form.errors.role }}</div>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="form.processing">
                                Update User
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
