<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-lg md:text-xl lg:text-2xl font-bold mb-2">
          Hello {{ user?.first_name }}!
          <button @click="handleLogout" class="text-primary text-sm md:text-md lg:text-md hover:underline text-base font-normal ml-2">
            Log Out
          </button>
        </h1>
        <p class="text-gray-600">
          From your account dashboard you can view your recent orders, manage your shipping and billing addresses,
          and edit your password and account details.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl font-bold text-gray-800 mb-2">{{ value }}</div>
            <div class="text-gray-600">Product<br class="hidden lg:block"/>&nbsp;in cart</div>
            <div class="mt-4">
                <font-awesome-icon icon="shopping-cart" size="2x" class="text-primary opacity-50" />
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl font-bold text-gray-800 mb-2">{{ value }}</div>
            <div class="text-gray-600">Product<br class="hidden lg:block"/>&nbsp;in wishlist</div>
            <div class="mt-4">
                <font-awesome-icon icon="heart" size="2x" class="text-primary opacity-50" />
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl font-bold text-gray-800 mb-2">{{ value }}</div>
            <div class="text-gray-600">Product <br class="hidden lg:block"/>&nbsp;Ordered</div>
            <div class="mt-4">
                <font-awesome-icon icon="box" size="2x" class="text-primary opacity-50" />
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-4xl font-bold text-gray-800 mb-2">{{ value }}</div>
            <div class="text-gray-600">On Going<br class="hidden lg:block"/>&nbsp;Order</div>
            <div class="mt-4">
                <font-awesome-icon icon="truck" size="2x" class="text-primary opacity-50" />
            </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <RecentOrders :orders="recentOrders" />
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