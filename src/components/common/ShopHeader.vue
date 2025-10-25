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
            <input v-model="searchQuery" type="text" :placeholder="$t('header.search_placeholder') || 'Search Products...'"
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

                    <RouterLink to="/cart" class="bg-white px-3 py-2 rounded flex items-center gap-2 hover:bg-gray-100 transition relative">
                        <div class="relative">
                            <font-awesome-icon icon="shopping-cart" class="text-gray-700 text-xl" />
                            <span v-if="cartStore.itemCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5">
                                {{ cartStore.itemCount }}
                            </span>
                        </div>
                    </RouterLink>


          <RouterLink v-if="authStore.isAuthenticated" to="/dashboard"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 transition flex items-center gap-2">
                        <font-awesome-icon icon="user-circle" />
            <span class="hidden md:inline">{{ $t('header.account') || 'Account' }}</span>
                    </RouterLink>

          <RouterLink v-else to="/login"
                        class="bg-primary text-white px-4 py-2 rounded hover:bg-opacity-90 transition flex items-center gap-2">
                        <font-awesome-icon icon="user" />
            <span class="hidden md:inline">{{ $t('header.login') || 'Login' }}</span>
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
    </header>

    <!-- <header class="bg-white shadow-sm sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-4">
          <button @click="showSidebar = true" class="lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h1 class="text-2xl md:text-3xl font-bold text-orange-500">FURSA⚡</h1>
        </div>
        <div class="hidden md:flex flex-1 max-w-2xl mx-8">
          <div class="relative w-full flex">
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
          </div>
        </div>
        <div class="flex items-center gap-4">
          <svg class="w-6 h-6 md:hidden cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <svg class="w-6 h-6 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <div class="relative cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span v-if="cartCount > 0"
              class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              {{ cartCount }}
            </span>
          </div>
        </div>
      </div>
    </header> -->
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
/* removed unused .badge class to avoid Tailwind @apply lint warning */
</style>