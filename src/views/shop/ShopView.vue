<template>
  <ShopLayout>
    <div class="min-h-screen mx-auto bg-gray-50 container lg:px-20 px-3 bg-transparent">
      <!-- Loading State -->
      <div v-if="loading" class="space-y-8 p-4">
        <!-- Hero Skeleton -->
        <div class="flex-1">
          <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Skeleton -->
            <div class="w-full lg:w-56 bg-white shadow-md p-4 space-y-2 rounded">
              <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
              <div v-for="n in 6" :key="n" class="h-10 bg-gray-100 rounded animate-pulse"></div>
            </div>

            <!-- Hero Skeleton -->
            <div class="flex-1 bg-gray-200 rounded h-96 animate-pulse"></div>
          </div>
        </div>

        <!-- Category Grid Skeleton -->
        <section class="max-w-7xl mx-auto px-4">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div
              class="grid grid-cols-2 xxs:grid-cols-3 xs:grid-cols-3 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-4">
              <div v-for="n in 6" :key="n" class="aspect-[4/3] bg-gray-200 rounded-lg animate-pulse"></div>
            </div>
          </div>
        </section>

        <!-- Featured Products Skeleton -->
        <section class="max-w-7xl mx-auto px-4">
          <div class="h-10 bg-gold-200 rounded mb-4 animate-pulse"></div>
          <div class="flex gap-4 overflow-hidden">
            <div v-for="n in 6" :key="n" class="flex-shrink-0 w-40">
              <div class="bg-white rounded-lg shadow p-4">
                <div class="aspect-square bg-gray-200 rounded mb-2 animate-pulse"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 animate-pulse"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3 animate-pulse"></div>
              </div>
            </div>
          </div>
        </section>

        <!-- Category Products Skeleton -->
        <div v-for="n in 3" :key="n" class="max-w-7xl mx-auto px-4">
          <div class="h-10 bg-gold-200 rounded mb-4 animate-pulse"></div>
          <div class="flex gap-4 overflow-hidden">
            <div v-for="i in 6" :key="i" class="flex-shrink-0 w-40">
              <div class="bg-white rounded-lg shadow p-4">
                <div class="aspect-square bg-gray-200 rounded mb-2 animate-pulse"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 animate-pulse"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3 animate-pulse"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div v-else>
        <!-- Hero Section -->
        <!-- Parent -->
        <div class="flex-1 py-4">
          <div class="max-w-7xl mx-auto flex flex-row gap-6 overflow-hidden">

            <!-- Sidebar -->
            <div class="w-full md:w-56 hidden md:block xl:block bg-white shadow-md flex-shrink-0 rounded">
              <RouterLink :to="`/categories`"
                class="flex items-center font-bold gap-3 p-2 bg-gray-100 rounded cursor-pointer transition-colors">
                Categories
              </RouterLink>
              <hr>
              <div v-for="(category, index) in shopData.categories" :key="index" class="relative group"
                @mouseenter="hovered = index" @mouseleave="hovered = null">
                <div
                  class="flex items-center font-semibold gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors">
                  <RouterLink :to="`/c/${category.slug}`" :key="category.id">
                    <span class="text-sm text-gray-700 flex-1">{{ category.name }}</span>
                  </RouterLink>
                </div>

                <div v-if="hovered === index && category.subcategories?.length"
                  class="absolute top-0 left-full ml-1 w-48 bg-white shadow-lg rounded p-2 z-50 transition-all duration-300 ease-in-out">
                  <div v-for="sub in category.subcategories" :key="sub.id"
                    class="p-2 text-sm text-gray-600 hover:bg-gray-100 rounded cursor-pointer">
                    <RouterLink :to="`/category/${category.slug}--${sub.slug}`">
                      {{ sub.name }}
                      <span v-if="sub.products_count > 0" class="ml-2 text-xs text-gray-400">
                        ({{ sub.products_count }})
                      </span>
                    </RouterLink>
                  </div>
                </div>
              </div>
            </div>

            <!-- Hero Banner -->
            <div class="flex-1 relative rounded overflow-hidden md:w-full">
              <div class="aspect-[10/9] md:aspect-[10/9] xl:aspect-[16/9] w-full w-full max-h-[384px]">
                <Swiper :modules="[Autoplay, Pagination]" :autoplay="{ delay: 4000, disableOnInteraction: false }"
                  :loop="true" :pagination="{ clickable: true }" class="mySwiper h-full rounded shadow-xl">
                  <SwiperSlide v-for="(slide, index) in slides" :key="index">
                    <div class="relative flex items-center justify-center text-center text-white h-full" :style="{
                      backgroundImage: `url(${slide.image})`,
                      backgroundSize: 'cover',
                      backgroundPosition: 'center',
                    }">
                      <div class="absolute inset-0 bg-black/50"></div>
                      <div class="relative z-10 max-w-2xl px-6">
                        <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                          {{ slide.title }}
                        </h1>
                        <p class="text-md sm:text-lg md:text-xl lg:text-xl font-light mb-6">
                          {{ slide.subtitle }}
                        </p>
                        <RouterLink v-if="slide.cta" :to="slide.cta.link"
                          class="inline-block bg-gold-500 hover:bg-gold-600 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-colors">
                          {{ slide.cta.text }}
                        </RouterLink>
                      </div>
                    </div>
                  </SwiperSlide>
                </Swiper>
              </div>
            </div>
          </div>
        </div>


        <!-- Category Grid Section -->
        <section class="max-w-7xl mx-auto mb-8">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-4">
              <RouterLink :to="`/c/${cat.slug}`" v-for="cat in shopData.categoryGrid" :key="cat.name"
                class="relative group cursor-pointer overflow-hidden rounded-lg">
                <div class="aspect-[4/3] overflow-hidden">
                  <img :src="cat.image || '/images/oil-droplet.jpg'" :alt="cat.name" loading="lazy"
                    @error="handleImageError"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-3">
                  <h3 class="text-white font-medium text-sm text-center">{{ cat.name }}</h3>
                </div>
              </RouterLink>
            </div>
          </div>
        </section>

        <!-- Featured Products Section -->
        <section class="max-w-7xl mx-auto mb-6 bg-white rounded">
          <div class="flex justify-between items-center mb-4 bg-gold-500 py-3 px-3 rounded">
            <h2 class="md:text-xl font-bold text-white">Featured Products</h2>
            <RouterLink :to="`/c?sort_by=if`" class="text-white hover:underline inline-flex items-center">
              More
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </RouterLink>
          </div>

          <div v-if="shopData.featured_products.length < 1" class="w-full py-16 text-center">
            <p class="text-gray-500 text-lg">No featured products available</p>
          </div>

          <Swiper v-else :modules="[Navigation, FreeMode]" :pagination="{
            type: 'progressbar',
          }" :slides-per-view="2" :freeMode="true" :space-between="10" :breakpoints="{
            480: { slidesPerView: 2, spaceBetween: 16 },
            640: { slidesPerView: 3, spaceBetween: 16 },
            768: { slidesPerView: 3, spaceBetween: 16 },
            1024: { slidesPerView: 4, spaceBetween: 20 },
            1280: { slidesPerView: 6, spaceBetween: 24 }
          }" navigation class="mySwiper rounded pb-6">
            <SwiperSlide v-for="product in shopData.featured_products" :key="product.id"
              class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative">
              <RouterLink :to="`/product/${product.slug}`">
                <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
                  <img :src="getImageUrl(product.images?.[0]?.path)" :alt="product.name" loading="lazy"
                    class="w-full h-full object-cover" @error="handleImageError">
                </div>
                <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
                <div class="flex flex-row line-clamp-1">
                  <span v-if="product.discount" class="text-gold-500 font-bold">₦ {{
                    product.discounted_price.toLocaleString() }}</span>
                  <span :class="product.discount ? 'text-gray-400 line-through text-xs' : 'text-gold-500 font-bold'">
                    ₦ {{ product.price.toLocaleString() }}
                  </span>
                </div>
                <div v-if="product.discount" class="mt-1 absolute top-1 right-2">
                  <span class="bg-gold-100 text-gold-500 text-xs px-2 py-1 rounded">
                    -{{ product.discount.value }}{{ product.discount.type === 'percentage' ? '%' : '' }}
                  </span>
                </div>
              </RouterLink>
            </SwiperSlide>
          </Swiper>
        </section>


        <div v-for="(category, idx) in shopData.categories_with_products" :key="category.id"
          class="max-w-7xl mx-auto mb-6">
          <div class="flex justify-between items-center mb-4 bg-gold-500 py-3 px-3 rounded">
            <h2 class="md:lg:text-xl font-bold text-white">{{ category.name }}</h2>
            <RouterLink :to="`/c/${category.slug}`" class="text-white hover:underline inline-flex items-center">
              More
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </RouterLink>
          </div>

          <div v-if="category.products.length < 1" class="w-full py-16 text-center">
            <p class="text-gray-500 text-lg">No products available in {{ category.name }}</p>
          </div>

          <Swiper v-else :modules="[Navigation, FreeMode]" :pagination="{
            type: 'progressbar',
          }" :slides-per-view="2" :space-between="10" :freeMode="true" :speed="700" :breakpoints="{
            480: { slidesPerView: 2, spaceBetween: 14 },
            640: { slidesPerView: 3, spaceBetween: 16 },
            768: { slidesPerView: 3, spaceBetween: 18 },
            1024: { slidesPerView: 4, spaceBetween: 20 },
            1280: { slidesPerView: 6, spaceBetween: 24 }
          }" navigation class="mySwiper rounded pb-10">
            <SwiperSlide v-for="product in category.products" :key="product.id"
              class="group bg-white rounded shadow-md hover:shadow-2xl transition-all duration-300 p-4 cursor-pointer relative">
              <RouterLink :to="`/product/${product.slug}`" class="block">
                <!-- Image -->
                <div class="aspect-square bg-gray-100 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                  <img :src="getImageUrl(product.images?.[0]?.path)" :alt="product.name" loading="lazy"
                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                    @error="handleImageError" />
                </div>

                <!-- Product Name -->
                <h3
                  class="text-sm font-semibold mb-1 truncate text-gray-800 group-hover:text-gold-500 transition-colors duration-200">
                  {{ product.name }}
                </h3>

                <!-- Prices -->
                <div class="flex items-center space-x-2 line-clamp-1">
                  <span v-if="product.discount" class="text-gold-500 font-bold text-sm">
                    ₦ {{ product.discounted_price.toLocaleString() }}
                  </span>
                  <span :class="product.discount
                    ? 'text-gray-400 line-through text-xs'
                    : 'text-gold-500 font-bold text-sm'">
                    ₦ {{ product.price.toLocaleString() }}
                  </span>
                </div>

                <!-- Discount Tag -->
                <div v-if="product.discount"
                  class="absolute top-3 right-3 bg-gold-100 text-gold-600 text-xs font-semibold px-2 py-1 rounded-full shadow-sm">
                  -{{ product.discount.value
                  }}{{ product.discount.type === 'percentage' ? '%' : '' }}
                </div>
              </RouterLink>
            </SwiperSlide>
          </Swiper>

        </div>

      </div>

      <!-- Error State -->
      <div v-if="error" class="max-w-7xl mx-auto px-4 py-20 text-center">
        <div class="text-red-500 text-xl mb-4">{{ error }}</div>
        <button @click="fetchShopData" class="bg-gold-500 text-white px-6 py-2 rounded hover:bg-gold-600">
          Retry
        </button>
      </div>
    </div>
  </ShopLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Pagination, Navigation, FreeMode } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { getImageUrl, handleImageError } from '@/utils/helpers';

