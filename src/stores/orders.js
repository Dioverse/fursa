import { defineStore } from 'pinia'
import api from '@/services/api'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [],
    order: null,
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
    byId: (state) => (id) => state.orders.find((o) => o.id === id),
  },

  actions: {
    async fetchOrders(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/admin-orders', { params })
        const payload = response?.data?.data || response?.data || {}
        this.orders = payload.data || []

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

    async fetchOrder(id) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/admin-orders/${id}`)
        this.order = response.data?.data || response.data || response.data?.order || response.data
        return response
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },

    async createOrder(payload) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/orders', payload)
        // optimistic prepend
        if (response?.data) this.orders.unshift(response.data)
        return response
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateOrder(id, payload) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put(`/admin-orders/update-status/${id}`, payload)
        const idx = this.orders.findIndex((o) => o.id === id)
        if (idx !== -1) this.orders[idx] = response.data
        return response
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteOrder(id) {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/orders/${id}`)
        this.orders = this.orders.filter((o) => o.id !== id)
      } catch (error) {
        this.error = error
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})

export default useOrdersStore
