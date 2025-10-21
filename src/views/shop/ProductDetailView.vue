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
                <div class="m-auto lg:w-full w-[60%]">

                    <!-- Main Slideshow - Flowbite Carousel -->
                    <div id="default-carousel" class="relative bg-gray-100 rounded-lg p-8 overflow-hidden"
                        data-carousel="slide">
                        <!-- Carousel wrapper -->
                        <div v-if="isLoading" class="animate-pulse bg-gray-200 h-full w-full rounded-lg md:h-96"></div>
                        <div v-else class="relative h-56 overflow-hidden rounded-lg md:h-96">
                            <transition name="fade" mode="out-in">
                                <img :key="currentImageIndex"
                                    v-lazy="getImageUrl(product.images?.[currentImageIndex]?.path)" :alt="product.name"
                                    class="w-full h-full object-contain cursor-pointer transition-opacity duration-300"
                                    @error="handleImageError" @click="showLightbox(currentImageIndex)" />
                            </transition>
                            <!-- Slider indicators -->
                            <!-- <div
                                class="absolute z-30 p-3 bg-black/10 rounded-lg flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                                <button v-for="(image, index) in product.images" :key="index" type="button"
                                    class="w-2 h-2 rounded-full"
                                    :class="currentImageIndex === index ? 'bg-gray-500' : 'bg-black/80'"
                                    @click="currentImageIndex = index"></button>
                            </div> -->
                            <!-- <button @click="prevImage" type="button"
                                class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                                data-carousel-prev>
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                                    <svg class="w-3 h-3 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 1 1 5l4 4" />
                                    </svg>
                                    <span class="sr-only">Previous</span>
                                </span>
                            </button>
                            <button @click="nextImage" type="button"
                                class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                                data-carousel-next>
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                                    <svg class="w-3 h-3 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                    <span class="sr-only">Next</span>
                                </span>
                            </button> -->
                        </div>

                    </div>

                    <!-- Thumbnail Images -->
                    <div v-if="isLoading" class="flex gap-2">
                        <div v-for="i in 4" :key="i" class="animate-pulse bg-gray-200 rounded w-20 h-20"></div>
                    </div>

                    <Swiper v-if="product.images?.length" :modules="[Navigation, FreeMode]" :slides-per-view="4"
                        :space-between="8" @swiper="onSwiper" :freeMode="true" navigation class="product-thumbnails"
                        :breakpoints="{ 1280: { slidesPerView: 4, spaceBetween: 8 } }">
                        
                        <!-- Custom Prev Button -->
                        <div @click="prevImage();swiperPrevSlide()"
                            class="swiper-button-prev makeshowflex !m-auto top-1/2 -translate-y-1/2 left-0 z-30 w-7 h-7 rounded-full bg-black/30 flex items-center justify-center cursor-pointer hover:bg-black/50">
                            <svg class="w-3 h-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </div>
                        
                        <SwiperSlide v-for="(img, i) in product.images" :key="i" class="py-1">
                            <button @click="currentImageIndex = i"
                                class="bg-gray-100 rounded p-1 cursor-pointer overflow-hidden w-[90px] h-20 transition ring-offset-2"
                                :class="i === currentImageIndex ? 'ring-2 ring-primary' : ''">
                                <img v-lazy="getImageUrl(img.path)" :alt="product.name"
                                    class="w-full h-full object-cover transition-opacity duration-300"
                                    @error="handleImageError" />
                            </button>
                        </SwiperSlide>

                        <!-- Custom Next Button -->
                        <div @click="nextImage();swiperNextSlide()"
                            class="swiper-button-next makeshowflex !m-auto top-1/2 -translate-y-1/2 right-0 z-30 w-7 h-7 rounded-full bg-black/30 flex items-center justify-center cursor-pointer hover:bg-black/50">
                            <svg class="w-3 h-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Swiper>


                    <!-- Lightbox -->
                    <VueEasyLightbox :visible="visibleRef" :imgs="product.images?.map(img => getImageUrl(img.path))"
                        :index="indexRef" @hide="visibleRef = false" />
                </div>


                <!-- Product Info -->
                <div>
                    <!-- Skeleton for title -->
                    <div v-if="isLoading" class="space-y-4">
                        <div class="animate-pulse bg-gray-200 h-10 w-3/4 rounded"></div>
                        <div class="animate-pulse bg-gray-200 h-6 w-1/2 rounded"></div>
                        <div class="animate-pulse bg-gray-200 h-8 w-1/3 rounded"></div>
                        <div class="space-y-2">
                            <div class="animate-pulse bg-gray-200 h-4 w-full rounded"></div>
                            <div class="animate-pulse bg-gray-200 h-4 w-full rounded"></div>
                            <div class="animate-pulse bg-gray-200 h-4 w-2/3 rounded"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="animate-pulse bg-gray-200 h-10 w-full rounded"></div>
                            <div class="animate-pulse bg-gray-200 h-10 w-full rounded"></div>
                        </div>
                    </div>

                    <!-- Actual Content -->
                    <div v-else>
                        <h1 class="text-3xl font-bold mb-4">{{ product.name }}</h1>

                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center">
                                <font-awesome-icon v-for="i in 5" :key="i" icon="star"
                                    :class="i <= product.rating ? 'text-yellow-400' : 'text-gray-300'" />
                            </div>
                            <span class="text-gray-600">({{ product.reviews }} reviews)</span>
                            <span class="text-green-600">✓ In Stock</span>
                        </div>

                        <div class="flex items-center space-x-2 line-clamp-1">
                            <div class="text-3xl font-bold text-primary mb-6">
                                ₦{{ getDisplayPrice(product) }}
                                <span v-if="product.discount" class="line-through text-sm text-gray-400">₦ {{
                                    priceToLocale(getBasePrice(product)) }}</span>
                            </div>
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
                            <button v-if="!isInCart(product.id).value" @click="addToCart(product)"
                                :disabled="loadingStates[product.id]"
                                class="flex-1 bg-gold-500 text-white py-2 disabled:opacity-50 disabled:cursor-not-allowed rounded hover:bg-gold-100 hover:text-black text-sm font-semibold">
                                <div v-if="loadingStates[product.id]" class="flex justify-center">
                                    <svg class="animate-spin h-4 w-4 text-gold-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </div>
                                <div v-else>Add to cart</div>
                            </button>

                            <!-- Quantity Control -->
                            <div v-else class="flex items-center rounded overflow-hidden w-max">
                                <button :disabled="getCartQuantity(product.id).value <= 1 || loadingStates[product.id]"
                                    @click="updateQuantity(product, getCartQuantity(product.id).value - 1)"
                                    class="px-[14px] py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                                    -
                                </button>

                                <div
                                    class="w-12 text-center border-x border-gold-300 text-sm flex justify-center items-center h-[38px]">
                                    <div v-if="loadingStates[product.id]" class="flex justify-center items-center">
                                        <svg class="animate-spin h-4 w-4 text-gold-500"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                                        {{ getCartQuantity(product.id).value }}
                                    </div>
                                </div>

                                <button :disabled="loadingStates[product.id]"
                                    @click="updateQuantity(product, getCartQuantity(product.id).value + 1)"
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
            </div>

            <!-- Tabs -->
            <div v-if="!isLoading" class="mt-12">
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
            <div v-if="!isLoading" class="mt-12">
                <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-6">Related Products</h2>
                <ProductGrid :makeSwiper="true" :products="relatedProducts" />
            </div>
        </div>
        <!-- Bottom CTA Section -->
        <CTA v-if="!isLoading" />
        <!-- Download Button -->
        <Brochure v-if="!isLoading" />
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
import { FreeMode, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import { useWishlistStore } from '@/stores/wishlist'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'
import { getBasePrice, getDisplayPrice, getImageUrl, handleImageError, priceToLocale, toNumber, toUcwords } from '@/utils/helpers'
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
const isLoading = ref(true)
const currentImageIndex = ref(0)

const tabs = [
    { id: 'description', label: 'Description' },
    { id: 'specifications', label: 'Specifications' },
    { id: 'reviews', label: 'Reviews' }
]

const swiperInstance = ref()

function onSwiper(swiper) {
    swiperInstance.value = swiper
}
const swiperNextSlide = () => {
    swiperInstance.value.slideNext()
};
const swiperPrevSlide = () => {
    swiperInstance.value.slidePrev()
};

const nextImage = () => {
    if (product.value.images?.length) {
        currentImageIndex.value = (currentImageIndex.value + 1) % product.value.images.length
    }
}

const prevImage = () => {
    if (product.value.images?.length) {
        currentImageIndex.value = (currentImageIndex.value - 1 + product.value.images.length) % product.value.images.length
    }
}

const visibleRef = ref(false)
const indexRef = ref(0)
const showLightbox = (index) => {
    indexRef.value = index
    visibleRef.value = true
}

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

onMounted(async () => {
    isLoading.value = true
    const id = route.params.id

    try {
        const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/products/${id}`)
        const json = await res.json()

        const apiProduct = json.data

        // 🔄 Map API to UI structure
        product.value = {
            id: apiProduct.id,
            name: apiProduct.name,
            price: apiProduct.price,
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
            specifications: [
                { name: 'Stock Quantity', value: apiProduct.stock_quantity },
                { name: 'Low Stock Threshold', value: apiProduct.low_stock_threshold },
                { name: 'Category', value: apiProduct.category?.name }
            ],
            images: apiProduct.images,
            reviewsList: [],
            ...(apiProduct.discount
                ? {
                    discount: apiProduct.discount,
                    discounted_price: apiProduct.discounted_price,
                }
                : { discount: null }),
        }

        isInWishlist.value = wishlistStore.items.some(item => item.id === product.value.id)

        // Related products
        relatedProducts.value = json.related.map(p => ({
            id: p.id,
            name: p.name,
            slug: p.slug,
            sku: p.sku,
            price: p.price,
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
    } finally {
        isLoading.value = false
    }
})
</script>
<style>
.swiper-button-next,
.swiper-button-prev {
    width: 20px;
    display: none;
    align-items: center;
}

.makeshowflex {
    display: flex !important;
}

.swiper-button-next svg,
.swiper-button-prev svg {
    width: 15px;
    height: 15px;
}

</style>