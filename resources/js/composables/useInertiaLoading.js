import { onMounted, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

/** Shared loading flag for table/filter pages (AsyncContent). */
const navigating = ref(false);

let listenersAttached = false;
let urlWatchRegistered = false;

function attachRouterListenersOnce() {
    if (listenersAttached) {
        return;
    }
    router.on('start', () => {
        navigating.value = true;
    });
    router.on('finish', () => {
        navigating.value = false;
    });
    router.on('error', () => {
        navigating.value = false;
    });
    router.on('cancel', () => {
        navigating.value = false;
    });
    listenersAttached = true;
}

export function useInertiaLoading() {
    const page = usePage();

    if (!urlWatchRegistered) {
        watch(
            () => page.url,
            () => {
                navigating.value = false;
            },
        );
        urlWatchRegistered = true;
    }

    onMounted(() => {
        attachRouterListenersOnce();
        navigating.value = false;
    });

    return { navigating };
}
