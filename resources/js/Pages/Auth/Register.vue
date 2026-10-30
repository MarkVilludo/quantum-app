<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
    name: '',
    email: '',
    year_id: '',
    password: '',
    password_confirmation: '',
});

const nameClientError = ref('');
const passwordClientError = ref('');

const NAME_LETTERS_AND_SPACES = /^[\p{L}\s]*$/u;

watch(
    () => form.name,
    (value) => {
        if (!value) {
            nameClientError.value = '';
            return;
        }

        let next = value;

        if (/\d/.test(next)) {
            nameClientError.value = 'Name cannot contain numbers.';
            next = next.replace(/\d/g, '');
        } else if (!NAME_LETTERS_AND_SPACES.test(next)) {
            nameClientError.value = 'Name cannot contain numbers or special characters.';
            next = next.replace(/[^\p{L}\s]/gu, '');
        } else {
            nameClientError.value = '';
        }

        if (next !== value) {
            form.name = next;
        }
    },
);

watch(
    () => form.password,
    (value) => {
        if (value.length > 0 && value.length < 8) {
            passwordClientError.value = 'Password must be at least 8 characters long.';
        } else {
            passwordClientError.value = '';
        }
    },
);

function validateBeforeSubmit() {
    nameClientError.value = '';
    passwordClientError.value = '';

    const trimmedName = form.name.trim();

    if (!trimmedName) {
        nameClientError.value = 'Enter your name.';
        return false;
    }

    if (/\d/.test(trimmedName)) {
        nameClientError.value = 'Name cannot contain numbers.';
        return false;
    }

    if (!/^[\p{L}\s]+$/u.test(trimmedName)) {
        nameClientError.value = 'Name cannot contain numbers or special characters.';
        return false;
    }

    if (form.password.length < 8) {
        passwordClientError.value = 'Password must be at least 8 characters long.';
        return false;
    }

    return true;
}

const submit = () => {
    if (!validateBeforeSubmit()) {
        return;
    }

    form
        .transform((data) => ({
            ...data,
            name: data.name.trim(),
        }))
        .post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <h2 class="mb-6 text-xl font-semibold text-gray-900">Create account</h2>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="nameClientError || form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <label for="year_id" class="block font-medium text-sm text-gray-700">Year Level</label>
                <select
                    id="year_id"
                    v-model="form.year_id"
                    required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring focus:ring-cyan-200"
                >
                    <option disabled value="">Select year level</option>
                    <option :value="1">1st Year</option>
                    <option :value="2">2nd Year</option>
                    <option :value="3">3rd Year</option>
                    <option :value="4">4th Year</option>
                </select>
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <p v-if="!passwordClientError && !form.errors.password" class="mt-1 text-xs text-gray-500">
                    At least 8 characters.
                </p>
                <InputError class="mt-2" :message="passwordClientError || form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-quantum-accent"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
