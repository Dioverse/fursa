<template>
    <DefaultLayout>
        <!-- 1️⃣ Hero Section -->
        <section class="relative bg-gray-900">
            <div class="relative">
            <!-- Background image -->
            <img src="/images/oil-splash.png" alt="Lubricants" class="w-full h-72 object-cover rounded-b-2xl">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-b-2xl flex flex-col items-center justify-center text-center text-white px-6">
                <h1 class="text-2xl md:text-4xl font-bold">Premium Lubricants. Trusted Performance.</h1>
                <p class="mt-3 max-w-2xl text-sm md:text-base">
                Shop high-quality engine oils and lubricants designed to protect, power, and perform every mile, every machine.
                </p>
            </div>
            </div>
        </section>

         <!-- 2️⃣ Features Section -->
        <section class="py-12 px-6">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-6 bg-white border rounded-xl shadow-sm p-6">
                
                    <!-- Feature 1 -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-yellow-500 text-white p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657-1.343-3-3-3S6 9.343 6 11s1.343 3 3 3 3-1.343 3-3z" />
                        </svg>
                        </div>
                        <div>
                        <h4 class="font-semibold">Secure Payment Gateways</h4>
                        <p class="text-sm text-gray-600">48+ gateways to ensure your security.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-yellow-500 text-white p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Genuine Customer Reviews</h4>
                            <p class="text-sm text-gray-600">Find verified reviews showcased on our platforms.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-yellow-500 text-white p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">24/7 Customer Support</h4>
                            <p class="text-sm text-gray-600">Always our support team is available for you.</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex items-start space-x-3">
                        <div class="bg-yellow-500 text-white p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Easy Return Policy</h4>
                            <p class="text-sm text-gray-600">If you're not satisfied, return it hassle-free.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 3️⃣ Categories Section -->
        <section class="py-6">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-3 gap-6">
                
                    <!-- Category 1 -->
                    <div class="relative rounded-lg overflow-hidden shadow-md">
                        <img src="/images/automotive.png" alt="Automotive Lubricant" class="w-full h-48 object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-end justify-center">
                        <!-- <h3 class="text-white text-lg font-semibold p-4">Automotive Lubricant</h3> -->
                        </div>
                    </div>

                    <!-- Category 2 -->
                    <div class="relative rounded-lg overflow-hidden shadow-md">
                        <img src="/images/industrial.png" alt="Industrial Lubricant" class="w-full h-48 object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-end justify-center">
                        <!-- <h3 class="text-white text-lg font-semibold p-4">Industrial Lubricant</h3> -->
                        </div>
                    </div>

                    <!-- Category 3 -->
                    <div class="relative rounded-lg overflow-hidden shadow-md">
                        <img src="/images/heavy-equipment.png" alt="Greases, Marine & Heavy Equipment Oils" class="w-full h-48 object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-end justify-center">
                        <!-- <h3 class="text-white text-lg font-semibold p-4">Greases, Marine & Heavy Equipment Oils</h3> -->
                        </div>
                    </div>

                </div>
            </div>
        </section>
        
        <section class="py-16 px-6">
            <div class="container mx-auto px-4">

                <!-- Breadcrumb -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <RouterLink to="/" class="text-gray-700 hover:text-primary">
                                <font-awesome-icon icon="home" class="mr-2" />
                                Home
                            </RouterLink>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <font-awesome-icon icon="chevron-right" class="mx-2 text-gray-400" />
                                <span class="text-gray-500">Shop</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Sidebar Filters -->
                    <aside class="lg:col-span-1">
                        <!-- <CategoryFilter @update="handleFilterUpdate" /> -->
                         <CategoryFilter 
                        :icategories="icategories"
                        :greases="greases"
                        :marines="marines"
                        @update:categories="filters.categories = $event"
                        />
                         <!-- <CategoryFilter 
                        @update:priceRange="filters.priceRange = $event" 
                        /> -->
                    </aside>

                    <!-- Main Content -->
                    <main class="lg:col-span-3">
                        <!-- Header -->
                        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <h1 class="text-2xl font-bold mb-4 md:mb-0">Motor Oil</h1>

                                <div class="flex items-center gap-4">
                                    <!-- View Toggle -->
                                    <div class="flex gap-2">
                                        <button @click="viewMode = 'grid'" class="p-2 rounded"
                                            :class="viewMode === 'grid' ? 'bg-primary text-white' : 'bg-gray-100'">
                                            <font-awesome-icon icon="th" />
                                        </button>
                                        <button @click="viewMode = 'list'" class="p-2 rounded"
                                            :class="viewMode === 'list' ? 'bg-primary text-white' : 'bg-gray-100'">
                                            <font-awesome-icon icon="list" />
                                        </button>
                                    </div>

                                    <!-- Sort Dropdown -->
                                    <select v-model="sortBy"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="featured">Featured</option>
                                        <option value="price-low">Price: Low to High</option>
                                        <option value="price-high">Price: High to Low</option>
                                        <option value="name">Name: A to Z</option>
                                        <option value="rating">Best Rating</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid/List -->
                        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="i in 6" :key="i" class="animate-pulse">
                                <div class="bg-gray-300 h-48 rounded-t-lg"></div>
                                <div class="bg-white p-4 rounded-b-lg">
                                    <div class="h-4 bg-gray-300 rounded mb-2"></div>
                                    <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="products.length === 0" class="text-center py-12 bg-white rounded-lg shadow-md">
                            <font-awesome-icon icon="box" size="3x" class="text-gray-400 mb-4" />
                            <p class="text-gray-600">No products found matching your criteria</p>
                            <button @click="clearFilters" class="mt-4 text-primary hover:underline">
                                Clear all filters
                            </button>
                        </div>

                        <div v-else class="grid gap-6"
                            :class="viewMode === 'grid' ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : 'grid-cols-1'">
                            <ProductCard v-for="product in paginatedProducts" :key="product.id" :product="product"
                                :view-mode="viewMode" />
                        </div>

                        <!-- Pagination -->
                        <div v-if="products.length > 0" class="mt-8 flex justify-center">
                            <nav class="flex items-center gap-2">
                                <button @click="currentPage--" :disabled="currentPage === 1"
                                    class="px-3 py-2 rounded border hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <font-awesome-icon icon="chevron-left" />
                                </button>

                                <button v-for="page in displayedPages" :key="page" @click="currentPage = page"
                                    class="px-3 py-2 rounded border min-w-[40px]"
                                    :class="currentPage === page ? 'bg-primary text-white' : 'hover:bg-gray-100'">
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

        <!-- Bottom CTA Section -->
        <CTA />
        <!-- Download Button -->
        <Brochure/>

    </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import ProductCard from '@/components/products/ProductCard.vue'
