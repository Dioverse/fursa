import { defineStore } from 'pinia'
import api from '@/services/api'

export const useInventoryLogsStore = defineStore('inventoryLogs', {
  state: () => ({
    logs: [],
    log: null,
    pagination: {
      currentPage: 1,
      current_page: 1,
      totalPages: 1,
      last_page: 1,
      totalItems: 0,
      total: 0,
      perPage: 10,
      per_page: 10,
      links: [],
    },
    loading: false,
    error: null,
  }),

  getters: {
    byId: (state) => (id) => state.logs.find((l) => l.id === id),
  },

  actions: {
    async fetchLogs(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/admin-inventory-logs', { params })
        const payload = response?.data?.data || response?.data || {}
        this.logs = payload.data || []

        this.pagination = {
          currentPage: payload.current_page || payload.page || 1,
          totalPages: payload.last_page || payload.total_pages || 1,
          totalItems: payload.total ?? payload.totalItems ?? 0,
          perPage: payload.per_page || payload.perPage || 10,
          firstPageUrl: payload.first_page_url,
          lastPageUrl: payload.last_page_url,
          nextPageUrl: payload.next_page_url,
          prevPageUrl: payload.prev_page_url,
          links: payload.links || [],
          current_page: payload.current_page || payload.page || 1,
          last_page: payload.last_page || payload.total_pages || 1,
          total: payload.total ?? payload.totalItems ?? 0,
          per_page: payload.per_page || payload.perPage || 10,
        }

        return response
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchLog(id) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/admin-inventory-logs/${id}`)
        this.log = response.data?.data || response.data || response.data?.log || response.data
        return response
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },

    async createLog(payload) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/admin-inventory-logs', payload)
        if (response?.data) this.logs.unshift(response.data)
        return response
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteLog(id) {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin-inventory-logs/${id}`)
        this.logs = this.logs.filter((l) => l.id !== id)
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})

export default useInventoryLogsStore
