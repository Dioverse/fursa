<template>
    <!-- Desktop Sidebar -->
    <aside v-if="$props.showDesktop"
        class="hidden sm:block md:block lg:block w-[10rem] md:w-48 lg:w-64 bg-white shadow-md text-sm md:text-sm lg:text-md">
        <nav class="p-1 md:p-2 lg:p-4 sticky top-[130px]">
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
                        <span>{{ $t('dashboard.sidebar.logout') }}</span>
                    </button>
                </li>
            </ul>

        </nav>
    </aside>

    <!-- Mobile Bottom Sheet -->
    <transition name="slide-up" v-if="authStore.token" :ddd="authStore.token" class="container mx-auto px-4 shadow-2xl">
        <div class="block sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-2xl rounded-t-2xl z-50 transition-all duration-300 ease-out"
            :class="expanded ? 'h-[16rem]' : 'h-[5rem]'" @touchstart.passive="onTouchStart"
            @touchmove.prevent="onTouchMove" @touchend="onTouchEnd">
            <!-- Handle (clickable) -->
            <div class="flex justify-center py-2 cursor-pointer active:scale-95 transition-transform"
                @click.stop="toggleExpanded">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Menu Links -->
            <div class="flex flex-wrap items-center px-3 transition-all duration-500 overflow-hidden"
                :class="{ 'h-[12rem] justify-start': expanded, 'h-[3rem] justify-around': !expanded }">
                <RouterLink v-for="(item, i) in visibleLinks" :key="i" :to="item.to"
                    class="flex flex-col items-center justify-center text-gray-600 hover:text-primary transition text-xs w-[25%] mb-3"
                    :class="{ 'text-primary': $route.path === item.to }" @click.stop>
                    <font-awesome-icon :icon="item.icon" class="text-lg mb-1" />
                    <span>{{ item.label }}</span>
                </RouterLink>
            </div>
            <!-- Language Switcher (Mobile) -->
            <div class="px-4 pb-3">
                <div class="relative" @keydown.escape="openLang = false" data-lang-dropdown>
                    <button @click="openLang = !openLang"
                        class="w-full flex items-center justify-between gap-2 px-3 py-2 border rounded-md bg-white hover:bg-gray-50">
                        <div class="flex items-center gap-2">
                            <img :src="`/images/language/${languageStore.currentLanguage.icon}`"
                                class="w-[18px] h-[14px] rounded" :alt="languageStore.currentLanguage.name">
                            <span class="text-xs">{{ languageStore.currentLanguage.name }}</span>
                        </div>
                        <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-gray-500 text-xs" />
                    </button>
                    <div v-if="openLang"
                        class="absolute left-0 bottom-full mb-2 w-full bg-white border rounded-md shadow-lg py-1 z-10">
                        <a v-for="lang in languageStore.allowedLanguages" :key="lang.code" href="#"
                            class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:bg-gray-50 text-xs"
                            @click.prevent="switchLang(lang.code)">
                            <img :src="`/images/language/${lang.icon}`" class="w-[18px] h-[14px] rounded"
                                :alt="lang.name">
                            <span>{{ lang.name }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <!-- Overlay -->
    <transition name="fade" v-if="authStore.token">
        <div v-if="expanded" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 sm:hidden"
            @click="expanded = false"></div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useLanguageStore } from '@/stores/language'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()
const { t } = useI18n()
const languageStore = useLanguageStore()
const openLang = ref(false)

const onClickOutside = (e) => {
    const el = e.target.closest('[data-lang-dropdown]')
    if (!el) openLang.value = false
}

const switchLang = (lang) => {
    languageStore.set(lang)
    openLang.value = false
}

const handleLogout = () => {
    authStore.logout()
    toast.success(t('dashboard.toasts.logged_out'))
    router.push('/')
}

defineProps({
    showDesktop: {
        type: Boolean,
        default: true
    },
})

// All menu links (reactive to language changes)
const fullLinks = computed(() => {
    // Determine the profile link based on the user's distributor status
    const profileLink = (authStore.user && authStore.user.distributor)
        ? { to: '/dashboard/profile-details', icon: 'user', label: t('dashboard.sidebar.profile') }
        : { to: '/dashboard/profile', icon: 'user', label: t('dashboard.sidebar.profile') };

    return [
        { to: '/dashboard', icon: 'dashboard', label: t('dashboard.sidebar.dashboard') },
        { to: '/dashboard/orders', icon: 'box', label: t('dashboard.sidebar.orders') },
        // Conditionally added profile link
        profileLink,
        { to: '/dashboard/addresses', icon: 'map-marker-alt', label: t('dashboard.sidebar.address') },
        { to: '/dashboard/wishlist', icon: 'heart', label: t('dashboard.sidebar.wishlist') },
    ];
});

const expanded = ref(false)
const startY = ref(0)
const deltaY = ref(0)

const visibleLinks = computed(() =>
    expanded.value ? fullLinks.value : fullLinks.value.slice(0, 4)
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

onMounted(() => {
    document.addEventListener('click', onClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside)
})
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
