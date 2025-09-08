// src/stores/users.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useToast } from 'vue-toastification'

export const useUsersStore = defineStore('users', () => {
  const toast = useToast()

  // State
  const users = ref([])
  const currentUser = ref(null)
  const isLoading = ref(false)
  const isCreating = ref(false)
  const isUpdating = ref(false)
  const isDeleting = ref(false)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0,
  })
  const filters = ref({
    search: '',
    role: '',
    status: '',
    date_from: '',
    date_to: '',
    sort_by: 'created_at',
    sort_order: 'desc',
  })

  // Getters
  const totalUsers = computed(() => pagination.value.total)
  const hasUsers = computed(() => users.value.length > 0)
  const activeUsers = computed(() => users.value.filter((user) => user.status === 'active'))
  const inactiveUsers = computed(() => users.value.filter((user) => user.status === 'inactive'))
  const pendingUsers = computed(() => users.value.filter((user) => user.status === 'pending'))

  const usersByRole = computed(() => {
    const roles = {}
    users.value.forEach((user) => {
      if (!roles[user.role]) {
        roles[user.role] = []
      }
      roles[user.role].push(user)
    })
    return roles
  })

  // Actions
  const fetchUsers = async (page = 1, customFilters = {}) => {
    try {
      isLoading.value = true

      const params = {
        page,
        per_page: pagination.value.per_page,
        ...filters.value,
        ...customFilters,
      }

      // Remove empty filters
      Object.keys(params).forEach((key) => {
        if (params[key] === '' || params[key] === null || params[key] === undefined) {
          delete params[key]
        }
      })

      const response = await api.get('/users', { params })

      console.log('Fetched users:', response.data)

      users.value = response.data.data.data || response.data.users || []
      pagination.value = {
        current_page: response.data.data.current_page || page,
        last_page: response.data.data.last_page || 1,
        per_page: response.data.data.per_page || 10,
        total: response.data.data.total || users.value.length,
        from: response.data.data.from || 0,
        to: response.data.data.to || users.value.length,
      }

      return { success: true, data: users.value }
    } catch (error) {
      console.error('Failed to fetch users:', error)
      toast.error('Failed to load users')
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const fetchUser = async (id) => {
    try {
      isLoading.value = true
      const response = await api.get(`/users/${id}`)
      currentUser.value = response.data.data || response.data.user || response.data
      console.log('Fetched user data:', currentUser.value)
      console.log('Full response:', response)
      return { success: true, data: currentUser.value }
    } catch (error) {
      console.error('Failed to fetch user:', error)
      toast.error('Failed to load user details')
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const createUser = async (userData) => {
    try {
      isCreating.value = true

      const formData = new FormData()
      Object.keys(userData).forEach((key) => {
        if (userData[key] !== null && userData[key] !== undefined) {
          if (key === 'avatar' && userData[key] instanceof File) {
            formData.append(key, userData[key])
          } else if (Array.isArray(userData[key])) {
            userData[key].forEach((item, index) => {
              formData.append(`${key}[${index}]`, item)
            })
          } else {
            formData.append(key, userData[key])
          }
        }
      })

      const response = await api.post('/admin/users', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      const newUser = response.data.user || response.data
      users.value.unshift(newUser)
      pagination.value.total += 1

      toast.success('User created successfully')
      return { success: true, data: newUser }
    } catch (error) {
      console.error('Failed to create user:', error)
      const errorMessage = error.response?.data?.message || 'Failed to create user'
      toast.error(errorMessage)
      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isCreating.value = false
    }
  }

  const updateUser = async (id, userData) => {
    try {
      isUpdating.value = true

      const formData = new FormData()
      formData.append('_method', 'PUT')

      Object.keys(userData).forEach((key) => {
        if (userData[key] !== null && userData[key] !== undefined) {
          if (key === 'avatar' && userData[key] instanceof File) {
            formData.append(key, userData[key])
          } else if (Array.isArray(userData[key])) {
            userData[key].forEach((item, index) => {
              formData.append(`${key}[${index}]`, item)
            })
          } else {
            formData.append(key, userData[key])
          }
        }
      })

      const response = await api.post(`/admin/users/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      const updatedUser = response.data.user || response.data
      const index = users.value.findIndex((user) => user.id === id)
      if (index !== -1) {
        users.value[index] = updatedUser
      }

      if (currentUser.value?.id === id) {
        currentUser.value = updatedUser
      }

      toast.success('User updated successfully')
      return { success: true, data: updatedUser }
    } catch (error) {
      console.error('Failed to update user:', error)
      const errorMessage = error.response?.data?.message || 'Failed to update user'
      toast.error(errorMessage)
      return {
        success: false,
        error: errorMessage,
        errors: error.response?.data?.errors || {},
      }
    } finally {
      isUpdating.value = false
    }
  }

  const deleteUser = async (id) => {
    try {
      isDeleting.value = true
      await api.delete(`/admin/${id}`)

      users.value = users.value.filter((user) => user.id !== id)
      pagination.value.total -= 1

      if (currentUser.value?.id === id) {
        currentUser.value = null
      }

      toast.success('User deleted successfully')
      return { success: true }
    } catch (error) {
      console.error('Failed to delete user:', error)
      const errorMessage = error.response?.data?.message || 'Failed to delete user'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    } finally {
      isDeleting.value = false
    }
  }

  const bulkAction = async (action, userIds, additionalData = {}) => {
    try {
      isUpdating.value = true

      const payload = {
        action,
        user_ids: userIds,
        ...additionalData,
      }

      const response = await api.post('/admin/users/bulk-action', payload)

      // Refresh users list
      await fetchUsers(pagination.value.current_page)

      const message = response.data.message || `${action} completed successfully`
      toast.success(message)

      return { success: true, data: response.data }
    } catch (error) {
      console.error('Bulk action failed:', error)
      const errorMessage = error.response?.data?.message || 'Bulk action failed'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    } finally {
      isUpdating.value = false
    }
  }

  const updateUserStatus = async (id, status, role) => {
    try {
      let response
      if (role === 'distributor') {
        response = await api.patch(`distributors/${id}/status`, { status })
      } else {
        response = await api.patch(`users/${id}/status`, { status })
      }

      const updatedUser = response.data.user || response.data
      const index = users.value.findIndex((user) => user.id === id)
      if (index !== -1) {
        users.value[index] = updatedUser
      }

      const statusText = {
        active: 'activated',
        inactive: 'deactivated',
        suspended: 'suspended',
        pending: 'set to pending',
      }

      toast.success(`User ${statusText[status] || 'updated'}`)
      return { success: true, data: updatedUser }
    } catch (error) {
      console.error('Failed to update user status:', error)
      const errorMessage = error.response?.data?.message || 'Failed to update user status'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    }
  }

  const toggleUserBan = async (id) => {
    try {
      const response = await api.post(`/users/toggle-ban/${id}`)
      const updatedUser = response.data.user
      const index = users.value.findIndex((user) => user.id === id)
      if (index !== -1) {
        users.value[index] = updatedUser
      }
      toast.success(response.data.message || 'User ban status toggled')
      return { success: true, data: updatedUser }
    } catch (error) {
      console.error('Failed to toggle user ban:', error)
      const errorMessage = error.response?.data?.message || 'Failed to toggle user ban'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    }
  }

  const impersonateUser = async (id) => {
    try {
      const response = await api.post(`/admin/users/${id}/impersonate`)

      // Store original admin token
      const originalToken = localStorage.getItem('admin_token')
      localStorage.setItem('original_admin_token', originalToken)

      // Set impersonation token
      const impersonationToken = response.data.token
      localStorage.setItem('admin_token', impersonationToken)
      api.defaults.headers.common['Authorization'] = `Bearer ${impersonationToken}`

      toast.info(`Now impersonating ${response.data.user.name}`)
      return { success: true, data: response.data }
    } catch (error) {
      console.error('Failed to impersonate user:', error)
      const errorMessage = error.response?.data?.message || 'Failed to impersonate user'
      toast.error(errorMessage)
      return { success: false, error: errorMessage }
    }
  }

  const stopImpersonation = async () => {
    try {
      await api.post('/admin/stop-impersonation')

      // Restore original admin token
      const originalToken = localStorage.getItem('original_admin_token')
      if (originalToken) {
        localStorage.setItem('admin_token', originalToken)
        localStorage.removeItem('original_admin_token')
        api.defaults.headers.common['Authorization'] = `Bearer ${originalToken}`
      }

      toast.info('Stopped impersonation')
      return { success: true }
    } catch (error) {
      console.error('Failed to stop impersonation:', error)
      return { success: false, error: error.message }
    }
  }

  const exportUsers = async (format = 'csv', customFilters = {}) => {
    try {
      const params = {
        format,
        ...filters.value,
        ...customFilters,
      }

      // Remove empty filters
      Object.keys(params).forEach((key) => {
        if (params[key] === '' || params[key] === null || params[key] === undefined) {
          delete params[key]
        }
      })

      const response = await api.get('/admin/users/export', {
        params,
        responseType: 'blob',
      })

      // Create download link
      const blob = new Blob([response.data])
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `users-export-${Date.now()}.${format}`
      document.body.appendChild(link)
      link.click()
      if (link.parentNode) {
        link.parentNode.removeChild(link)
      } else if (typeof link.remove === 'function') {
        link.remove()
      }
      window.URL.revokeObjectURL(url)

      toast.success(`Users exported as ${format.toUpperCase()}`)
      return { success: true }
    } catch (error) {
      console.error('Failed to export users:', error)
      toast.error('Failed to export users')
      return { success: false, error: error.message }
    }
  }

  const sendPasswordReset = async (id) => {
    try {
      await api.post(`/users/${id}/password-reset`)
      toast.success('Password reset email sent')
      return { success: true }
    } catch (error) {
      console.error('Failed to send password reset:', error)
      toast.error('Failed to send password reset')
      return { success: false, error: error.message }
    }
  }

  // Filter and search utilities
  const updateFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const resetFilters = () => {
    filters.value = {
      search: '',
      role: '',
      status: '',
      date_from: '',
      date_to: '',
      sort_by: 'created_at',
      sort_order: 'desc',
    }
  }

  const clearCurrentUser = () => {
    currentUser.value = null
  }

  return {
    // State
    users,
    currentUser,
    isLoading,
    isCreating,
    isUpdating,
    isDeleting,
    pagination,
    filters,

    // Getters
    totalUsers,
    hasUsers,
    activeUsers,
    inactiveUsers,
    pendingUsers,
    usersByRole,

    // Actions
    fetchUsers,
    fetchUser,
    createUser,
    updateUser,
    deleteUser,
    bulkAction,
    updateUserStatus,
    toggleUserBan,
    impersonateUser,
    stopImpersonation,
    exportUsers,
    sendPasswordReset,

    // Utilities
    updateFilters,
    resetFilters,
    clearCurrentUser,
  }
})
