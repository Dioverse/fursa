<template>
    <div id="app">

        <!-- 🌐 GLOBAL PAGE LOADER -->
        <div
            v-if="isRouteLoading"
            class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-xs z-[59]"
        >
            <div class="loader-circle"></div>
        </div>

        <!-- PAGE TRANSITION + ROUTER VIEW -->
        <RouterView v-slot="{ Component }">
            <transition name="fade" mode="out-in">
                <component :is="Component" />
            </transition>
        </RouterView>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { isRouteLoading } from '@/appLoading'   // <-- existing global loader state

const authStore = useAuthStore()

onMounted(() => {
    authStore.checkAuth()
})
</script>

<style>
/* ----------------------------- */
/* Premium Loader - Option A */
/* ----------------------------- */
.loader-circle {
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;          /* light gray */
    border-top-color: #b8974f;          /* your gold brand color */
    border-radius: 50%;
    animation: spin 0.9s ease infinite;
}

@keyframes spin {
    0% { transform: rotate(0); }
    100% { transform: rotate(360deg); }
}

/* ----------------------------- */
/* Page Fade Transition */
/* ----------------------------- */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* ----------------------------- */
/* Custom Scrollbar */
/* ----------------------------- */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #b8974f;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9a7d3f;
}
</style>
