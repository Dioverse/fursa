<template>
  <DefaultLayout>
    <!-- Hero -->
    <section class="relative bg-gray-900">
      <div class="relative">
        <img src="/images/oil-splash.png" alt="Lubricants" class="w-full h-72 object-cover rounded-b-2xl">
        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-b-2xl flex flex-col items-center justify-center text-center text-white px-6">
          <h1 class="text-2xl md:text-4xl font-bold">Premium Lubricants. Trusted Performance.</h1>
          <p class="mt-3 max-w-2xl text-sm md:text-base">
            Shop high-quality engine oils and lubricants designed to protect, power, and perform every mile, every machine.
          </p>
        </div>
      </div>
    </section>

    <section class="py-16 px-6">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

          <!-- Sidebar -->
          <aside class="lg:col-span-1">
            <!-- CategoryFilter fetches categories itself and emits update -->
            <CategoryFilter @update="handleFilterUpdate" />
          </aside>

          <!-- Products area -->
          <main class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
              <h1 class="text-2xl font-bold mb-4 md:mb-0">Motor Oil</h1>

              <div class="flex items-center gap-4">
                <div class="flex gap-2">
                  <button @click="viewMode = 'grid'" class="p-2 rounded" :class="viewMode === 'grid' ? 'bg-primary text-white' : 'bg-gray-100'">
                    <font-awesome-icon icon="th" />
                  </button>
                  <button @click="viewMode = 'list'" class="p-2 rounded" :class="viewMode === 'list' ? 'bg-primary text-white' : 'bg-gray-100'">
                    <font-awesome-icon icon="list" />
                  </button>
                </div>

                <select v-model="sortBy" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                  <option value="featured">Featured</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="name">Name: A to Z</option>
                  <option value="rating">Best Rating</option>
                </select>
              </div>
            </div>

            <!-- Loading skeleton -->
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="i in 6" :key="i" class="animate-pulse">
                <div class="bg-gray-300 h-48 rounded-t-lg"></div>
                <div class="bg-white p-4 rounded-b-lg">
                  <div class="h-4 bg-gray-300 rounded mb-2"></div>
                  <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                </div>
              </div>
            </div>

            <!-- No results -->
            <div v-else-if="filteredProducts.length === 0" class="text-center py-12 bg-white rounded-lg shadow-md">
              <font-awesome-icon icon="box" size="3x" class="text-gray-400 mb-4" />
              <p class="text-gray-600">No products found matching your criteria</p>
              <button @click="clearFilters" class="mt-4 text-primary hover:underline">Clear all filters</button>
            </div>

            <!-- Product grid -->
            <div v-else class="grid gap-6" :class="viewMode === 'grid' ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : 'grid-cols-1'">
              <ProductCard v-for="product in paginatedProducts" :key="product.id" :product="product" :view-mode="viewMode" />
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="mt-8 flex justify-center">
              <nav class="flex items-center gap-2">
                <button @click="currentPage--" :disabled="currentPage === 1"
                  class="px-3 py-2 rounded border hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                  <font-awesome-icon icon="chevron-left" />
                </button>

                <button v-for="page in displayedPages" :key="page" @click="currentPage = page"
                  class="px-3 py-2 rounded border min-w-[40px]" :class="currentPage === page ? 'bg-primary text-white' : 'hover:bg-gray-100'">
                  {{ page }}
                </button>

                <button @click="currentPage++" :disabled="currentPage === totalPages"
                  class="px-3 py-2 rounded border hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                  <font-awesome-icon icon="chevron-right" />
                </button>
              </nav>
            </div>
          </main>
        </div>
      </div>
    </section>

    <CTA />
    <Brochure />
  </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import ProductCard from '@/components/products/ProductCard.vue'
import CategoryFilter from '@/components/products/CategoryFilter.vue'
import CTA from '@/components/common/CTA.vue'
import Brochure from '@/components/common/Brochure.vue'

const products = ref([])
const loading = ref(false)

const viewMode = ref('grid')
const sortBy = ref('featured')
const currentPage = ref(1)
const perPage = ref(12)

// filters will be populated by CategoryFilter emit
const filters = ref({
  categories: [],         // array of subcategory ids (numbers)
  priceRange: { min: null, max: null }
})

// fetch products
onMounted(async () => {
  loading.value = true
  try {
    const resp = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/products`)
    const apiProducts = resp.data?.data?.products?.data ?? []

    // Map API product -> UI product (normalize price & category_id)
    const hostBase = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/api.*$/i, '')
    products.value = apiProducts.map(p => {
      // robust price extraction
      const price = Number(p.base_price ?? p.price ?? p.distributor_price ?? 0)

      // image handling: prefer url, then path -> storage
      let image = null
      if (p.images && p.images.length > 0) {
        const firstImg = p.images[0]
        image = firstImg.url || (firstImg.path ? `${hostBase}/storage/${firstImg.path}` : null)
      }

      return {
        id: Number(p.id),
        name: p.name,
        price: Number.isFinite(price) ? price : 0,
        sku: p.sku ?? String(p.id).padStart(6, '0'),
        rating: p.rating ?? Math.floor(Math.random() * 2) + 4,
        image: image ?? '/images/mrs_motor_oil.png',
        volume: p.short_description ?? 'N/A',
        // product.category_id refers to subcategory.id (as you noted)
        category_id: Number(p.category_id ?? p.category?.id ?? NaN)
      }
    })
  } catch (err) {
    console.error('Failed to load products:', err)
  } finally {
    loading.value = false
  }
})

// compute filtered products
const filteredProducts = computed(() => {
  let result = [...products.value]

  // category filter: subcategory IDs
  if (filters.value.categories && filters.value.categories.length > 0) {
    const selected = new Set(filters.value.categories.map(Number))
    result = result.filter(p => selected.has(Number(p.category_id)))
  }

  // price filter: only if min/max are finite numbers
  const { min, max } = filters.value.priceRange || { min: null, max: null }
  if (min !== null && min !== '' && Number.isFinite(Number(min))) {
    result = result.filter(p => p.price >= Number(min))
  }
  if (max !== null && max !== '' && Number.isFinite(Number(max))) {
    result = result.filter(p => p.price <= Number(max))
  }

  // sorting
  switch (sortBy.value) {
    case 'price-low': result.sort((a, b) => a.price - b.price); break
    case 'price-high': result.sort((a, b) => b.price - a.price); break
    case 'name': result.sort((a, b) => a.name.localeCompare(b.name)); break
    case 'rating': result.sort((a, b) => b.rating - a.rating); break
  }

  return result
})

// pagination & helpers
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / perPage.value))
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredProducts.value.slice(start, start + perPage.value)
})
const displayedPages = computed(() => {
  const pages = []
  const maxPages = 5
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(totalPages.value, start + maxPages - 1)
  if (end - start < maxPages - 1) start = Math.max(1, end - maxPages + 1)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

// handle category filter updates (CategoryFilter emits { categories, priceRange })
function handleFilterUpdate(newFilters) {
  // ensure arrays and numeric values are normalized
  filters.value = {
    categories: Array.isArray(newFilters.categories) ? newFilters.categories.map(Number) : [],
    priceRange: {
      min: newFilters.priceRange?.min ?? null,
      max: newFilters.priceRange?.max ?? null
    }
  }
  currentPage.value = 1
}

function clearFilters() {
  filters.value = { categories: [], priceRange: { min: null, max: null } }
}

// reset page when filters change
watch(filters, () => { currentPage.value = 1 }, { deep: true })
</script>
