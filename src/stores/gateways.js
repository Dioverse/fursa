import { defineStore } from 'pinia'
import api from '@/services/api'

export const useGatewayStore = defineStore('gateways', {
  state: () => ({
    gateways: {
      paystack: null,
      flutterwave: null,
    },
    loading: false,
    error: null,
  }),

  getters: {
    paystack: (s) => s.gateways.paystack || {},
    flutterwave: (s) => s.gateways.flutterwave || {},
  },

  actions: {
    async fetchGateways() {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.get('/admin/site/gateways')
        const body = data?.data || data || {}
        const gateways = body.gateways || body || {}
        this.gateways = {
          paystack: gateways.paystack || this.gateways.paystack,
          flutterwave: gateways.flutterwave || this.gateways.flutterwave,
        }
        return this.gateways
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },

    async updatePaystack(payload) {
      this.loading = true
      this.error = null
      try {
        const normalized = {
          status: (payload.status ?? '').toString().trim(),
          currency: (payload.currency ?? '').toString().trim(),
          public_key: (payload.public_key ?? '').toString().trim(),
          secret_key: (payload.secret_key ?? '').toString().trim(),
          image: (payload.image ?? '').toString().trim(),
        }
        const { data } = await api.post('/admin/site/gateway/paystack', normalized)
        const body = data?.data || data || {}
        // Server may return updated gateway or full gateways object
        const updated = body.gateway || body.paystack || body.gateways?.paystack || normalized
  this.gateways = { ...this.gateways, paystack: { ...this.gateways.paystack, ...updated } }
        return this.gateways.paystack
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },

    async updateFlutterwave(payload) {
      this.loading = true
      this.error = null
      try {
        const normalized = {
          status: (payload.status ?? '').toString().trim(),
          currency: (payload.currency ?? '').toString().trim(),
          public_key: (payload.public_key ?? '').toString().trim(),
          secret_key: (payload.secret_key ?? '').toString().trim(),
          encryption_key: (payload.encryption_key ?? '').toString().trim(),
          image: (payload.image ?? '').toString().trim(),
        }
        const { data } = await api.post('/admin/site/gateway/flutterwave', normalized)
        const body = data?.data || data || {}
        const updated =
          body.gateway || body.flutterwave || body.gateways?.flutterwave || normalized
  this.gateways = { ...this.gateways, flutterwave: { ...this.gateways.flutterwave, ...updated } }
        return this.gateways.flutterwave
      } catch (e) {
        this.error = e
        throw e
      } finally {
        this.loading = false
      }
    },
  },
})

export default useGatewayStore
