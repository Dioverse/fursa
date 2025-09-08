// src/composables/useAuth.js
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { apiHelpers } from '@/services/api'
import { useToast } from 'vue-toastification'

/**
 * Authentication composable for Vue components
 */
export function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()
  const toast = useToast()

  // Local reactive state
  const isInitializing = ref(false)
  const sessionWarningShown = ref(false)

  // Computed properties
  const user = computed(() => authStore.user)
  const isAuthenticated = computed(() => authStore.isAuthenticated)
  const isLoading = computed(() => authStore.isLoading)
  const permissions = computed(() => authStore.permissions)
  const userInitials = computed(() => authStore.userInitials)
  const isAdmin = computed(() => authStore.isAdmin)
  const isSuperAdmin = computed(() => authStore.isSuperAdmin)

  // Permission helpers
  const hasPermission = (permission) => {
    return authStore.hasPermission(permission)
  }

  const hasAnyPermission = (permissionsList) => {
    return authStore.hasAnyPermission(permissionsList)
  }

  const hasAllPermissions = (permissionsList) => {
    return permissionsList.every((permission) => authStore.hasPermission(permission))
  }

  // Authentication methods
  const login = async (credentials) => {
    const result = await authStore.login(credentials)

    if (result.success) {
      // Start session monitoring
      startSessionMonitoring()
    }

    return result
  }

  const logout = async (showConfirmation = true) => {
    if (showConfirmation) {
      const confirmed = window.confirm('Are you sure you want to logout?')
      if (!confirmed) return false
    }

    await authStore.logout()

    // Stop session monitoring
    stopSessionMonitoring()

    // Redirect to login
    router.push('/login')
    return true
  }

  const refreshProfile = async () => {
    try {
      await authStore.fetchProfile()
      return true
    } catch (error) {
      console.error('Failed to refresh profile:', error)
      return false
    }
  }

  // Session management
  let sessionCheckInterval = null
  let sessionWarningTimeout = null

  const startSessionMonitoring = () => {
    if (sessionCheckInterval) return

    sessionCheckInterval = setInterval(() => {
      checkSessionExpiry()
    }, 60000) // Check every minute

    // Update activity on user interaction
    const updateActivity = () => authStore.updateActivity()

    document.addEventListener('mousedown', updateActivity)
    document.addEventListener('keydown', updateActivity)
    document.addEventListener('scroll', updateActivity)
    document.addEventListener('touchstart', updateActivity)
  }

  const stopSessionMonitoring = () => {
    if (sessionCheckInterval) {
      clearInterval(sessionCheckInterval)
      sessionCheckInterval = null
    }

    if (sessionWarningTimeout) {
      clearTimeout(sessionWarningTimeout)
      sessionWarningTimeout = null
    }

    // Remove activity listeners
    const updateActivity = () => authStore.updateActivity()

    document.removeEventListener('mousedown', updateActivity)
    document.removeEventListener('keydown', updateActivity)
    document.removeEventListener('scroll', updateActivity)
    document.removeEventListener('touchstart', updateActivity)
  }

  const checkSessionExpiry = () => {
    if (!authStore.isAuthenticated) return

    const lastActivity = authStore.lastActivity
    const now = Date.now()
    const sessionTimeout = 8 * 60 * 60 * 1000 // 8 hours
    const warningTime = 15 * 60 * 1000 // 15 minutes before expiry

    if (lastActivity && now - lastActivity > sessionTimeout) {
      // Session expired
      handleSessionExpiry()
    } else if (
      lastActivity &&
      now - lastActivity > sessionTimeout - warningTime &&
      !sessionWarningShown.value
    ) {
      // Show warning
      showSessionWarning()
    }
  }

  const handleSessionExpiry = async () => {
    toast.warning('Your session has expired. Please login again.')
    await logout(false)
  }

  const showSessionWarning = () => {
    sessionWarningShown.value = true

    toast.warning('Your session will expire in 15 minutes due to inactivity.', {
      timeout: 10000,
      onClose: () => {
        sessionWarningShown.value = false
      },
    })

    // Reset warning after 5 minutes
    sessionWarningTimeout = setTimeout(
      () => {
        sessionWarningShown.value = false
      },
      5 * 60 * 1000,
    )
  }

  // Route protection helpers
  const requireAuth = () => {
    if (!isAuthenticated.value) {
      router.push('/login')
      return false
    }
    return true
  }

  const requirePermission = (permission) => {
    if (!requireAuth()) return false

    if (!hasPermission(permission)) {
      toast.error('Access denied: Insufficient permissions')
      router.push('/admin/dashboard')
      return false
    }

    return true
  }

  const requireRole = (role) => {
    if (!requireAuth()) return false

    if (user.value?.role !== role && !isSuperAdmin.value) {
      toast.error('Access denied: Insufficient role permissions')
      router.push('/admin/dashboard')
      return false
    }

    return true
  }

  // Profile management
  const updateProfile = async (profileData) => {
    return await authStore.updateProfile(profileData)
  }

  const changePassword = async (passwordData) => {
    return await authStore.changePassword(passwordData)
  }

  // Initialization
  const initialize = async () => {
    if (isInitializing.value) return

    isInitializing.value = true

    try {
      const success = await authStore.initializeAuth()

      if (success) {
        startSessionMonitoring()
      }

      return success
    } catch (error) {
      console.error('Auth initialization failed:', error)
      return false
    } finally {
      isInitializing.value = false
    }
  }

  // Utility functions
  const getUserDisplayName = () => {
    return user.value?.name || user.value?.email || 'User'
  }

  const getUserAvatar = () => {
    return user.value?.avatar || null
  }

  const getRoleDisplayName = (role = null) => {
    const targetRole = role || user.value?.role

    const roleNames = {
      super_admin: 'Super Admin',
      admin: 'Administrator',
      manager: 'Manager',
      editor: 'Editor',
    }

    return roleNames[targetRole] || targetRole
  }

  // Lifecycle hooks
  onMounted(() => {
    if (authStore.isAuthenticated) {
      startSessionMonitoring()
    }
  })

  onUnmounted(() => {
    stopSessionMonitoring()
  })

  return {
    // State
    user,
    isAuthenticated,
    isLoading,
    permissions,
    userInitials,
    isAdmin,
    isSuperAdmin,
    isInitializing,

    // Methods
    login,
    logout,
    refreshProfile,
    updateProfile,
    changePassword,
    initialize,

    // Permission helpers
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,

    // Route protection
    requireAuth,
    requirePermission,
    requireRole,

    // Utility functions
    getUserDisplayName,
    getUserAvatar,
    getRoleDisplayName,

    // Session management
    startSessionMonitoring,
    stopSessionMonitoring,
  }
}
