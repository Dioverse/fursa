<template>
    <div>
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="i in 8" :key="i" class="animate-pulse">
                <div class="bg-gray-300 h-48 rounded-t-lg"></div>
                <div class="bg-white p-4 rounded-b-lg">
                    <div class="h-4 bg-gray-300 rounded mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                    <div class="h-8 bg-gray-300 rounded"></div>
                </div>
            </div>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-12">
            <font-awesome-icon icon="box" size="3x" class="text-gray-400 mb-4" />
            <p class="text-gray-600">No products found</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>
    </div>
</template>

<script setup>
import ProductCard from './ProductCard.vue'

defineProps({
    products: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
})
</script>