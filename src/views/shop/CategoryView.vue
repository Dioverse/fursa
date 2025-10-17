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
          <aside class="hidden lg:block w-64 flex-shrink-0 sticky top-24">
            <div class="bg-white rounded-lg shadow-sm p-4">
              <div class="space-y-6">
    <!-- Categories -->
    <div>
      <h3 class="font-bold text-lg mb-4">CATEGORY</h3>
      <div class="space-y-1">
        <div
          v-for="cat in state.categories"
          :key="cat.id"
          class="group relative"
        >
          <div
            class="font-semibold text-gray-800 py-2 px-3 rounded hover:bg-orange-50 cursor-pointer transition-colors"
          >
            {{ cat.name }}
          </div>

          <div
            v-if="cat.subcategories && cat.subcategories.length > 0"
            class="absolute left-full top-0 ml-2 bg-white border border-gray-200 rounded-lg shadow-xl min-w-[200px] py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all"
          >
            <div
              class="px-3 py-2 font-semibold text-gray-700 border-b text-sm"
            >
              {{ cat.name }}
            </div>
            <div
              v-for="sub in cat.subcategories"
              :key="sub.id"
              class="cursor-pointer py-2 px-3 hover:bg-orange-50 text-gray-600 text-sm"
            >
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
        <input
          type="number"
          placeholder="Min"
          class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
          v-model.number="state.filters.priceMin"
        />
        <span>-</span>
        <input
          type="number"
          placeholder="Max"
          class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
          v-model.number="state.filters.priceMax"
        />
      </div>

      <div class="space-y-2">
        <label
          v-for="range in priceRanges"
          :key="range.label"
          class="flex items-center gap-2 cursor-pointer text-sm"
        >
          <input
            type="radio"
            name="price"
            class="w-4 h-4"
            @change="
              () => {
                handleFilterChange('priceMin', range.min)
                handleFilterChange(
                  'priceMax',
                  range.max === Infinity ? null : range.max
                )
              }
            "
          />
          {{ range.label }}
        </label>
      </div>
    </div>

    <!-- Discount -->
    <div class="pb-6 border-b">
      <h3 class="font-bold text-lg mb-3">DISCOUNT</h3>
      <div class="space-y-2">
        <label
          v-for="discount in discountRanges"
          :key="discount.value"
          class="flex items-center gap-2 cursor-pointer text-sm"
        >
          <input
            type="radio"
            name="discount"
            class="w-4 h-4"
            :value="discount.value"
            @change="handleFilterChange('minDiscount', discount.value)"
          />
          {{ discount.label }}
        </label>
      </div>
    </div>

    <!-- Rating -->
    <div>
      <h3 class="font-bold text-lg mb-3">PRODUCT RATING</h3>
      <div class="space-y-2">
        <label
          v-for="rating in ratingOptions"
          :key="rating.value"
          class="flex items-center gap-2 cursor-pointer text-sm"
        >
          <input
            type="radio"
            name="rating"
            class="w-4 h-4"
            :value="rating.value"
            @change="handleFilterChange('minRating', rating.value)"
          />
          <span class="flex items-center gap-1">
            <span
              v-for="i in 5"
              :key="i"
              class="text-yellow-400"
            >
              {{ i <= rating.stars ? '★' : '☆' }}
            </span>
            <span class="ml-1">&nbsp;&nbsp;&above;</span>
          </span>
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
                  <button
                    @click="showSortDropdown = !showSortDropdown"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                  >
                    <span class="text-sm">Sort by</span>
                    <ChevronDownIcon class="w-4 h-4" />
                  </button>

                  <div
                    v-if="showSortDropdown"
                    class="absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-10 min-w-[200px]"
                  >
                    <button
                      v-for="(label, value) in sortOptions"
                      :key="value"
                      @click="handleSort(value, label)"
                      class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm"
                    >
                      {{ label }}
                    </button>
                  </div>
                </div>

                <button
                  @click="showMobileFilters = true"
                  class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-300 rounded"
                >
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
              <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500" />
              <p class="mt-4 text-gray-600">Loading products...</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="text-center py-12">
              <p class="text-red-500">{{ error }}</p>
            </div>

            <!-- Products Grid -->
            <div v-if="!loading && !error" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div
                v-for="product in products"
                :key="product.id"
                class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow"
              >
                <div class="relative">
                  <img
                    :src="product.images?.[0]?.path || '/placeholder.png'"
                    :alt="product.name"
                    class="w-full h-48 object-cover"
                  />
                  <span
                    v-if="product.discount"
                    class="absolute top-2 left-2 bg-orange-500 text-white px-2 py-1 text-xs rounded font-semibold"
                  >
                    -{{ discountLabel(product) }}
                  </span>

                  <button class="absolute top-2 right-2 bg-white rounded-full p-2 hover:bg-orange-50">
                    <HeartIcon class="w-5 h-5 text-gray-600" />
                  </button>
                </div>

                <div class="p-4">
                  <h3 class="text-sm text-gray-800 mb-2 line-clamp-2">{{ product.name }}</h3>
                  <div class="mb-2">
                    <span class="text-lg font-bold text-gray-900">
                      ₦ {{ priceToLocale(product.discounted_price ?? product.price) }}
                    </span>
                    <span v-if="product.discount" class="text-sm text-gray-400 line-through ml-2">
                      ₦ {{ priceToLocale(product.price) }}
                    </span>
                  </div>

                  <div v-if="product.rating" class="flex items-center gap-1 mb-2">
                    <span v-for="i in 5" :key="i" class="text-yellow-400 text-xs">
                      {{ i <= Math.floor(product.rating) ? '★' : '☆' }}
                    </span>
                    <span class="text-xs text-gray-500 ml-1">({{ product.reviews }})</span>
                  </div>

                  <button class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 text-sm font-semibold">
                    Add to cart
                  </button>
                </div>
              </div>
            </div>

            <div ref="observerTarget" id="scroll-trigger" />

            <div v-if="loadingMore" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500" />
            </div>

            <div v-if="!loading && !error && products.length === 0" class="text-center py-12">
              <p class="text-gray-600">No products match your filters.</p>
              <button
                @click="resetFilters"
                class="mt-4 px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600"
              >
                Reset Filters
              </button>
            </div>
          </main>
        </div>
      </div>

      <!-- Mobile Filters Drawer -->
      <div
        v-if="showMobileFilters"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 lg:hidden"
        @click="showMobileFilters = false"
      >
        <div class="fixed inset-y-0 right-0 w-80 bg-white overflow-y-auto" @click.stop>
          <div class="p-4">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
              <h3 class="font-bold text-lg">Filters</h3>
              <button @click="showMobileFilters = false">
                <XIcon class="w-6 h-6" />
              </button>
            </div>

            <SidebarContent />

            <div class="flex gap-2 mt-6 pt-4 border-t">
              <button @click="resetFilters" class="flex-1 py-2 border border-gray-300 rounded">Reset</button>
              <button
                @click="applyFilters"
                class="flex-1 py-2 bg-orange-500 text-white rounded hover:bg-orange-600"
              >
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

