<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center justify-center space-x-1 md:space-x-2">
                    <li>
                        <RouterLink to="/shop" class="text-gray-700 hover:text-primary"><font-awesome-icon
                                icon="shop" /></RouterLink>
                    </li>
                    <li class="mx-[3px]"><font-awesome-icon icon="chevron-right"
                            class="h-[10px] w-[10px] text-gray-400" /></li>
                    <li>
                        <RouterLink :to="`/c/${product.catSlug}`" class="text-gray-700 hover:text-primary">{{
                            product.category }}</RouterLink>
                    </li>
                    <li class="mx-[3px]"><font-awesome-icon icon="chevron-right"
                            class="h-[10px] w-[10px] text-gray-400" /></li>
                    <li class="text-gray-500">{{ product?.name }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images -->
                <!-- Product Images -->
                <div>
                    <!-- Main Image -->
                    <div class="bg-gray-100 lg:w-full w-[60%] rounded-lg m-auto p-8 cursor-pointer relative overflow-hidden"
                        @click="showLightbox(0)">
                        <div v-if="!product.images?.length" class="animate-pulse bg-gray-200 h-80 w-full rounded-lg">
                        </div>

                        <img v-else v-lazy="getImageUrl(product.images[0]?.path)" :alt="product.name"
                            class="w-full h-auto object-contain transition-opacity duration-300"
                            @error="handleImageError" />
                    </div>
                    <div class="mb-4"></div>
                    <!-- Thumbnail Images -->
                    <Swiper :modules="[Navigation]" :slides-per-view="4" :space-between="2" navigation
                        class="product-thumbnails" :breakpoints="{
                            640: { slidesPerView: 10, spaceBetween: 4 },
                            768: { slidesPerView: 10, spaceBetween: 5 },
                            1024: { slidesPerView: 10, spaceBetween: 6 }
                        }">
                        <SwiperSlide v-for="(img, i) in product.images" :key="i">
                            <button
                                class="bg-gray-100 rounded p-1 hover:ring-2 hover:ring-primary cursor-pointer overflow-hidden w-[90px] h-full"
                                @click="showLightbox(i)">
                                <div v-if="!img?.path" class="animate-pulse bg-gray-200 h-20 w-[90px] rounded"></div>
                                <img v-else v-lazy="getImageUrl(img.path)" :alt="product.name"
                                    class="w-full h-full object-cover transition-opacity duration-300"
                                    @error="handleImageError" />
                            </button>
                        </SwiperSlide>
                    </Swiper>

                    <!-- Lightbox -->
                    <VueEasyLightbox :visible="visibleRef" :imgs="product.images?.map(img => getImageUrl(img.path))"
                        :index="indexRef" @hide="visibleRef = false" />
                </div>


                <!-- Product Info -->
                <div>
                    <h1 class="text-3xl font-bold mb-4">{{ product.name }}</h1>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center">
                            <font-awesome-icon v-for="i in 5" :key="i" icon="star"
                                :class="i <= product.rating ? 'text-yellow-400' : 'text-gray-300'" />
                        </div>
                        <span class="text-gray-600">({{ product.reviews }} reviews)</span>
                        <span class="text-green-600">✓ In Stock</span>
                    </div>

                    <div class="text-3xl font-bold text-primary mb-6">
                        ₦{{ product?.discountedPrice?.toLocaleString() || product?.price?.toLocaleString() }}
                    </div>

                    <div class="space-y-4 mb-6">
                        <p class="text-gray-600">{{ product.description }}</p>
                        <div v-if="product.sku">
                            <span class="font-semibold">SKU:</span> {{ product.sku }}
                        </div>
                        <div v-if="product.category">
                            <span class="font-semibold">Category:</span> {{ product.category }}
                        </div>
                    </div>

                    <div class="flex w-full space-x-2 justify-end">
                        <!-- Add To Cart -->
                        <button v-if="!isInCart(product.id)" @click="addToCart(product)"
                            :disabled="loadingStates[product.id]"
                            class="flex-1 bg-gold-500 text-white py-2 disabled:opacity-50 disabled:cursor-not-allowed rounded hover:bg-gold-100 hover:text-black text-sm font-semibold">
                            {{ loadingStates[product.id] ? 'Adding...' : 'Add to cart' }}
                        </button>

                        <!-- Quantity Control -->
                        <div v-else class="flex items-center rounded overflow-hidden w-max">
                            <button :disabled="getCartQuantity(product.id) <= 1 || loadingStates[product.id]"
                                @click="updateQuantity(product, getCartQuantity(product.id) - 1)"
                                class="px-[14px] py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                                -
                            </button>

                            <div
                                class="w-12 text-center border-x border-gold-300 text-sm flex justify-center items-center h-[38px]">
                                <div v-if="loadingStates[product.id]" class="flex justify-center items-center">
                                    <svg class="animate-spin h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </div>
                                <div v-else contenteditable="true"
                                    class="w-full text-center outline-none overflow-hidden"
                                    :data-product-id="product.id" @blur="onQuantityBlur($event, product)"
                                    @keydown.enter.prevent="onQuantityEnter($event, product)">
                                    {{ getCartQuantity(product.id) }}
                                </div>
                            </div>

                            <button :disabled="loadingStates[product.id]"
                                @click="updateQuantity(product, getCartQuantity(product.id) + 1)"
                                class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                                +
                            </button>
                        </div>

                        <!-- Wishlist -->
                        <button @click="toggleWishlist(product)" :class="[
                            'w-1/6 py-2 rounded text-sm font-semibold flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
                            wishlistStore.items.find(p => p.id === product.id)
                                ? 'bg-red-100 text-red-500 hover:bg-red-500 hover:text-white'
                                : 'bg-gold-100 text-mprimary hover:bg-gold-500 hover:text-white'
                        ]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                :fill="wishlistStore.items.find(p => p.id === product.id) ? 'currentColor' : 'none'"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.8 8.6c0 5.4-9.2 11.1-9.8 11.4a1.5 1.5 0 0 1-1 0c-.6-.3-9.8-6-9.8-11.4A5 5 0 0 1 6.8 4.5c1.6 0 3.1.8 4 2a5.1 5.1 0 0 1 4-2 5 5 0 0 1 4.9 4.1z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Features -->
                    <div class="border-t pt-6">
                        <h3 class="font-semibold mb-4">Key Features:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Premium quality motor oil</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Suitable for all vehicle types</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Extended engine protection</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <font-awesome-icon icon="check" class="text-green-600 mt-1" />
                                <span>Improved fuel efficiency</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-12">
                <div class="border-b">
                    <nav class="-mb-px flex gap-8">
                        <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition"
                            :class="activeTab === tab.id ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <div class="py-8">
                    <div v-if="activeTab === 'description'" class="prose max-w-none">
                        <p>{{ product.fullDescription }}</p>
                    </div>

                    <div v-if="activeTab === 'specifications'" class="space-y-4">
                        <table class="w-full">
                            <tbody>
                                <tr v-for="spec in product.specifications" :key="spec.name" class="border-b">
                                    <td class="py-3 font-medium">{{ spec.name }}</td>
                                    <td class="py-3">{{ spec.value }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 font-medium">Tags</td>
                                    <td class="py-3">
                                        <p v-for="tag in product.tags" :key="tag"
                                            class="text-gray-700 hover:text-primary py-[1px]">
                                            <RouterLink :to="`/c?tag=${tag}`">{{ toUcwords(tag) }}</RouterLink>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'reviews'" class="space-y-6">
                        <div v-for="review in product.reviewsList" :key="review.id" class="border-b pb-6">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold">{{ review.author }}</h4>
                                    <div class="flex items-center gap-2">
                                        <div class="flex">
                                            <font-awesome-icon v-for="i in 5" :key="i" icon="star" size="sm"
                                                :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'" />
                                        </div>
                                        <span class="text-sm text-gray-600">{{ review.date }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600">{{ review.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Related Products -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">Related Products</h2>
                <ProductGrid :makeSwiper="true" :products="relatedProducts" />
            </div>
        </div>
        <!-- Bottom CTA Section -->
        <CTA />
        <!-- Download Button -->
        <Brochure />
    </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import ProductGrid from '@/components/products/ProductGrid.vue'
import { addToCart, getCartQuantity, isInCart, onQuantityBlur, onQuantityEnter, toggleWishlist, updateQuantity, loadingStates } from '@/utils/neut';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import { useWishlistStore } from '@/stores/wishlist'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'
import { getImageUrl, handleImageError, toUcwords } from '@/utils/helpers'
import VueEasyLightbox from 'vue-easy-lightbox'
import 'vue-easy-lightbox/external-css/vue-easy-lightbox.css'

const route = useRoute()
const product = ref({})
const relatedProducts = ref([])
const wishlistStore = useWishlistStore()
const toast = useToast()

const quantity = ref(1)
const activeTab = ref('description')
const isInWishlist = ref(false)

const tabs = [
    { id: 'description', label: 'Description' },
    { id: 'specifications', label: 'Specifications' },
    { id: 'reviews', label: 'Reviews' }
]

// const addToCart = async () => {
//     if (loadingStates.value) return

//     loadingStates.value = true

//     try {
//         await cartStore.addItem(product.value, quantity.value)
//     } catch (error) {
//         console.error('Error adding to cart:', error)
//         toast.error('Failed to add product to cart')
//     } finally {
//         loadingStates.value = false
//     }
// }

const saveWishlist = () => {
    if (isInWishlist.value) {
        wishlistStore.remove(product.value.id)
        isInWishlist.value = false
        toast.info(`${product.value.name} removed from wishlist`)
    } else {
        wishlistStore.add(product.value)
        isInWishlist.value = true
        toast.success(`${product.value.name} added to wishlist`)
    }
}

const visibleRef = ref(false)
const indexRef = ref(0)
const showLightbox = (index) => {
    indexRef.value = index
    visibleRef.value = true
}

onMounted(async () => {
    const id = route.params.id

    try {
        const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/products/${id}`)
        const json = await res.json()

        const apiProduct = json.data

        // 🔄 Map API to UI structure
        product.value = {
            id: apiProduct.id,
            name: apiProduct.name,
            price: parseFloat(apiProduct.price),
            discountedPrice: apiProduct.discounted_price ? parseFloat(apiProduct.discounted_price) : null,
            sku: apiProduct.sku,
            rating: apiProduct.rating || 0,
            reviews: apiProduct.reviews || 0,
            description: apiProduct.short_description,
            fullDescription: apiProduct.description,
            category: apiProduct.category?.name || null,
            catSlug: apiProduct.category?.slug || null,
            tags: apiProduct.tags || [],
            stock_quantity: apiProduct.stock_quantity,
            low_stock_threshold: apiProduct.low_stock_threshold,
            discount: apiProduct.discount || null,
            specifications: [
                { name: 'Stock Quantity', value: apiProduct.stock_quantity },
                { name: 'Low Stock Threshold', value: apiProduct.low_stock_threshold },
                { name: 'Category', value: apiProduct.category?.name }
            ],
            images: apiProduct.images,
            reviewsList: []
        }

        isInWishlist.value = wishlistStore.items.some(item => item.id === product.value.id)

        // Related products
        relatedProducts.value = json.related.map(p => ({
            id: p.id,
            name: p.name,
            slug: p.slug,
            sku: p.sku,
            price: p.price,//parseFloat(p.base_price), // ensure number
            category: p.category?.name || "Uncategorized",
            image: p.images[0]?.path,
            ...(p.discount
                ? {
                    discount: p.discount,
                    discounted_price: p.discounted_price,
                }
                : { discount: null }),
        }))
    } catch (error) {
        console.error('Error fetching product:', error)
        toast.error('Failed to load product')
    }
})
</script>