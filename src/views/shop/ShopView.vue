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
              <div v-for="n in cat_grid" :key="n" class="aspect-[4/3] bg-gray-200 rounded-lg animate-pulse"></div>
            </div>
          </div>
        </section>

        <!-- Featured Products Skeleton -->
        <section class="max-w-7xl mx-auto px-4">
          <div class="h-10 bg-orange-200 rounded mb-4 animate-pulse"></div>
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
          <div class="h-10 bg-orange-200 rounded mb-4 animate-pulse"></div>
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
              <div class="flex items-center font-bold gap-3 p-2 bg-gray-100 rounded cursor-pointer transition-colors">
                Categories
              </div>
              <hr>
              <div v-for="(category, index) in shopData.categories" :key="index" class="relative group"
                @mouseenter="hovered = index" @mouseleave="hovered = null">
                <!-- Category Item -->
                <div class="flex items-center font-semibold gap-3 p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors">
                   <!-- :class="{ '': hovered === index }" -->
                  <RouterLink :to="`/product/${category.slug}`" :key="category.id">
                    <span class="text-xl">{{ category.icon }}</span>
                    <span class="text-sm text-gray-700 flex-1">{{ category.name }}</span>
                  </RouterLink>

                  <!-- Caret Icon -->
                  <!-- <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-90': hovered === index }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg> -->
                </div>

                <!-- Subcategories Popup -->
                <div v-if="hovered === index" class="absolute top-0 left-full ml-1 w-48 bg-white shadow-lg rounded p-2 z-50 transition-all duration-300 ease-in-out">
                  <div v-for="sub in category.subcategories" :key="sub.id" class="p-2 text-sm text-gray-600 hover:bg-gray-100 rounded cursor-pointer">
                    <RouterLink :to="`/product/${category.slug}--${sub.slug}`">
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
            <div
              class="flex-1 relative bg-gradient-to-r from-orange-500 to-orange-400 rounded-xl overflow-hidden shadow-xl">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center py-6 px-12">
                <!-- Left Content -->
                <div class="text-white space-y-6 z-10">
                  <div class="flex items-center gap-2">
                    <div class="text-white text-3xl font-bold">
                      FURSA
                      <span
                        class="inline-flex items-center justify-center w-8 h-8 bg-white text-primary rounded-full text-xl ml-1">⭐</span>
                    </div>
                  </div>
                  <div class="space-y-3">
                    <h1 class="text-3xl font-bold leading-tight">Send. Track. Collect.</h1>
                    <p class="text-2xl font-light leading-relaxed">
                      Send your packages securely anywhere in Nigeria.
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

        <!-- Category Grid Section -->
        <section class="max-w-7xl mx-auto mb-8">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
              <RouterLink :to="`/product/${cat.parent.slug}--${cat.slug}`" v-for="cat in shopData.categoryGrid" :key="cat.name"
                class="relative group cursor-pointer overflow-hidden rounded-lg">
                <div class="aspect-[4/3] overflow-hidden">
                  <img :src="cat.image" :alt="cat.name" loading="lazy" @error="handleImageError"
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
          <div class="flex justify-between items-center mb-4 bg-primary py-1 px-3 rounded">
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

          <!-- Slider Container -->
          <div class="relative group/slider">
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
                <div :to="`/product/${product.slug}`" v-for="product in shopData.featured_products" :key="product.id"
                  class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative flex-shrink-0 w-[calc(50%-0.5rem)] md:w-[calc(25%-0.75rem)] lg:w-[calc(16.666%-0.833rem)]">
                  <RouterLink :to="`/product/${product.slug}`">
                    <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
                      <img :src="getImageUrl(product.images[0]?.path)" :alt="product.name" loading="lazy"
                        class="w-full h-full object-cover" @error="handleImageError">
                    </div>
                    <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
                    <div class="flex flex-col">
                      <span v-if="product.discount" class="text-primary font-bold">₦ {{
                        product.discounted_price.toLocaleString() }}</span>
                      <span :class="product.discount ? 'text-gray-400 line-through text-xs' : 'text-primary font-bold'">
                        ₦ {{ product.price.toLocaleString() }}
                      </span>
                    </div>
                    <div v-if="product.discount" class="mt-1 absolute top-1 right-2">
                      <span class="bg-orange-100 text-primary text-xs px-2 py-1 rounded">
                        -{{ product.discount.value }}%
                      </span>
                    </div>
                  </RouterLink>
                </div>
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
          <div class="flex justify-between items-center mb-4 bg-primary py-1 px-3 rounded">
            <h2 class="text-2xl font-bold flex items-center">
              <span class="text-white">{{ category.name }}</span>
            </h2>
            <RouterLink :to="`/product/${category.slug}`" class="text-white hover:underline inline-flex items-center">
              More
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </RouterLink>
          </div>

          <!-- Slider Container -->
          <div class="relative group/slider">
            <!-- Previous Button -->
            <button @click="scrollCategorySlider(idx, -1)"
              class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white shadow-lg rounded-full p-3 opacity-0 group-hover/slider:opacity-100 transition-opacity">
              <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>

            <!-- Scrollable Products -->
            <div :ref="el => categorySliders[idx] = el" class="flex gap-4 overflow-x-auto scrollbar-hide scroll-smooth px-1 py-2">
              <RouterLink :to="`/product/${category.slug}`" v-for="product in category.products_by_subcats"
                :key="product.id"
                class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 cursor-pointer relative flex-shrink-0 w-[calc(50%-0.5rem)] md:w-[calc(25%-0.75rem)] lg:w-[calc(16.666%-0.833rem)]">
                <div class="aspect-square bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
                  <img :src="getImageUrl(product.images[0]?.path)" :alt="product.name" loading="lazy"
                    class="w-full h-full object-cover" @error="handleImageError">
                </div>
                <h3 class="text-sm font-medium mb-1 truncate">{{ product.name }}</h3>
                <div class="flex flex-col">
                  <span v-if="product.discount" class="text-primary font-bold">₦ {{
                    product.discounted_price.toLocaleString() }}</span>
                  <span :class="product.discount ? 'text-gray-400 line-through text-xs' : 'text-primary font-bold'">
                    ₦ {{ product.price.toLocaleString() }}
                  </span>
                </div>
                <div v-if="product.discount" class="mt-1 absolute top-1 right-2">
                  <span class="bg-orange-100 text-primary text-xs px-2 py-1 rounded">
                    -{{ product.discount.value }}%
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