import CategoryFilter from '@/components/products/CategoryFilter.vue'
import productsService from '@/services/products.service'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'
import axios from "axios"

const route = useRoute()
const router = useRouter()

const products = ref([])
const minPrice = ref(null)
const maxPrice = ref(null)

const loading = ref(false)
const viewMode = ref('grid')
const sortBy = ref('featured')
const currentPage = ref(1)
const perPage = ref(12)

const filters = ref({
    categories: [],
    priceRange: { min: null, max: null }
})

const icategories = ref([])
const greases = ref([])
const marines = ref([])
const automotiveLubricants = ref([])

// Mock products data
onMounted(async () => {
    loading.value = true
    try {
        // Replace with actual API call
        const resp = await axios.get('https://back.fursaenergy.com/public/api/products')
        const apiProducts = resp.data?.data?.products?.data ?? []
        // products.value = apiProducts
        products.value = apiProducts.map(p => ({
            id: p.id,
            name: p.name,
            price: parseFloat(p.base_price),
            sku: p.id.toString().padStart(6, "0"),
            rating: Math.floor(Math.random() * 2) + 4,
            image: p.images.length ? p.images[0].url : "../../public/images/mrs_motor_oil.png",
            volume: p.short_description || "N/A",
            category: p.category?.name || "Uncategorized"
        }))
    } catch (error) {
        console.error('Failed to load products:', error)
    } finally {
        loading.value = false
    }
})

const filteredProducts = computed(() => {
  let result = [...products.value]

  // Category filter
  if (filters.value.categories.length > 0) {
    result = result.filter(p => filters.value.categories.includes(p.category_id))
  }

  // Price filter
  if (filters.value.priceRange.min !== null) {
    result = result.filter(p => p.price >= filters.value.priceRange.min)
  }
  if (filters.value.priceRange.max !== null) {
    result = result.filter(p => p.price <= filters.value.priceRange.max)
  }

  // Sorting
  switch (sortBy.value) {
    case "price-low":
      result.sort((a, b) => a.price - b.price)
      break
    case "price-high":
      result.sort((a, b) => b.price - a.price)
      break
    case "name":
      result.sort((a, b) => a.name.localeCompare(b.name))
      break
    case "rating":
      result.sort((a, b) => b.rating - a.rating)
      break
  }

  return result
})


// const filteredProducts = computed(() => {
//     let result = [...products.value]

//     // Apply category filter
//     if (filters.value.categories.length > 0) {
//         // Filter by categories
//     }

//     // Apply price filter
//     if (filters.value.priceRange.min) {
//         result = result.filter(p => p.price >= filters.value.priceRange.min)
//     }
//     if (filters.value.priceRange.max) {
//         result = result.filter(p => p.price <= filters.value.priceRange.max)
//     }

//     // Apply sorting
//     switch (sortBy.value) {
//         case 'price-low':
//             result.sort((a, b) => a.price - b.price)
//             break
//         case 'price-high':
//             result.sort((a, b) => b.price - a.price)
//             break
//         case 'name':
//             result.sort((a, b) => a.name.localeCompare(b.name))
//             break
//         case 'rating':
//             result.sort((a, b) => b.rating - a.rating)
//             break
//     }

//     return result
// })

const totalPages = computed(() =>
    Math.ceil(filteredProducts.value.length / perPage.value)
)

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * perPage.value
    const end = start + perPage.value
    return filteredProducts.value.slice(start, end)
})

const displayedPages = computed(() => {
    const pages = []
    const maxPages = 5
    let start = Math.max(1, currentPage.value - 2)
    let end = Math.min(totalPages.value, start + maxPages - 1)

    if (end - start < maxPages - 1) {
        start = Math.max(1, end - maxPages + 1)
    }

    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    return pages
})

function applyPriceFilter() {
  filters.value.priceRange.min = minPrice.value ? parseFloat(minPrice.value) : null
  filters.value.priceRange.max = maxPrice.value ? parseFloat(maxPrice.value) : null
}


const handleFilterUpdate = (newFilters) => {
    filters.value = newFilters
    currentPage.value = 1
}

const clearFilters = () => {
    filters.value = {
        categories: [],
        priceRange: { min: '', max: '' }
    }
}

// Watch for route query changes
watch(() => route.query, (query) => {
    if (query.category) {
        // Apply category from URL
    }
    if (query.search) {
        // Apply search from URL
    }
}, { immediate: true })
</script>