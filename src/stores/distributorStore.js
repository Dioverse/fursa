// stores/distributorStore.js
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useDistributorStore = defineStore('distributor', {
  state: () => ({
    distributors: [],
    territories: [],
    stats: {
      totalDistributors: 0,
      pendingApplications: 0,
      activeDistributors: 0,
      totalRevenue: 0,
      monthlyGrowth: 0,
      topPerformers: [],
    },
    pagination: {
      // camelCase (new) and snake_case (legacy) keys for compatibility
      currentPage: 1,
      current_page: 1,
      totalPages: 1,
      last_page: 1,
      totalItems: 0,
      total: 0,
      perPage: 10,
      per_page: 10,
      links: [],
      firstPageUrl: null,
      lastPageUrl: null,
      nextPageUrl: null,
      prevPageUrl: null,
    },
    loading: false,
    error: null,
  }),

  getters: {
    // Get distributors by status
    distributorsByStatus: (state) => (status) => {
      return state.distributors.filter((distributor) => distributor.status === status)
    },

    // Get distributors by territory
    distributorsByTerritory: (state) => (territoryId) => {
      return state.distributors.filter((distributor) => distributor.territory_id === territoryId)
    },

    // Get top performing distributors
    topPerformers: (state) => {
      return state.distributors
        .filter((distributor) => distributor.status === 'active')
        .sort((a, b) => (b.performance_score || 0) - (a.performance_score || 0))
        .slice(0, 10)
    },

    // Get distributors with pending applications
    pendingApplications: (state) => {
      return state.distributors.filter((distributor) => distributor.status === 'pending')
    },

    // Get distributor by ID
    getDistributorById: (state) => (id) => {
      return state.distributors.find((distributor) => distributor.id === id)
    },

    // Get territory by ID
    getTerritoryById: (state) => (id) => {
      return state.territories.find((territory) => territory.id === id)
    },
  },

  actions: {
    // Fetch all distributors with filters
    async fetchDistributors(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/distributors', { params })

        // API response shape:
        // { message: '...', data: { current_page, data: [...], last_page, total, per_page, ... } }
        const payload = response?.data?.data || response?.data || {}

        this.distributors = payload.data || []

        // map pagination fields from API to our store shape
        this.pagination = {
          // camelCase
          currentPage: payload.current_page || payload.page || 1,
          totalPages: payload.last_page || payload.total_pages || 1,
          totalItems: payload.total ?? payload.totalItems ?? 0,
          perPage: payload.per_page || payload.perPage || 10,
          firstPageUrl: payload.first_page_url,
          lastPageUrl: payload.last_page_url,
          nextPageUrl: payload.next_page_url,
          prevPageUrl: payload.prev_page_url,
          links: payload.links || [],

          // snake_case (legacy)
          current_page: payload.current_page || payload.page || 1,
          last_page: payload.last_page || payload.total_pages || 1,
          total: payload.total ?? payload.totalItems ?? 0,
          per_page: payload.per_page || payload.perPage || 10,
        }

        // keep stats roughly in sync
        this.stats.totalDistributors = this.pagination.totalItems || this.stats.totalDistributors

        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Fetch distributor by ID
    async fetchDistributor(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/distributors/${id}`)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Create new distributor
    async createDistributor(distributorData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/distributors', distributorData)
        this.distributors.unshift(response.data)
        this.stats.totalDistributors++
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Update distributor
    async updateDistributor(id, distributorData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put(`/distributors/${id}`, distributorData)
        const index = this.distributors.findIndex((d) => d.id === id)
        if (index !== -1) {
          this.distributors[index] = response.data
        }
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Delete distributor
    async deleteDistributor(id) {
      this.loading = true
      this.error = null

      try {
        await api.delete(`/distributors/${id}`)
        this.distributors = this.distributors.filter((d) => d.id !== id)
        this.stats.totalDistributors--
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Approve distributor
    async approveDistributor(id) {
      this.loading = true
      this.error = null

      try {
        const body = { status: "approved" }
        const response = await api.patch(`/distributors/${id}/status`, body)
        const index = this.distributors.findIndex((d) => d.id === id)
        if (index !== -1) {
          this.distributors[index].status = 'approved'
          this.distributors[index].approved_at = new Date().toISOString()
        }
        this.stats.pendingApplications--
        this.stats.activeDistributors++
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Reject distributor
    async rejectDistributor(id) {
      this.loading = true
      this.error = null

      try {
        const body = { status: "rejected" }
        const response = await api.patch(`/distributors/${id}/status`, body)
        const index = this.distributors.findIndex((d) => d.id === id)
        if (index !== -1) {
          this.distributors[index].status = 'rejected'
          this.distributors[index].rejected_at = new Date().toISOString()
        }
        this.stats.pendingApplications--
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Suspend distributor
    async suspendDistributor(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.suspendDistributor(id)
        const index = this.distributors.findIndex((d) => d.id === id)
        if (index !== -1) {
          this.distributors[index].status = 'suspended'
          this.distributors[index].suspended_at = new Date().toISOString()
        }
        this.stats.activeDistributors--
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Activate distributor
    async activateDistributor(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.activateDistributor(id)
        const index = this.distributors.findIndex((d) => d.id === id)
        if (index !== -1) {
          this.distributors[index].status = 'active'
          this.distributors[index].activated_at = new Date().toISOString()
        }
        this.stats.activeDistributors++
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Bulk actions
    async bulkApprove(distributorIds) {
      this.loading = true
      this.error = null

      try {
        const response = await api.bulkApprove(distributorIds)
        distributorIds.forEach((id) => {
          const index = this.distributors.findIndex((d) => d.id === id)
          if (index !== -1) {
            this.distributors[index].status = 'approved'
            this.distributors[index].approved_at = new Date().toISOString()
          }
        })
        this.stats.pendingApplications -= distributorIds.length
        this.stats.activeDistributors += distributorIds.length
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async bulkReject(distributorIds) {
      this.loading = true
      this.error = null

      try {
        const response = await api.bulkReject(distributorIds)
        distributorIds.forEach((id) => {
          const index = this.distributors.findIndex((d) => d.id === id)
          if (index !== -1) {
            this.distributors[index].status = 'rejected'
            this.distributors[index].rejected_at = new Date().toISOString()
          }
        })
        this.stats.pendingApplications -= distributorIds.length
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async bulkSuspend(distributorIds) {
      this.loading = true
      this.error = null

      try {
        const response = await api.bulkSuspend(distributorIds)
        distributorIds.forEach((id) => {
          const index = this.distributors.findIndex((d) => d.id === id)
          if (index !== -1) {
            this.distributors[index].status = 'suspended'
            this.distributors[index].suspended_at = new Date().toISOString()
          }
        })
        this.stats.activeDistributors -= distributorIds.length
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Send distributor invitation
    async sendInvitation(invitationData) {
      this.loading = true
      this.error = null

      try {
        let response

        // If caller passed a FormData (full create with files), post to create distributor endpoint
        if (invitationData instanceof FormData) {
          response = await api.post('/register', invitationData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          })
        } else {
          // Fallback: simple invite endpoint
          response = await api.post('/distributors/invite', invitationData)
        }

        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Fetch territories
    async fetchTerritories() {
      try {
        const response = await api.getTerritories()
        this.territories = response.data
        return response
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    // Fetch distributor stats
    async fetchStats() {
      try {
        const response = await api.getStats()
        this.stats = response.data
        return response
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    // Export distributors
    async exportDistributors(filters = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await api.exportDistributors(filters)

        // Create download link
        const blob = new Blob([response.data], {
          type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `distributors-${new Date().toISOString().split('T')[0]}.xlsx`
        document.body.appendChild(link)
        link.click()
        // Safe remove: check parentNode first
        if (link.parentNode) {
          link.parentNode.removeChild(link)
        } else if (typeof link.remove === 'function') {
          link.remove()
        }
        window.URL.revokeObjectURL(url)

        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Get distributor performance data
    async getPerformanceData(id, period = 'month') {
      try {
        const response = await api.getPerformanceData(id, period)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      }
    },

    // Update distributor territory
    async updateTerritory(distributorId, territoryId) {
      this.loading = true
      this.error = null

      try {
        const response = await api.updateTerritory(distributorId, territoryId)
        const index = this.distributors.findIndex((d) => d.id === distributorId)
        if (index !== -1) {
          this.distributors[index].territory_id = territoryId
        }
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    // Clear store data
    clearDistributors() {
      this.distributors = []
      this.pagination = {
        currentPage: 1,
        current_page: 1,
        totalPages: 1,
        last_page: 1,
        totalItems: 0,
        total: 0,
        perPage: 10,
        per_page: 10,
        links: [],
        firstPageUrl: null,
        lastPageUrl: null,
        nextPageUrl: null,
        prevPageUrl: null,
      }
    },

    // Clear errors
    clearError() {
      this.error = null
    },
  },
})
