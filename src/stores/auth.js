import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '@/services/auth.service'
import router from '@/router'
import { useToast } from 'vue-toastification'
import { useCartStore } from '@/stores/cart'



export const useAuthStore = defineStore('auth', () => {

  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
  });

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

      const response = await authService.login(credentials);
      // Mock API call - replace with actual API
      // const response = await new Promise((resolve) => {
      //   setTimeout(() => {
      //     resolve({
      //       data: {
      //         token: 'fake-jwt-token-' + Date.now(),
      //         user: {
      //           id: 1,
      //           firstName: 'John',
      //           lastName: 'Doe',
      //           email: credentials.email,
      //           phone: '+234-XXX-XXX-XXXX',
      //         },
      //       },
      //     })
      //   }, 1000)
      // })

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
      const response = await authService.register(userData);
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
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
  }
})
