// services/posts.service.js
import api from './api'

const postsService = {
  /**
   * Fetch list of posts
   * @param {Object} params - optional query params { search, categories, sort, per_page, page }
   */
  async list(params = {}) {
    return api.get('/posts', { params })
  },

  async listCategories() {
    return api.get('/blog-categories')
  },

  /**
   * Fetch single post details by slug
   * @param {String} slug
   */
  async view(slug) {
    return api.get(`/posts/${slug}`)
  }
}

export default postsService
