<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    quantum_xp: Number,
    rank: Object,
    rank_ladder: Object,
});

const tierEmojis = ['🌱', '⚡', '🧭', '🏃', '🐛', '⚔️', '🚀', '🏗️', '🔬', '🛡️', '🌌', '✨'];
const selectedTier = ref(null);

const rankLadder = computed(() => props.rank_ladder ?? {});
const titleBands = computed(() => rankLadder.value.title_bands ?? []);
const xpRewards = computed(() => rankLadder.value.xp_rewards ?? []);
const nextLevel = computed(() => rankLadder.value.next_level ?? null);

const levelProgress = computed(() => {
    const inLevel = props.rank?.xp_in_level ?? 0;
    const forNext = Math.max(1, props.rank?.xp_for_next_level ?? 1);
    return Math.min(100, Math.round((inLevel / forNext) * 100));
});

const formatLevelRange = (band) => {
    if (band.max_level == null) {
        return `Levels ${band.min_level}+`;
    }
    return `Levels ${band.min_level}–${band.max_level}`;
};

const stateLabel = (state) => {
    if (state === 'current') return 'Current tier';
    if (state === 'unlocked') return 'Unlocked';
    return 'Locked';
};

const stateClasses = (state) => {
    if (state === 'current') {
        return 'border-quantum-accent ring-2 ring-quantum-accent/40 shadow-lg shadow-quantum-accent/10';
    }
    if (state === 'unlocked') {
        return 'border-quantum-success/50 bg-quantum-success/5';
    }
    return 'border-quantum-stroke/40 opacity-90';
};

const openTier = (band, index) => {
    selectedTier.value = { ...band, emoji: tierEmojis[index % tierEmojis.length], levelRange: formatLevelRange(band) };
};

const closeTier = () => {
    selectedTier.value = null;
};
</script>

