import api from './api'

const authService = {
  async login(credentials) {
    return api.post('/login', credentials)
  },

  async register(userData) {
    return api.post('/register', userData)
  },

  async logout() {
    return api.post('/logout')
  },

  async getCurrentUser() {
    return api.get('/auth/me')
  },

  async updateProfile(data) {
    return api.put('/auth/profile', data)
  },

  async changePassword(data) {
    return api.post('/change-password', data)
  },

  async forgotPassword(email) {
    return api.post('/forgot-password', { email })
  },

  async resetPassword(token, password) {
    return api.post('/reset-password', { token, password })
  },

  async verifyEmail(token) {
    return api.post('/verify-email', { token })
  },

  async resendVerification(email) {
    return api.post('/resend-verification', { email })
  },
}

export default authService
