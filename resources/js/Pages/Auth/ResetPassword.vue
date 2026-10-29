<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const passwordsMatch = computed(
    () =>
        form.password.length > 0 &&
        form.password_confirmation.length > 0 &&
        form.password === form.password_confirmation,
);

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Choose new password" />

        <h2 class="mb-2 text-xl font-semibold text-gray-900">Choose a new password</h2>
        <p class="mb-6 text-sm text-gray-600">
            Pick a strong password you haven&rsquo;t used here before. You&rsquo;ll sign in with it on the website and in the mobile app.
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-gray-50"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    readonly
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="New password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <p class="mt-1 text-xs text-gray-500">At least 8 characters.</p>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm new password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <p v-if="passwordsMatch" class="mt-1 text-xs font-medium text-green-600">Passwords match</p>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <PrimaryButton
                class="w-full justify-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Update password
            </PrimaryButton>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            <Link
                :href="route('login')"
                class="font-medium text-quantum-primary hover:text-quantum-primary-dark focus:outline-none focus:ring-2 focus:ring-quantum-accent focus:ring-offset-2 rounded-sm"
            >
                Back to sign in
            </Link>
        </p>
    </GuestLayout>
</template>
