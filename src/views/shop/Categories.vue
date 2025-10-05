<template>
  <ShopLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- BREADCRUMB -->
      <div class="bg-transparent border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-gray-600">
          <div class="px-4">Home &gt; Solar Energy Products</div>
        </div>
      </div>

      <!-- MAIN CONTENT -->
      <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex gap-6">
          <!-- SIDEBAR - Desktop -->
          <aside class="hidden lg:block w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm p-4 sticky top-24">
              <!-- CATEGORIES -->
              <div class="mb-6">
                <h3 class="font-bold text-lg mb-4">CATEGORY</h3>
                <div class="space-y-1">
                  <div 
                    v-for="cat in categories" 
                    :key="cat.id" 
                    class="relative category-item group">
                    <div 
                      class="font-semibold text-gray-800 py-2 px-3 rounded hover:bg-orange-50 cursor-pointer transition-colors flex items-center justify-between"
                      @click="selectedCategory = cat.id">
                      <span>{{ cat.name }}</span>
                      <svg v-if="cat.subcategories && cat.subcategories.length > 0" 
                        class="w-4 h-4" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </div>
                    
                    <!-- Subcategories Popup -->
                    <div 
                      v-if="cat.subcategories && cat.subcategories.length > 0"
                      class="absolute left-full top-0 ml-2 bg-white border border-gray-200 rounded-lg shadow-xl z-100 min-w-[200px] py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none group-hover:pointer-events-auto">
                      <div class="px-3 py-2 font-semibold text-gray-700 border-b border-gray-100 text-sm">
                        {{ cat.name }}
                      </div>
                      <div 
                        v-for="sub in cat.subcategories" 
                        :key="sub.id"
                        @click="selectedCategory = sub.id"
                        :class="{'bg-orange-50 text-orange-600': selectedCategory === sub.id}"
                        class="cursor-pointer py-2 px-3 hover:bg-orange-50 text-gray-600 text-sm transition-colors">
                        {{ sub.name }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PRICE RANGE -->
              <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-lg mb-3">PRICE (₦)</h3>
                <div class="flex gap-2 mb-3">
                  <input type="number" v-model.number="filters.priceMin" placeholder="Min"
                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                  <span class="text-gray-500">-</span>
                  <input type="number" v-model.number="filters.priceMax" placeholder="Max"
                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                </div>
                <button @click="applyFilters"
                  class="w-full py-2 bg-orange-500 text-white rounded hover:bg-orange-600 text-sm font-semibold">
                  Apply
                </button>
                
                <!-- Price Range Options -->
                <div class="mt-3 space-y-2">
                  <label v-for="range in priceRanges" :key="range.label" class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" :value="range" v-model="selectedPriceRange" @change="applyPriceRange"
                      name="priceRange" class="w-4 h-4" />
                    <span class="text-sm text-gray-700">{{ range.label }}</span>
                  </label>
                </div>
              </div>

              <!-- DISCOUNT -->
              <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-lg mb-3">DISCOUNT PERCENTAGE</h3>
                <div class="space-y-2">
                  <label v-for="discount in discountRanges" :key="discount.value"
                    class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" :value="discount.value" v-model="filters.minDiscount" @change="applyFilters"
                      name="discount" class="w-4 h-4" />
                    <span class="text-sm text-gray-700">{{ discount.label }}</span>
                  </label>
                </div>
              </div>

              <!-- PRODUCT RATING -->
              <div class="mb-6 pb-6 border-b">
                <h3 class="font-bold text-lg mb-3">PRODUCT RATING</h3>
                <div class="space-y-2">
                  <label v-for="rating in ratingOptions" :key="rating.value" class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" :value="rating.value" v-model="filters.minRating" @change="applyFilters"
                      name="rating" class="w-4 h-4" />
                    <span class="flex items-center gap-1 text-sm">
                      <span v-for="i in 5" :key="i" class="text-yellow-400">
                        {{ i <= rating.stars ? '★' : '☆' }}
                      </span>
                      <span class="text-gray-700 ml-1">& above</span>
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </aside>

          <!-- MAIN PRODUCTS AREA -->
          <main class="flex-1">
            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
              <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-4 flex-wrap">
                  
                  <!-- Sort By Dropdown -->
                  <div class="relative">
                    <button @click="showSortDropdown = !showSortDropdown"
                      class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
                      <span class="text-sm">{{ selectedSortLabel }}</span>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                    <div v-if="showSortDropdown"
                      class="absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-[200px]">
                      <button v-for="(value, label) in sortByOptions" :key="value" @click="selectSort(value, label)"
                        :class="{'bg-orange-50 text-orange-600': filters.sortBy === value}"
                        class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm">
                        {{ label }}
                      </button>
                    </div>
                  </div>
                </div>

                <button @click="showMobileFilters = true"
                  class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-300 rounded">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                  </svg>
                  <span class="text-sm">Filter</span>
                </button>
              </div>
            </div>

            <!-- Products Heading -->
            <div class="mb-4">
              <h2 class="text-2xl font-bold text-gray-800 mb-2">SOLAR ENERGY PRODUCTS</h2>
              <p class="text-sm text-gray-600">
                ({{ filteredProducts.length }} products found)
              </p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
              <p class="mt-4 text-gray-600">Loading products...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center py-12">
              <p class="text-red-500">{{ error }}</p>
            </div>

            <!-- Products Grid -->
            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              <div v-for="product in filteredProducts" :key="product.id"
                class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <div class="relative">
                  <img :src="getProductImage(product)" :alt="product.name" class="w-full h-48 object-cover" />
                  <span v-if="product.discount"
                    class="absolute top-2 left-2 bg-orange-500 text-white px-2 py-1 text-xs rounded font-semibold">
                    -{{ getDiscountPercentage(product) }}%
                  </span>
                  <button class="absolute top-2 right-2 bg-white rounded-full p-2 hover:bg-orange-50 transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                  </button>
                </div>

                <div class="p-4">
                  <h3 class="text-sm text-gray-800 mb-2 line-clamp-2 h-10">
                    {{ product.name }}
                  </h3>

                  <div class="mb-2">
                    <span class="text-lg font-bold text-gray-900">
                      ₦ {{ (product.discounted_price ?? product.price).toLocaleString() }}
                    </span>
                    <span v-if="product.discount" class="text-sm text-gray-400 line-through ml-2">
                      ₦ {{ product.price.toLocaleString() }}
                    </span>
                  </div>

                  <div v-if="product.rating" class="flex items-center gap-1 mb-2">
                    <span v-for="i in 5" :key="i" class="text-yellow-400 text-xs">
                      {{ i <= Math.floor(product.rating) ? '★' : '☆' }}
                    </span>
                    <span class="text-xs text-gray-500 ml-1">({{ product.reviews || 0 }})</span>
                  </div>

                  <button @click="addToCart(product)"
                    class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 transition-colors text-sm font-semibold">
                    Add to cart
                  </button>
                </div>
              </div>
            </div>
            <div id="infinite-scroll-trigger"></div>

            <!-- Empty State -->
            <div v-if="!loading && !error && filteredProducts.length === 0" class="text-center py-12">
              <p class="text-gray-600">No products match your filters. Try adjusting your search.</p>
              <button @click="resetFilters" class="mt-4 px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                Reset Filters
              </button>
            </div>
          </main>
        </div>
      </div>

      <!-- Mobile Filters Overlay -->
      <div v-if="showMobileFilters" class="fixed inset-0 bg-black bg-opacity-50 z-50 lg:hidden" @click="showMobileFilters = false">
        <div class="fixed inset-y-0 right-0 w-80 bg-white overflow-y-auto" @click.stop>
          <div class="p-4">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
              <h3 class="font-bold text-lg">Filters</h3>
              <button @click="showMobileFilters = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            
            <!-- Mobile filter content (same as desktop sidebar) -->
            <div class="space-y-6">
              <!-- Price Range -->
              <div>
                <h4 class="font-semibold mb-2">Price Range</h4>
                <div class="flex gap-2 mb-2">
                  <input type="number" v-model.number="filters.priceMin" placeholder="Min"
                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                  <span>-</span>
                  <input type="number" v-model.number="filters.priceMax" placeholder="Max"
                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
                </div>
              </div>

              <!-- Discount -->
              <div>
                <h4 class="font-semibold mb-2">Discount</h4>
                <div class="space-y-2">
                  <label v-for="discount in discountRanges" :key="discount.value" class="flex items-center gap-2">
                    <input type="radio" :value="discount.value" v-model="filters.minDiscount" name="mobile-discount" class="w-4 h-4" />
                    <span class="text-sm">{{ discount.label }}</span>
                  </label>
                </div>
              </div>

              <!-- Buttons -->
              <div class="flex gap-2 pt-4 border-t">
                <button @click="resetFilters" class="flex-1 py-2 border border-gray-300 rounded">
                  Reset
                </button>
                <button @click="applyFilters(); showMobileFilters = false"
                  class="flex-1 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                  Apply
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ShopLayout>
</template>

<script setup>
import ShopLayout from '@/layouts/ShopLayout.vue';
import { ref, computed, onMounted } from 'vue';

const cartCount = ref(2);
const showSidebar = ref(false);
const showMobileFilters = ref(false);
const showSortDropdown = ref(false);
const searchQuery = ref('');
const brandSearch = ref('');

const products = ref([]);
const allProducts = ref([]);
const categories = ref([]);
const sortByOptions = ref({});
const loading = ref(true);
const loadingMore = ref(false);
const error = ref(null);
const hasMore = ref(true);
const currentPage = ref(1);
const perPage = ref(4);

const selectedCategory = ref(null);
const selectedBrands = ref([]);
const selectedPriceRange = ref(null);
const selectedSortLabel = ref('Sort by');

const filters = ref({
  priceMin: null,
  priceMax: null,
  minDiscount: null,
  minRating: null,
  sortBy: null
});

const storageUrl = import.meta.env.VITE_STORAGE_URL;
const placeholderImage = '/images/placeholder.png';

// Computed: Extract unique brands from products
const allBrands = computed(() => {
  const brands = new Set();
  allProducts.value.forEach(product => {
    if (product.brand) {
      brands.add(product.brand);
    }
  });
  return Array.from(brands).sort();
});

// Computed: Filtered brands based on search
const filteredBrands = computed(() => {
  if (!brandSearch.value) return allBrands.value;
  return allBrands.value.filter(brand => 
    brand.toLowerCase().includes(brandSearch.value.toLowerCase())
  );
});

// Computed: Extract price ranges from products
const priceRanges = computed(() => {
  const prices = allProducts.value.map(p => p.discounted_price ?? p.price);
  const minPrice = Math.min(...prices);
  const maxPrice = Math.max(...prices);
  
  const ranges = [
    { label: `₦0 - ₦50,000`, min: 0, max: 50000 },
    { label: `₦50,000 - ₦100,000`, min: 50000, max: 100000 },
    { label: `₦100,000 - ₦250,000`, min: 100000, max: 250000 },
    { label: `₦250,000 - ₦500,000`, min: 250000, max: 500000 },
    { label: `₦500,000+`, min: 500000, max: Infinity }
  ];
  
  return ranges.filter(range => range.min <= maxPrice);
});

// Computed: Extract discount ranges from products
const discountRanges = computed(() => {
  const discounts = allProducts.value
    .filter(p => p.discount)
    .map(p => getDiscountPercentage(p));
  
  const maxDiscount = Math.max(...discounts, 0);
  
  const ranges = [
    { label: '50% or more', value: 50 },
    { label: '40% or more', value: 40 },
    { label: '30% or more', value: 30 },
    { label: '20% or more', value: 20 },
    { label: '10% or more', value: 10 }
  ];
  
  return ranges.filter(range => range.value <= maxDiscount);
});

// Computed: Rating options
const ratingOptions = [
  { label: '4 stars & above', value: 4, stars: 4 },
  { label: '3 stars & above', value: 3, stars: 3 },
  { label: '2 stars & above', value: 2, stars: 2 },
  { label: '1 star & above', value: 1, stars: 1 }
];

// Computed: Filtered and sorted products
const filteredProducts = computed(() => {
  let result = [...allProducts.value];

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) ||
      (p.description && p.description.toLowerCase().includes(query))
    );
  }

  // Category filter
  if (selectedCategory.value) {
    result = result.filter(p => p.category_id === selectedCategory.value);
  }

  // Brand filter
  if (selectedBrands.value.length > 0) {
    result = result.filter(p => selectedBrands.value.includes(p.brand));
  }

  // Price filter
  if (filters.value.priceMin !== null || filters.value.priceMax !== null) {
    result = result.filter(p => {
      const price = p.discounted_price ?? p.price;
      const min = filters.value.priceMin ?? 0;
      const max = filters.value.priceMax ?? Infinity;
      return price >= min && price <= max;
    });
  }

  // Discount filter
  if (filters.value.minDiscount) {
    result = result.filter(p => {
      if (!p.discount) return false;
      return getDiscountPercentage(p) >= filters.value.minDiscount;
    });
  }

  // Rating filter
  if (filters.value.minRating) {
    result = result.filter(p => (p.rating || 0) >= filters.value.minRating);
  }

  // Sorting
  if (filters.value.sortBy) {
    switch (filters.value.sortBy) {
      case 'hp': // Highest Price
        result.sort((a, b) => (b.discounted_price ?? b.price) - (a.discounted_price ?? a.price));
        break;
      case 'lp': // Lowest Price
        result.sort((a, b) => (a.discounted_price ?? a.price) - (b.discounted_price ?? b.price));
        break;
      case 'if': // Featured
        result.sort((a, b) => (b.featured ? 1 : 0) - (a.featured ? 1 : 0));
        break;
    }
  }

  return result;
});