<template>
    <Head title="My Progression" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Levels &amp; title tiers</h1>
                <p class="text-sm text-gray-500">Your Quantum XP journey matches the mobile app</p>
            </div>
        </template>

        <div class="px-4 py-6 sm:px-6 lg:px-8 space-y-8">
            <!-- Current rank hero -->
            <section class="overflow-hidden rounded-2xl border border-quantum-stroke/30 bg-gradient-to-br from-quantum-bg-mid via-quantum-surface to-quantum-bg-dark p-6 text-quantum-text-primary shadow-lg sm:p-8">
                <p class="text-sm font-medium text-quantum-accent">Current rank</p>
                <h2 class="mt-1 text-2xl font-bold sm:text-3xl">
                    {{ rank?.title }} · Level {{ rank?.level ?? 1 }}
                </h2>
                <p class="mt-2 text-quantum-secondary">{{ quantum_xp }} Quantum XP total</p>

                <div class="mt-6">
                    <div class="mb-2 flex justify-between text-xs text-quantum-secondary">
                        <span>Progress this level</span>
                        <span>{{ rank?.xp_in_level ?? 0 }} / {{ rank?.xp_for_next_level ?? 1 }} XP</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-quantum-bg-dark/80">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-quantum-gold via-quantum-accent to-quantum-primary transition-all duration-500"
                            :style="{ width: `${levelProgress}%` }"
                        />
                    </div>
                </div>

                <div
                    v-if="nextLevel"
                    class="mt-6 rounded-xl border border-quantum-accent/30 bg-quantum-bg-dark/40 p-4 text-sm"
                >
                    <p class="font-semibold text-quantum-accent">Next level</p>
                    <p class="mt-1 text-quantum-text-primary">
                        {{ nextLevel.title }} · Level {{ nextLevel.level }} —
                        <span class="text-quantum-gold">{{ nextLevel.xp_remaining }} XP</span> to go
                        ({{ nextLevel.total_xp_required }} total XP)
                    </p>
                </div>
            </section>

            <!-- Earn XP -->
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900">How to earn XP</h3>
                <ul class="mt-3 space-y-2">
                    <li
                        v-for="(row, i) in xpRewards"
                        :key="i"
                        class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-2 text-sm"
                    >
                        <span class="text-gray-700">{{ row.label }}</span>
                        <span class="font-semibold text-quantum-primary">+{{ row.xp }} XP</span>
                    </li>
                </ul>
            </section>

            <!-- Title tiers grid -->
            <section>
                <h3 class="text-base font-semibold text-gray-900">Title tiers</h3>
                <p class="mt-1 text-sm text-gray-500">Tap a tier to see requirements and rewards</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <button
                        v-for="(band, index) in titleBands"
                        :key="`${band.min_level}-${band.title}`"
                        type="button"
                        class="group rounded-xl border bg-white p-5 text-left transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-quantum-primary"
                        :class="stateClasses(band.state)"
                        @click="openTier(band, index)"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-3xl transition group-hover:scale-110">{{ tierEmojis[index % tierEmojis.length] }}</span>
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize"
                                :class="{
                                    'bg-quantum-accent/20 text-cyan-800': band.state === 'current',
                                    'bg-green-100 text-green-800': band.state === 'unlocked',
                                    'bg-gray-100 text-gray-600': band.state === 'locked',
                                }"
                            >
                                {{ stateLabel(band.state) }}
                            </span>
                        </div>
                        <h4 class="mt-3 font-semibold text-gray-900">{{ band.title }}</h4>
                        <p class="mt-1 text-xs text-gray-500">{{ formatLevelRange(band) }}</p>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-gray-200">
                            <div
                                class="h-full rounded-full bg-quantum-primary transition-all"
                                :style="{ width: `${band.progress_percent ?? 0}%` }"
                            />
                        </div>
                        <p class="mt-2 text-xs text-gray-600">
                            <template v-if="band.state === 'locked' && band.xp_remaining">
                                {{ band.xp_remaining }} XP to unlock · from {{ band.total_xp_at_min }} total XP
                            </template>
                            <template v-else-if="band.state === 'current'">
                                {{ band.progress_percent }}% through this tier
                            </template>
                            <template v-else>
                                Earned at {{ band.total_xp_at_min }}+ total XP
                            </template>
                        </p>
                        <p class="mt-3 text-xs font-medium text-quantum-primary opacity-0 transition group-hover:opacity-100">
                            View details →
                        </p>
                    </button>
                </div>
            </section>
        </div>

        <!-- Tier detail modal -->
        <Teleport to="body">
            <div
                v-if="selectedTier"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
                @click.self="closeTier"
            >
                <div
                    class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl border border-quantum-stroke/30 bg-gradient-to-b from-white to-slate-50 p-6 shadow-2xl"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="flex items-start gap-3">
                        <span class="text-4xl">{{ selectedTier.emoji }}</span>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ selectedTier.title }}</h3>
                            <p class="text-sm text-gray-500">{{ selectedTier.levelRange }}</p>
                        </div>
                    </div>

                    <p class="mt-4 rounded-lg bg-quantum-primary/10 px-3 py-2 text-sm font-medium text-quantum-primary-dark">
                        {{ stateLabel(selectedTier.state) }}
                    </p>

                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        <p><span class="font-semibold text-gray-900">Requirement:</span> {{ selectedTier.requirement }}</p>
                        <p><span class="font-semibold text-gray-900">Reward:</span> {{ selectedTier.reward }}</p>
                        <p v-if="selectedTier.state === 'locked' && selectedTier.xp_remaining">
                            <span class="font-semibold text-gray-900">XP remaining:</span>
                            {{ selectedTier.xp_remaining }} Quantum XP
                        </p>
                    </div>

                    <div class="mt-4 border-t pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Ways to earn XP</p>
                        <ul class="mt-2 space-y-1 text-sm text-gray-600">
                            <li v-for="(row, i) in xpRewards" :key="i">• {{ row.label }} (+{{ row.xp }} XP)</li>
                        </ul>
                    </div>

                    <button
                        type="button"
                        class="mt-6 w-full rounded-lg bg-quantum-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-quantum-primary-dark"
                        @click="closeTier"
                    >
                        Close
                    </button>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
