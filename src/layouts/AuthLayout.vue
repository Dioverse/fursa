<template>
    <div class="min-h-screen flex relative">
        <!-- Top-right language switcher -->
        <div class="absolute top-4 right-4 z-20">
            <div class="relative" @keydown.escape="open = false" data-lang-dropdown>
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 border rounded-md bg-white/90 hover:bg-white shadow-sm">
                    <img :src="`/images/language/${languageStore.currentLanguage.icon}`" class="w-[20px] h-[15px] rounded" :alt="languageStore.currentLanguage.name">
                    <span class="hidden sm:inline text-sm">{{ languageStore.currentLanguage.name }}</span>
                    <font-awesome-icon :icon="['fas','chevron-down']" class="text-gray-500 text-xs" />
                </button>
                <div v-if="open" class="absolute right-0 mt-2 w-40 bg-white border rounded-md shadow-lg py-1">
                    <a v-for="lang in languageStore.allowedLanguages" :key="lang.code" href="#"
                       class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 text-sm"
                       @click.prevent="switchLang(lang.code)">
                        <img :src="`/images/language/${lang.icon}`" class="w-[20px] h-[15px] rounded" :alt="lang.name">
                        <span>{{ lang.name }}</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- Left Sidebar -->
        <div 
            class="hidden lg:flex lg:w-2/5 bg-cover bg-center p-12 items-center justify-center relative"
            style="background-image: url('/images/login-hero.png');">
            
            <!-- Overlay (optional for readability) -->
            <!-- <div class="absolute inset-0 bg-black/50"></div> -->

            <!-- Content -->
            <!-- <div class="relative text-white text-center max-w-md z-10">
                <img src="/images/logo.png" alt="Fursa Energy" class="w-32 mx-auto mb-8 filter brightness-0 invert" />
                <slot name="sidebar">
                    <h1 class="text-4xl font-bold mb-4">Welcome to Fursa Energy</h1>
                    <p class="text-lg opacity-90">Your trusted partner for premium lubricants and energy solutions</p>
                </slot>
            </div> -->
        </div>


                <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8 text-center">
                    <img src="/images/logo.png" alt="Fursa Energy" class="w-24 mx-auto" />
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
// Auth layout for login/register pages with a lightweight language switcher
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useLanguageStore } from '@/stores/language'

const open = ref(false)
const languageStore = useLanguageStore()

const onClickOutside = (e) => {
    // close dropdown if clicked outside
    const el = e.target.closest('[data-lang-dropdown]')
    if (!el) open.value = false
}

const switchLang = (lang) => {
    languageStore.set(lang)
    open.value = false
}

onMounted(() => {
    document.addEventListener('click', onClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside)
})
</script>