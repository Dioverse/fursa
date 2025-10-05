import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'


// Lazy load views
const HomeView = () => import('@/views/HomeView.vue')
const LoginView = () => import('@/views/auth/LoginView.vue')
const RegisterView = () => import('@/views/auth/RegisterView.vue')
const DashboardView = () => import('@/views/dashboard/DashboardView.vue')
const OrdersView = () => import('@/views/dashboard/OrdersView.vue')
const OrderDetailsView = () => import('@/views/dashboard/OrderDetailsView.vue')
const TrackOrdersView = () => import('@/views/dashboard/TrackOrdersView.vue')
const ProfileView = () => import('@/views/dashboard/ProfileView.vue')
const AddressesView = () => import('@/views/dashboard/AddressesView.vue')
const WishlistView = () => import('@/views/dashboard/WishlistView.vue')
const ShopView = () => import('@/views/shop/ShopView.vue')
const ProductDetailView = () => import('@/views/shop/ProductDetailView.vue')
const CategoryView = () => import('@/views/shop/CategoryView.vue')
const CartView = () => import('@/views/cart/CartView.vue')
const CheckoutView = () => import('@/views/cart/CheckoutView.vue')
const DistributorRegistrationView = () =>
  import('@/views/distributor/DistributorRegistrationView.vue')
const VerifyView = () => import('@/views/auth/VerifyView.vue')
const VerifyEmailView = () => import('@/views/auth/VerifyEmailView.vue')
const ForgotPasswordView = () => import('@/views/auth/ForgotPasswordView.vue')
const ResetPassword = () => import('@/views/auth/ResetPassword.vue')

// Additional views
const AboutView = () => import('@/views/AboutView.vue')
const ContactView = () => import('@/views/ContactView.vue')
const BlogView = () => import('@/views/BlogView.vue')
const BlogCategoryView = () => import('@/views/BlogCategoryView.vue')
const BlogDetailsView = () => import('@/views/BlogDetailsView.vue')
const FAQView = () => import('@/views/FAQView.vue')
const TermsView = () => import('@/views/TermsView.vue')
const PrivacyView = () => import('@/views/PrivacyView.vue')

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { title: 'Home' },
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { title: 'Login', guest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { title: 'Register', guest: true },
    },
    {
      path: '/verify',
      name: 'verify',
      component: VerifyView,
      meta: { title: 'Email Verification', guest: true },
    },
    {
      // path: '/email/verify/{id}/{hash}?expires={expired}&signature={signature}',
      path: '/verify-email',
      name: 'email.verify',
      component: VerifyEmailView,
      meta: { title: 'Verify Email', guest: true },
    },
    {
      path: '/forgot-password',
      name: 'forgot.password',
      component: ForgotPasswordView,
      meta: { title: 'Forgot Password', guest: true },
    },
    {
      path: '/reset-password',
      name: 'reset.password',
      component: ResetPassword,
      meta: { title: 'Reset Password', guest: true },
    },
    {
      path: '/shop',
      name: 'shop',
      component: ShopView,
      meta: { title: 'Shop' },
    },
    {
      path: '/shop/:slug',
      name: 'category',
      component: Categories,
      meta: { title: 'Categories'},
    },
    // {
    //   path: '/shop/category/:slug',
    //   name: 'category',
    //   component: CategoryView,
    //   meta: { title: 'Category' },
    // },
    {
      path: '/product/:id',
      name: 'product-detail',
      component: ProductDetailView,
      meta: { title: 'Product Details' },
    },

    {
      path: '/cart',
      name: 'cart',
      component: CartView,
      meta: { title: 'Shopping Cart' },
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: CheckoutView,
      meta: { title: 'Checkout', requiresAuth: true },
    },
    {
      path: '/distributor-registration',
      name: 'distributor-registration',
      component: DistributorRegistrationView,
      meta: { title: 'Become a Distributor' },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { title: 'Dashboard', requiresAuth: true },
    },
    {
      path: '/dashboard/orders',
      name: 'orders',
      component: OrdersView,
      meta: { title: 'My Orders', requiresAuth: true },
    },
    {
      path: '/dashboard/orders/:id',
      name: 'order-details',
      component: OrderDetailsView,
      meta: { title: 'Order Details', requiresAuth: true },
    },
    {
      path: '/dashboard/track-order/:id',
      name: 'track-order',
      component: TrackOrdersView,
      meta: { title: 'Track Order' },
    },
    {
      path: '/dashboard/profile',
      name: 'profile',
      component: ProfileView,
      meta: { title: 'Profile', requiresAuth: true },
    },
    {
      path: '/dashboard/wishlist',
      name: 'wishlist',
      component: WishlistView,
      meta: { title: 'Wishlist', requiresAuth: true },
    },
    {
      path: '/dashboard/addresses',
      name: 'addresses',
      component: AddressesView,
      meta: { title: 'Addresses', requiresAuth: true },
    },
    {
      path: '/about',
      name: 'about',
      component: AboutView,
      meta: { title: 'About Us' },
    },
    {
      path: '/contact',
      name: 'contact',
      component: ContactView,
      meta: { title: 'Contact Us' },
    },
    {
      path: '/blog',
      name: 'blog',
      component: BlogView,
      meta: { title: 'Blog' },
    },
    {
      path: '/blog/c/:slug',
      name: 'blog-category',
      component: BlogCategoryView,
      meta: { title: 'Blog Category' },
    },
    {
      path: '/blog/:slug',
      name: 'blog-detail',
      component: BlogDetailsView,
      meta: { title: 'Blog Details' },
    },
    {
      path: '/faq',
      name: 'faq',
      component: FAQView,
      meta: { title: 'FAQ' },
    },
    {
      path: '/terms',
      name: 'terms',
      component: TermsView,
      meta: { title: 'Terms & Conditions' },
    },
    {
      path: '/privacy',
      name: 'privacy',
      component: PrivacyView,
      meta: { title: 'Privacy Policy' },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFoundView.vue'),
      meta: { title: '404 Not Found' },
    },
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    return { top: 0 }
  },
})



