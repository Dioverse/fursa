import api from './api'

const ordersService = {
  async getOrders(params = {}) {
    return api.get('/orders', { params })
  },

  async getOrder(id) {
    return api.get(`/orders/${id}`)
  },

  async createOrder(orderData) {
    return api.post('/orders', orderData)
  },

  async updateOrder(id, updates) {
    return api.patch(`/orders/${id}`, updates)
  },

  async cancelOrder(id, reason) {
    return api.post(`/orders/${id}/cancel`, { reason })
  },

  async trackOrder(id) {
    return api.get(`/orders/${id}/tracking`)
  },

  async getOrderInvoice(id) {
    return api.get(`/orders/${id}/invoice`, {
      responseType: 'blob',
    })
  },

  async reorder(orderId) {
    return api.post(`/orders/${orderId}/reorder`)
  },

  async getOrderStatistics() {
    return api.get('/orders/statistics')
  },
}

export default ordersService
