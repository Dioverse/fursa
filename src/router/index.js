import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'


// Lazy load views
const HomeView = () => import('@/views/HomeView.vue')
const LoginView = () => import('@/views/auth/LoginView.vue')
const RegisterView = () => import('@/views/auth/RegisterView.vue')
const DashboardView = () => import('@/views/dashboard/DashboardView.vue')
const OrdersView = () => import('@/views/dashboard/OrdersView.vue')
const ProfileView = () => import('@/views/dashboard/ProfileView.vue')
const WishlistView = () => import('@/views/dashboard/WishlistView.vue')
const ShopView = () => import('@/views/shop/ShopView.vue')
const ProductDetailView = () => import('@/views/shop/ProductDetailView.vue')
const CategoryView = () => import('@/views/shop/CategoryView.vue')
const CartView = () => import('@/views/cart/CartView.vue')
const CheckoutView = () => import('@/views/cart/CheckoutView.vue')
const DistributorRegistrationView = () =>
  import('@/views/distributor/DistributorRegistrationView.vue')
const VerifyView = () => import('@/views/auth/VerifyView.vue')
// Additional views
const AboutView = () => import('@/views/AboutView.vue')
const ContactView = () => import('@/views/ContactView.vue')
const BlogView = () => import('@/views/BlogView.vue')
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
      meta: { title: 'Verify', guest: true },
    },
    {
      path: '/shop',
      name: 'shop',
      component: ShopView,
      meta: { title: 'Shop' },
    },
    {
      path: '/shop/category/:slug',
      name: 'category',
      component: CategoryView,
      meta: { title: 'Category' },
    },
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

  // Set page title
  document.title = to.meta.title ? `${to.meta.title} - Fursa Energy` : 'Fursa Energy'

  // Check authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
