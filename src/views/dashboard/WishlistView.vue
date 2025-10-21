<template>
    <DashboardLayout>
        <div class="space-y-6">
            <h1 class="lg:text-2xl md:text-xl text-lg font-bold">My Wishlist</h1>

            <!-- Empty State -->
            <div
                v-if="wishlistStore.items.length === 0"
                class="bg-white rounded-lg shadow-md p-12 text-center"
            >
                <font-awesome-icon icon="heart" size="3x" class="text-gray-400 mb-4" />
                <h2 class="text-xl font-semibold mb-2">Your wishlist is empty</h2>
                <p class="text-gray-600 mb-6">
                    Save items you like to purchase them later
                </p>
                <RouterLink
                    to="/shop"
                    class="inline-flex items-center gap-0.5 md:gap-2 bg-primary text-xs text-white px-2 md:px-3 lg:px-6 py-3 rounded hover:bg-opacity-90 transition"
                >
                    <font-awesome-icon icon="shopping-cart" />
                    <span>Continue Shopping</span>
                </RouterLink>
            </div>

            <!-- Wishlist Items -->
            <div
                v-else
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="item in wishlistStore.items"
                    :key="item.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition"
                >
                    <div class="relative">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <font-awesome-icon icon="image" size="3x" class="text-gray-400" />
                        </div>
                        <button
                            @click="removeFromWishlist(item.id)"
                            class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:shadow-lg transition"
                        >
                            <font-awesome-icon icon="times" class="text-red-500" />
                        </button>
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold mb-2">{{ item.name }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ item.short_description }}</p>
                        <p class="text-primary text-xl font-bold mb-4">
                            ₦{{ Number(item.price).toFixed(2) }}
                        </p>

                        <button
                            @click="moveToCart(item)"
                            class="w-full bg-primary text-white py-2 rounded hover:bg-opacity-90 transition"
                        >
                            <font-awesome-icon icon="shopping-cart" class="mr-2" />
                            Move to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { useToast } from 'vue-toastification'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'

const toast = useToast()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

// Remove from wishlist
const removeFromWishlist = (id) => {
    wishlistStore.remove(id)
    toast.success('Item removed from wishlist')
}

// Move to cart
const moveToCart = (item) => {
    cartStore.addItem(item)
    wishlistStore.remove(item.id)
    toast.success('Item moved to cart')
}
</script>