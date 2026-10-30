<script setup>
import { router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const props = defineProps({
    analytics: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const fromDate = ref(props.filters.from);
const toDate = ref(props.filters.to);

watch(
    () => props.filters,
    (next) => {
        fromDate.value = next.from;
        toDate.value = next.to;
    },
    { deep: true },
);

const totalInRange = computed(() => props.analytics.total ?? 0);
const hasAnyData = computed(() => (props.analytics.counts ?? []).some((n) => n > 0));

const canvasRef = ref(null);
let chartInstance = null;

const formatShortLabel = (isoDate) => {
    const d = new Date(`${isoDate}T12:00:00`);
    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
};

const buildChart = () => {
    if (!canvasRef.value) {
        return;
    }

    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }

    const labels = props.analytics.labels ?? [];
    const counts = props.analytics.counts ?? [];

    chartInstance = new Chart(canvasRef.value, {
        type: 'bar',
        data: {
            labels: labels.map(formatShortLabel),
            datasets: [
                {
                    label: 'Certificates completed',
                    data: counts,
                    backgroundColor: 'rgba(99, 102, 241, 0.75)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 36,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title(items) {
                            const idx = items[0]?.dataIndex ?? 0;
                            return labels[idx] ?? '';
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 12,
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                    title: { display: true, text: 'Graduates' },
                },
            },
        },
    });
};

watch(
    () => props.analytics,
    () => buildChart(),
    { deep: true },
);

onMounted(() => buildChart());

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});

const applyRange = () => {
    router.get(
        route('dashboard'),
        { from: fromDate.value, to: toDate.value },
        { preserveState: true, preserveScroll: true },
    );
};

const resetRange = () => {
    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - 29);
    const toIso = (d) => d.toISOString().slice(0, 10);
    fromDate.value = toIso(start);
    toDate.value = toIso(end);
    applyRange();
};
</script>

<template>
    <section
        class="mt-8 rounded-xl border border-quantum-stroke/30 bg-white p-6 shadow-sm"
        aria-labelledby="graduation-analytics-heading"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 id="graduation-analytics-heading" class="text-base font-semibold text-gray-900">
                    Graduation certificates
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Students who completed all year-level finals and earned a certificate.
                </p>
                <p class="mt-3 text-2xl font-semibold tabular-nums text-quantum-primary">
                    {{ totalInRange }}
                    <span class="text-sm font-normal text-gray-500">in selected range</span>
                </p>
            </div>

            <form class="flex flex-wrap items-end gap-3" @submit.prevent="applyRange">
                <div>
                    <label for="analytics-from" class="block text-xs font-medium text-gray-600">From</label>
                    <input
                        id="analytics-from"
                        v-model="fromDate"
                        type="date"
                        class="qr-input mt-1"
                        required
                    />
                </div>
                <div>
                    <label for="analytics-to" class="block text-xs font-medium text-gray-600">To</label>
                    <input id="analytics-to" v-model="toDate" type="date" class="qr-input mt-1" required />
                </div>
                <button type="submit" class="qr-btn-primary">Apply</button>
                <button type="button" class="qr-btn-secondary" @click="resetRange">Last 30 days</button>
            </form>
        </div>

        <div class="relative mt-6 h-72 w-full">
            <canvas ref="canvasRef" role="img" aria-label="Bar chart of graduation certificates by day" />
            <p
                v-if="!hasAnyData"
                class="pointer-events-none absolute inset-0 flex items-center justify-center text-sm text-gray-400"
            >
                No certificates completed in this date range.
            </p>
        </div>
    </section>
</template>
