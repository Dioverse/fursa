<template>
    <header class="bg-secondary sticky top-0 z-50 shadow-md z-60">
        <div class="sm:container md:container lg:container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <RouterLink to="/" class="text-white text-2xl font-bold flex items-center gap-2">
                    <img src="/images/logo.png" alt="Fursa Energy" class="h-10" />
                </RouterLink>

                <!-- Desktop Search Bar -->
                <div class="hidden md:flex flex-1 max-w-md mx-6">
                    <div class="relative w-full" ref="searchRef">
                        <input v-model="searchQuery" type="text"
                            :placeholder="$t('header.search_placeholder') || 'Search Products...'"
                            class="w-full px-4 py-2 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            @keyup.enter="handleSearch" @input="onSearchInput" @keydown.esc.prevent="hideSuggestions" />
                        <button @click="handleSearch"
                            class="absolute right-1 top-1/2 -translate-y-1/2 bg-primary text-white px-3 py-1 rounded">
                            <font-awesome-icon icon="search" />
                        </button>

                        <!-- Suggestions Dropdown -->
                        <div v-if="showSuggestions && suggestions.length"
                            class="absolute left-0 right-0 mt-2 bg-white border rounded-lg shadow-lg z-50">
                            <ul class="max-h-72 overflow-y-auto py-2">
                                <li v-for="item in suggestions" :key="item.id"
                                    class="px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-3"
                                    @click="goToProduct(item)">
                                    <img :src="getThumb(item)" alt="thumb"
                                        class="w-8 h-8 rounded object-cover bg-gray-100" @error="onImgError" />
                                    <span class="text-sm text-gray-800 line-clamp-1">{{ item.name }}</span>
                                </li>
                            </ul>
                            <div class="px-3 py-2 border-t bg-gray-50 text-right">
                                <button class="text-sm text-primary hover:underline" @click="handleSearch">See all
                                    results</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-2 md:gap-4">

                    <!-- Cart -->
                    <RouterLink to="/cart"
                        class="relative bg-white px-3 py-2 rounded flex items-center gap-2 hover:bg-gray-100 transition">
                        <font-awesome-icon icon="shopping-cart" class="text-gray-700" />
                        <span v-if="cartStore.itemCount"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                            {{ cartStore.itemCount }}
                        </span>
                    </RouterLink>

                    <!-- Account Dropdown -->
                    <div class="relative" v-if="authStore.isAuthenticated">
                        <!-- Account Button -->
                        <button @click="toggleAccountMenu"
                            class="bg-primary text-white px-3 py-[9px] sm:py-[6px] rounded-md hover:bg-opacity-90 transition flex items-center gap-1.5 text-sm md:text-base">
                            <font-awesome-icon icon="circle-user" class="text-sm md:text-base" />
                            <span class="hidden sm:inline text-xs md:text-sm">{{ $t('header.account') || 'Account'
                                }}</span>
                            <font-awesome-icon :icon="showAccountMenu ? 'chevron-up' : 'chevron-down'"
                                class="text-xs md:text-sm" />
                        </button>

                        <!-- Dropdown Menu -->
                        <transition name="slide-fade">
                            <div v-if="showAccountMenu" ref="accountMenuRef"
                                class="absolute right-0 mt-2 w-40 sm:w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                                <RouterLink to="/dashboard"
                                    class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 transition text-sm"
                                    @click="closeAccountMenu">
                                    <font-awesome-icon icon="tachometer-alt" class="text-gray-600" />
                                    <span>{{ $t('header.dashboard') || 'Dashboard' }}</span>
                                </RouterLink>

                                <RouterLink to="/dashboard/orders"
                                    class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 transition text-sm"
                                    @click="closeAccountMenu">
                                    <font-awesome-icon icon="box" class="text-gray-600" />
                                    <span>{{ $t('header.orders') || 'Orders' }}</span>
                                </RouterLink>

                                <RouterLink to="/dashboard/wishlist"
                                    class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 transition text-sm"
                                    @click="closeAccountMenu">
                                    <font-awesome-icon icon="heart" class="text-red-500" />
                                    <span>{{ $t('header.wishlist') || 'Wishlist' }}</span>
                                </RouterLink>

                                <hr class="my-1" />

                                <button @click="logout"
                                    class="w-full text-left flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 transition text-sm">
                                    <font-awesome-icon icon="sign-out-alt" class="text-gray-600" />
                                    <span>{{ $t('header.logout') || 'Logout' }}</span>
                                </button>
                            </div>
                        </transition>
                    </div>
                    <RouterLink v-else to="/login"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 transition flex items-center gap-2">
                        <font-awesome-icon icon="user" />
                        <span class="hidden md:inline">{{ $t('header.login') || 'Login' }}</span>
                    </RouterLink>

                    <!-- Language Selector -->
                    <div class="relative text-xs sm:text-sm">
                        <button @click="showLangMenu = !showLangMenu"
                            class="flex items-center gap-1 text-white hover:text-primary transition">
                            <!-- <span>{{ languageStore.current.toUpperCase() }}</span> -->
                            <img :src="`/images/language/${languageStore.currentLanguage.icon}`"
                                class="w-[20px] h-[15px] rounded" :alt="languageStore.currentLanguage.name">
                            <span class="sm:block hidden">{{ languageStore.currentLanguage.name }}</span>
                            <font-awesome-icon :icon="showLangMenu ? 'chevron-up' : 'chevron-down'" />
                        </button>

                        <!-- Dropdown -->
                        <transition name="fade">
                            <div v-if="showLangMenu"
                                class="absolute right-0 mt-2 bg-white rounded shadow-lg py-2 w-32 z-20">
                                <a v-for="lang in languageStore.allowedLanguages" :key="lang.code" href="#"
                                    class="flex items-center gap-x-1.5 nowrap block px-4 py-2 hover:bg-gray-100"
                                    @click.prevent="switchLang(lang.code)">
                                    <img :src="`/images/language/${lang.icon}`" class="w-[20px] h-[15px] rounded"
                                        :alt="lang.icon">
                                    <span>{{ lang.name }}</span>
                                </a>
                            </div>
                        </transition>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white ml-2">
                        <font-awesome-icon :icon="mobileMenuOpen ? 'times' : 'bars'" size="lg" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <transition name="slide-fade">
            <nav v-show="mobileMenuOpen || windowWidth >= 768"
                class="bg-white border-t md:border-t-0 md:border-b md:relative absolute w-full md:w-auto ">
                <div class="container mx-auto px-2 sm:px-0 md:px-4">
                    <div
                        class="flex flex-col md:flex-row md:items-center md:justify-between bg-white/80 backdrop-blur-md border-b border-gray-100 rounded-lg md:rounded-none py-2 shadow-sm">
                        <!-- Mobile Search -->
                        <transition name="fade">
                            <div v-if="mobileMenuOpen" class="md:hidden gap-1.5 px-2.5 md:py-1.5 py-3">
                                <div class="relative w-full" ref="mobileSearchRef">
                                    <input v-model="searchQuery" type="text"
                                        :placeholder="$t('header.search_placeholder') || 'Search Products...'"
                                        class="w-full px-4 py-2 pr-10 rounded-lg bg-black/50 focus:outline-none focus:ring-2 focus:ring-primary placeholder:text-white/80 text-white"
                                        @keyup.enter="handleSearch" @input="onSearchInput"
                                        @keydown.esc.prevent="hideSuggestions" />
                                    <button @click="handleSearch"
                                        class="absolute right-1 top-1/2 -translate-y-1/2 bg-primary text-white px-3 py-1 rounded">
                                        <font-awesome-icon icon="search" />
                                    </button>

                                    <!-- Suggestions (Mobile) -->
                                    <div v-if="showSuggestions && suggestions.length"
                                        class="absolute left-0 right-0 mt-2 bg-white border rounded-lg shadow-lg z-50">
                                        <ul class="max-h-72 overflow-y-auto py-2">
                                            <li v-for="item in suggestions" :key="item.id"
                                                class="px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-3"
                                                @click="goToProduct(item)">
                                                <img :src="getThumb(item)" alt="thumb"
                                                    class="w-8 h-8 rounded object-cover bg-gray-100"
                                                    @error="onImgError" />
                                                <span class="text-sm text-gray-800 line-clamp-1">{{ item.name }}</span>
                                            </li>
                                        </ul>
                                        <div class="px-3 py-2 border-t bg-gray-50 text-right">
                                            <button class="text-sm text-primary hover:underline"
                                                @click="handleSearch">See all results</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <!-- Navigation Links -->
                        <ul
                            class="flex flex-col md:flex-row md:items-center gap-[2px] md:gap-0.1 xl:gap-5 text-gray-700 text-[14px] font-medium">
                            <li v-for="link in computedLinks" :key="link.to" class="group">
                                <RouterLink :to="link.to"
                                    class="flex items-center gap-1.5 px-2.5 md:py-1.5 py-3 rounded-md hover:bg-primary/10 hover:text-primary transition-all duration-200"
                                    @click="mobileMenuOpen = false">
                                    <font-awesome-icon :icon="link.icon"
                                        class="text-gray-500 text-sm group-hover:text-primary transition-colors" />
                                    <span class="truncate">{{ link.label }}</span>
                                </RouterLink>
                            </li>
                        </ul>

                        <!-- Distributor Button -->
                        <RouterLink v-if="authStore?.user?.role!='distributor'" to="/distributor-registration"
                            class="bg-primary text-white px-4 py-1.5 rounded-md hover:bg-primary/90 active:scale-[0.97] transition-all duration-200 flex items-center gap-1.5 text-sm md:text-[14px] w-full md:w-auto justify-center mt-2 md:mt-0">
                            <font-awesome-icon icon="truck" class="text-xs" />
                            <span class="flex nowrap">{{ $t('header.distributor') || 'Distributor' }}<span
                                    class="hidden sm:block">&nbsp;{{ $t('header.registration') || 'Registration'
                                    }}</span></span>
                        </RouterLink>
                    </div>

                </div>
            </nav>
        </transition>
    </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getImageUrl } from '@/utils/helpers'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useLanguageStore } from '@/stores/language'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const languageStore = useLanguageStore()
