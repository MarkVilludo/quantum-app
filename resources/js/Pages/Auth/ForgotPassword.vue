<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
    },
    mailDevInboxUrl: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
});

const sent = computed(() => Boolean(props.status));

const submit = () => {
    form.post(route('password.email'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot password" />

        <h2 class="mb-2 text-xl font-semibold text-gray-900">Reset your password</h2>
        <p class="mb-6 text-sm text-gray-600">
            Enter the email you use for Quantum Quizzes. We&rsquo;ll send a secure link to choose a new password.
        </p>

        <div
            v-if="sent"
            class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-900"
            role="status"
        >
            <p class="font-medium">{{ status }}</p>
            <p class="mt-2 text-green-800">
                Open the email and click <strong>Choose a new password</strong>. The link expires after a short time.
            </p>
            <p v-if="mailDevInboxUrl" class="mt-3 text-green-800">
                Local dev: view captured mail in
                <a
                    :href="mailDevInboxUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-semibold text-quantum-primary underline decoration-quantum-accent/50 underline-offset-2 hover:text-quantum-primary-dark"
                >
                    MailHog
                </a>.
            </p>
        </div>

        <form v-if="!sent" @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email address" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@school.edu"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <PrimaryButton
                class="w-full justify-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Send reset link
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
