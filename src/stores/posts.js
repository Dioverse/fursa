import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import postsService from '@/services/posts.service'

export const usePostStore = defineStore('posts', () => {
  const loading = ref(false)
  const error = ref(null)

  async function fetchPosts(params = {}) {
    loading.value = true
    error.value = null
    try {
      const response = await postsService.list(params)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch posts'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchBlogCategories() {
    loading.value = true
    error.value = null
    try {
      const response = await postsService.listCategories()
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch categories'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchPostDetails(slug) {
    loading.value = true
    error.value = null
    try {
      const response = await postsService.view(slug)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch post details'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    fetchPosts,
    fetchBlogCategories,
    fetchPostDetails,
    loading,
    error,
  }
})