// Navigation guards

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const toast = useToast()

  document.title = to.meta.title
    ? `${to.meta.title} - Fursa Energy`
    : 'Fursa Energy'

  const isAuthenticated = authStore.isAuthenticated
  const isVerified = authStore.user?.email_verified_at !== null

  // Always allow verification routes
  if (to.name === 'verify' || to.name === 'email.verify') {
    return next()
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Not logged in, redirect to login
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  if (to.meta.requiresAuth && isAuthenticated && !isVerified) {
    // Logged in but not verified, block access to other pages
    toast.warning('Please verify your account to continue.')
    return next({ name: 'verify' })
  }

  if (to.meta.guest && isAuthenticated) {
    // Logged-in user trying to access guest-only routes (login, register, etc.)
    return next({ name: 'dashboard' })
  }

  next()
})

// router.beforeEach((to, from, next) => {
//   const authStore = useAuthStore()
//   const toast = useToast()

//   document.title = to.meta.title ? `${to.meta.title} - Fursa Energy` : 'Fursa Energy'

//   const isAuthenticated = authStore.isAuthenticated
//   const isVerified = authStore.user?.email_verified_at !== null

//   if (to.meta.requiresAuth && !isAuthenticated) {
 
//     next({ name: 'login', query: { redirect: to.fullPath } })
//   } 
//   else if (to.meta.requiresAuth && isAuthenticated && !isVerified && to.name !== 'verify') {
 
//     toast.warning("Please verify your account to continue.")
//     router.push('/verify')
//     // next({ name: 'verify-email' })
//   } 
//   else if (to.meta.guest && isAuthenticated) {
//     // Logged in user trying to access guest-only routes
//     next({ name: 'dashboard' })
//   } 
//   else {
//     next()
//   }
// })


// router.beforeEach((to, from, next) => {
//   const authStore = useAuthStore()

//   // Set page title
//   document.title = to.meta.title ? `${to.meta.title} - Fursa Energy` : 'Fursa Energy'

//   // Check authentication
//   if (to.meta.requiresAuth && !authStore.isAuthenticated) {
//     next({ name: 'login', query: { redirect: to.fullPath } })
//   } else if (to.meta.guest && authStore.isAuthenticated) {
//     next({ name: 'dashboard' })
//   } else {
//     next()
//   }
// })

export default router
