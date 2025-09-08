// src/stores/products.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

export const useProductsStore = defineStore('products', () => {
  const toast = useToast()

  // State
  const products = ref([])
  const currentProduct = ref(null)
  const categories = ref([])
  const brands = ref([])
  const isLoading = ref(false)
  const isCreating = ref(false)
  const isUpdating = ref(false)
  const isDeleting = ref(false)

  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0,
  })

  const filters = ref({
    search: '',
    category_id: '',
    brand_id: '',
    status: '',
    stock_status: '',
    price_from: '',
    price_to: '',
    date_from: '',
    date_to: '',
    featured: '',
    sort_by: 'created_at',
    sort_order: 'desc',
  })

  const statistics = ref({
    total_products: 0,
    active_products: 0,
    out_of_stock: 0,
    low_stock: 0,
    total_value: 0,
    categories_count: 0,
  })

  // Getters
  const totalProducts = computed(() => pagination.value.total)
  const hasProducts = computed(() => products.value.length > 0)
  const activeProducts = computed(() => products.value.filter((p) => p.status === 'active'))
  const outOfStockProducts = computed(() => products.value.filter((p) => p.stock_quantity === 0))
  const lowStockProducts = computed(() =>
    products.value.filter((p) => p.stock_quantity > 0 && p.stock_quantity <= p.low_stock_threshold),
  )
  const featuredProducts = computed(() => products.value.filter((p) => p.is_featured))
  const productsByCategory = computed(() => {
    const grouped = {}
    products.value.forEach((p) => {
      const categoryName = p.category?.name || 'Uncategorized'
      if (!grouped[categoryName]) {
        grouped[categoryName] = []
      }
      grouped[categoryName].push(p)
    })
    return grouped
  })

  // Helper Functions
  const processFormData = (productData, method = 'POST') => {
    const formData = new FormData()
    if (method === 'PUT') {
      formData.append('_method', 'PUT')
    }

    Object.entries(productData).forEach(([key, value]) => {
      if (value === null || value === undefined || value === '') return

      if (['images', 'gallery'].includes(key)) {
        if (Array.isArray(value)) {
          value.forEach((file, index) => {
            if (file instanceof File) {
              formData.append(`${key}[${index}]`, file)
            } else if (typeof file === 'string') {
              formData.append(`existing_${key}[${index}]`, file)
            }
          })
        }
      } else if (
        ['seo_data', 'specifications', 'variants'].includes(key) ||
        Array.isArray(value) ||
        (typeof value === 'object' && value !== null)
      ) {
        formData.append(key, JSON.stringify(value))
      } else {
        formData.append(key, value.toString())
      }
    })

    return formData
  }

  // Actions
  const fetchProducts = async (page = 1, customFilters = {}) => {
    try {
      isLoading.value = true
      const params = {
        page,
        per_page: pagination.value.per_page,
        ...filters.value,
        ...customFilters,
      }

      Object.keys(params).forEach((key) => {
        const value = params[key]
        if (
          value === '' ||
          value === null ||
          value === undefined ||
          (Array.isArray(value) && value.length === 0) ||
          (typeof value === 'object' && Object.keys(value).length === 0)
        ) {
          delete params[key]
        }
      })

      const response = await api.get('/admin-products', { params })
      const responseData = response.data.data || response.data

      if (responseData.data && Array.isArray(responseData.data)) {
        products.value = responseData.data
        pagination.value = {
          current_page: responseData.current_page || page,
          last_page: responseData.last_page || 1,
          per_page: responseData.per_page || 10,
          total: responseData.total || 0,
          from: responseData.from || 0,
          to: responseData.to || 0,
        }
      } else if (Array.isArray(responseData)) {
        products.value = responseData
        pagination.value = {
          current_page: page,
          last_page: 1,
          per_page: responseData.length,
          total: responseData.length,
          from: 1,
          to: responseData.length,
        }
      } else {
        throw new Error('Unexpected response format')
      }

      return { success: true, data: products.value }
    } catch (error) {
      console.error('Failed to fetch products:', error)
      toast.error(error.response?.data?.message || 'Failed to load products')
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const fetchProduct = async (id) => {
    try {
      isLoading.value = true
      const response = await api.get(`/admin-products/${id}`)
      currentProduct.value = response.data.product || response.data
      return { success: true, data: currentProduct.value }
    } catch (error) {
      console.error('Failed to fetch product:', error)
      toast.error('Failed to load product details')
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const createProduct = async (productData) => {
    try {
      isCreating.value = true
      const formData = processFormData(productData)

      const response = await api.post('/admin-products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      const newProduct = response.data.product || response.data
      products.value.unshift(newProduct)
      pagination.value.total++

      toast.success('Product created successfully')
      return { success: true, data: newProduct }
    } catch (error) {
      console.error('Failed to create product:', error)
      const errorMessage = error.response?.data?.message || 'Failed to create product'
      toast.error(errorMessage)
      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isCreating.value = false
    }
  }

  const updateProduct = async (id, productData) => {
    try {
      isUpdating.value = true
      const formData = processFormData(productData, 'PUT')

      const response = await api.post(`/admin-products/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      const updatedProduct = response.data.product || response.data
      const index = products.value.findIndex((p) => p.id === id)
      if (index !== -1) {
        products.value[index] = updatedProduct
      }

      toast.success('Product updated successfully')
      return { success: true, data: updatedProduct }
    } catch (error) {
      console.error('Failed to update product:', error)
      const errorMessage = error.response?.data?.message || 'Failed to update product'
      toast.error(errorMessage)
      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isUpdating.value = false
    }
  }

  const deleteProduct = async (id) => {
    try {
      isDeleting.value = true
      await api.delete(`/admin-products/${id}`)
      products.value = products.value.filter((p) => p.id !== id)
      pagination.value.total--

      toast.success('Product deleted successfully')
      return { success: true }
    } catch (error) {
      console.error('Failed to delete product:', error)
      toast.error(error.response?.data?.message || 'Failed to delete product')
      return { success: false, error: error.message }
    } finally {
      isDeleting.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      const response = await api.get('/admin-categories')
      categories.value = response.data.data || response.data
      return { success: true, data: categories.value }
    } catch (error) {
      console.error('Failed to fetch categories:', error)
      return { success: false, error: error.message }
    }
  }

  const fetchBrands = async () => {
    try {
      const response = await api.get('/admin-brands')
      brands.value = response.data.data || response.data
      return { success: true, data: brands.value }
    } catch (error) {
      console.error('Failed to fetch brands:', error)
      return { success: false, error: error.message }
    }
  }

  const searchProducts = (searchTerm, additionalFilters = {}) =>
    fetchProducts(1, { search: searchTerm, ...additionalFilters })

  const refetchProducts = () => fetchProducts(pagination.value.current_page)

  const fetchNextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
      return fetchProducts(pagination.value.current_page + 1)
    }
    return Promise.resolve({ success: false, error: 'No more pages' })
  }

  const fetchPreviousPage = () => {
    if (pagination.value.current_page > 1) {
      return fetchProducts(pagination.value.current_page - 1)
    }
    return Promise.resolve({ success: false, error: 'Already on first page' })
  }

  return {
    // State
    products,
    currentProduct,
    categories,
    brands,
    isLoading,
    isCreating,
    isUpdating,
    isDeleting,
    pagination,
    filters,
    statistics,

    // Getters
    totalProducts,
    hasProducts,
    activeProducts,
    outOfStockProducts,
    lowStockProducts,
    productsByCategory,
    featuredProducts,

    // Actions
    fetchProducts,
    fetchProduct,
    createProduct,
    updateProduct,
    deleteProduct,
    fetchCategories,
    fetchBrands,
    refetchProducts,
    fetchNextPage,
    fetchPreviousPage,
    searchProducts,
  }
})
