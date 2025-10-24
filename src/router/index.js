// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { authMiddleware } from '@/middleware/auth'

// Auth Views
import LoginView from '@/views/auth/LoginView.vue'
import ResetPasswordView from '@/views/auth/ResetPasswordView.vue'

// Layouts
import AdminLayout from '@/layouts/AdminLayout.vue'

// Admin Views - Eager loaded critical views
import DashboardView from '@/views/admin/dashboard/DashboardView.vue'

// Lazy-loaded views for better performance
const UsersView = () => import('@/views/admin/users/UsersView.vue')
const UserDetailView = () => import('@/views/admin/users/UserDetailView.vue')
const ProductsView = () => import('@/views/admin/products/ProductsView.vue')
const ProductDetailView = () => import('@/views/admin/products/ProductDetailView.vue')
const DistributorsView = () => import('@/views/admin/distributors/DistributorsView.vue')
const DistributorDetailView = () => import('@/views/admin/distributors/DistributorDetailView.vue')
const OrdersView = () => import('@/views/admin/orders/OrdersView.vue')
const OrderDetailView = () => import('@/views/admin/orders/OrderDetailView.vue')
const ContentView = () => import('@/views/admin/content/ContentView.vue')
const SettingsView = () => import('@/views/admin/settings/SettingsView.vue')
const ProfileView = () => import('@/views/admin/profile/ProfileView.vue')
const InventoryLogsView = () => import('@/views/admin/inventory/InventoryLogsView.vue')
const InventoryLogDetailView = () => import('@/views/admin/inventory/InventoryLogDetailView.vue')
const NotFoundView = () => import('@/views/NotFoundView.vue')

