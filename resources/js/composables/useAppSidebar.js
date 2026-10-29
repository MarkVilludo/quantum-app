import { ref, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useAppSidebar() {
    const sidebarOpen = ref(false);
    const page = usePage();

    const closeSidebar = () => {
        sidebarOpen.value = false;
    };

    const toggleSidebar = () => {
        sidebarOpen.value = !sidebarOpen.value;
    };

    watch(
        () => page.url,
        () => closeSidebar(),
    );

    watch(sidebarOpen, (open) => {
        if (typeof document === 'undefined' || typeof window === 'undefined') {
            return;
        }
        const lockScroll = open && window.matchMedia('(max-width: 1023px)').matches;
        document.body.classList.toggle('overflow-hidden', lockScroll);
    });

    const onKeydown = (event) => {
        if (event.key === 'Escape' && sidebarOpen.value) {
            closeSidebar();
        }
    };

    onMounted(() => window.addEventListener('keydown', onKeydown));
    onUnmounted(() => {
        window.removeEventListener('keydown', onKeydown);
        document.body.classList.remove('overflow-hidden');
    });

    return { sidebarOpen, closeSidebar, toggleSidebar };
}
