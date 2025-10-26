<template>
    <DashboardLayout>
        <div class="space-y-6">
            <h1 class="lg:text-2xl md:text-xl text-lg font-bold">{{ $t('wishlist.title') }}</h1>

            <!-- Empty State -->
            <div v-if="wishlistStore.items.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                <font-awesome-icon icon="heart" size="3x" class="text-gray-300 mb-4" />
                <h2 class="text-xl font-semibold mb-2">{{ $t('wishlist.empty.title') }}</h2>
                <p class="text-gray-600 mb-6">{{ $t('wishlist.empty.subtitle') }}</p>
                <RouterLink to="/shop"
                    class="inline-flex items-center gap-2 bg-gold-500 text-white text-sm px-4 py-2 rounded hover:bg-gold-600 transition">
                    <font-awesome-icon icon="shopping-cart" />
                    <span>{{ $t('wishlist.continue_shopping') }}</span>
                </RouterLink>
            </div>

            <!-- Wishlist Items -->
            <div v-else class="grid grid-cols-1 sm:flex sm:flex-col sm:grid sm:grid-cols-2 md:grid md:grid-cols-2 lg:grid lg:grid-cols-3 gap-4 md:gap-6">
                <div v-for="item in wishlistStore.items" :key="item.id"
                    class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl transition-all duration-300 group relative border border-gray-100 hover:border-gold-200 hover:ring-2 hover:ring-gold-100 w-full mb-4 sm:mb-2">

                    <RouterLink :to="`/product/${item.slug}`" class="block relative xxs:flex xxs:flex-row xs:flex xs:flex-row items-center">
                        <div
                            class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden xxs:h-1/3 xxs:w-1/3 xxs:flex-shrink-0 xs:h-1/3 xs:w-1/3 xs:flex-shrink-0 lg:h-full lg:w-full">
                            <img :src="getImageUrl(item.images?.[0]?.path)" :alt="item.name" loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                @error="handleImageError" />
                        </div>
                        <div class="p-4 sm:p-2 sm:flex-1 xxs:w-full xs:w-full">
                            <h3 class="font-semibold text-sm mb-1 line-clamp-1" :title="item.name">{{ item.name }}</h3>
                            <div class="flex items-center justify-between text-[11px] text-xs text-gray-500 mb-2">
                                <span v-if="item.sku" class="truncate">#{{ item.sku }}</span>
                                <span v-if="(item.stock_quantity ?? 0) > 0"
                                    class="inline-flex items-center gap-1 rounded-full bg-green-50 text-green-700 px-2 py-0.5">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    {{ $t('shop.in_stock') }}
                                </span>
                            </div>
                            <p class="text-gray-500 text-xs mb-2 line-clamp-2" v-if="item.short_description">
                                {{ item.short_description }}
                            </p>
                            <div class="flex items-end justify-between mb-3">
                                <span class="text-sm font-bold text-primary">
                                    ₦ {{ getDisplayPrice(item) }}
                                    <span v-if="item.discount" class="line-through text-xs md:text-sm text-gray-400 ml-1">₦ {{ priceToLocale(getBasePrice(item)) }}</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click.prevent="moveToCart(item)" :disabled="(item.stock_quantity ?? 0) <= 0"
                                    class="flex-1 bg-gold-500 text-white py-2 rounded hover:bg-gold-600 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm">
                                    <font-awesome-icon icon="shopping-cart" class="mr-2" />
                                    {{ $t('wishlist.move_to_cart') }}
                                </button>
                            </div>
                        </div>
                    </RouterLink>

                    <span v-if="item.discount"
                        class="absolute top-2 right-2 bg-mprimary-500 text-white px-2 py-1 text-[10px] md:text-xs rounded font-semibold shadow">
                        {{ discountLabel(item) }}
                    </span>

                    <button :aria-label="$t('wishlist.remove_item_aria', { name: item.name })"
                        @click="removeFromWishlist(item.id)"
                        class="absolute top-2 left-2 bg-white/90 hover:bg-white rounded-full p-2 shadow-md hover:shadow-lg transition">
                        <font-awesome-icon icon="times" class="text-red-500" />
                    </button>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>

<script setup>
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { discountLabel, getBasePrice, getDisplayPrice, priceToLocale, getImageUrl, handleImageError } from '@/utils/helpers'

const { t } = useI18n()
const toast = useToast()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

// Remove from wishlist
const removeFromWishlist = (id) => {
    wishlistStore.remove(id)
    toast.info(t('wishlist.removed'))
}

// Move to cart
const moveToCart = async (item) => {
    await cartStore.addItem(item)
    wishlistStore.remove(item.id)
    toast.success(t('wishlist.moved_to_cart'))
}
</script>