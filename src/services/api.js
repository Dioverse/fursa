import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 30000, // Increased timeout for file uploads
})

// Request interceptor
apiClient.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    
    // Add authorization token
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    // Handle FormData requests
    if (config.data instanceof FormData) {
      // Remove Content-Type header to let browser set it with boundary
      delete config.headers['Content-Type']
      // Keep Accept header
      config.headers['Accept'] = 'application/json'
    } else {
      // For JSON requests, ensure Content-Type is set
      config.headers['Content-Type'] = 'application/json'
    }

    return config
  },
  (error) => {
    console.error('Request error:', error)
    return Promise.reject(error)
  },
)

// Response interceptor
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      // Server responded with error status
      if (error.response.status === 401) {
        // Unauthorized - logout user
        const authStore = useAuthStore()
        authStore.logout()
        router.push('/login')
      } else if (error.response.status === 403) {
        // Forbidden
        console.error('Access forbidden:', error.response.data)
      } else if (error.response.status === 404) {
        // Not found
        console.error('Resource not found:', error.response.data)
      } else if (error.response.status === 422) {
        // Validation error - pass through to caller
        console.warn('Validation error:', error.response.data)
      } else if (error.response.status >= 500) {
        // Server error
        console.error('Server error:', error.response.data)
      }
    } else if (error.request) {
      // Request made but no response
      console.error('Network error:', error.request)
      console.error('This might be a CORS issue or server is down')
    } else {
      // Something else happened
      console.error('Error:', error.message)
    }
    return Promise.reject(error)
  },
)

export default apiClient