import { defineStore } from 'pinia'
import api from '@/services/api'

export const useSiteStore = defineStore('site', {
  state: () => ({
    info: {
      site_name: 'Fursa Energy',
      site_logo: '',
      tax: null,
    },
    loading: false,
    error: null,
  }),

  getters: {
    name: (s) => s.info?.site_name || 'Fursa Energy',
    logo: (s) => s.info?.site_logo || '',
    taxNumber: (s) => {
      const t = s.info?.tax
      const n = typeof t === 'string' ? parseFloat(t) : t
      return isNaN(n) ? null : n
    },
  },

  actions: {
    async fetchSiteInfo() {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.get('/admin/site/info')
        // Endpoint returns raw keys (no data envelope)
        const body = data?.data || data || {}
        this.info = {
          site_name: body.site_name || this.info.site_name,
          site_logo: body.site_logo || this.info.site_logo,
          tax: body.tax ?? this.info.tax,
        }
        return this.info
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },
    async updateSiteInfo(payload) {
      this.loading = true
      this.error = null
      try {
        // Try POST to same endpoint by convention; adjust if backend differs
        const { data } = await api.post('/admin/site/info', payload)
        const body = data?.data || data || {}
        this.info = {
          site_name: body.site_name ?? payload.site_name ?? this.info.site_name,
          site_logo: body.site_logo ?? payload.site_logo ?? this.info.site_logo,
          tax: body.tax ?? payload.tax ?? this.info.tax,
        }
        return this.info
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },
  },
})

export default useSiteStore
