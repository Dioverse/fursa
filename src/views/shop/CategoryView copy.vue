<template>
  <ShopLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Breadcrumb -->
      <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-gray-600">
          Home
          <span v-if="categorySlug"> &gt; {{ categorySlug }}</span>
          <span v-if="subcategorySlug"> &gt; {{ subcategorySlug }}</span>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex gap-6">
          <!-- Desktop Sidebar -->
          <aside class="hidden lg:block w-64 flex-shrink-0">
            <div class="overflow-y-auto max-h-[calc(100vh-6rem)] bg-white rounded-lg shadow-sm p-4 sticky top-24">
              <div class="space-y-6">
                <!-- Name Search -->
                <div class="pb-6 border-b">
                  <h3 class="font-bold text-lg mb-3">SEARCH</h3>
                  <input type="text" placeholder="Search by name..."
                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm" v-model="filters.name"
                    @keyup.enter="state.currentPage = 1; fetchProducts(1)" />
                </div>

                <!-- Categories -->
                <div>
                  <h3 class="font-bold text-lg mb-4">CATEGORY</h3>
                  <div class="space-y-1">
                    <div v-for="cat in categories" :key="cat.id" class="group relative">
                      <div @click="navigateToCategory(cat.slug)" :class="[
                        'font-semibold text-gray-800 py-2 px-3 rounded cursor-pointer transition-colors',
                        selectedCategoryId === cat.id ? 'bg-primary-100 text-primary-600' : 'hover:bg-primary-50'
                      ]">
                        {{ cat.name }}
                      </div>

                      <div v-if="cat.subcategories && cat.subcategories.length > 0"
                        class="absolute left-full top-0 ml-2 bg-white border border-gray-200 rounded-lg shadow-xl min-w-[200px] py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                        <div class="px-3 py-2 font-semibold text-gray-700 border-b text-sm">
                          {{ cat.name }}
                        </div>
                        <div v-for="sub in cat.subcategories" :key="sub.id" @click="navigateToSubcategory(sub.slug)"
                          :class="[
                            'cursor-pointer py-2 px-3 text-gray-600 text-sm transition-colors',
                            selectedSubcategoryId === sub.id ? 'bg-primary-100 text-primary-600 font-semibold' : 'hover:bg-primary-50'
                          ]">
                          {{ sub.name }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Price Range -->
                <div class="pb-6 border-b">
                  <h3 class="font-bold text-lg mb-3">PRICE (₦)</h3>
                  <div class="flex gap-2 mb-3">
                    <input type="number" placeholder="Min"
                      class="w-full px-2 py-1 border border-gray-300 rounded text-sm" v-model.number="filters.minPrice"
                      @change="state.currentPage = 1; fetchProducts(1)" />
                    <span>-</span>
                    <input type="number" placeholder="Max"
                      class="w-full px-2 py-1 border border-gray-300 rounded text-sm" v-model.number="filters.maxPrice"
                      @change="state.currentPage = 1; fetchProducts(1)" />
                  </div>

                  <div class="space-y-2">
                    <label v-for="range in priceRanges" :key="range.label"
                      class="flex items-center gap-2 cursor-pointer text-sm">
                      <input type="radio" name="price" class="w-4 h-4"
                        @change="setPrice(range.min, range.max === Infinity ? null : range.max)" />
                      {{ range.label }}
                    </label>
                  </div>
                </div>

                <!-- Sorting for Desktop -->
                <div>
                  <h3 class="font-bold text-lg mb-3">SORT BY</h3>
                  <div class="space-y-2">
                    <label v-for="(label, value) in sortOptions" :key="value"
                      class="flex items-center gap-2 cursor-pointer text-sm">
                      <input type="radio" name="sort" class="w-4 h-4" @change="handleSort(value)" />
                      {{ label }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </aside>

          <!-- Main Content -->
          <main class="flex-1">
            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
              <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="relative">
                  <button @click="showSortDropdown = !showSortDropdown"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 lg:hidden">
                    <span class="text-sm">Sort by</span>
                    <ChevronDownIcon class="w-4 h-4" />
                  </button>

                  <div v-if="showSortDropdown"
                    class="absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-[200px]">
                    <button v-for="(label, value) in sortOptions" :key="value"
                      @click="handleSort(value); showSortDropdown = false"
                      class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm">
                      {{ label }}
                    </button>
                  </div>
                </div>

                <button @click="showMobileFilters = true"
                  class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-300 rounded">
                  <FilterIcon class="w-4 h-4" />
                  <span class="text-sm">Filter</span>
                </button>
              </div>
            </div>

            <!-- Products Heading -->
            <div class="mb-4">
              <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ pageTitle }}</h2>
              <p class="text-sm text-gray-600">({{ products.length }} products found)</p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500" />
              <p class="mt-4 text-gray-600">Loading products...</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="text-center py-12">
              <p class="text-red-500">{{ error }}</p>
              <button @click="fetchProducts(1)"
                class="mt-4 px-6 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">
                Retry
              </button>
            </div>

            <!-- Products Grid -->
            <div v-if="!loading && !error"
              class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
              <div v-for="product in products" :key="product.id"
                class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow relative">
                <div class="relative">
                  <!-- Product Image -->
                  <img :src="getProductImage(product)" :alt="product.name"
                    class="w-full h-48 object-cover bg-gray-200" />

                  <!-- Featured Icon -->
                  <div v-if="product.is_featured" class="absolute top-2 left-2 text-white rounded-full p-1 shadow-md"
                    title="Featured Product">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#d4942a" class="w-4 h-4" viewBox="0 0 24 24">
                      <path
                        d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87 7.1-1.01L12 2z" />
                    </svg>
                  </div>

                  <!-- 💸 Discount Badge -->
                  <span v-if="product.discount"
                    class="absolute top-2 right-2 bg-primary-500 text-white px-2 py-1 text-xs rounded font-semibold">
                    -{{ discountLabel(product) }}
                  </span>
                </div>

                <!-- Product Details -->
                <div class="px-4 pt-4">
                  <h3 class="text-sm text-gray-800 mb-2 line-clamp-1">{{ product.name }}</h3>
                  <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ product.short_description }}</p>

                  <div>
                    <span class="text-lg font-bold text-gray-900">
                      ₦ {{ getDisplayPrice(product) }}
                    </span>
                    <span v-if="product.discount" class="text-sm text-gray-400 line-through ml-2">
                      ₦ {{ priceToLocale(getBasePrice(product)) }}
                    </span>
                  </div>

                  <div v-if="product.stock_quantity" class="text-xs text-gray-600 line-clamp-1">
                    <span v-if="product.low_stock_threshold && product.stock_quantity <= product.low_stock_threshold"
                      class="text-red-500 font-semibold">
                      Available: {{ product.stock_quantity }} (Low stock)
                    </span>
                    <span v-else class="invisible">Available</span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex w-full space-x-2 px-4 pb-4">
                  <!-- <button
                    class="flex-1 bg-gold-500 text-white py-2 rounded hover:bg-gold-100 hover:text-black text-sm font-semibold">
                    Add to cart
                  </button> -->
                  <div>
                    <div class="flex items-center rounded overflow-hidden">
                      <!-- Decrement Button -->
                      <button :disabled="loadingQuantity || quantity <= 1" @click="updateQuantity(quantity - 1)"
                        class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                        -
                      </button>

                      <!-- Input / Loader -->
                      <div
                        class="w-12 text-center border-x border-gold-300 text-sm flex justify-center items-center h-[38px]">
                        <div v-if="loadingQuantity">
                          <svg class="animate-spin h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                          </svg>
                        </div>
                        <div v-else>
                          <input type="number" v-model="quantity" min="1" @change="updateQuantity(quantity)" class="w-full text-center focus:outline-none focus:ring-0 [appearance:textfield]
                 [&::-webkit-outer-spin-button]:appearance-none
                 [&::-webkit-inner-spin-button]:appearance-none" />
                        </div>
                      </div>

                      <!-- Increment Button -->
                      <button :disabled="loadingQuantity" @click="updateQuantity(quantity + 1)"
                        class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                        +
                      </button>
                    </div>
                  </div>

                  <button
                    class="w-1/4 bg-gold-100 text-primary py-2 rounded hover:bg-gold-500  hover:text-white text-sm font-semibold flex justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="1.5" role="img" aria-label="Add to favorites">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.8 8.6c0 5.4-9.2 11.1-9.8 11.4a1.5 1.5 0 0 1-1 0c-.6-.3-9.8-6-9.8-11.4A5 5 0 0 1 6.8 4.5c1.6 0 3.1.8 4 2a5.1 5.1 0 0 1 4-2 5 5 0 0 1 4.9 4.1z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"
                      role="img" aria-label="Favorited">
                      <path
                        d="M12.1 21.35a1.5 1.5 0 0 1-1.1-.46C4.3 14.6 2 12.3 2 9.6 2 6.9 4.2 4.7 6.9 4.7c1.6 0 3.1.8 4 2 0 0 .1.2.1.2s.1-.1.1-.2c.9-1.2 2.4-2 4-2 2.7 0 4.9 2.2 4.9 4.9 0 2.7-2.3 5-8.9 11.3a1.5 1.5 0 0 1-1.1.46z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>


            <div ref="observerTarget" id="scroll-trigger" />

            <div v-if="loadingMore" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500" />
            </div>

            <div v-if="!loading && !error && products.length === 0" class="text-center py-12">
              <p class="text-gray-600">No products match your filters.</p>
              <button @click="resetFilters"
                class="mt-4 px-6 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">
                Reset Filters
              </button>
            </div>
          </main>
        </div>
      </div>

      <!-- Mobile Filters Drawer -->
      <div v-if="showMobileFilters" class="fixed inset-0 bg-black bg-opacity-50 z-50 lg:hidden"
        @click="showMobileFilters = false">
        <div class="fixed inset-y-0 right-0 w-80 bg-white overflow-y-auto" @click.stop>
          <div class="p-4">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
              <h3 class="font-bold text-lg">Filters</h3>
              <button @click="showMobileFilters = false">
                <XIcon class="w-6 h-6" />
              </button>
            </div>

            <div class="space-y-6">
              <!-- Name Search in Mobile -->
              <div class="pb-4 border-b">
                <h3 class="font-bold text-lg mb-3">SEARCH</h3>
                <input type="text" placeholder="Search by name..."
                  class="w-full px-2 py-1 border border-gray-300 rounded text-sm" v-model="filters.name" />
              </div>

              <!-- Categories in Mobile -->
              <div>
                <h3 class="font-bold text-lg mb-4">CATEGORY</h3>
                <div class="space-y-1">
                  <div v-for="cat in categories" :key="cat.id">
                    <div @click="navigateToCategory(cat.slug); showMobileFilters = false" :class="[
                      'font-semibold text-gray-800 py-2 px-3 rounded cursor-pointer transition-colors',
                      selectedCategoryId === cat.id ? 'bg-primary-100 text-primary-600' : 'hover:bg-primary-50'
                    ]">
                      {{ cat.name }}
                    </div>
                    <div v-if="cat.subcategories && cat.subcategories.length > 0" class="ml-4 space-y-1 mt-1">
                      <div v-for="sub in cat.subcategories" :key="sub.id"
                        @click="navigateToSubcategory(sub.slug); showMobileFilters = false" :class="[
                          'cursor-pointer py-2 px-3 text-gray-600 text-sm rounded transition-colors',
                          selectedSubcategoryId === sub.id ? 'bg-primary-100 text-primary-600 font-semibold' : 'hover:bg-primary-50'
                        ]">
                        {{ sub.name }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Price Range in Mobile -->
              <div class="pb-4 border-b">
                <h3 class="font-bold text-lg mb-3">PRICE (₦)</h3>
                <div class="flex gap-2 mb-3">
                  <input type="number" placeholder="Min" class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                    v-model.number="filters.minPrice" />
                  <span>-</span>
                  <input type="number" placeholder="Max" class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                    v-model.number="filters.maxPrice" />
                </div>
              </div>
            </div>

            <div class="flex gap-2 mt-6 pt-4 border-t">
              <button @click="resetFilters; showMobileFilters = false"
                class="flex-1 py-2 border border-gray-300 rounded hover:bg-gray-50">
                Reset
              </button>
              <button @click="state.currentPage = 1; fetchProducts(1); showMobileFilters = false"
                class="flex-1 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">
                Apply
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ShopLayout>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch, nextTick } from 'vue';
import ShopLayout from '@/layouts/ShopLayout.vue';

