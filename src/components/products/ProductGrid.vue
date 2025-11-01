<template>
    <div>
        <div v-if="loading" class="flex flex-wrap justify-center gap-6">
            <div v-for="i in makeSwiper ? 1 : 4" :key="i" class=" w-3/4 xs:w-2/3 sm:w-1/2 md:w-1/3 lg:w-1/4 animate-pulse">
                <div class="bg-gray-300 w-full h-48 rounded-t-lg"></div>
                <div class="bg-white p-4 rounded-b-lg">
                    <div class="h-4 bg-gray-300 rounded mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                    <div class="h-8 bg-gray-300 rounded"></div>
                </div>
            </div>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-12">
            <font-awesome-icon icon="box" size="3x" class="text-gray-400 mb-4" />
            <p class="text-gray-600">{{ $t('shop.no_products_found') }}</p>
        </div>

        <div v-else class="">
            <div v-if="makeSwiper" class="swiper-container">
                <Swiper :modules="[Navigation, FreeMode]" :pagination="{
            type: 'progressbar',
          }" :slides-per-view="1" :space-between="10" :freeMode="true" :speed="700" :breakpoints="{
            490: { slidesPerView: 1, spaceBetween: 14 },
            640: { slidesPerView: 3, spaceBetween: 16 },
            768: { slidesPerView: 3, spaceBetween: 18 },
            1024: { slidesPerView: 4, spaceBetween: 20 },
            1500: { slidesPerView: 4, spaceBetween: 24 }
                }" navigation>
                    <SwiperSlide v-for="product in products" :key="product.id">
                        <ProductCard :product="product" />
                    </SwiperSlide>
                </Swiper>
            </div>

            <div v-else class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import ProductCard from './ProductCard.vue'
import { FreeMode, Navigation } from 'swiper/modules';
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

defineProps({
    products: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    },
    makeSwiper: { // The optional prop to toggle Swiper mode
        type: Boolean,
        default: false,
    }
})
</script>