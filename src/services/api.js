import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'
import { useToast } from 'vue-toastification'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 30000,
})

// Request Interceptor
apiClient.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
      config.headers.Accept = 'application/json'
    }

    return config
  },
  (error) => Promise.reject(error)
)

// Response Interceptor
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const toast = useToast()
    const authStore = useAuthStore()

    if (error.response) {
      const status = error.response.status
      const message =
        error.response.data?.message ||
        error.response.data?.error ||
        'Something went wrong.'

      // Handle authentication-related issues
      if (status === 401 || ['banned', 'unauthorized', 'Unauthenticated.'].some((m) =>message.toLowerCase().includes(m.toLowerCase()))) {
        authStore.logout()
        router.push('/login')
      } else if (message.toLowerCase().includes('unverified')) {
        toast.warning('Please verify your email before continuing.')
        router.push('/verify')
      } else if (status === 403) {
        toast.error('Access forbidden.')
      } else if (status === 404) {
        toast.error('Resource not found.')
      } else if (status === 422) {
        // Validation errors (do not redirect)
        console.warn('Validation error:', error.response.data)
      } else if (status >= 500) {
        toast.error('Server error. Please try again later.')
      }
    } else if (error.request) {
      toast.error('Network error. Please check your connection.')
    } else {
      toast.error('Unexpected error occurred.')
    }

    return Promise.reject(error)
  }
)

export default apiClient