const storageUrl = import.meta.env.VITE_STORAGE_URL || '';
const apiUrl = import.meta.env.VITE_API_BASE_URL || '/api';

// Icons (inline SVG components)
const ChevronDownIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 9l-7 7-7-7" />
    </svg>
  `
}

const FilterIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.447.894l-4 2.5A1 1 0 0110 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
    </svg>
  `
}

const XIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M6 18L18 6M6 6l12 12" />
    </svg>
  `
}

const HeartIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
        role="img" aria-label="Add to favorites">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.8 8.6c0 5.4-9.2 11.1-9.8 11.4a1.5 1.5 0 0 1-1 0c-.6-.3-9.8-6-9.8-11.4A5 5 0 0 1 6.8 4.5c1.6 0 3.1.8 4 2a5.1 5.1 0 0 1 4-2 5 5 0 0 1 4.9 4.1z"/>
    </svg>
  `
}

const HeartIconFilled = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"
        role="img" aria-label="Favorited">
      <path d="M12.1 21.35a1.5 1.5 0 0 1-1.1-.46C4.3 14.6 2 12.3 2 9.6 2 6.9 4.2 4.7 6.9 4.7c1.6 0 3.1.8 4 2 0 0 .1.2.1.2s.1-.1.1-.2c.9-1.2 2.4-2 4-2 2.7 0 4.9 2.2 4.9 4.9 0 2.7-2.3 5-8.9 11.3a1.5 1.5 0 0 1-1.1.46z"/>
    </svg>
  `
}

// Reactive state
const state = reactive({
  products: [],
  categories: [],
  loading: true,
  loadingMore: false,
  error: null,
  currentPage: 1,
  hasMore: true,
  pageTitle: 'PRODUCTS',
});

const filters = reactive({
  name: '',
  minPrice: null,
  maxPrice: null,
  sortBy: null,
});

const selectedCategoryId = ref(null);
const selectedSubcategoryId = ref(null);
const showSortDropdown = ref(false);
const showMobileFilters = ref(false);
const observerTarget = ref(null);

const sortOptions = {
  hp: 'Highest Price',
  lp: 'Lowest Price',
  if: 'Featured',
};

const priceRanges = [
  { label: '₦0 - ₦50,000', min: 0, max: 50000 },
  { label: '₦50,000 - ₦100,000', min: 50000, max: 100000 },
  { label: '₦100,000 - ₦250,000', min: 100000, max: 250000 },
  { label: '₦250,000 - ₦500,000', min: 250000, max: 500000 },
  { label: '₦500,000+', min: 500000, max: Infinity },
];

// Utility functions
const priceToLocale = (val) => {
  if (val == null) return '0';
  return Number(val).toLocaleString('en-NG');
};

const getBasePrice = (product) => {
  // Backend returns base_price or distributor_price depending on user
  return product.base_price ?? product.distributor_price ?? product.price ?? 0;
};

const getDisplayPrice = (product) => {
  const price = getBasePrice(product);
  if (product.discount && product.discount.type === 'percentage') {
    const discounted = price - (price * (product.discount.value / 100));
    return priceToLocale(discounted);
  }
  return priceToLocale(price);
};

const discountLabel = (product) => {
  const d = product.discount;
  if (!d) return '';
  if (d.type === 'percentage') return `${d.value}%`;
  return '0%';
};

const getProductImage = (product) => {
  if (product.images && product.images.length > 0) {
    return storageUrl + product.images[0].path;
  }
  return '/placeholder.png';
};

const buildQueryParams = () => {
  const params = new URLSearchParams();

  if (selectedCategoryId.value) {
    params.append('category', selectedCategoryId.value);
  }
  if (selectedSubcategoryId.value) {
    params.append('category', selectedSubcategoryId.value);
  }
  if (filters.name) {
    params.append('name', filters.name);
  }
  if (filters.minPrice != null) {
    params.append('min_price', filters.minPrice);
  }
  if (filters.maxPrice != null) {
    params.append('max_price', filters.maxPrice);
  }
  if (filters.sortBy) {
    params.append('sort_by', filters.sortBy);
  }
  params.append('page', state.currentPage);
  params.append('per_page', 24);

  return params.toString();
};

// Fetch products from API
const fetchProducts = async (page = 1, isLoadMore = false) => {
  try {
    if (!isLoadMore) state.loading = true;
    else state.loadingMore = true;

    state.currentPage = page;

    const queryString = buildQueryParams();
    const res = await fetch(`${apiUrl}/products?${queryString}`);

    if (!res.ok) throw new Error('Failed to fetch products');

    const response = await res.json();

    const newProducts = response.data.products.data || [];
    const hasMore = response.data.products.current_page < response.data.products.last_page;

    if (isLoadMore) {
      state.products = [...state.products, ...newProducts];
    } else {
      state.products = [];
      state.products = newProducts;
      if (response.data.categories) {
        state.categories = response.data.categories;
      }
    }

    state.hasMore = hasMore;
    state.loading = false;
    state.loadingMore = false;
    state.error = null;
  } catch (err) {
    state.error = 'Failed to load products';
    state.loading = false;
    state.loadingMore = false;
    console.error('Fetch error:', err);
  }
};

// Initialize on mount
onMounted(() => {
  // Extract slug from URL (format: /c/category--subcategory or /c/category)
  const pathname = window.location.pathname;
  const match = pathname.match(/\/shop\/c\/(.+)?$/);
  const slug = (match && match[1]) || '';

  if (slug) {
    const parts = slug.split('--');
    const categorySlug = parts[0];
    const subcategorySlug = parts[1] || null;

    // Find category ID by slug
    const category = state.categories.find(c => c.slug === categorySlug);
    if (category) {
      selectedCategoryId.value = category.id;
      state.pageTitle = categorySlug.replace(/-/g, ' ').toUpperCase();

      // Find subcategory ID if provided
      if (subcategorySlug) {
        const subcategory = category.subcategories?.find(s => s.slug === subcategorySlug);
        if (subcategory) {
          selectedSubcategoryId.value = subcategory.id;
          state.pageTitle = subcategorySlug.replace(/-/g, ' ').toUpperCase();
        }
      }
    }
  }

  // Load all products (with or without filters from URL)
  fetchProducts(1).then(() => {
    nextTick(() => setupObserver());
  });
});

// Infinite scroll setup
let observer;
const setupObserver = () => {
  if (observer) observer.disconnect();

  observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting && !state.loadingMore && state.hasMore) {
        const nextPage = state.currentPage + 1;
        fetchProducts(nextPage, true);
      }
    },
    { rootMargin: '200px' }
  );

  if (observerTarget.value) observer.observe(observerTarget.value);
};

// Filter handlers
const handleSort = (value) => {
  filters.sortBy = value;
  state.currentPage = 1;
  fetchProducts(1);
};

const setPrice = (min, max) => {
  filters.minPrice = min;
  filters.maxPrice = max;
  state.currentPage = 1;
  fetchProducts(1);
};

const navigateToCategory = (categorySlug) => {
  selectedCategoryId.value = categorySlug;
  selectedSubcategoryId.value = null;
  state.pageTitle = categorySlug.replace(/-/g, ' ').toUpperCase();
  state.currentPage = 1;
  resetFilters(false);
  fetchProducts(1);
};

const navigateToSubcategory = (subcategorySlug) => {
  selectedSubcategoryId.value = subcategorySlug;
  state.pageTitle = subcategorySlug.replace(/-/g, ' ').toUpperCase();
  state.currentPage = 1;
  resetFilters(false);
  fetchProducts(1);
};

const resetFilters = (fetchData = true) => {
  filters.name = '';
  filters.minPrice = null;
  filters.maxPrice = null;
  filters.sortBy = null;
  state.currentPage = 1;

  if (fetchData) fetchProducts(1);
};

// Computed properties
const products = computed(() => state.products);
const categories = computed(() => state.categories);
const loading = computed(() => state.loading);
const loadingMore = computed(() => state.loadingMore);
const error = computed(() => state.error);
const pageTitle = computed(() => state.pageTitle);
const categorySlug = computed(() => {
  if (selectedSubcategoryId.value) return null;
  const cat = state.categories.find(c => c.id === selectedCategoryId.value);
  return cat ? cat.slug : null;
});
const subcategorySlug = computed(() => {
  if (!selectedSubcategoryId.value) return null;
  for (const cat of state.categories) {
    const sub = cat.subcategories?.find(s => s.id === selectedSubcategoryId.value);
    if (sub) return sub.slug;
  }
  return null;
});

const quantity = ref(1)
const loadingQuantity = ref(false)
// Disable all buttons/links globally during update
const togglePageInteractivity = (disable) => {
  const elements = document.querySelectorAll('button, a')
  elements.forEach(el => {
    if (disable) el.setAttribute('disabled', true)
    else el.removeAttribute('disabled')
  })
}

const updateQuantity = async (newQty) => {
  if (loadingQuantity.value || newQty < 1) return

  loadingQuantity.value = true
  togglePageInteractivity(true)

  try {
    const res = await fetch(`/api/cart/update`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: 123, quantity: newQty }),
    })

    if (!res.ok) throw new Error('Failed to update quantity')

    // On success
    quantity.value = newQty
  } catch (err) {
    console.error('Error updating quantity:', err)
  } finally {
    loadingQuantity.value = false
    togglePageInteractivity(false)
  }
}
</script>

<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>