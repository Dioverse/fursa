<template>
    <!-- Desktop Sidebar -->
    <aside
        class="hidden sm:block md:block lg:block w-[10rem] md:w-48 lg:w-64 bg-white shadow-md text-sm md:text-sm lg:text-md">
        <nav class="p-1 md:p-2 lg:p-4 sticky top-[87px]">
            <ul class="space-y-2">
                <li v-for="item in fullLinks" :key="item.to">
                    <RouterLink :to="item.to"
                        class="flex items-center gap-3 px-3 py-2 md:px-4 md:py-3 rounded-lg hover:bg-gray-100 transition"
                        :class="{ 'bg-primary text-white': $route.path === item.to }">
                        <font-awesome-icon :icon="item.icon" />
                        <span>{{ item.label }}</span>
                    </RouterLink>
                </li>
                <li>
                    <button @click="handleLogout"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition text-left">
                        <font-awesome-icon icon="sign-out" />
                        <span>Log Out</span>
                    </button>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Mobile Bottom Sheet -->
    <transition name="slide-up" class="container mx-auto px-4 shadow-2xl">
        <div class="block sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-2xl rounded-t-2xl z-50 transition-all duration-300 ease-out"
            :class="expanded ? 'h-[15rem]' : 'h-[5rem]'" @touchstart.passive="onTouchStart"
            @touchmove.prevent="onTouchMove" @touchend="onTouchEnd">
            <!-- Handle (clickable) -->
            <div class="flex justify-center py-2 cursor-pointer active:scale-95 transition-transform"
                @click.stop="toggleExpanded">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Menu Links -->
            <div class="flex flex-wrap justify-around items-center px-3 transition-all duration-500 overflow-hidden"
                :class="{ 'h-[12rem]': expanded, 'h-[3rem]': !expanded }">
                <RouterLink v-for="(item, i) in visibleLinks" :key="i" :to="item.to"
                    class="flex flex-col items-center justify-center text-gray-600 hover:text-primary transition text-xs w-[20%] mb-3"
                    :class="{ 'text-primary': $route.path === item.to }" @click.stop>
                    <font-awesome-icon :icon="item.icon" class="text-lg mb-1" />
                    <span>{{ item.label }}</span>
                </RouterLink>
            </div>
        </div>
    </transition>

    <!-- Overlay -->
    <transition name="fade">
        <div v-if="expanded" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 sm:hidden"
            @click="expanded = false"></div>
    </transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const handleLogout = () => {
    authStore.logout()
    toast.success('Logged out successfully')
    router.push('/')
}

// All menu links
const fullLinks = [
    { to: '/dashboard', icon: 'dashboard', label: 'Dashboard' },
    { to: '/dashboard/orders', icon: 'box', label: 'Orders' },
    { to: '/dashboard/profile', icon: 'user', label: 'Profile' },
    { to: '/dashboard/addresses', icon: 'map-marker-alt', label: 'Address' },
    { to: '/dashboard/wishlist', icon: 'heart', label: 'Wishlist' },
    { to: '/dashboard/settings', icon: 'cog', label: 'Settings' },
]

const expanded = ref(false)
const startY = ref(0)
const deltaY = ref(0)

const visibleLinks = computed(() =>
    expanded.value ? fullLinks : fullLinks.slice(0, 4)
)

// Touch handlers
const onTouchStart = (e) => {
    e.preventDefault()
    startY.value = e.touches[0].clientY
}

const onTouchMove = (e) => {
    e.preventDefault()
    deltaY.value = e.touches[0].clientY - startY.value
}

const onTouchEnd = (e) => {
    e.preventDefault()
    if (deltaY.value < -30) expanded.value = true // drag up
    if (deltaY.value > 30) expanded.value = false // drag down
    deltaY.value = 0
}

// Click toggle
const toggleExpanded = () => {
    expanded.value = !expanded.value
}
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s cubic-bezier(0.25, 1.25, 0.5, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
    transform: translateY(100%);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
