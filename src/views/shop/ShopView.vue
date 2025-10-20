<template>
  <ShopLayout>
    <div class="min-h-screen mx-auto bg-gray-50 container lg:px-20 bg-transparent">
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
            <div class="flex-1 bg-gray-200 rounded-xl h-96 animate-pulse"></div>
          </div>
        </div>

        <!-- Category Grid Skeleton -->
        <section class="max-w-7xl mx-auto px-4">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
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
        <div class="flex-1 py-4 h-[384px]">
          <div class="max-w-7xl max-h-full mx-auto flex flex-col lg:flex-row gap-6">
            <!-- Sidebar -->
            <div class="w-full lg:w-56 bg-white shadow-md flex-shrink-0 rounded">
              <RouterLink :to="`/categories`" class="flex items-center font-bold gap-3 p-2 bg-gray-100 rounded cursor-pointer transition-colors">
                Categories
              </RouterLink>
              <hr>
              <div v-for="(category, index) in shopData.categories" :key="index" class="relative group"
                @mouseenter="hovered = index" @mouseleave="hovered = null">
                <!-- Category Item -->
                <div
                  class="flex items-center font-semibold gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors">
                  <RouterLink :to="`/c/${category.slug}`" :key="category.id">
                    <span class="text-sm text-gray-700 flex-1">{{ category.name }}</span>
                  </RouterLink>
                </div>

                <!-- Subcategories Popup -->
                <div v-if="hovered === index && category.subcategories && category.subcategories.length > 0"
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
            <div class="relative flex-1 overflow-hidden">
              <Swiper :modules="[Autoplay, Pagination]" :autoplay="{ delay: 4000, disableOnInteraction: false }"
                :loop="true" :pagination="{ clickable: true }" class="mySwiper rounded-xl shadow-xl">
                <SwiperSlide v-for="(slide, index) in slides" :key="index">
                  <div
                    class="relative flex items-center justify-center text-center text-white h-full"
                    :style="{
                      backgroundImage: `url(${slide.image})`,
                      backgroundSize: 'cover',
                      backgroundPosition: 'center',
                    }">
                    <!-- Overlay (dark layer for better text visibility) -->
                    <div class="absolute inset-0 bg-black/50"></div>

                    <!-- Text Content -->
                    <div class="relative z-10 max-w-2xl px-6">
                      <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
                        {{ slide.title }}
                      </h1>
                      <p class="text-lg md:text-2xl font-light mb-6">
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

        <!-- Category Grid Section -->
        <section class="max-w-7xl mx-auto mb-8">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
              <RouterLink :to="`/c/${cat.slug}`" v-for="cat in shopData.categoryGrid"
                :key="cat.name" class="relative group cursor-pointer overflow-hidden rounded-lg">
                <div class="aspect-[4/3] overflow-hidden">
                  <img :src="cat.image || '/images/oil-droplet.jpg'" :alt="cat.name" loading="lazy" @error="handleImageError"
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
        <section class="max-w-7xl mx-auto mb-8 bg-white">
          <div class="flex justify-between items-center mb-4 bg-gold-500 py-1 px-3 rounded">
            <h2 class="text-2xl font-bold flex items-center">
              <span class="text-white">Featured Products</span>
            </h2>
            <a href="#" class="text-white hover:underline inline-flex items-center">
              More
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>

          <!-- Empty State -->
          <div v-if="shopData.featured_products.length < 1" class="w-full py-16 text-center">
            <p class="text-gray-500 text-lg">No featured products available</p>
          </div>

          <!-- Slider Container -->
          <div v-else class="relative group/slider">
            <!-- Previous Button -->
            <button @click="scrollSlider(featuredSlider, -1)"
              class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-3 opacity-0 group-hover/slider:opacity-100 transition-opacity disabled:opacity-30"
              :disabled="!canScrollLeft(featuredSlider)">
              <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>

            <!-- Scrollable Products -->
            <div ref="featuredSlider" class="flex gap-4 overflow-x-auto scrollbar-hide scroll-smooth px-1 py-2">
              <RouterLink :to="`/product/${product.slug}`" v-for="product in shopData.featured_products" :key="product.id"
                class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative flex-shrink-0 w-[calc(50%-0.5rem)] md:w-[calc(25%-0.75rem)] lg:w-[calc(16.666%-0.833rem)]">
                <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
                  <img :src="getImageUrl(product.images?.[0]?.path)" :alt="product.name" loading="lazy"
                    class="w-full h-full object-cover" @error="handleImageError">
                </div>
                <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
                <div class="flex flex-col">
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
            </div>

            <!-- Next Button -->
            <button @click="scrollSlider(featuredSlider, 1)"
              class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-3 opacity-0 group-hover/slider:opacity-100 transition-opacity disabled:opacity-30"
              :disabled="!canScrollRight(featuredSlider)">
              <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </section>

        <!-- Categories with Products -->
        <div v-for="(category, idx) in shopData.categories_with_products" :key="category.id"
          class="max-w-7xl mx-auto mb-8">
          <div class="flex justify-between items-center mb-4 bg-gold-500 py-1 px-3 rounded">
            <h2 class="text-2xl font-bold flex items-center">
              <span class="text-white">{{ category.name }}</span>
            </h2>
            <RouterLink :to="`/c/${category.slug}`" class="text-white hover:underline inline-flex items-center">
              More
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </RouterLink>
          </div>

          <!-- Empty State -->
          <div v-if="category.products.length < 1" class="w-full py-16 text-center">
            <p class="text-gray-500 text-lg">No products available in {{ category.name }}</p>
          </div>

          <!-- Slider Container -->
          <div v-else class="relative group/slider">
            <!-- Previous Button -->
            <button @click="scrollCategorySlider(idx, -1)"
              class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-3 opacity-0 group-hover/slider:opacity-100 transition-opacity">
              <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>

            <!-- Scrollable Products -->
            <div :ref="el => categorySliders[idx] = el"
              class="flex gap-4 overflow-x-auto scrollbar-hide scroll-smooth px-1 py-2">
              <RouterLink :to="`/product/${product.slug}`" v-for="product in category.products"
                :key="product.id"
                class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative flex-shrink-0 w-[calc(50%-0.5rem)] md:w-[calc(25%-0.75rem)] lg:w-[calc(16.666%-0.833rem)]">
                <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
                  <img :src="getImageUrl(product.images?.[0]?.path)" :alt="product.name" loading="lazy"
                    class="w-full h-full object-cover" @error="handleImageError">
                </div>
                <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
                <div class="flex flex-col">
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
            </div>

            <!-- Next Button -->
            <button @click="scrollCategorySlider(idx, 1)"
              class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-3 opacity-0 group-hover/slider:opacity-100 transition-opacity">
              <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
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
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

