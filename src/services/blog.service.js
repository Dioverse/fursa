// services/blog.service.js
import api from './api'

const blogService = {
  /**
   * Fetch list of posts
   * @param {Object} params - optional query params { search, categories, sort, per_page, page }
   */
  async list(params = {}) {
    return api.get('/posts', { params })
  },

  /**
   * Fetch single post details by ID
   * @param {Number|String} id
   */
  async view(id) {
    return api.get(`/posts/${id}`)
  }
}

export default blogService
