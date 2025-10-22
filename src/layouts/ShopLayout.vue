<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <AppHeader />
        <main :class="['flex-grow w-full',bgColor]">
            <slot />
        </main>
        <AppFooter />
    </div>
</template>

<script setup>
import ShopHeader from '@/components/common/ShopHeader.vue';
import AppFooter from '@/components/common/AppFooter.vue'
import AppHeader from '@/components/common/AppHeader.vue';

defineProps({
    bgColor: {
        type: String,
        default: () => " bg-[#b8974fcf]"
    },
})
</script>