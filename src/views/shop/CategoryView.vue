<template>
  <ShopLayout>
    <div class="xs:px-[clamp(2rem,4vw,4rem)] xxs:px-0 min-h-screen bg-gray-50">
      <!-- Breadcrumb -->
      <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-gray-600">
          Home
          <span v-if="categorySlug"> &gt;
            <RouterLink :to="`/c/${categorySlug}`" v-if="subcategorySlug">{{ categoryTitle }}</RouterLink>
            <span v-else>{{ categoryTitle }}</span>
          </span>
          <span v-if="subcategorySlug"> &gt; {{ subcategoryTitle }}</span>
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
                    @keyup.enter="state.currentPage = 1;" />
                </div>

                <!-- Categories -->
                <div>
                  <h3 class="font-bold text-lg mb-4">CATEGORY</h3>
                  <div class="space-y-1">
                    <div class="group-relative">
                      <div @click="navigateToAllProducts()" :class="[
                        'font-semibold text-gray-800 py-2 px-3 rounded cursor-pointer transition-colors',
                        (!selectedCategorySlug && !selectedSubcategorySlug) ? 'bg-mprimary-100 text-mprimary-600' : 'hover:bg-mprimary-50'
                      ]">
                        All Products
                      </div>
                    </div>
                    <div v-for="cat in categories" :key="cat.id" class="group relative">
                      <div @click="navigateToCategory(cat.slug)" :class="[
                        'font-semibold text-gray-800 py-2 px-3 rounded cursor-pointer transition-colors',
                        selectedCategorySlug === cat.slug ? 'bg-mprimary-100 text-mprimary-600' : 'hover:bg-mprimary-50'
                      ]">
                        {{ cat.name }}
                      </div>

                      <div v-if="cat.subcategories && cat.subcategories.length > 0"
                        class="absolute left-full top-0 ml-2 bg-white border border-gray-200 rounded-lg shadow-xl min-w-[200px] py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                        <div class="px-3 py-2 font-semibold text-gray-700 border-b text-sm">
                          {{ cat.name }}
                        </div>
                        <div v-for="sub in cat.subcategories" :key="sub.id"
                          @click="navigateToSubcategory(cat.slug, sub.slug)" :class="[
                            'cursor-pointer py-2 px-3 text-gray-600 text-sm transition-colors',
                            selectedSubcategorySlug === sub.slug ? 'bg-mprimary-100 text-mprimary-600 font-semibold' : 'hover:bg-mprimary-50'
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
                      @change="state.currentPage = 1;" />
                    <span>-</span>
                    <input type="number" placeholder="Max"
                      class="w-full px-2 py-1 border border-gray-300 rounded text-sm" v-model.number="filters.maxPrice"
                      @change="state.currentPage = 1;" />
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
              <h2 id="page-header" class="text-2xl font-bold text-gray-800 mb-2">{{ pageTitle }}</h2>
              <p v-if="!loading && !loadingMore" class="text-sm text-gray-600">({{ products.length }} products found)</p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-mprimary-500" />
              <p class="mt-4 text-gray-600">Loading products...</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="text-center py-12">
              <p class="text-red-500">{{ error }}</p>
              <button @click="fetchProducts(1)"
                class="mt-4 px-6 py-2 bg-mprimary-500 text-white rounded hover:bg-mprimary-600">
                Retry
              </button>
            </div>

            <!-- Products Grid -->
            <div>
              <div v-if="!loading && !error"
                class="grid grid-cols-2 xxs:grid-cols-2 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
                <div v-for="product in products" :key="product.id"
                  class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow relative">
                  <div class="relative">
                    <!-- Product Image -->
                    <img :src="getImageUrl(product.images[0].path)" :alt="product.name"
                      class="w-full h-48 object-cover bg-gray-200" @error="handleImageError" />

                    <!-- Featured Icon -->
                    <div v-if="product.is_featured" class="absolute top-2 left-2 text-white rounded-full p-1 shadow-md"
                      title="Featured Product">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="#d4942a" class="w-4 h-4" viewBox="0 0 24 24">
                        <path
                          d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87 7.1-1.01L12 2z" />
                      </svg>
                    </div>

                    <!-- Discount Badge -->
                    <span v-if="product.discount"
                      class="absolute top-2 right-2 bg-mprimary-500 text-white px-2 py-1 text-xs rounded font-semibold">
                      {{ discountLabel(product) }}
                    </span>
                  </div>

                  <!-- Product Details -->
                  <div class="px-4 pt-4">
                    <h3 class="text-sm text-gray-800 mb-2 line-clamp-1">
                      {{ product.name }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-2 line-clamp-1">
                      {{ product.short_description }}
                    </p>

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

                    <!-- Add To Cart -->
                    <button v-if="!isInCart(product.id)" @click="addToCart(product)"
                      :disabled="loadingStates[product.id]"
                      class="flex-1 bg-gold-500 text-white py-2 disabled:opacity-50 disabled:cursor-not-allowed rounded hover:bg-gold-100 hover:text-black text-sm font-semibold">
                      {{ loadingStates[product.id] ? 'Adding...' : 'Add to cart' }}
                    </button>

                    <!-- Quantity Control -->
                    <div v-else class="flex items-center rounded overflow-hidden flex-1 justify-between">
                      <!-- Decrement -->
                      <button :disabled="getCartQuantity(product.id) <= 1 || loadingStates[product.id]"
                        @click="updateQuantity(product, getCartQuantity(product.id) - 1)"
                        class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                        -
                      </button>

                      <!-- Quantity Display / Loader -->
                      <div
                        class="w-12 text-center border-x border-gold-300 text-sm flex justify-center items-center h-[38px]">
                        <div v-if="loadingStates[product.id]" class="flex justify-center items-center">
                          <svg class="animate-spin h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                          </svg>
                        </div>
                        <div v-else contenteditable="true" class="w-full text-center outline-none"
                          :data-product-id="product.id" @blur="onQuantityBlur($event, product)"
                          @keydown.enter.prevent="onQuantityEnter($event, product)">
                          {{ getCartQuantity(product.id) }}
                        </div>
                      </div>

                      <!-- Increment -->
                      <button :disabled="loadingStates[product.id]"
                        @click="updateQuantity(product, getCartQuantity(product.id) + 1)"
                        class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                        +
                      </button>
                    </div>

                    <!-- Wishlist -->
                    <button @click="toggleWishlist(product)" :class="[
                      'w-1/4 py-2 rounded text-sm font-semibold flex justify-center items-center transition-colors',
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

                </div>
              </div>
            </div>

            <div ref="observerTarget" id="scroll-trigger" />

            <div v-if="loadingMore" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-mprimary-500" />
            </div>

            <div v-if="!loading && !error && products.length === 0" class="text-center py-12">
              <p class="text-gray-600">No products match your filters.</p>
              <button @click="resetFilters"
                class="mt-4 px-6 py-2 bg-mprimary-500 text-white rounded hover:bg-mprimary-600">
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
                  <div>
                    <div @click="navigateToAllProducts(); showMobileFilters = false" :class="[
                      'font-semibold text-gray-800 py-2 px-3 rounded cursor-pointer transition-colors',
                      (!selectedCategorySlug && !selectedSubcategorySlug) ? 'bg-mprimary-100 text-mprimary-600' : 'hover:bg-mprimary-50'
                    ]">
                      All Products
                    </div>
                  </div>


                  <div v-for="cat in categories" :key="cat.id">
                    <div @click="navigateToCategory(cat.slug); showMobileFilters = false" :class="[
                      'font-semibold text-gray-800 py-2 px-3 rounded cursor-pointer transition-colors',
                      selectedCategorySlug === cat.slug ? 'bg-mprimary-100 text-mprimary-600' : 'hover:bg-mprimary-50'
                    ]">
                      {{ cat.name }}
                    </div>
                    <div v-if="cat.subcategories && cat.subcategories.length > 0" class="ml-4 space-y-1 mt-1">
                      <div v-for="sub in cat.subcategories" :key="sub.id"
                        @click="navigateToSubcategory(cat.slu, sub.slug); showMobileFilters = false" :class="[
                          'cursor-pointer py-2 px-3 text-gray-600 text-sm rounded transition-colors',
                          selectedSubcategorySlug === sub.slug ? 'bg-mprimary-100 text-mprimary-600 font-semibold' : 'hover:bg-mprimary-50'
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
              <div class="space-y-2">
                <label v-for="range in priceRanges" :key="range.label"
                  class="flex items-center gap-2 cursor-pointer text-sm">
                  <input type="radio" name="price" class="w-4 h-4"
                    @change="setPrice(range.min, range.max === Infinity ? null : range.max)" />
                  {{ range.label }}
                </label>
              </div>
            </div>

            <div class="flex gap-2 mt-6 pt-4 border-t">
              <button @click="resetFilters; showMobileFilters = false"
                class="flex-1 py-2 border border-gray-300 rounded hover:bg-gray-50">
                Reset
              </button>
              <button @click="state.currentPage = 1; showMobileFilters = false"
                class="flex-1 py-2 bg-mprimary-500 text-white rounded hover:bg-mprimary-600">
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
import { useCartStore } from '@/stores/cart';
import { useWishlistStore } from '@/stores/wishlist';
import { useRoute, useRouter } from 'vue-router';
import { getImageUrl, handleImageError } from '@/utils/helpers';

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

