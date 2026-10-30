<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageContainer from '@/Components/PageContainer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    release: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const fileInput = ref(null);

const form = useForm({
    version_name: props.release?.version_name ?? '1.0',
    version_code: props.release?.version_code ?? 1,
    apk: null,
});

const onFileChange = (event) => {
    form.apk = event.target.files?.[0] ?? null;
};

const submit = () => {
    form.post(route('admin.mobile-app.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('apk');
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
};
</script>

<template>
    <Head title="Android app release" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Android app (APK)</h1>
                <p class="text-sm text-gray-500">Upload the latest Quantum Realm build for homepage download</p>
            </div>
        </template>

        <PageContainer>
            <div
                v-if="page.props.flash?.success"
                class="mb-6 rounded-lg border border-quantum-success/30 bg-quantum-success/10 px-4 py-3 text-sm text-quantum-success"
            >
                {{ page.props.flash.success }}
            </div>

            <div v-if="release" class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Current release</h2>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Version</dt>
                        <dd class="font-medium text-gray-900">
                            {{ release.version_name }}
                            <span v-if="release.version_code" class="text-gray-500">({{ release.version_code }})</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">File</dt>
                        <dd class="font-medium text-gray-900">{{ release.original_filename }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Size</dt>
                        <dd class="font-medium text-gray-900">{{ release.size_human }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Public link</dt>
                        <dd>
                            <a :href="release.download_url" class="text-quantum-primary hover:underline" target="_blank" rel="noopener">
                                {{ release.download_url }}
                            </a>
                        </dd>
                    </div>
                </dl>
            </div>

            <form class="max-w-xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm" @submit.prevent="submit">
                <h2 class="text-base font-semibold text-gray-900">Upload new APK</h2>

                <div>
                    <InputLabel for="version_name" value="Version name" />
                    <TextInput id="version_name" v-model="form.version_name" class="mt-1 block w-full" required />
                    <InputError class="mt-1" :message="form.errors.version_name" />
                </div>

                <div>
                    <InputLabel for="version_code" value="Version code (optional)" />
                    <TextInput id="version_code" v-model="form.version_code" type="number" min="1" class="mt-1 block w-full" />
                    <InputError class="mt-1" :message="form.errors.version_code" />
                </div>

                <div>
                    <InputLabel for="apk" value="APK file" />
                    <input
                        id="apk"
                        ref="fileInput"
                        type="file"
                        accept=".apk,application/vnd.android.package-archive"
                        class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-quantum-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-quantum-primary-dark"
                        required
                        @change="onFileChange"
                    />
                    <p class="mt-1 text-xs text-gray-500">Build with Android Studio or <code class="rounded bg-gray-100 px-1">./gradlew assembleDebug</code> (max 500 MB).</p>
                    <InputError class="mt-1" :message="form.errors.apk" />
                </div>

                <button type="submit" class="qr-btn-primary" :disabled="form.processing">
                    {{ form.processing ? 'Uploading…' : 'Publish download' }}
                </button>
            </form>
        </PageContainer>
    </AuthenticatedLayout>
</template>
