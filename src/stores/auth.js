// src/stores/auth.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

export const useAuthStore = defineStore('auth', () => {
  const toast = useToast()

  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('admin_token'))
  const isLoading = ref(false)
  const permissions = ref([])
  const lastActivity = ref(Date.now())

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isSuperAdmin = computed(() => user.value?.role === 'super_admin')
  const userInitials = computed(() => {
    if (!user.value) return ''
    const names = user.value.name.split(' ')
    return names.map((name) => name.charAt(0).toUpperCase()).join('')
  })

  const hasPermission = computed(() => (permission) => {
    if (isSuperAdmin.value) return true
    return permissions.value.includes(permission)
  })

  const hasAnyPermission = computed(() => (permissionsList) => {
    if (isSuperAdmin.value) return true
    return permissionsList.some((permission) => permissions.value.includes(permission))
  })

  // Actions
  // const login = async (credentials) => {
  //   try {
  //     isLoading.value = true

  //     const response = await api.post('/Login', credentials)
  //     const { user: userData, token: authToken, permissions: userPermissions } = response.data

  //     // Set authentication data
  //     user.value = userData
  //     token.value = authToken
  //     permissions.value = userPermissions || []
  //     lastActivity.value = Date.now()

  //     // Store token in localStorage
  //     localStorage.setItem('admin_token', authToken)

  //     // Set default authorization header
  //     api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`

  //     toast.success(`Welcome back, ${userData.name}!`)
  //     return { success: true, user: userData }
  //   } catch (error) {
  //     const errorMessage = error.response?.data?.message || 'Login failed'
  //     toast.error(errorMessage)

  //     return {
  //       success: false,
  //       error: errorMessage,
  //       errors: error.response?.data?.errors || {},
  //     }
  //   } finally {
  //     isLoading.value = false
  //   }
  // }
  const login = async (credentials) => {
    try {
      isLoading.value = true

      const response = await api.post('/login', credentials)
      const data = response.data

      // Extract token properly
      const authToken = data.token

      // Map the actual response structure to your expected format
      const userData = {
        id: data.user.id,
        name: `${data.user.first_name} ${data.user.last_name}`, // Combine first and last name
        email: data.user.email,
        phone: data.user.phone,
        roles: [data.user.role], // Convert single role to array format
        avatar: data.user.avatar || null, // Default to null if not provided
        isActive: data.user.status === 'active', // Convert status to boolean
        currentProfile: data.user.role, // Use role as current profile
        profiles: [data.user.role], // Array of available profiles
        // Additional user data you might want to store
        firstName: data.user.first_name,
        lastName: data.user.last_name,
        status: data.user.status,
        emailVerifiedAt: data.user.email_verified_at,
        createdAt: data.user.created_at,
        updatedAt: data.user.updated_at,
      }

      // Set permissions based on role
      const userPermissions = [data.user.role] // You can expand this based on your role-permission mapping

      // Set authentication data
      user.value = userData
      token.value = authToken
      permissions.value = userPermissions
      lastActivity.value = Date.now()

      // Store token in localStorage
      localStorage.setItem('admin_token', authToken)

      // Set default authorization header
      api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`

      // Success message using the proper name
      toast.success(`Welcome back, ${userData.name}!`)

      return {
        success: true,
        user: userData,
        token: authToken,
        message: data.message,
      }
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Login failed'
      toast.error(errorMessage)

      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async (showMessage = true) => {
    try {
      if (token.value) {
        await api.post('/logout')
      }
    } catch (error) {
      console.error('Logout API error:', error)
    } finally {
      // Clear local state
      user.value = null
      token.value = null
      permissions.value = []
      lastActivity.value = null

      // Remove from localStorage
      localStorage.removeItem('admin_token')

      // Remove authorization header
      delete api.defaults.headers.common['Authorization']

      if (showMessage) {
        toast.info('You have been logged out')
      }
    }
  }

  const fetchProfile = async () => {
    try {
      isLoading.value = true
      // Backend exposes this under the admin namespace
      const response = await api.get('auth/admin/profile-details')

      user.value = response.data.user
      permissions.value = response.data.permissions || []
      lastActivity.value = Date.now()

      return response.data.user
  } catch (error) {
      console.error('Profile fetch error:', error)

      // If token is invalid, logout silently
      if (error.response?.status === 401) {
        await logout(false)
        throw new Error('Authentication expired')
      }

      throw error
    } finally {
      isLoading.value = false
    }
  }

  const updateProfile = async (profileData) => {
    try {
      isLoading.value = true

      const formData = new FormData()
      Object.keys(profileData).forEach((key) => {
        if (profileData[key] !== null && profileData[key] !== undefined) {
          formData.append(key, profileData[key])
        }
      })

      const response = await api.post('/auth/admin/profile', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      user.value = response.data.user
      toast.success('Profile updated successfully')

      return { success: true, user: response.data.user }
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Profile update failed'
      toast.error(errorMessage)

      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isLoading.value = false
    }
  }

  const changePassword = async (passwordData) => {
    try {
      isLoading.value = true

      await api.put('/auth/admin/change-password', passwordData)

      toast.success('Password changed successfully')
      return { success: true, message: 'Password changed successfully' }
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Password change failed'
      toast.error(errorMessage)

      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isLoading.value = false
    }
  }

  const forgotPassword = async (email) => {
    try {
      isLoading.value = true

      await api.post('/auth/admin/forgot-password', { email })

      toast.success('Password reset instructions sent to your email')
      return { success: true }
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Failed to send reset instructions'
      toast.error(errorMessage)

      return { success: false, error: errorMessage }
    } finally {
      isLoading.value = false
    }
  }

  const resetPassword = async (resetData) => {
    try {
      isLoading.value = true

      await api.post('/auth/admin/reset-password', resetData)

      toast.success('Password reset successfully')
      return { success: true }
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Password reset failed'
      toast.error(errorMessage)

      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isLoading.value = false
    }
  }

  // Session management
  const updateActivity = () => {
    lastActivity.value = Date.now()
  }

  const isSessionExpired = () => {
    if (!lastActivity.value || !token.value) return true

    const sessionTimeout = 8 * 60 * 60 * 1000 // 8 hours
    return Date.now() - lastActivity.value > sessionTimeout
  }

  const refreshToken = async () => {
    try {
      const response = await api.post('/auth/admin/refresh')

      token.value = response.data.token
      localStorage.setItem('admin_token', response.data.token)
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

      updateActivity()
      return true
    } catch (error) {
      console.error('Token refresh failed:', error)
      await logout(false)
      return false
    }
  }

  // Initialize auth state
  const initializeAuth = async () => {
    if (!token.value) return false

    try {
      // Set authorization header
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

      // Check if session is expired
      if (isSessionExpired()) {
        await logout(false)
        return false
      }

      // Try to refresh token and fetch profile
      await fetchProfile()
      return true
    } catch (error) {
      console.error('Auth initialization failed:', error)
      await logout(false)
      return false
    }
  }

  // Auto-logout on session expiry
  const setupSessionMonitoring = () => {
    setInterval(
      () => {
        if (isAuthenticated.value && isSessionExpired()) {
          logout()
          toast.warning('Session expired. Please login again.')
        }
      },
      5 * 60 * 1000,
    ) // Check every 5 minutes
  }

  return {
    // State
    user,
    token,
    isLoading,
    permissions,
    lastActivity,

    // Getters
    isAuthenticated,
    isAdmin,
    isSuperAdmin,
    userInitials,
    hasPermission,
    hasAnyPermission,

    // Actions
    login,
    logout,
    fetchProfile,
    updateProfile,
    changePassword,
    forgotPassword,
    resetPassword,
    initializeAuth,
    updateActivity,
    refreshToken,
    setupSessionMonitoring,
  }
})
