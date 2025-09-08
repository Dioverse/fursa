// src/stores/notification.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref([])

  const addNotification = (notification) => {
    const id = Date.now()
    notifications.value.push({
      id,
      type: 'info',
      duration: 5000,
      ...notification,
      createdAt: new Date(),
    })

    if (notification.duration !== 0) {
      setTimeout(() => {
        removeNotification(id)
      }, notification.duration || 5000)
    }

    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex((n) => n.id === id)
    if (index !== -1) {
      notifications.value.splice(index, 1)
    }
  }

  const success = (message, options = {}) => {
    return addNotification({
      type: 'success',
      message,
      ...options,
    })
  }

  const error = (message, options = {}) => {
    return addNotification({
      type: 'error',
      message,
      ...options,
    })
  }

  const warning = (message, options = {}) => {
    return addNotification({
      type: 'warning',
      message,
      ...options,
    })
  }

  const info = (message, options = {}) => {
    return addNotification({
      type: 'info',
      message,
      ...options,
    })
  }

  const clearAll = () => {
    notifications.value = []
  }

  return {
    notifications,
    addNotification,
    removeNotification,
    success,
    error,
    warning,
    info,
    clearAll,
  }
})
