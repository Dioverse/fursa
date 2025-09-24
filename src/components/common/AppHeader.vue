<template>
    <header class="bg-secondary sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <RouterLink to="/" class="text-white text-2xl font-bold flex items-center gap-2">
                    <img src="/images/logo.png" alt="Fursa Energy" class="h-10" />
                    <!-- <span class="hidden md:inline">Fursa Energy</span> -->
                </RouterLink>

                <!-- Search Bar -->
                <div class="flex-1 max-w-md mx-4 md:mx-8">
                    <div class="relative">
                        <input v-model="searchQuery" type="text" placeholder="Search Products..."
                            class="w-full px-4 py-2 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            @keyup.enter="handleSearch">
                        <button @click="handleSearch"
                            class="absolute right-1 top-1/2 -translate-y-1/2 bg-primary text-white px-3 py-1 rounded">
                            <font-awesome-icon icon="search" />
                        </button>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-2 md:gap-4">
                    <button class="bg-white px-3 py-2 rounded flex items-center gap-2 hover:bg-gray-100 transition">
                        <font-awesome-icon icon="heart" class="text-red-500" />
                        <span class="hidden md:inline">{{ wishlistStore.count }}</span>
                    </button>

                    <RouterLink to="/cart"
                        class="bg-white px-3 py-2 rounded flex items-center gap-2 hover:bg-gray-100 transition">
                        <font-awesome-icon icon="shopping-cart" class="text-gray-700" />
                        <span class="badge">{{ cartStore.itemCount }}</span>
                    </RouterLink>

                    <RouterLink v-if="authStore.isAuthenticated" to="/dashboard"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 transition flex items-center gap-2">
                        <font-awesome-icon icon="user-circle" />
                        <span class="hidden md:inline">Account</span>
                    </RouterLink>

                    <RouterLink v-else to="/login"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 transition flex items-center gap-2">
                        <font-awesome-icon icon="user" />
                        <span class="hidden md:inline">Login</span>
                    </RouterLink>

                    <!-- Language Selector -->
                    <div class="relative">
                        <!-- Button -->
                        <button  @click="showLangMenu = !showLangMenu" class="flex items-center gap-1 text-white">
                            <span>{{ languageStore.current.toUpperCase() }}</span>
                            <span>{{ languageStore.currentName }}</span>
                            <font-awesome-icon :icon="showLangMenu ? 'chevron-up' : 'chevron-down'" />
                        </button>

                        <!-- Dropdown -->
                        <div v-if="showLangMenu" class="absolute right-0 mt-2 bg-white rounded shadow-lg py-2 w-32">
                        <a v-for="lang in languageStore.allowedLanguages" :key="lang.code" href="#" class="block px-4 py-2 hover:bg-gray-100" @click.prevent="switchLang(lang.code)">
                            {{ lang.name }}
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white">
                    <font-awesome-icon :icon="mobileMenuOpen ? 'times' : 'bars'" size="lg" />
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="bg-white border-b" :class="{ 'hidden md:block': !mobileMenuOpen }">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <ul class="flex flex-col md:flex-row md:items-center gap-2 md:gap-6 py-4">
                        <li>
                            <RouterLink to="/" class="flex items-center gap-2 hover:text-primary transition">
                                <font-awesome-icon icon="home" />
                                <span>Home</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/shop" class="flex items-center gap-2 hover:text-primary transition">
                                <font-awesome-icon icon="shop" />
                                <span>Shop</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/about" class="flex items-center gap-2 hover:text-primary transition">
                                <font-awesome-icon icon="info-circle" />
                                <span>About Us</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/blog" class="flex items-center gap-2 hover:text-primary transition">
                                <font-awesome-icon icon="blog" />
                                <span>Blog</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/contact" class="flex items-center gap-2 hover:text-primary transition">
                                <font-awesome-icon icon="phone" />
                                <span>Contact Us</span>
                            </RouterLink>
                        </li>
                        <li>
                            <RouterLink to="/business" class="flex items-center gap-2 hover:text-primary transition">
                                <font-awesome-icon icon="briefcase" />
                                <span>Buy For Business</span>
                            </RouterLink>
                        </li>
                    </ul>

                    <RouterLink to="/distributor-registration"
                        class="bg-primary text-white px-6 py-2 rounded hover:bg-opacity-90 transition inline-flex items-center gap-2 mb-4 md:mb-0">
                        <font-awesome-icon icon="truck" />
                        <span>Become a Distributor</span>
                    </RouterLink>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useLanguageStore } from '@/stores/language'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore  = useWishlistStore()

const searchQuery = ref('')
const mobileMenuOpen = ref(false)
const showLangMenu = ref(false)
const languageStore = useLanguageStore()

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({
      name: 'shop',
      query: { search: searchQuery.value }
    })
    searchQuery.value = ''
    mobileMenuOpen.value = false
  }
}

const switchLang = (lang) => {
  languageStore.set(lang)
  showLangMenu.value = false
}
</script>


<style scoped>
.badge {
    @apply bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center;
}
</style>