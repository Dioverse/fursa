import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import productsService from '@/services/products.service'

export const useProductsStore = defineStore('products', () => {
  const products = ref([])
  const categories = ref([])
  const loading = ref(false)
  const error = ref(null)
  const filters = ref({
    category: '',
    priceMin: null,
    priceMax: null,
    search: '',
  })

  const filteredProducts = computed(() => {
    let result = [...products.value]

    // Apply category filter
    if (filters.value.category) {
      result = result.filter((p) => p.category === filters.value.category)
    }

    // Apply price range filter
    if (filters.value.priceMin) {
      result = result.filter((p) => p.price >= filters.value.priceMin)
    }
    if (filters.value.priceMax) {
      result = result.filter((p) => p.price <= filters.value.priceMax)
    }

    // Apply search filter
    if (filters.value.search) {
      const searchLower = filters.value.search.toLowerCase()
      result = result.filter(
        (p) =>
          p.name.toLowerCase().includes(searchLower) || p.sku.toLowerCase().includes(searchLower),
      )
    }

    return result
  })

  async function fetchProducts(params = {}) {
    loading.value = true
    error.value = null

    try {
      // Mock API call - replace with actual API
      const response = await new Promise((resolve) => {
        setTimeout(() => {
          resolve({
            data: [
              {
                id: 1,
                name: 'MRS 5L Motorcycle engine oil',
                price: 145000,
                sku: 'A23WERT5',
                category: 'motor-oil',
                rating: 4,
                image: null,
              },
              {
                id: 2,
                name: 'MRS Premium Motor Oil 20W-50',
                price: 125000,
                sku: 'B45TYUI8',
                category: 'motor-oil',
                rating: 5,
                image: null,
              },
              {
                id: 3,
                name: 'MRS Diesel Engine Oil',
                price: 165000,
                sku: 'C67HJKL2',
                category: 'heavy-duty',
                rating: 4,
                image: null,
              },
              {
                id: 4,
                name: 'MRS Hydraulic Oil ISO 68',
                price: 155000,
                sku: 'D89MNOP4',
                category: 'industrial',
                rating: 5,
                image: null,
              },
              {
                id: 5,
                name: 'MRS Gear Oil EP 90',
                price: 135000,
                sku: 'E12QWER6',
                category: 'gear-oil',
                rating: 4,
                image: null,
              },
              {
                id: 6,
                name: 'MRS Transmission Fluid ATF',
                price: 115000,
                sku: 'F34ASDF7',
                category: 'transmission',
                rating: 5,
                image: null,
              },
              {
                id: 7,
                name: 'MRS Marine Engine Oil',
                price: 175000,
                sku: 'G56ZXCV9',
                category: 'marine',
                rating: 4,
                image: null,
              },
              {
                id: 8,
                name: 'MRS Agricultural Oil',
                price: 145000,
                sku: 'H78BNML2',
                category: 'agricultural',
                rating: 5,
                image: null,
              },
            ],
          })
        }, 500)
      })

      products.value = response.data
      return response.data
    } catch (err) {
      error.value = err.message || 'Failed to fetch products'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    try {
      // Mock API call
      const response = await new Promise((resolve) => {
        setTimeout(() => {
          resolve({
            data: [
              { id: 1, name: 'Motor Oil', slug: 'motor-oil', count: 24 },
              { id: 2, name: 'Heavy Duty Oil', slug: 'heavy-duty', count: 18 },
              { id: 3, name: 'Industrial', slug: 'industrial', count: 12 },
              { id: 4, name: 'Agricultural', slug: 'agricultural', count: 8 },
              { id: 5, name: 'Marine', slug: 'marine', count: 6 },
              { id: 6, name: 'Gear Oil', slug: 'gear-oil', count: 15 },
              { id: 7, name: 'Transmission', slug: 'transmission', count: 10 },
            ],
          })
        }, 300)
      })

      categories.value = response.data
      return response.data
    } catch (err) {
      error.value = err.message || 'Failed to fetch categories'
      throw err
    }
  }

  function setFilter(key, value) {
    filters.value[key] = value
  }

  function clearFilters() {
    filters.value = {
      category: '',
      priceMin: null,
      priceMax: null,
      search: '',
    }
  }

  return {
    products,
    categories,
    loading,
    error,
    filters,
    filteredProducts,
    fetchProducts,
    fetchCategories,
    setFilter,
    clearFilters,
  }
})