//
// Icons (tiny inline components)
//
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
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
         viewBox="0 0 24 24" class="w-4 h-4 text-red-500">
      <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
               2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81
               14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4
               6.86-8.55 11.54L12 21.35z"/>
    </svg>
  `
}

//
// Reactive state (mirrors React state structure)
//
const state = reactive({
  products: [],
  categories: [],
  loading: true,
  loadingMore: false,
  error: null,
  filters: {
    sortBy: null,
    priceMin: null,
    priceMax: null,
    minDiscount: null,
    minRating: null,
  },
  currentPage: 1,
  perPage: 12,
  hasMore: true,
  showMobileFilters: false,
  showSortDropdown: false,
  selectedCategory: null,
  selectedSubcategory: null,
  pageTitle: 'PRODUCTS',
  categorySlug: null,
  subcategorySlug: null,
});

const observerTarget = ref(null);

const sortOptions = {
  hp: 'Highest Price',
  lp: 'Lowest Price',
  if: 'Featured',
};

const showSortDropdown = ref(false);
const showMobileFilters = ref(false);

//
// Utils
//
const parseSlug = (slug) => {
  if (!slug) return { categorySlug: null, subcategorySlug: null };
  const parts = slug.split('--');
  if (parts.length === 2) return { categorySlug: parts[0], subcategorySlug: parts[1] };
  return { categorySlug: slug, subcategorySlug: null };
};

const priceToLocale = (val) => {
  if (val == null) return '0';
  return Number(val).toLocaleString('en-NG');
};

const discountLabel = (product) => {
  const d = product.discount;
  if (!d) return '';
  if (d.type === 'percentage') return `${d.value}%`;
  // if absolute discount value
  return `${Math.round(((product.price - (product.discounted_price ?? product.price)) / product.price) * 100)}%`;
};

//
// Sample backend response (copied/adapted from your React sample)
//
const generateSampleResponse = (page = 1, filters = {}, categorySlug = null, subcategorySlug = null) => {
  const allProducts = [
    { id: 1, name: 'Premium Wireless Headphones', price: 45000, discounted_price: 32000, discount: { type: 'percentage', value: 30 }, rating: 4.5, reviews: 128, category_id: 1, images: [{ path: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop' }] },
    { id: 2, name: 'Smart Watch Pro', price: 85000, discounted_price: 59500, discount: { type: 'percentage', value: 30 }, rating: 4.2, reviews: 95, category_id: 1, images: [{ path: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop' }] },
    { id: 3, name: '4K Webcam', price: 28000, discounted_price: 19600, discount: { type: 'percentage', value: 30 }, rating: 4.7, reviews: 203, category_id: 2, images: [{ path: 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400&h=400&fit=crop' }] },
    { id: 4, name: 'Mechanical Keyboard RGB', price: 35000, discounted_price: 24500, discount: { type: 'percentage', value: 30 }, rating: 4.6, reviews: 156, category_id: 2, images: [{ path: 'https://images.unsplash.com/photo-1587829191301-26e5f6c0d827?w=400&h=400&fit=crop' }] },
    { id: 5, name: 'USB-C Hub 7-in-1', price: 22000, discounted_price: 16500, discount: { type: 'percentage', value: 25 }, rating: 4.4, reviews: 87, category_id: 3, images: [{ path: 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&h=400&fit=crop' }] },
    { id: 6, name: 'Portable SSD 1TB', price: 125000, discounted_price: 87500, discount: { type: 'percentage', value: 30 }, rating: 4.8, reviews: 312, category_id: 3, images: [{ path: 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=400&fit=crop' }] },
    { id: 7, name: 'Laptop Stand Aluminum', price: 18000, discounted_price: 12600, discount: { type: 'percentage', value: 30 }, rating: 4.3, reviews: 64, category_id: 4, images: [{ path: 'https://images.unsplash.com/photo-1588438281241-e41d8b73bbb6?w=400&h=400&fit=crop' }] },
    { id: 8, name: 'LED Monitor Light Bar', price: 32000, discounted_price: 22400, discount: { type: 'percentage', value: 30 }, rating: 4.5, reviews: 118, category_id: 4, images: [{ path: 'https://images.unsplash.com/photo-1593642632495-8a3defddd8d0?w=400&h=400&fit=crop' }] },
    { id: 9, name: 'Wireless Mouse Pro', price: 15000, discounted_price: 10500, discount: { type: 'percentage', value: 30 }, rating: 4.6, reviews: 225, category_id: 2, images: [{ path: 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=400&fit=crop' }] },
    { id: 10, name: 'Phone Stand Mount', price: 8000, discounted_price: 5200, discount: { type: 'percentage', value: 35 }, rating: 4.1, reviews: 42, category_id: 4, images: [{ path: 'https://images.unsplash.com/photo-1605559424843-9e4c3ca4b47e?w=400&h=400&fit=crop' }] },
    { id: 11, name: 'Gaming Mouse Pad XL', price: 12000, discounted_price: 8400, discount: { type: 'percentage', value: 30 }, rating: 4.4, reviews: 156, category_id: 2, images: [{ path: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=400&fit=crop' }] },
    { id: 12, name: 'USB-C Cable 2m', price: 5000, discounted_price: 2500, discount: { type: 'percentage', value: 50 }, rating: 4.7, reviews: 512, category_id: 3, images: [{ path: 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&h=400&fit=crop' }] },
  ];

  let filtered = allProducts.slice();

  if (subcategorySlug === 'audio') filtered = allProducts.filter(p => p.category_id === 1);
  else if (subcategorySlug === 'video') filtered = allProducts.filter(p => p.category_id === 2);
  else if (categorySlug === 'electronics' && !subcategorySlug) filtered = allProducts;

  // Apply sort
  if (filters.sortBy === 'hp') {
    filtered.sort((a, b) => (b.discounted_price ?? b.price) - (a.discounted_price ?? a.price));
  } else if (filters.sortBy === 'lp') {
    filtered.sort((a, b) => (a.discounted_price ?? a.price) - (b.discounted_price ?? b.price));
  }

  // TODO: add price filter/minDiscount/minRating logic if needed

  const start = (page - 1) * 12;
  const paginated = filtered.slice(start, start + 12);

  return {
    success: true,
    data: {
      products: {
        data: paginated,
        current_page: page,
        last_page: Math.ceil(filtered.length / 12),
      },
      categories: [
        {
          id: 1,
          name: 'Audio',
          slug: 'audio',
          subcategories: [{ id: 10, name: 'Headphones', slug: 'headphones' }, { id: 11, name: 'Speakers', slug: 'speakers' }],
        },
        {
          id: 2,
          name: 'Video',
          slug: 'video',
          subcategories: [{ id: 20, name: 'Webcams', slug: 'webcams' }, { id: 21, name: 'Monitors', slug: 'monitors' }],
        },
        {
          id: 3,
          name: 'Accessories',
          slug: 'accessories',
          subcategories: [{ id: 30, name: 'Cables', slug: 'cables' }, { id: 31, name: 'Storage', slug: 'storage' }],
        },
        {
          id: 4,
          name: 'Peripherals',
          slug: 'peripherals',
          subcategories: [{ id: 40, name: 'Keyboards', slug: 'keyboards' }, { id: 41, name: 'Mice', slug: 'mice' }],
        },
      ],
      filters: {
        sort_by: {
          hp: 'Highest Price',
          lp: 'Lowest Price',
          if: 'Featured',
        },
      },
    },
  };
};

//
// Fetch products (mimic your React fetchProducts)
//
const fetchProducts = async (page = 1, isLoadMore = false) => {
  try {
    if (!isLoadMore) state.loading = true;
    else state.loadingMore = true;

    // In production you'd use real API call and pass params:
    // const params = new URLSearchParams({...})
    // const res = await fetch(`/api/shop/${state.categorySlug}/${state.subcategorySlug || ''}?${params}`)
    // const response = await res.json();

    await new Promise(r => setTimeout(r, 250)); // small simulated delay
    const response = generateSampleResponse(page, state.filters, state.categorySlug, state.subcategorySlug);

    const newProducts = response.data.products.data;
    const hasMore = response.data.products.current_page < response.data.products.last_page;

    if (isLoadMore) state.products = [...state.products, ...newProducts];
    else {
      state.products = newProducts;
      state.categories = response.data.categories;
    }

    state.hasMore = hasMore;
    state.loading = false;
    state.loadingMore = false;
    state.error = null;
  } catch (err) {
    state.error = 'Failed to load products';
    state.loading = false;
    state.loadingMore = false;
  }
};

//
// Initialize from URL on mount
//
onMounted(() => {
  const pathname = window.location.pathname;
  const match = pathname.match(/\/shop\/c\/(.+)?$/);
  const slug = (match && match[1]) || '';

  const parsed = parseSlug(slug);
  state.categorySlug = parsed.categorySlug;
  state.subcategorySlug = parsed.subcategorySlug;
  state.pageTitle = state.subcategorySlug ? state.subcategorySlug.replace(/-/g, ' ').toUpperCase() : state.categorySlug ? state.categorySlug.replace(/-/g, ' ').toUpperCase() : 'PRODUCTS';

  fetchProducts(1).then(() => {
    // setup intersection observer after initial load to avoid immediate load-more
    nextTick(setupObserver);
  });
});

//
// Infinite scroll observer
//
let observer;
const setupObserver = () => {
  if (observer) observer.disconnect();
  observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting && !state.loadingMore && state.hasMore) {
        // increment page and fetch
        const next = state.currentPage + 1;
        state.currentPage = next;
        fetchProducts(next, true);
      }
    },
    { rootMargin: '200px' }
  );

  if (observerTarget.value) observer.observe(observerTarget.value);
};

//
// Sorting / filters handlers
//
const handleSort = (value, label) => {
  state.filters.sortBy = value;
  showSortDropdown.value = false;
  state.currentPage = 1;
  fetchProducts(1);
};

const handleFilterChange = (filterKey, value) => {
  state.filters[filterKey] = value;
  state.currentPage = 1;
};

const applyFilters = () => {
  showMobileFilters.value = false;
  state.currentPage = 1;
  fetchProducts(1);
};

const resetFilters = () => {
  state.filters = { sortBy: null, priceMin: null, priceMax: null, minDiscount: null, minRating: null };
  state.currentPage = 1;
  fetchProducts(1);
};

//
// Price, discount, rating UI data (same arrays you used)
//
const priceRanges = [
  { label: '₦0 - ₦50,000', min: 0, max: 50000 },
  { label: '₦50,000 - ₦100,000', min: 50000, max: 100000 },
  { label: '₦100,000 - ₦250,000', min: 100000, max: 250000 },
  { label: '₦250,000 - ₦500,000', min: 250000, max: 500000 },
  { label: '₦500,000+', min: 500000, max: Infinity },
];

const discountRanges = [
  { label: '50% or more', value: 50 },
  { label: '40% or more', value: 40 },
  { label: '30% or more', value: 30 },
  { label: '20% or more', value: 20 },
  { label: '10% or more', value: 10 },
];

const ratingOptions = [
  { label: '4★ & above', value: 4, stars: 4 },
  { label: '3★ & above', value: 3, stars: 3 },
  { label: '2★ & above', value: 2, stars: 2 },
  { label: '1★ & above', value: 1, stars: 1 },
];

//
// SidebarContent as a local component so template stays clean
//
// const SidebarContent = {
//   setup() {
//     return () => (
//       <div class="space-y-6">
//         <!-- Categories -->
//         <div>
//           <h3 class="font-bold text-lg mb-4">CATEGORY</h3>
//           <div class="space-y-1">
//             {state.categories.map(cat => (
//               <div key={cat.id} class="group relative">
//                 <div class="font-semibold text-gray-800 py-2 px-3 rounded hover:bg-orange-50 cursor-pointer transition-colors">
//                   {cat.name}
//                 </div>

//                 {cat.subcategories?.length > 0 && (
//                   <div class="absolute left-full top-0 ml-2 bg-white border border-gray-200 rounded-lg shadow-xl min-w-[200px] py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
//                     <div class="px-3 py-2 font-semibold text-gray-700 border-b text-sm">{cat.name}</div>
//                     {cat.subcategories.map(sub => (
//                       <div key={sub.id} class="cursor-pointer py-2 px-3 hover:bg-orange-50 text-gray-600 text-sm">
//                         {sub.name}
//                       </div>
//                     ))}
//                   </div>
//                 )}
//               </div>
//             ))}
//           </div>
//         </div>

//         <!-- Price Range -->
//         <div class="pb-6 border-b">
//           <h3 class="font-bold text-lg mb-3">PRICE (₦)</h3>
//           <div class="flex gap-2 mb-3">
//             <input type="number" placeholder="Min" value={state.filters.priceMin ?? ''} onInput={(e) => handleFilterChange('priceMin', e.target.value ? Number(e.target.value) : null)} class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
//             <span>-</span>
//             <input type="number" placeholder="Max" value={state.filters.priceMax ?? ''} onInput={(e) => handleFilterChange('priceMax', e.target.value ? Number(e.target.value) : null)} class="w-full px-2 py-1 border border-gray-300 rounded text-sm" />
//           </div>

//           <div class="space-y-2">
//             {priceRanges.map(range => (
//               <label key={range.label} class="flex items-center gap-2 cursor-pointer text-sm">
//                 <input type="radio" name="price" onChange={() => { handleFilterChange('priceMin', range.min); handleFilterChange('priceMax', range.max === Infinity ? null : range.max); }} class="w-4 h-4" />
//                 {range.label}
//               </label>
//             ))}
//           </div>
//         </div>

//         <!-- Discount -->
//         <div class="pb-6 border-b">
//           <h3 class="font-bold text-lg mb-3">DISCOUNT</h3>
//           <div class="space-y-2">
//             {discountRanges.map(discount => (
//               <label key={discount.value} class="flex items-center gap-2 cursor-pointer text-sm">
//                 <input type="radio" name="discount" value={discount.value} onChange={(e) => handleFilterChange('minDiscount', Number(e.target.value))} class="w-4 h-4" />
//                 {discount.label}
//               </label>
//             ))}
//           </div>
//         </div>

//         <!-- Rating -->
//         <div>
//           <h3 class="font-bold text-lg mb-3">PRODUCT RATING</h3>
//           <div class="space-y-2">
//             {ratingOptions.map(rating => (
//               <label key={rating.value} class="flex items-center gap-2 cursor-pointer text-sm">
//                 <input type="radio" name="rating" value={rating.value} onChange={(e) => handleFilterChange('minRating', Number(e.target.value))} class="w-4 h-4" />
//                 <span class="flex items-center gap-1">
//                   {[...Array(5)].map((_, i) => (
//                     <span key={i} class="text-yellow-400">{i < rating.stars ? '★' : '☆'}</span>
//                   ))}
//                   <span class="ml-1">& above</span>
//                 </span>
//               </label>
//             ))}
//           </div>
//         </div>
//       </div>
//     );
//   },
// };

//
// Small helpers exposed to template
//
const products = computed(() => state.products);
const categories = computed(() => state.categories);
const loading = computed(() => state.loading);
const loadingMore = computed(() => state.loadingMore);
const error = computed(() => state.error);
const pageTitle = computed(() => state.pageTitle);
const categorySlug = computed(() => state.categorySlug);
const subcategorySlug = computed(() => state.subcategorySlug);

</script>

<style>
/* example: line-clamp utility fallback if not using plugin */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
