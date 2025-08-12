<template>
    <aside class="w-64 bg-white shadow-md">
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <RouterLink to="/dashboard"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition"
                        :class="{ 'bg-primary text-white': $route.path === '/dashboard' }">
                        <font-awesome-icon icon="dashboard" />
                        <span>Dashboard</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/dashboard/orders"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition"
                        :class="{ 'bg-primary text-white': $route.path === '/dashboard/orders' }">
                        <font-awesome-icon icon="box" />
                        <span>Orders</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/dashboard/track-order"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition">
                        <font-awesome-icon icon="search" />
                        <span>Track your Order</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/dashboard/profile"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition"
                        :class="{ 'bg-primary text-white': $route.path === '/dashboard/profile' }">
                        <font-awesome-icon icon="user" />
                        <span>Account Details</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/dashboard/addresses"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition">
                        <font-awesome-icon icon="map-marker-alt" />
                        <span>My Address</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/dashboard/wishlist"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition"
                        :class="{ 'bg-primary text-white': $route.path === '/dashboard/wishlist' }">
                        <font-awesome-icon icon="heart" />
                        <span>Wishlist</span>
                    </RouterLink>
                </li>
                <li>
                    <button @click="handleLogout"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition text-left">
                        <font-awesome-icon icon="sign-out" />
                        <span>Log Out</span>
                    </button>
                </li>
            </ul>
        </nav>
    </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const handleLogout = () => {
    authStore.logout()
    toast.success('Logged out successfully')
    router.push('/')
}
</script>