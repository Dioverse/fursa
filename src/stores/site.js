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
    async updateSiteName(site_name) {
      this.loading = true
      this.error = null
      try {
        const payload = { site_name, name: site_name }
        const { data } = await api.put('/admin/site/name', payload)
        const body = data?.data || data || {}
        this.info.site_name = body.site_name ?? body.name ?? site_name ?? this.info.site_name
        return this.info.site_name
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },

    async updateSiteTax(tax) {
      this.loading = true
      this.error = null
      try {
        const payload = { tax }
        const { data } = await api.put('/admin/site/tax', payload)
        const body = data?.data || data || {}
        const value = body.tax ?? tax
        this.info.tax = typeof value === 'string' ? parseFloat(value) : value
        return this.info.tax
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },

    async updateSiteLogoUrl(site_logo) {
      this.loading = true
      this.error = null
      try {
        const payload = { site_logo, url: site_logo }
        const { data } = await api.put('/admin/site/logo', payload)
        const body = data?.data || data || {}
        const url = body.site_logo || body.url || body.path || body.location || body.file || site_logo
        if (url) this.info.site_logo = url
        return this.info.site_logo
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
