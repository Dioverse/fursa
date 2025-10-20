<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-2">
          Hello {{ user?.first_name }}!
          <button @click="handleLogout" class="text-primary hover:underline text-base font-normal ml-2">
            Log Out
          </button>
        </h1>
        <p class="text-gray-600">
          From your account dashboard you can view your recent orders, manage your shipping and billing addresses,
          and edit your password and account details.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard :value="stats.ongoingOrders" label="On Going Order" icon="truck" />
        <StatsCard :value="stats.cartItems" label="Product in cart" icon="shopping-cart" />
        <StatsCard :value="stats.wishlistItems" label="Product in wishlist" icon="heart" />
        <StatsCard :value="stats.totalOrders" label="Product ordered" icon="box" />
      </div>

      <!-- Recent Orders -->
      <RecentOrders :orders="recentOrders" />

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
          <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <font-awesome-icon icon="shield-alt" size="2x" class="text-yellow-600" />
          </div>
          <h3 class="font-semibold mb-2">Secure Payment Gateways</h3>
          <p class="text-sm text-gray-600">48+ gateways to ensure your security.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <font-awesome-icon icon="star" size="2x" class="text-blue-600" />
          </div>
          <h3 class="font-semibold mb-2">Genuine Customer Reviews</h3>
          <p class="text-sm text-gray-600">Find verified reviews showcased on our platforms</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <font-awesome-icon icon="headset" size="2x" class="text-green-600" />
          </div>
          <h3 class="font-semibold mb-2">24/7 Customer Support</h3>
          <p class="text-sm text-gray-600">Always our support team is available for you.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition cursor-pointer">
          <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <font-awesome-icon icon="undo" size="2x" class="text-purple-600" />
          </div>
          <h3 class="font-semibold mb-2">Easy Return Policy</h3>
          <p class="text-sm text-gray-600">If you're not satisfied, return it hassle-free.</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import StatsCard from '@/components/dashboard/StatsCard.vue'
import RecentOrders from '@/components/dashboard/RecentOrders.vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import api from '@/services/api'
import axios from 'axios'

const baseUrl = import.meta.env.VITE_API_BASE_URL
const token = localStorage.getItem('token')

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()
const cartStore = useCartStore()

const user = computed(() => authStore.user)
const recentOrders = ref([])
const loading = ref(false)
const error = ref(null)

const stats = ref({
  ongoingOrders: 0,
  cartItems: cartStore.itemCount,
  wishlistItems: 0,
  totalOrders: 0
})

const fetchDashboard = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`${baseUrl}/dashboard`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    stats.value = {
      ongoingOrders: Number(data.data.orders_summary.pending ?? 0),
      cartItems: Number(cartStore.itemCount),
      wishlistItems: Number(data.data.wishlistItems ?? 0),
      totalOrders: Number(data.data.orders_summary.total ?? 0)
    }

    recentOrders.value = data.data.recent_orders || []
  } catch (err) {
    console.error('Failed to fetch dashboard:', err)
    error.value = 'Could not load dashboard stats'
  } finally {
    loading.value = false
  }
}

async function syncCart() {
  try {
    if (cartStore.items.length > 0) {
      const payload = {
        cart: cartStore.items.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
        })),
      }

      await api.post('/carts', payload)
      // toast.success('Cart synchronized with server')
    }

    // Fetch fresh cart from server to overwrite local copy
    const res = await api.get('/carts')
    if (res.data && res.data.data) {
      cartStore.items = res.data.data.map((item) => ({
        id: item.product_id,
        name: item.product?.name,
        price: item.product?.discounted_price,
        sku: item.product?.sku,
        image: item.product?.image,
        volume: item.product?.volume || '5 Litres',
        quantity: item.quantity,
      }))
      cartStore.saveCart()
    }
  } catch (error) {
    // console.error('Cart sync failed:', error)
    // toast.error('Failed to synchronize cart')
  }
}

const handleLogout = () => {
  authStore.logout()
  toast.success('Logged out successfully')
  router.push('/')
}

onMounted(() => {
  syncCart()
  fetchDashboard()
})

</script>