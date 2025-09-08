// src/middleware/auth.js
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

/**
 * Authentication middleware for protecting routes
 */
export const authMiddleware = {
  /**
   * Check if user is authenticated
   */
  requiresAuth: async (to, from, next) => {
    const authStore = useAuthStore()
    const toast = useToast()

    try {
      // If no token, redirect to login
      if (!authStore.token) {
        return next({
          name: 'login',
          query: { redirect: to.fullPath },
        })
      }

      // If user data not loaded, try to fetch it
      if (!authStore.user) {
        await authStore.fetchProfile()
      }

      // If still no user data, redirect to login
      if (!authStore.user) {
        return next({
          name: 'login',
          query: { redirect: to.fullPath },
        })
      }

      // Update activity
      authStore.updateActivity()

      next()
    } catch (error) {
      console.error('Auth middleware error:', error)
      toast.error('Authentication required')

      next({
        name: 'login',
        query: { redirect: to.fullPath },
      })
    }
  },

  /**
   * Redirect authenticated users away from guest pages
   */
  requiresGuest: (to, from, next) => {
    const authStore = useAuthStore()

    if (authStore.isAuthenticated) {
      next({ name: 'admin.dashboard' })
    } else {
      next()
    }
  },

  /**
   * Check if user has required permission
   */
  requiresPermission: (permission) => {
    return async (to, from, next) => {
      const authStore = useAuthStore()
      const toast = useToast()

      try {
        // First ensure user is authenticated
        await authMiddleware.requiresAuth(to, from, (result) => {
          if (typeof result === 'object') {
            // Redirect to login if auth failed
            return next(result)
          }
        })

        // Check permission
        if (!authStore.hasPermission(permission)) {
          toast.error('Access denied: Insufficient permissions')
          return next({ name: 'admin.dashboard' })
        }

        next()
      } catch (error) {
        console.error('Permission middleware error:', error)
        toast.error('Access verification failed')
        next({ name: 'admin.dashboard' })
      }
    }
  },

  /**
   * Check if user has any of the required permissions
   */
  requiresAnyPermission: (permissions) => {
    return async (to, from, next) => {
      const authStore = useAuthStore()
      const toast = useToast()

      try {
        // First ensure user is authenticated
        await authMiddleware.requiresAuth(to, from, (result) => {
          if (typeof result === 'object') {
            return next(result)
          }
        })

        // Check if user has any of the required permissions
        if (!authStore.hasAnyPermission(permissions)) {
          toast.error('Access denied: Insufficient permissions')
          return next({ name: 'admin.dashboard' })
        }

        next()
      } catch (error) {
        console.error('Permission middleware error:', error)
        toast.error('Access verification failed')
        next({ name: 'admin.dashboard' })
      }
    }
  },

  /**
   * Check if user has required role
   */
  requiresRole: (role) => {
    return async (to, from, next) => {
      const authStore = useAuthStore()
      const toast = useToast()

      try {
        // First ensure user is authenticated
        await authMiddleware.requiresAuth(to, from, (result) => {
          if (typeof result === 'object') {
            return next(result)
          }
        })

        // Check role
        const userRole = authStore.user?.role
        if (userRole !== role && !authStore.isSuperAdmin) {
          toast.error('Access denied: Insufficient role permissions')
          return next({ name: 'admin.dashboard' })
        }

        next()
      } catch (error) {
        console.error('Role middleware error:', error)
        toast.error('Access verification failed')
        next({ name: 'admin.dashboard' })
      }
    }
  },

  /**
   * Check if user is admin or super admin
   */
  requiresAdmin: async (to, from, next) => {
    const authStore = useAuthStore()
    const toast = useToast()

    try {
      // First ensure user is authenticated
      await authMiddleware.requiresAuth(to, from, (result) => {
        if (typeof result === 'object') {
          return next(result)
        }
      })

      // Check if user is admin or super admin
      if (!authStore.isAdmin && !authStore.isSuperAdmin) {
        toast.error('Access denied: Admin privileges required')
        return next({ name: 'admin.dashboard' })
      }

      next()
    } catch (error) {
      console.error('Admin middleware error:', error)
      toast.error('Access verification failed')
      next({ name: 'admin.dashboard' })
    }
  },

  /**
   * Check if user is super admin
   */
  requiresSuperAdmin: async (to, from, next) => {
    const authStore = useAuthStore()
    const toast = useToast()

    try {
      // First ensure user is authenticated
      await authMiddleware.requiresAuth(to, from, (result) => {
        if (typeof result === 'object') {
          return next(result)
        }
      })

      // Check if user is super admin
      if (!authStore.isSuperAdmin) {
        toast.error('Access denied: Super admin privileges required')
        return next({ name: 'admin.dashboard' })
      }

      next()
    } catch (error) {
      console.error('Super admin middleware error:', error)
      toast.error('Access verification failed')
      next({ name: 'admin.dashboard' })
    }
  },
}

/**
 * Route meta helper functions
 */
export const routeHelpers = {
  /**
   * Apply authentication middleware to route
   */
  protect: (route, middleware = []) => {
    const middlewares = Array.isArray(middleware) ? middleware : [middleware]

    return {
      ...route,
      beforeEnter: async (to, from, next) => {
        for (const middlewareFunc of middlewares) {
          await new Promise((resolve) => {
            middlewareFunc(to, from, (result) => {
              if (result === undefined) {
                resolve()
              } else {
                next(result)
                return
              }
            })
          })
        }
        next()
      },
    }
  },

  /**
   * Create a protected route with permission check
   */
  withPermission: (route, permission) => {
    return routeHelpers.protect(route, [
      authMiddleware.requiresAuth,
      authMiddleware.requiresPermission(permission),
    ])
  },

  /**
   * Create a protected route with role check
   */
  withRole: (route, role) => {
    return routeHelpers.protect(route, [
      authMiddleware.requiresAuth,
      authMiddleware.requiresRole(role),
    ])
  },
}
