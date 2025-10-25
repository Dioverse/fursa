import { ref, computed } from 'vue'
import { useProductsStore } from '@/stores/products'
import productsService from '@/services/products.service'

export function useProducts() {
  const productsStore = useProductsStore()
  const loading = ref(false)
  const error = ref(null)

  const products = computed(() => productsStore.products)
  const categories = computed(() => productsStore.categories)
  const filteredProducts = computed(() => productsStore.filteredProducts)

  async function loadProducts(params = {}) {
    loading.value = true
    error.value = null

    try {
      await productsStore.fetchProducts(params)
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  async function loadCategories() {
    try {
      await productsStore.fetchCategories()
    } catch (err) {
      error.value = err.message
      throw err
    }
  }

  async function loadProduct(id) {
    loading.value = true
    error.value = null

    try {
      const response = await productsService.getProduct(id)
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  function setFilter(key, value) {
    productsStore.setFilter(key, value)
  }

  function clearFilters() {
    productsStore.clearFilters()
  }

  function searchProducts(query) {
    productsStore.setFilter('search', query)
  }

  return {
    products,
    categories,
    filteredProducts,
    loading,
    error,
    loadProducts,
    loadCategories,
    loadProduct,
    setFilter,
    clearFilters,
    searchProducts,
  }
}