const shopData = ref({
  featured_products: [],
  categories_with_products: [],
  categories: [],
  categoryGrid: []
});

const loading = ref(true);
const error = ref(null);
const hovered = ref(null);

// Default grid (desktop)
let cat_grid = 12;
if (window.innerWidth <= 1024) {
  cat_grid = 6;
}

const fetchShopData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const apiUrl = import.meta.env.VITE_API_BASE_URL;
    const res = await fetch(`${apiUrl}/shop?featured_limit=12&products_per_category=12&cat_grid_limit=${cat_grid}`);

    if (!res.ok) {
      throw new Error('Failed to fetch shop data');
    }

    const response = await res.json();
    shopData.value = response.data
  } catch (err) {
    error.value = err.message;
    console.error('Fetch error:', err);
  } finally {
    loading.value = false;
  }
};

const slides = [
  {
    title: "Order. Track. Recieve.",
    subtitle: "Send your packages securely anywhere in Nigeria.",
    image: "/images/oil-bg.png",
    cta: { text: "Get Started", link: "#" },
  },
  {
    title: "Fast and Reliable",
    subtitle: "We deliver your goods safely and on time.",
    image: "/images/hero-bg.png",
    cta: { text: "Track Package", link: "#" },
  },
];

onMounted(() => {
  fetchShopData();
});
</script>