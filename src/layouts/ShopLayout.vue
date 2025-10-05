<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <ShopHeader />
        <main class="flex-grow w-full bg-[#b8974fcf]">
            <slot />
        </main>
        <AppFooter />
    </div>
</template>

<script setup>
import ShopHeader from '@/components/common/ShopHeader.vue';
import AppFooter from '@/components/common/AppFooter.vue'
</script>