// Reactive state
const state = reactive({
  products: [],
  categories: [],
  loading: true,
  loadingMore: false,
  error: null,
  currentPage: 1,
  hasMore: true,
  pageTitle: 'ALL PRODUCTS',
});

const filters = reactive({
  name: '',
  minPrice: null,
  maxPrice: null,
  sortBy: null,
});

const selectedCategorySlug = ref(null);
const selectedSubcategorySlug = ref(null);
const showSortDropdown = ref(false);
const showMobileFilters = ref(false);
const observerTarget = ref(null);
const loadingStates = ref({});

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

const cartStore = useCartStore();
const wishlistStore = useWishlistStore();

// Utility functions
const priceToLocale = (val) => {
  if (val == null) return '0';
  return Number(val).toLocaleString('en-NG');
};

const getBasePrice = (product) => {
  return product.price ?? product.base_price ?? product.distributor_price ?? 0;
};

const getDisplayPrice = (product) => {
  const price = getBasePrice(product);
  if (product.discount) {
    let discounted;
    if (product.discount.type === 'percentage') {
      discounted = price - (price * (product.discount.value / 100));
    } else {
      discounted = price - product.discount.value
    }
    return priceToLocale(discounted);
  }
  return priceToLocale(price);
};

const discountLabel = (product) => {
  const d = product.discount;
  if (!d) return '';
  if (d.type === 'percentage') return `${d.value}% off`;
  return `₦${d.value} off`;
};

