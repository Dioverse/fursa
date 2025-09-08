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
  const isUpdatingImages = ref(false)
  const productStats = ref({
    total: 0,
    active: 0,
    out_of_stock: 0,
    low_stock: 0,
  })

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

  // Image Update Action
  const updateProductImages = async (productId, formData) => {
    try {
      isUpdatingImages.value = true
      const response = await api.put(`/api/products/${productId}/images`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      // Update the current product's images if it's being viewed
      if (currentProduct.value?.id === productId) {
        currentProduct.value = {
          ...currentProduct.value,
          images: response.data.images,
        }
      }

      // Update the product in the list
      const index = products.value.findIndex((p) => p.id === productId)
      if (index !== -1) {
        products.value[index] = {
          ...products.value[index],
          images: response.data.images,
        }
      }

      return response.data
    } catch (error) {
      console.error('Error updating product images:', error)
      throw error
    } finally {
      isUpdatingImages.value = false
    }
  }

  // Getters
  const totalProducts = computed(() => pagination.value.total)
  const hasProducts = computed(() => products.value.length > 0)

  const activeProducts = computed(() =>
    products.value.filter((product) => product.status === 'active'),
  )

  const outOfStockProducts = computed(() =>
    products.value.filter((product) => product.stock_quantity === 0),
  )

  const lowStockProducts = computed(() =>
    products.value.filter(
      (product) =>
        product.stock_quantity > 0 && product.stock_quantity <= product.low_stock_threshold,
    ),
  )

  const productsByCategory = computed(() => {
    const grouped = {}
    products.value.forEach((product) => {
      const categoryName = product.category?.name || 'Uncategorized'
      if (!grouped[categoryName]) {
        grouped[categoryName] = []
      }
      grouped[categoryName].push(product)
    })
    return grouped
  })

  const featuredProducts = computed(() => products.value.filter((product) => product.is_featured))

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

      // Remove empty filters more comprehensively
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
      const responseData = response.data.data
      const stats = response.data.stats

      console.log('Product statistics:', stats)

      if (responseData && Array.isArray(responseData.data)) {
        // Paginated response
        products.value = responseData.data
        productStats.value = {
          total: stats?.total || '0',
          active: stats?.active || '0',
          out_of_stock: stats?.out_of_stock || '0',
          low_stock: stats?.low_stock || '0',
        }
        pagination.value = {
          current_page: responseData.current_page || page,
          last_page: responseData.last_page || 1,
          per_page: responseData.per_page || 10,
          total: responseData.total || 0,
          from: responseData.from || 0,
          to: responseData.to || 0,
          next_page_url: responseData.next_page_url || null,
          prev_page_url: responseData.prev_page_url || null,
        }
      } else if (Array.isArray(responseData)) {
        // Direct array response
        products.value = responseData
        productStats.value = {
          total: stats?.total || '0',
          active: stats?.active || '0',
          out_of_stock: stats?.out_of_stock || '0',
          low_stock: stats?.low_stock || '0',
        }
        pagination.value = {
          current_page: page,
          last_page: 1,
          per_page: responseData.length,
          total: responseData.length,
          from: 1,
          to: responseData.length,
          next_page_url: null,
          prev_page_url: null,
        }
      } else {
        throw new Error('Unexpected response format')
      }

      // Transform products data if needed
      products.value = products.value.map((product) => ({
        ...product,
        // Parse tags if they're stored as JSON string
        tags:
          typeof product.tags === 'string' ? JSON.parse(product.tags || '[]') : product.tags || [],
        // Ensure numeric values are properly typed
        base_price: parseFloat(product.base_price || 0),
        distributor_price: parseFloat(product.distributor_price || 0),
        stock_quantity: parseInt(product.stock_quantity || 0),
        low_stock_threshold: parseInt(product.low_stock_threshold || 0),
        // Add computed properties
        is_low_stock:
          parseInt(product.stock_quantity || 0) <= parseInt(product.low_stock_threshold || 5),
        margin: parseFloat(product.base_price || 0) - parseFloat(product.distributor_price || 0),
        margin_percentage:
          parseFloat(product.base_price || 0) > 0
            ? ((parseFloat(product.base_price || 0) - parseFloat(product.distributor_price || 0)) /
                parseFloat(product.base_price || 0)) *
              100
            : 0,
      }))

      return {
        success: true,
        stats: productStats.value,
        data: products.value,
        pagination: pagination.value,
      }
    } catch (error) {
      console.error('Failed to fetch products:', error)

      // More specific error handling
      let errorMessage = 'Failed to load products'
      if (error.response?.status === 401) {
        errorMessage = 'Authentication required'
      } else if (error.response?.status === 403) {
        errorMessage = 'Access denied'
      } else if (error.response?.status === 404) {
        errorMessage = 'Products endpoint not found'
      } else if (error.response?.status >= 500) {
        errorMessage = 'Server error. Please try again later.'
      } else if (!navigator.onLine) {
        errorMessage = 'No internet connection'
      }

      toast.error(errorMessage)

      return {
        success: false,
        error: error.message,
        status: error.response?.status,
      }
    } finally {
      isLoading.value = false
    }
  }

  // Helper function for refetching current page
  const refetchProducts = () => {
    return fetchProducts(pagination.value.current_page)
  }

  // Helper function for fetching next page
  const fetchNextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
      return fetchProducts(pagination.value.current_page + 1)
    }
    return Promise.resolve({ success: false, error: 'No more pages' })
  }

  // Helper function for fetching previous page
  const fetchPreviousPage = () => {
    if (pagination.value.current_page > 1) {
      return fetchProducts(pagination.value.current_page - 1)
    }
    return Promise.resolve({ success: false, error: 'Already on first page' })
  }

  // Helper function for searching products
  const searchProducts = (searchTerm, additionalFilters = {}) => {
    const searchFilters = {
      search: searchTerm,
      ...additionalFilters,
    }
    return fetchProducts(1, searchFilters)
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

  const createProduct = async (formData) => {
    try {
      console.log('Store: Creating product with FormData:', formData)
      isCreating.value = true

      // Debug: Log all FormData entries in store
      console.log('=== Store FormData Contents ===')
      for (let [key, value] of formData.entries()) {
        console.log(`${key}:`, value instanceof File ? `File: ${value.name}` : value)
      }

      const response = await api.post('/admin-products', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      const newProduct = response.data.product || response.data
      products.value.unshift(newProduct)
      pagination.value.total += 1

      toast.success('Product created successfully')
      return { success: true, data: newProduct }
    } catch (error) {
      console.error('Failed to create product:', error)
      console.error('Error details:', {
        status: error.response?.status,
        statusText: error.response?.statusText,
        data: error.response?.data,
      })

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

      // Add _method to the FormData for Laravel method spoofing
      if (productData instanceof FormData) {
        productData.append('_method', 'PATCH')
      } else {
        // If it's a regular object, convert to FormData
        const formData = new FormData()

        // Add the method override
        formData.append('_method', 'PATCH')

        // Add all other data
        Object.entries(productData).forEach(([key, value]) => {
          if (value !== null && value !== undefined) {
            if (Array.isArray(value)) {
              // Handle arrays (like tags)
              if (key === 'tags') {
                formData.append(key, JSON.stringify(value))
              } else {
                // Handle file arrays
                value.forEach((item, index) => {
                  if (item instanceof File) {
                    formData.append(`${key}[${index}]`, item)
                  } else {
                    formData.append(`${key}[${index}]`, item)
                  }
                })
              }
            } else if (value instanceof File) {
              formData.append(key, value)
            } else {
              formData.append(key, typeof value === 'object' ? JSON.stringify(value) : value)
            }
          }
        })

        productData = formData
      }

      // Use POST instead of PATCH
      const response = await api.post(`/admin-products/${id}`, productData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      const updatedProduct = response.data.product || response.data
      const index = products.value.findIndex((product) => product.id === id)
      if (index !== -1) {
        products.value[index] = updatedProduct
      }

      if (currentProduct.value?.id === id) {
        currentProduct.value = updatedProduct
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

      products.value = products.value.filter((product) => product.id !== id)
      pagination.value.total -= 1

      if (currentProduct.value?.id === id) {
        currentProduct.value = null
      }

      toast.success('Product deleted successfully')
      return { success: true }
    } catch (error) {
      console.error('Failed to delete product:', error)
      const errorMessage = error.response?.data?.message || 'Failed to delete product'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    } finally {
      isDeleting.value = false
    }
  }

  const bulkAction = async (action, productIds, additionalData = {}) => {
    try {
      isUpdating.value = true

      const payload = {
        action,
        product_ids: productIds,
        ...additionalData,
      }

      const response = await api.post('/admin-products-bulk-action', payload)

      // Refresh products list
      await fetchProducts(pagination.value.current_page)

      const message = response.data.message || `${action} completed successfully`
      toast.success(message)

      return { success: true, data: response.data }
    } catch (error) {
      console.error('Bulk action failed:', error)
      const errorMessage = error.response?.data?.message || 'Bulk action failed'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    } finally {
      isUpdating.value = false
    }
  }

  const updateProductStatus = async (id, status) => {
    try {
      const response = await api.post(`/admin-products-toggle/${id}`, { status })

      const updatedProduct = response.data.product || response.data
      const index = products.value.findIndex((product) => product.id === id)
      if (index !== -1) {
        products.value[index] = updatedProduct
      }

      toast.success(response.data.message)
      return { success: true, data: updatedProduct }
    } catch (error) {
      console.error('Failed to update product status:', error)
      const errorMessage = error.response?.data?.message || 'Failed to update product status'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    }
  }

  const duplicateProduct = async (id) => {
    try {
      isCreating.value = true
      const response = await api.post(`/admin/products/${id}/duplicate`)

      const duplicatedProduct = response.data.product || response.data
      products.value.unshift(duplicatedProduct)
      pagination.value.total += 1

      toast.success('Product duplicated successfully')
      return { success: true, data: duplicatedProduct }
    } catch (error) {
      console.error('Failed to duplicate product:', error)
      const errorMessage = error.response?.data?.message || 'Failed to duplicate product'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    } finally {
      isCreating.value = false
    }
  }

  const updateStock = async (id, stockData) => {
    try {
      const response = await api.post(`/admin-products-stock/${id}`, stockData)

      const updatedProduct = response.data.product || response.data
      const index = products.value.findIndex((product) => product.id === id)
      if (index !== -1) {
        products.value[index] = updatedProduct
      }

      toast.success('Stock updated successfully')
      return { success: true, data: updatedProduct }
    } catch (error) {
      console.error('Failed to update stock:', error)
      const errorMessage = error.response?.data?.message || 'Failed to update stock'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    }
  }

  const fetchCategories = async () => {
    try {
      const response = await api.get('/categories?sub=true')
      categories.value = response.data.data || response.data.categories || []
      return { success: true, data: categories.value }
    } catch (error) {
      console.error('Failed to fetch categories:', error)
      return { success: false, error: error.message }
    }
  }

  const fetchBrands = async () => {
    try {
      const response = await api.get('/admin/brands')
      brands.value = response.data.data || response.data.brands || []
      return { success: true, data: brands.value }
    } catch (error) {
      console.error('Failed to fetch brands:', error)
      return { success: false, error: error.message }
    }
  }

  const fetchStatistics = async () => {
    try {
      const response = await api.get('/admin/products/statistics')
      statistics.value = response.data.statistics || response.data
      return { success: true, data: statistics.value }
    } catch (error) {
      console.error('Failed to fetch statistics:', error)
      return { success: false, error: error.message }
    }
  }

  const exportProducts = async (format = 'csv', customFilters = {}) => {
    try {
      const params = {
        format,
        ...filters.value,
        ...customFilters,
      }

      Object.keys(params).forEach((key) => {
        if (params[key] === '' || params[key] === null || params[key] === undefined) {
          delete params[key]
        }
      })

      const response = await api.get('/admin/products/export', {
        params,
        responseType: 'blob',
      })

      const blob = new Blob([response.data])
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `products-export-${Date.now()}.${format}`
      document.body.appendChild(link)
      link.click()
      if (link.parentNode) {
        link.parentNode.removeChild(link)
      } else if (typeof link.remove === 'function') {
        link.remove()
      }
      window.URL.revokeObjectURL(url)

      toast.success(`Products exported as ${format.toUpperCase()}`)
      return { success: true }
    } catch (error) {
      console.error('Failed to export products:', error)
      toast.error('Failed to export products')
      return { success: false, error: error.message }
    }
  }

  const importProducts = async (file, options = {}) => {
    try {
      isCreating.value = true

      const formData = new FormData()
      formData.append('file', file)

      Object.keys(options).forEach((key) => {
        formData.append(key, options[key])
      })

      const response = await api.post('/admin/products/import', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      const result = response.data
      toast.success(`Successfully imported ${result.imported_count} products`)

      // Refresh products list
      await fetchProducts()

      return { success: true, data: result }
    } catch (error) {
      console.error('Failed to import products:', error)
      const errorMessage = error.response?.data?.message || 'Failed to import products'
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

  // Utility functions
  const updateFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const resetFilters = () => {
    filters.value = {
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
    }
  }

  const clearCurrentProduct = () => {
    currentProduct.value = null
  }

  const generateSKU = (productName, categoryId = null) => {
    const prefix = categoryId ? `CAT${categoryId}` : 'PROD'
    const timestamp = Date.now().toString().slice(-6)
    const namePrefix = productName
      .split(' ')
      .map((word) => word.charAt(0))
      .join('')
      .toUpperCase()
      .slice(0, 3)
    return `${prefix}-${namePrefix}-${timestamp}`
  }

  return {
    // State
    products,
    currentProduct,
    categories,
    isUpdatingImages,
    brands,
    isLoading,
    isCreating,
    isUpdating,
    isDeleting,
    pagination,
    filters,
    statistics,
    productStats,

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
    bulkAction,
    updateProductStatus,
    duplicateProduct,
    updateStock,
    updateProductImages,
    fetchCategories,
    fetchBrands,
    fetchStatistics,
    exportProducts,
    importProducts,

    // Utilities
    updateFilters,
    resetFilters,
    clearCurrentProduct,
    generateSKU,
  }
})