const routes = [
  // Root redirect
  {
    path: '/',
    redirect: '/admin/dashboard',
  },

  // Auth routes
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    beforeEnter: authMiddleware.requiresGuest,
    meta: {
      title: 'Login',
      layout: 'auth',
    },
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: ResetPasswordView,
    beforeEnter: authMiddleware.requiresGuest,
    meta: {
      title: 'Reset Password',
      layout: 'auth',
    },
  },

  // Admin routes
  {
    path: '/admin',
    component: AdminLayout,
    beforeEnter: authMiddleware.requiresAuth,
    meta: {
      requiresAuth: true,
      layout: 'admin',
    },
    children: [
      // Dashboard
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: DashboardView,
        meta: {
          title: 'Dashboard',
          breadcrumb: 'Dashboard',
        },
      },

      // User Management
      {
        path: 'users',
        name: 'admin.users',
        component: UsersView,
        meta: {
          title: 'Users',
          breadcrumb: 'Users',
        },
      },
      {
        path: 'analytics',
        name: 'admin.analytics',
        component: () => import('@/views/admin/analytics/AnalyticsView.vue'),
        meta: {
          title: 'Analytics',
          breadcrumb: 'Analytics',
        },
      },
      {
        path: 'users/:id',
        name: 'admin.users.detail',
        component: UserDetailView,
        meta: {
          title: 'User Details',
          breadcrumb: 'User Details',
        },
        props: true,
      },

      // Product Management
      {
        path: 'products',
        name: 'admin.products',
        component: ProductsView,
        meta: {
          title: 'Products',
          breadcrumb: 'Products',
        },
      },
      {
        path: 'products/create',
        name: 'admin.products.create',
        component: () => import('@/views/admin/products/ProductCreateView.vue'),
        meta: {
          title: 'Create Product',
          breadcrumb: 'Create Product',
        },
      },
      {
        path: 'products/:id',
        name: 'admin.products.detail',
        component: ProductDetailView,
        meta: {
          title: 'Product Details',
          breadcrumb: 'Product Details',
        },
        props: true,
      },

      // Distributor Management
      {
        path: 'distributors',
        name: 'admin.distributors',
        component: DistributorsView,
        meta: {
          title: 'Distributors',
          breadcrumb: 'Distributors',
        },
      },
      {
        path: 'distributors/:id',
        name: 'admin.distributors.detail',
        component: DistributorDetailView,
        meta: {
          title: 'Distributor Details',
          breadcrumb: 'Distributor Details',
        },
        props: true,
      },

      // Order Management
      {
        path: 'orders',
        name: 'admin.orders',
        component: OrdersView,
        meta: {
          title: 'Orders',
          breadcrumb: 'Orders',
        },
      },
      {
        path: 'orders/:id',
        name: 'admin.orders.detail',
        component: OrderDetailView,
        meta: {
          title: 'Order Details',
          breadcrumb: 'Order Details',
        },
        props: true,
      },

      // Content Management
      // Inventory Logs
      {
        path: 'inventory/logs',
        name: 'admin.inventory.logs',
        component: InventoryLogsView,
        meta: {
          title: 'Inventory Logs',
          breadcrumb: 'Inventory Logs',
        },
      },
      {
        path: 'inventory/logs/:id',
        name: 'admin.inventory.logs.detail',
        component: InventoryLogDetailView,
        meta: {
          title: 'Inventory Log Details',
          breadcrumb: 'Inventory Log Details',
        },
        props: true,
      },

      {
        path: 'content',
        name: 'admin.content',
        component: ContentView,
        meta: {
          title: 'Content Management',
          breadcrumb: 'Content',
        },
      },

      // Settings
      {
        path: 'settings',
        name: 'admin.settings',
        component: SettingsView,
        meta: {
          title: 'Settings',
          breadcrumb: 'Settings',
        },
      },

      // Profile (no special permission required)
      {
        path: 'profile',
        name: 'admin.profile',
        component: ProfileView,
        meta: {
          title: 'Profile',
          breadcrumb: 'Profile',
        },
      },

      // Category Management
      {
        path: 'categories',
        name: 'admin.categories',
        component: () => import('@/views/admin/categories/CategoriesView.vue'),
        meta: {
          title: 'Categories',
          breadcrumb: 'Categories',
        },
      },
      {
        path: 'categories/:id',
        name: 'admin.categories.detail',
        component: () => import('@/views/admin/categories/CategoryDetailView.vue'),
        meta: {
          title: 'Category Details',
          breadcrumb: 'Category Details',
        },
        props: true,
      },
    ],
  },

  // 404 catch-all route
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundView,
    meta: {
      title: 'Page Not Found',
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    } else {
      return {
        top: 0,
        behavior: 'smooth',
      }
    }
  },
})

// Global navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Set page title
  document.title = to.meta.title ? `${to.meta.title} | Fursa Energy Admin` : 'Fursa Energy Admin'

  // Initialize auth if token exists but user data is missing
  if (!authStore.user && authStore.token && !authStore.isLoading) {
    try {
      await authStore.initializeAuth()
    } catch (error) {
      console.error('Failed to initialize auth:', error)
    }
  }

  // Handle authentication requirements
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
  const requiresGuest = to.matched.some((record) => record.meta.requiresGuest)

  if (requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login with return URL
    next({
      name: 'login',
      query: { redirect: to.fullPath },
    })
    return
  }

  if (requiresGuest && authStore.isAuthenticated) {
    // Redirect authenticated users away from guest pages
    next({ name: 'admin.dashboard' })
    return
  }

  // Update activity for authenticated users
  if (authStore.isAuthenticated) {
    authStore.updateActivity()
  }

  next()
})

router.afterEach((to, _from) => {
  // Log navigation in development
  if (import.meta.env.DEV) {
    console.log(`📍 Navigated to: ${to.name} (${to.path})`)
  }

  // You can add analytics tracking here
  // analytics.track('page_view', { page: to.path })
})

// Handle navigation errors
router.onError((error) => {
  console.error('Router error:', error)

  // You can add error reporting here
  // errorReporting.captureException(error)
})

export default router
