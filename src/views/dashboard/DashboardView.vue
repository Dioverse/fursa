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
                    From your account dashboard you can view your recent orders, manage your shipping and billing
                    addresses,
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
import ordersService from '@/services/orders.service'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()
const cartStore = useCartStore()

const user = computed(() => authStore.user)
const recentOrders = ref([])

const stats = ref({
    ongoingOrders: 18,
    cartItems: cartStore.itemCount,
    wishlistItems: 0,
    totalOrders: 5
})

const handleLogout = () => {
    authStore.logout()
    toast.success('Logged out successfully')
    router.push('/')
}

onMounted(async () => {
    try {
        // Fetch recent orders - using mock data for now
        recentOrders.value = [
            { id: '#2345', date: 'January 7, 2025', status: 'Processing', total: 250000, items: 7 },
            { id: '#2345', date: 'January 7, 2025', status: 'Completed', total: 430000, items: 6 },
            { id: '#2345', date: 'January 7, 2025', status: 'Completed', total: 430000, items: 6 },
            { id: '#2345', date: 'January 7, 2025', status: 'Completed', total: 430000, items: 6 }
        ]
    } catch (error) {
        console.error('Failed to load dashboard data:', error)
    }
})
</script>