const buildQueryParams = (n) => {
  const params = new URLSearchParams();

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
  if (!n) { return params.toString(); }

  params.append('page', state.currentPage);
  params.append('per_page', 24);

  if (selectedCategorySlug.value) {
    params.append('category', selectedCategorySlug.value); // Now uses slug
  }
  if (selectedSubcategorySlug.value) {
    params.append('category', selectedSubcategorySlug.value); // Now uses slug
  }
  return params.toString();
};

// Fetch products from API
const fetchProducts = async (page = 1, isLoadMore = false) => {
  try {
    if (!isLoadMore) state.loading = true;
    else state.loadingMore = true;

    state.currentPage = page;

    const queryString = buildQueryParams(true);
    const res = await fetch(`${apiUrl}/products?${queryString}`);

    if (!res.ok) throw new Error('Failed to fetch products');

    const response = await res.json();

    const newProducts = response.data.products.data || [];
    const hasMore = response.data.products.current_page < response.data.products.last_page;

    if (isLoadMore) {
      state.products = [...state.products, ...newProducts];
    } else {
      state.products = newProducts;
      if (response.data.categories) {
        state.categories = response.data.categories;
      }

      const top = document.getElementById('page-header').getBoundingClientRect().top + window.scrollY - 120
      window.scrollTo({ top, behavior: 'smooth' })
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

const isInitialized = ref(false);
// Initialize on mount
onMounted(async () => {
  // 1. Parse URL for category/subcategory SLUG first
  const pathname = window.location.pathname;
  const match = pathname.match(/\/c\/(.+)?$/);
  const slug = (match && match[1]) || '';

  if (slug) {
    const parts = slug.split('--');
    const categorySlug = parts[0];
    const subcategorySlug = parts[1] || null;

    // Set the reactive category/subcategory slugs.
    // NOTE: The main watcher will be disabled by isInitialized.value = false 
    // when these are set.
    if (categorySlug) {
      selectedCategorySlug.value = categorySlug;
    }
    if (subcategorySlug) {
      selectedSubcategorySlug.value = subcategorySlug;
    }
    // We can't set the pageTitle reliably here without categories,
    // so we'll set it after the first fetch if needed.
  }

  // 2. Set filters from URL params (This step also triggers the watcher if it were enabled)
  const urlParams = new URLSearchParams(window.location.search);

  if (urlParams.get('name')) filters.name = urlParams.get('name');
  if (urlParams.get('min_price')) filters.minPrice = Number(urlParams.get('min_price'));
  if (urlParams.get('max_price')) filters.maxPrice = Number(urlParams.get('max_price'));
  if (urlParams.get('sort_by')) filters.sortBy = urlParams.get('sort_by');

  // 3. Perform the initial data fetch with all parameters
  //    This replaces the original fetch call AND the one from the watcher.
  await fetchProducts(1);

  // 4. Set the page title and categories based on the result of the fetch.
  if (state.categories.length > 0) {
    const targetSlug = selectedSubcategorySlug.value || selectedCategorySlug.value;
    if (targetSlug) {
      // Logic to find the title after categories have been fetched
      const category = state.categories.find(c => c.slug === selectedCategorySlug.value)
      const subcategory = category?.subcategories?.find(s => s.slug === selectedSubcategorySlug.value)

      if (subcategory) {
        state.pageTitle = subcategory.name.toUpperCase();
      } else if (category) {
        state.pageTitle = category.name.toUpperCase();
      }
    }
  }


  // 5. Enable the watcher and setup the intersection observer
  isInitialized.value = true;
  nextTick(() => setupObserver());
});

watch(
  [filters, selectedCategorySlug, selectedSubcategorySlug],
  () => {
    if (!isInitialized.value) return; // Skip initial watch trigger
    const queryString = buildQueryParams(false);
    const newUrl = `${window.location.pathname}?${queryString}`;
    window.history.replaceState({}, '', newUrl);
    fetchProducts(1);
  },
  { deep: true }
);

const readable = slug =>
  slug ? slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : ''

const categoryTitle = computed(() => readable(categorySlug.value))
const subcategoryTitle = computed(() => readable(subcategorySlug.value))

// Infinite scroll setup
let observer;
const setupObserver = () => {
  if (observer) observer.disconnect();

  observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting && !state.loading && !state.loadingMore && state.hasMore) {
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
  // fetchProducts(1);
};

const setPrice = (min, max) => {
  filters.minPrice = min;
  filters.maxPrice = max;
  state.currentPage = 1;
  // fetchProducts(1);
};

const router = useRouter()
const route = useRoute()

const changeRoute = (pass) => {
  const currentQuery = { ...route.query }
  router.replace({
    path: `/c${pass}`,
    query: currentQuery
  })
}

const navigateToAllProducts = () => {
  selectedCategorySlug.value = null
  selectedSubcategorySlug.value = null
  state.pageTitle = 'ALL PRODUCTS'
  state.currentPage = 1
  resetFilters(false)
  changeRoute("")
  // fetchProducts(1)
}

const navigateToCategory = (categorySlug) => {
  selectedCategorySlug.value = categorySlug
  selectedSubcategorySlug.value = null
  state.pageTitle = categorySlug.replace(/-/g, ' ').toUpperCase()
  state.currentPage = 1
  resetFilters(false)
  changeRoute(`/${categorySlug}`)
  // fetchProducts(1)
}

const navigateToSubcategory = (categorySlug, subcategorySlug) => {
  selectedSubcategorySlug.value = subcategorySlug;
  state.pageTitle = subcategorySlug.replace(/-/g, ' ').toUpperCase() + `<small>${categorySlug.replace(/-/g, ' ').toUpperCase()}</small>`;
  state.currentPage = 1;
  resetFilters(false);
  changeRoute(`/${categorySlug}--${subcategorySlug}`)
  // fetchProducts(1);
};

const resetFilters = (fetchData = true) => {
  filters.name = '';
  filters.minPrice = null;
  filters.maxPrice = null;
  filters.sortBy = null;
  state.currentPage = 1;

  // if (fetchData) fetchProducts(1);
};

// Computed properties
const products = computed(() => state.products);
const categories = computed(() => state.categories);
const loading = computed(() => state.loading);
const loadingMore = computed(() => state.loadingMore);
const error = computed(() => state.error);
const pageTitle = computed(() => state.pageTitle);
// Update computed properties
const categorySlug = computed(() => {
  if (selectedSubcategorySlug.value) return null;
  return selectedCategorySlug.value; // Just return the slug directly
});

const subcategorySlug = computed(() => {
  return selectedSubcategorySlug.value; // Just return the slug directly
});

// Cart helpers
const isInCart = (productId) => {
  return cartStore.items.some(item => (item.product_id || item.id) === productId);
};

const getCartQuantity = (productId) => {
  const item = cartStore.items.find(i => (i.product_id || i.id) === productId);
  return item ? item.quantity : 0;
};

// Quantity input handlers
const onQuantityBlur = (e, product) => {
  const newQty = parseInt(e.target.innerText, 10);
  if (!isNaN(newQty) && newQty !== getCartQuantity(product.id)) {
    updateQuantity(product, newQty);
  } else {
    e.target.innerText = getCartQuantity(product.id);
  }
};

const onQuantityEnter = (e, product) => {
  const newQty = parseInt(e.target.innerText, 10);
  if (!isNaN(newQty) && newQty !== getCartQuantity(product.id)) {
    updateQuantity(product, newQty);
  }
  e.target.blur();
};

// Cart actions
const addToCart = async (product) => {
  loadingStates.value[product.id] = true;
  try {
    await cartStore.addItem(product, 1);
    console.log('object');
  } catch (error) {
    console.error('Error adding to cart:', error);
  } finally {
    loadingStates.value[product.id] = false;
  }
};

const updateQuantity = async (product, quantity) => {
  loadingStates.value[product.id] = true;

  try {
    if (quantity <= 0) {
      cartStore.removeItem(product.id);
    } else {
      cartStore.updateQuantity(product.id, quantity);
    }
  } catch (error) {
    console.error('Error updating quantity:', error);
  } finally {
    loadingStates.value[product.id] = false;
  }
};

// Wishlist actions
const toggleWishlist = (product) => {
  const exists = wishlistStore.items.find(p => p.id === product.id);
  if (exists) {
    wishlistStore.remove(product.id);
  } else {
    wishlistStore.add(product);
  }
};
</script>

<style>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>