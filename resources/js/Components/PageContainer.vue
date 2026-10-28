<template>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div
                    v-if="$slots.toolbar"
                    class="flex flex-wrap items-center gap-3 border-b border-gray-100 bg-gray-50/80 px-6 py-4"
                >
                    <slot name="toolbar" />
                </div>
                <div class="p-6">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