// Methods
const getProductImage = (product) => {
  if (product.images && product.images.length > 0) {
    return storageUrl + product.images[0].path;
  }
  return placeholderImage;
};

const getDiscountPercentage = (product) => {
  if (!product.discount) return 0;
  
  if (product.discount.type === 'percentage') {
    return Number(product.discount.value);
  } else {
    // Calculate percentage from fixed amount
    return Math.round((product.discount.value / product.price) * 100);
  }
};

const applyPriceRange = () => {
  if (selectedPriceRange.value) {
    filters.value.priceMin = selectedPriceRange.value.min;
    filters.value.priceMax = selectedPriceRange.value.max === Infinity ? null : selectedPriceRange.value.max;
    applyFilters();
  }
};

const applyFilters = () => {
  // Filters are reactive, so this just triggers recomputation
  showMobileFilters.value = false;
};

const resetFilters = () => {
  filters.value = {
    priceMin: null,
    priceMax: null,
    minDiscount: null,
    minRating: null,
    sortBy: null
  };
  selectedCategory.value = null;
  selectedBrands.value = [];
  selectedPriceRange.value = null;
  searchQuery.value = '';
  selectedSortLabel.value = 'Sort by';
};

const selectSort = (value, label) => {
  filters.value.sortBy = value;
  selectedSortLabel.value = label;
  showSortDropdown.value = false;
};

