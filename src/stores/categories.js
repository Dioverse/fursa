// src/stores/categories.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

export const useCategoriesStore = defineStore('categories', () => {
  const categories = ref([])
  const toast = useToast()

  const fetchCategories = async () => {
    try {
      const res = await api.get('/categories')
      categories.value = res.data.data
      // Optionally transform into nested structure if API returns flat list
      // For now assume API returns nested categories with subcategories array
    } catch (err) {
      console.error('Failed to fetch categories', err)
      toast.error(err.response?.data?.message || 'Failed to load categories')
    }
  }

  const createCategory = async (payload) => {
    try {
      const res = await api.post('/categories', payload)
      categories.value.push(res.data)
      toast.success('Category created')
      return res.data
    } catch (err) {
      console.error('Failed to create category', err)
      toast.error(err.response?.data?.message || 'Failed to create category')
      throw err
    }
  }

  const updateCategory = async (id, payload) => {
    try {
      const res = await api.put(`/categories/${id}`, payload)
      // update local
      const idx = categories.value.findIndex((c) => c.id === id)
      if (idx !== -1) categories.value[idx] = res.data
      toast.success('Category updated')
      return res.data
    } catch (err) {
      console.error('Failed to update category', err)
      toast.error(err.response?.data?.message || 'Failed to update category')
      throw err
    }
  }

  const deleteCategory = async (id) => {
    try {
      await api.delete(`/categories/${id}`)
      categories.value = categories.value.filter((c) => c.id !== id)
      toast.success('Category deleted')
    } catch (err) {
      console.error('Failed to delete category', err)
      toast.error(err.response?.data?.message || 'Failed to delete category')
      throw err
    }
  }

  return {
    categories,
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory,
  }
})