const categoryGrid = [
  {
    name: 'Phones & Tablets',
    image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'
  },
  {
    name: 'TV & Audio',
    image: 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=400&h=300&fit=crop'
  },
  {
    name: 'Beauty Must Have',
    image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=300&fit=crop'
  },
  {
    name: 'Appliances',
    image: 'https://images.unsplash.com/photo-1556911220-bff31c812dba?w=400&h=300&fit=crop'
  },
  {
    name: 'Generators & Inverters',
    image: 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=400&h=300&fit=crop'
  },
  {
    name: 'Fashion',
    image: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=400&h=300&fit=crop'
  },
  {
    name: 'Home & Office',
    image: 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=400&h=300&fit=crop'
  },
  {
    name: 'Computing',
    image: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop'
  },
  {
    name: 'Wristwatches',
    image: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=400&h=300&fit=crop'
  },
  {
    name: 'Mobile Accessories',
    image: 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=300&fit=crop'
  },
  {
    name: 'Sneakers',
    image: 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400&h=300&fit=crop'
  },
  {
    name: 'Automobile',
    image: 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=400&h=300&fit=crop'
  }
];

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
    const res = await fetch(`${apiUrl}/shop?featured_limit=12&products_per_category=12`);

    if (!res.ok) {
      throw new Error('Failed to fetch shop data');
    }

    const response = await res.json();

    shopData.value = {
      ...response.data,
      cat_grid
    };
  } catch (err) {
    error.value = err.message;
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
</style>