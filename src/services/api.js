// src/services/api.js
import axios from 'axios'
import { useToast } from 'vue-toastification'

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api',
  timeout: 30000,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: false, // Set to false for API-only authentication
})

// Track active requests for loading states
let activeRequests = 0
const loadingCallbacks = new Set()

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('admin_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    // Remove CSRF related headers for API requests
    delete config.headers['X-CSRF-TOKEN']
    delete config.headers['X-XSRF-TOKEN']

    // Add timestamp to prevent caching for GET requests
    if (config.method === 'get') {
      config.params = {
        ...config.params,
        _t: Date.now(),
      }
    }

    // Track request count
    activeRequests++
    notifyLoadingChange(true)

    // Log request in development
    if (import.meta.env.DEV) {
      console.log(`🚀 API Request: ${config.method?.toUpperCase()} ${config.url}`, {
        params: config.params,
        data: config.data,
        headers: config.headers,
      })
    }

    return config
  },
  (error) => {
    activeRequests--
    notifyLoadingChange(false)
    return Promise.reject(error)
  },
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    activeRequests--
    notifyLoadingChange(false)

    // Log response in development
    if (import.meta.env.DEV) {
      console.log(
        `✅ API Response: ${response.config.method?.toUpperCase()} ${response.config.url}`,
        {
          status: response.status,
          data: response.data,
        },
      )
    }

    return response
  },
  async (error) => {
    activeRequests--
    notifyLoadingChange(false)

    const toast = useToast()
    const originalRequest = error.config

    // Log error in development
    if (import.meta.env.DEV) {
      console.error(`❌ API Error: ${error.config?.method?.toUpperCase()} ${error.config?.url}`, {
        status: error.response?.status,
        message: error.response?.data?.message,
        data: error.response?.data,
        headers: error.response?.headers,
      })
    }

    // Handle different types of errors
    if (error.response) {
      const { status, data } = error.response

      switch (status) {
        case 401:
          // Unauthorized - token expired or invalid
          if (!originalRequest._retry && originalRequest.url !== '/auth/admin/refresh') {
            originalRequest._retry = true

            try {
              // Try to refresh token
              const refreshResponse = await api.post('/auth/admin/refresh')
              const newToken = refreshResponse.data.token

              // Update stored token
              localStorage.setItem('admin_token', newToken)

              // Retry original request with new token
              originalRequest.headers.Authorization = `Bearer ${newToken}`
              return api(originalRequest)
            } catch (refreshError) {
              // Refresh failed, redirect to login
              handleAuthFailure()
              return Promise.reject(refreshError)
            }
          } else {
            handleAuthFailure()
            toast.error('Session expired. Please login again.')
          }
          break

        case 403:
          toast.error(data.message || 'Access denied')
          break

        case 404:
          if (!originalRequest.suppressNotFoundError) {
            toast.error(data.message || 'Resource not found')
          }
          break

        case 419:
          // CSRF token mismatch - specific handling
          console.error('CSRF token mismatch. This should not happen with API routes.')
          toast.error('Authentication error. Please refresh the page and try again.')
          break

        case 422:
          // Validation errors - let components handle these
          if (data.message && !originalRequest.suppressValidationError) {
            toast.error(data.message)
          }
          break

        case 429:
          toast.error('Too many requests. Please slow down.')
          break

        case 500:
          toast.error('Server error. Please try again later.')
          break

        case 503:
          toast.error('Service temporarily unavailable')
          break

        default:
          if (status >= 400 && status < 500) {
            toast.error(data.message || 'Client error occurred')
          } else if (status >= 500) {
            toast.error(data.message || 'Server error occurred')
          }
      }
    } else if (error.request) {
      // Network error
      if (error.code === 'ECONNABORTED') {
        toast.error('Request timeout. Please check your connection.')
      } else {
        toast.error('Network error. Please check your connection.')
      }
    } else {
      // Request setup error
      toast.error('Request failed to send')
    }

    return Promise.reject(error)
  },
)

// Helper function to handle authentication failures
const handleAuthFailure = () => {
  localStorage.removeItem('admin_token')
  delete api.defaults.headers.common['Authorization']

  // Redirect to login if not already there
  if (window.location.pathname !== '/login') {
    window.location.href = '/login'
  }
}

// Loading state management
const notifyLoadingChange = (isLoading) => {
  const hasActiveRequests = activeRequests > 0
  loadingCallbacks.forEach((callback) => {
    callback(hasActiveRequests)
  })
}

// Export API utilities
export const apiHelpers = {
  /**
   * Subscribe to loading state changes
   */
  onLoadingChange: (callback) => {
    loadingCallbacks.add(callback)
    return () => loadingCallbacks.delete(callback)
  },

  /**
   * Get current loading state
   */
  isLoading: () => activeRequests > 0,

  /**
   * Upload file with progress tracking
   */
  uploadFile: (url, file, onProgress) => {
    const formData = new FormData()
    formData.append('file', file)

    return api.post(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        if (onProgress) {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          onProgress(percentCompleted)
        }
      },
    })
  },

  /**
   * Download file
   */
  downloadFile: async (url, filename) => {
    try {
      const response = await api.get(url, {
        responseType: 'blob',
      })

      // Create blob link to download
      const blob = new Blob([response.data])
      const link = document.createElement('a')
      link.href = window.URL.createObjectURL(blob)
      link.download = filename || 'download'
      // Append to DOM briefly for Firefox compatibility
      document.body.appendChild(link)
      link.click()

      // Clean up safely
      if (link.parentNode) {
        link.parentNode.removeChild(link)
      } else if (typeof link.remove === 'function') {
        link.remove()
      }
      window.URL.revokeObjectURL(link.href)

      return response
    } catch (error) {
      console.error('Download failed:', error)
      throw error
    }
  },

  /**
   * Make request without showing error toast
   */
  silent: (config) => {
    return api({
      ...config,
      suppressNotFoundError: true,
      suppressValidationError: true,
    })
  },

  /**
   * Cancel all pending requests
   */
  cancelAllRequests: () => {
    // This would require implementing request cancellation
    // For now, just reset the counter
    activeRequests = 0
    notifyLoadingChange(false)
  },
}

export default api
