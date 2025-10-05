<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <ShopHeader />
        <main class="flex-grow container mx-auto px-4">
            <slot />
        </main>
        <AppFooter />
    </div>
</template>

<script setup>
import ShopHeader from '@/components/common/ShopHeader.vue';
import AppFooter from '@/components/common/AppFooter.vue'
</script>