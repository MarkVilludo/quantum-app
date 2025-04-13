<script setup>
import { useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    'year_id': '',
    role: '', // default role
})

const successMessage = ref(''); // Success message state

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            form.reset();
            successMessage.value = 'Subject created successfully!'; // Set success message on successful creation
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
    <Head title="Create User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create User Account</h2>
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
                            <label class="block text-gray-700">Password</label>
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

                        <div v-if="form.role === 'student'">
                            <label for="year_id" class="block font-medium text-sm text-gray-700">Year Level</label>
                            <select
                                id="year_id"
                                v-model="form.year_id"
                                required
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring focus:ring-indigo-200"
                            >
                                <option disabled value="">Select year level</option>
                                <option :value="1">1st Year</option>
                                <option :value="2">2nd Year</option>
                                <option :value="3">3rd Year</option>
                                <option :value="4">4th Year</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="form.processing">
                                Create User
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