const { t } = useI18n()
// use $t from vue-i18n directly in template; provide computed fallbacks where needed

const searchQuery = ref('')
const suggestions = ref([])
const showSuggestions = ref(false)
const searchRef = ref(null)
const mobileSearchRef = ref(null)
let searchTimer = null
const mobileMenuOpen = ref(false)
const showLangMenu = ref(false)
const windowWidth = ref(window.innerWidth)

const computedLinks = computed(() => [
    { to: '/', label: t('header.nav.home') || 'Home', icon: 'home' },
    { to: '/shop', label: t('header.nav.shop') || 'Shop', icon: 'shop' },
    { to: '/about', label: t('header.nav.about') || 'About Us', icon: 'info-circle' },
    { to: '/blog', label: t('header.nav.blog') || 'Blog', icon: 'blog' },
    { to: '/contact', label: t('header.nav.contact') || 'Contact Us', icon: 'phone' },
    // { to: '/business', label: t('header.nav.business') || 'Buy For Business', icon: 'briefcase' },
])

const handleSearch = () => {
    const q = searchQuery.value.trim()
    if (!q) return
    router.push({ name: 'category-list', query: { name: q } })
    hideSuggestions()
    mobileMenuOpen.value = false
}

const onSearchInput = () => {
    const q = searchQuery.value.trim()
    if (searchTimer) clearTimeout(searchTimer)
    if (q.length < 2) { suggestions.value = []; showSuggestions.value = false; return }
    searchTimer = setTimeout(fetchSuggestions, 250)
}

