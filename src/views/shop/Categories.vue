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
      </div>

      <!-- Main Content -->
      <div v-else>
        <!-- Hero Section -->
        <div class="flex-1 py-4 h-[384px]">
          <div class="max-w-7xl max-h-full mx-auto flex flex-col lg:flex-row gap-6">
            <!-- Hero Banner -->
            <div class="flex-1 relative bg-gradient-to-r from-orange-500 to-orange-400 rounded-xl overflow-hidden shadow-xl">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center py-6 px-12">
                //
              </div>
            </div>
          </div>
        </div>

        <!-- Category Grid Section -->
        <section class="max-w-7xl mx-auto mb-8">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
              <RouterLink :to="`/shop/${cat.parent.slug}--${cat.slug}`" v-for="cat in shopData.categoryGrid" :key="cat.name"
                class="relative group cursor-pointer overflow-hidden rounded-lg">
                <div class="aspect-[4/3] overflow-hidden">
                  <img :src="cat.image" :alt="cat.name" loading="lazy" @error="handleImageError"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-3">
                  <h3 class="text-white font-medium text-sm text-center">{{ cat.name }}</h3>
                </div>
              </RouterLink :to="`/shop/${cat.parent.slug}--${cat.slug}`">
            </div>
          </div>
        </section>
      </div>

      <!-- Error State -->
      <div v-if="error" class="max-w-7xl mx-auto px-4 py-20 text-center">
        <div class="text-red-500 text-xl mb-4">{{ error }}</div>
        <button @click="fetchCategoryData" class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600">
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
  categoryGrid: []
});

const loading = ref(true);
const error = ref(null);
const categorySliders = ref([]);

const fetchCategoryData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const apiUrl = import.meta.env.VITE_API_BASE_URL;
    const res = await fetch(`${apiUrl}/cat?featured_limit=12&products_per_category=12`);

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
  fetchCategoryData();
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