const shopData = ref({
  featured_products: [],
  categories_with_products: [],
  categories: [],
  categoryGrid: []
});

const loading = ref(true);
const error = ref(null);
const hovered = ref(null);
const featuredSlider = ref(null);
const categorySliders = ref([]);

const fetchShopData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const apiUrl = import.meta.env.VITE_API_BASE_URL;
    const res = await fetch(`${apiUrl}/shop?featured_limit=12&products_per_category=12`);

    if (!res.ok) {
      throw new Error('Failed to fetch shop data');
    }

    const response = await res.json();
    shopData.value = response.data;
  } catch (err) {
    error.value = err.message;
    console.error('Fetch error:', err);
  } finally {
    loading.value = false;
  }
};

const getImageUrl = (path) => {
  if (!path) return '/images/oil-droplet.jpg';
  const apiUrl = import.meta.env.VITE_STORAGE_URL;
  return `${apiUrl}${path}`;
};

const handleImageError = (e) => {
  e.target.src = '/images/oil-droplet.jpg';
};

const canScrollLeft = (slider) => {
  if (!slider) return false;
  return slider.scrollLeft > 0;
};

const canScrollRight = (slider) => {
  if (!slider) return false;
  return slider.scrollLeft < (slider.scrollWidth - slider.clientWidth);
};

const scrollSlider = (slider, direction) => {
  if (slider) {
    const scrollAmount = slider.offsetWidth * 0.8;
    slider.scrollBy({
      left: direction * scrollAmount,
      behavior: 'smooth'
    });
  }
};

const scrollCategorySlider = (index, direction) => {
  const slider = categorySliders.value[index];
  if (slider) {
    const scrollAmount = slider.offsetWidth * 0.8;
    slider.scrollBy({
      left: direction * scrollAmount,
      behavior: 'smooth'
    });
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

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.mySwiper {
  width: 100%;
  height: 100%;
}
</style>