const fetchSuggestions = async () => {
    try {
        const apiUrl = import.meta.env.VITE_API_BASE_URL
        const url = `${apiUrl}/products?name=${encodeURIComponent(searchQuery.value.trim())}&per_page=5`
        const res = await fetch(url)
        if (!res.ok) throw new Error('Failed')
        const json = await res.json()
        suggestions.value = json?.data?.products?.data || []
        showSuggestions.value = suggestions.value.length > 0
    } catch (e) {
        suggestions.value = []
        showSuggestions.value = false
    }
}

const hideSuggestions = () => {
    showSuggestions.value = false
}

const goToProduct = (item) => {
    hideSuggestions()
    mobileMenuOpen.value = false
    router.push(`/product/${item.slug}`)
}

const getThumb = (item) => item?.images?.[0]?.path ? getImageUrl(item.images[0].path) : '/images/oil-droplet.jpg'
const onImgError = (e) => { e.target.src = '/images/oil-droplet.jpg' }

const showAccountMenu = ref(false)
const accountMenuRef = ref(null)

const toggleAccountMenu = () => {
    showAccountMenu.value = !showAccountMenu.value
}

const closeAccountMenu = () => {
    showAccountMenu.value = false
}

const logout = () => {
    authStore.logout()
    showAccountMenu.value = false
    router.push('/login')
}

// Detect clicks outside to close dropdown
const handleClickOutside = (e) => {
    if (
        showAccountMenu.value &&
        accountMenuRef.value &&
        !accountMenuRef.value.contains(e.target) &&
        !e.target.closest('button') // ignore clicks on the Account button
    ) {
        showAccountMenu.value = false
    }

    // Close suggestions when clicking outside search boxes
    const containers = [searchRef.value, mobileSearchRef.value]
    const clickedInside = containers.some(c => c && c.contains(e.target))
    if (!clickedInside) hideSuggestions()
}

const handleClickOutsideLanguage = (e) => {
    if (
        showLangMenu.value &&
        !e.target.closest('button') // ignore clicks on the Account button
    ) {
        showLangMenu.value = false
    }
}

const switchLang = (lang) => {
    languageStore.set(lang)
    showLangMenu.value = false
}

const updateWidth = () => (windowWidth.value = window.innerWidth)
const handleGlobalClick = (event) => {
    handleClickOutside(event)
    handleClickOutsideLanguage(event)
}
onMounted(() => {
    document.addEventListener('click', handleGlobalClick)
    window.addEventListener('resize', updateWidth)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleGlobalClick)
    window.removeEventListener('resize', updateWidth)
})
</script>

<style scoped>
/* Smooth slide + fade transition */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.25s ease;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateY(-8px);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
