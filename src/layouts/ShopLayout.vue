<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <AppHeader />
        <main class="flex-grow w-full bg-[#b8974fcf]">
            <slot />
        </main>
        <AppFooter />
    </div>
</template>

<script setup>
import ShopHeader from '@/components/common/ShopHeader.vue';
import AppFooter from '@/components/common/AppFooter.vue'
import AppHeader from '@/components/common/AppHeader.vue';
</script>