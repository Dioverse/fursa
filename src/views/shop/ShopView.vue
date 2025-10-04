<template>
  <ShopLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Main Content -->
      <div class="flex-1 p-6">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6">
          <!-- Sidebar -->
          <div class="w-full lg:w-56 bg-white shadow-md flex-shrink-0">
            <div class="flex items-center font-bold gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors">
              Categories
            </div><hr>
            <div v-for="(category, index) in shopData.categories" :key="index" class="relative group"
              @mouseenter="hovered = index" @mouseleave="hovered = null">
              <!-- Category Item -->
              <div
                class="flex items-center font-semibold gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors">
                <span class="text-xl">{{ category.icon }}</span>
                <span class="text-sm text-gray-700 flex-1">{{ category.name }}</span>

                <!-- Caret Icon (rotates on hover) -->
                <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-90': hovered === index }"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>

              <!-- Subcategories Popup -->
              <div v-if="hovered === index"
                class="absolute top-0 left-full ml-1 w-48 bg-white shadow-lg rounded p-2 z-50 transition-all duration-300 ease-in-out">
                <div v-for="sub in category.subcategories" :key="sub.id"
                  class="p-2 text-sm text-gray-600 hover:bg-gray-100 rounded cursor-pointer">
                  {{ sub.name }}
                  <span v-if="sub.products_count > 0" class="ml-2 text-xs text-gray-400">
                    ({{ sub.products_count }})
                  </span>
                </div>
              </div>
            </div>
          </div>


          <!-- Hero Section -->
          <div
            class="flex-1 relative bg-gradient-to-r from-orange-500 to-orange-400 rounded-xl overflow-hidden shadow-xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center p-12">
              <!-- Left Content -->
              <div class="text-white space-y-6 z-10">
                <div class="flex items-center gap-2">
                  <div class="text-white text-3xl font-bold">
                    FURSA
                    <span
                      class="inline-flex items-center justify-center w-8 h-8 bg-white text-orange-500 rounded-full text-xl ml-1">⭐</span>
                  </div>
                </div>
                <div class="text-4xl font-bold">DELIVERY</div>

                <div class="space-y-3">
                  <h1 class="text-5xl font-bold leading-tight">Send. Track. Collect.</h1>
                  <p class="text-2xl font-light leading-relaxed">
                    Send your packages<br />
                    securely anywhere<br />
                    in Nigeria.
                  </p>
                </div>

                <p class="text-sm opacity-90">**T&Cs Apply**</p>
              </div>

              <!-- Right Content - Truck Image -->
              <div class="relative h-96 lg:h-full flex items-center justify-center">
                <div class="relative">
                  <!-- Nigeria Map Background -->
                  <div class="absolute inset-0 flex items-center justify-center">
                    <svg viewBox="0 0 300 350" class="w-full h-full opacity-30">
                      <path d="M150,20 L280,80 L270,200 L220,260 L140,250 L80,210 L60,120 L100,50 Z" fill="#0d9488"
                        stroke="#0f766e" stroke-width="2" />
                    </svg>
                  </div>

                  <!-- Truck -->
                  <div class="relative z-10 flex items-center justify-center">
                    <svg class="w-72 h-72 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="1.5">
                      <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
                      <path d="M15 18H9" />
                      <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
                      <circle cx="17" cy="18" r="2" />
                      <circle cx="7" cy="18" r="2" />
                    </svg>
                  </div>

                  <!-- Play Button -->
                  <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <div
                      class="bg-pink-500 rounded-full p-4 shadow-lg cursor-pointer hover:bg-pink-600 transition-colors flex items-center justify-center w-16 h-16">
                      <svg class="w-6 h-6 text-white ml-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z" />
                      </svg>
                    </div>
                  </div>

                  <!-- FURSA Delivery Badge -->
                  <div
                    class="absolute top-8 right-8 bg-red-500 text-white px-4 py-2 rounded shadow-lg transform rotate-3">
                    <div class="text-xs font-bold">FURSA</div>
                    <div class="text-xs">DELIVERY</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Sellers Section -->
      <section class="max-w-7xl mx-auto px-4 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold flex items-center">
            <span class="bg-orange-500 text-white px-3 py-1 rounded">Featured Products</span>
          </h2>
          <a href="#" class="text-orange-500 hover:underline">See All ></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div v-for="product in shopData.featured_products" :key="product.id"
            class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative">
            <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
              <img :src="getImageUrl(product.images[0]?.path)" :alt="product.name" class="w-full h-full object-cover"
                @error="handleImageError">
            </div>
            <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
            <div class="flex items-center gap-2">
              <span v-if="product.discount" class="text-orange-500 font-bold">₦ {{
                product.discounted_price.toLocaleString() }}</span>
              <span :class="product.discount ? 'text-gray-400 line-through text-xs' : 'text-orange-500 font-bold'">
                ₦ {{ product.price.toLocaleString() }}
              </span>
            </div>
            <div v-if="product.discount" class="mt-1 absolute top-1 right-2">
              <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded">
                -{{ product.discount.value }}%
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Categories with Products -->
      <div v-for="category in shopData.categories_with_products" :key="category.id" class="max-w-7xl mx-auto px-4 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">{{ category.name }}</h2>
          <a href="#" class="text-orange-500 hover:underline">See All ></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div v-for="product in category.products_by_subcats" :key="product.id"
            class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative">
            <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
              <img :src="getImageUrl(product.images[0]?.path)" :alt="product.name" class="w-full h-full object-cover"
                @error="handleImageError">
            </div>
            <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
            <div class="flex items-center gap-2">
              <span v-if="product.discount" class="text-orange-500 font-bold">₦ {{
                product.discounted_price.toLocaleString() }}</span>
              <span :class="product.discount ? 'text-gray-400 line-through text-xs' : 'text-orange-500 font-bold'">
                ₦ {{ product.price.toLocaleString() }}
              </span>
            </div>
            <div v-if="product.discount" class="mt-1 absolute top-1 right-2">
              <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded">
                -{{ product.discount.value }}%
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-orange-500"></div>
      </div>

      <!-- Error State -->
      <div v-if="error" class="max-w-7xl mx-auto px-4 py-20 text-center">
        <div class="text-red-500 text-xl mb-4">{{ error }}</div>
        <button @click="fetchShopData" class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600">
          Retry
        </button>
      </div>
    </div>
  </ShopLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ShopLayout from '@/layouts/ShopLayout.vue';

const shopData = ref({
  featured_products: [],
  categories_with_products: [],
  tagged_products: []
});

const loading = ref(true);
const error = ref(null);

const fetchShopData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const apiUrl = import.meta.env.VITE_API_BASE_URL;
    const res = await fetch(`${apiUrl}/shop`);

    if (!res.ok) {
      throw new Error('Failed to fetch shop data');
    }

    const response = await res.json();
    shopData.value = response.data;
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
};

const getImageUrl = (path) => {
  if (!path) return 'https://via.placeholder.com/300x300?text=No+Image';
  const apiUrl = import.meta.env.VITE_STORAGE_URL;
  return `${apiUrl}${path}`;
};

const handleImageError = (e) => {
  e.target.src = 'https://via.placeholder.com/300x300?text=Product+Image';
};

const getAllProducts = () => {
  const allProducts = [];
  shopData.value.categories_with_products.forEach(category => {
    allProducts.push(...category.products);
  });
  return allProducts;
};



const hovered = ref(null);   // stores index on hover

onMounted(() => {
  fetchShopData();
});
</script>