const addToCart = (product) => {
  cartCount.value++;
  console.log('Added to cart:', product.name);
};

// Fetch data
onMounted(async () => {
  try {
    await fetchProducts();
    setupInfiniteScroll();
  } catch (err) {
    console.error('Error fetching products:', err);
    error.value = 'Failed to load products.';
    loading.value = false;
  }
});

// Fetch products function
const fetchProducts = async (page = 1) => {
  try {
    if (page === 1) {
      loading.value = true;
    } else {
      loadingMore.value = true;
    }

    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/products?per_page=${perPage.value}&page=${page}`);
    const json = await res.json();

    const newProducts = json.data?.products?.data || [];
    
    if (page === 1) {
      allProducts.value = newProducts;
      categories.value = json.data?.categories || [];
      sortByOptions.value = json.data?.filters?.sort_by || {};
    } else {
      allProducts.value = [...allProducts.value, ...newProducts];
    }

    // Check if there are more pages
    const pagination = json.data?.products;
    hasMore.value = pagination?.current_page < pagination?.last_page;
    currentPage.value = pagination?.current_page || page;

  } catch (err) {
    console.error('Error fetching products:', err);
    error.value = 'Failed to load products.';
    throw err;
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

// Setup infinite scroll
const setupInfiniteScroll = () => {
  const observer = new IntersectionObserver(
    (entries) => {
      const target = entries[0];
      if (target.isIntersecting && !loadingMore.value && hasMore.value) {
        loadMoreProducts();
      }
    },
    {
      root: null,
      rootMargin: '200px',
      threshold: 0.1
    }
  );

  // Observe the loading trigger element
  const trigger = document.getElementById('infinite-scroll-trigger');
  if (trigger) {
    observer.observe(trigger);
  }
};

// Load more products
const loadMoreProducts = async () => {
  if (loadingMore.value || !hasMore.value) return;
  
  await fetchProducts(currentPage.value + 1);
};

// Close dropdowns when clicking outside
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      showSortDropdown.value = false;
    }
  });
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>