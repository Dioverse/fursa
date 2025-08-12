import api from './api'

const authService = {
  async login(credentials) {
    return api.post('/auth/login', credentials)
  },

  async register(userData) {
    return api.post('/auth/register', userData)
  },

  async logout() {
    return api.post('/auth/logout')
  },

  async getCurrentUser() {
    return api.get('/auth/me')
  },

  async updateProfile(data) {
    return api.put('/auth/profile', data)
  },

  async changePassword(data) {
    return api.post('/auth/change-password', data)
  },

  async forgotPassword(email) {
    return api.post('/auth/forgot-password', { email })
  },

  async resetPassword(token, password) {
    return api.post('/auth/reset-password', { token, password })
  },

  async verifyEmail(token) {
    return api.post('/auth/verify-email', { token })
  },

  async resendVerification(email) {
    return api.post('/auth/resend-verification', { email })
  },
}

export default authService
