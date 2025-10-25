import api from './api'

const productsService = {
  async getProducts(params = {}) {
    return api.get('/products', { params })
  },

  async getProduct(id) {
    return api.get(`/products/${id}`)
  },

  async getCategories() {
    return api.get('/categories')
  },

  async getDynamicCategories() {
    return api.get('/cats?limit=7')
  },

  async getCategory(slug) {
    return api.get(`/categories/${slug}`)
  },

  async searchProducts(query) {
    return api.get('/products/search', { params: { q: query } })
  },

  async getFeaturedProducts() {
    return api.get('/products/featured')
  },

  async getRelatedProducts(productId) {
    return api.get(`/products/${productId}/related`)
  },

  async getProductReviews(productId) {
    return api.get(`/products/${productId}/reviews`)
  },

  async addProductReview(productId, review) {
    return api.post(`/products/${productId}/reviews`, review)
  },

  async getProductsByCategory(categorySlug, params = {}) {
    return api.get(`/categories/${categorySlug}/products`, { params })
  },
}

export default productsService
