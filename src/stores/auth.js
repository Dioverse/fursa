import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '@/services/auth.service'
import router from '@/router'
import { useToast } from 'vue-toastification'
import { useCartStore } from '@/stores/cart'

export const useAuthStore = defineStore('auth', () => {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  })

  const toast = useToast()
  const user = ref(null)
  const token = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const cartStore = useCartStore()

  const isAuthenticated = computed(() => !!token.value)
  const userFullName = computed(() => {
    if (!user.value) return ''
    return `${user.value.firstName} ${user.value.lastName}`
  })

  async function login(credentials) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.login(credentials)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      await cartStore.syncCart()

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function register(userData) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.register(userData)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function forgotPassword(userData) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.forgotPassword(userData)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Request failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function resetPassword(userData) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.resetPassword(userData)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Request failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('cart')
    router.push('/login')
  }

  function checkAuth() {
    const savedToken = localStorage.getItem('token')
    const savedUser = localStorage.getItem('user')

    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
    }
  }

  async function updateProfile(profileData) {
    loading.value = true
    try {
      // Mock API call
      const response = await new Promise((resolve) => {
        setTimeout(() => {
          resolve({
            data: {
              user: { ...user.value, ...profileData },
            },
          })
        }, 1000)
      })

      user.value = response.data.user
      localStorage.setItem('user', JSON.stringify(user.value))
      toast.success('Profile updated successfully')

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Update failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updatePassword(passwordData) {
    loading.value = true
    try {
      // Simulate API call (replace this mock with real API endpoint)
      const response = await api.post('/change-password', passwordData)

      toast.success('Password updated successfully')
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Password update failed'
      toast.error(error.value)
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    userFullName,
    login,
    register,
    logout,
    checkAuth,
    updateProfile,
    updatePassword,
    forgotPassword,
    resetPassword
  }
})
