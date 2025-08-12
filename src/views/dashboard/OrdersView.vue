<template>
    <DashboardLayout>
        <div class="space-y-6">
            <h1 class="text-2xl font-bold">Your Orders</h1>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex gap-4 overflow-x-auto">
                    <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
                        class="px-4 py-2 rounded-lg whitespace-nowrap transition"
                        :class="activeTab === tab.value ? 'bg-primary text-white' : 'bg-gray-100 hover:bg-gray-200'">
                        {{ tab.label }}
                        <span class="ml-2 badge">{{ tab.count }}</span>
                    </button>
                </div>
            </div>

            <!-- Orders List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order Id
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ order.id }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-500">{{ order.date }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="getStatusClass(order.status)">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">₦{{ order.total.toLocaleString() }}</span>
                                    <span class="text-xs text-gray-500 block">for {{ order.items }} items</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="viewOrder(order.id)"
                                        class="text-primary hover:text-opacity-80 mr-3">
                                        <font-awesome-icon icon="eye" />
                                    </button>
                                    <button @click="trackOrder(order.id)"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        <font-awesome-icon icon="map-marker-alt" />
                                    </button>
                                    <button v-if="order.status === 'Completed'" @click="reorder(order.id)"
                                        class="text-green-600 hover:text-green-800">
                                        <font-awesome-icon icon="redo" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalOrders)
                        }} of {{ totalOrders }} orders
                    </div>
                    <div class="flex gap-2">
                        <button @click="currentPage--" :disabled="currentPage === 1"
                            class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                            <font-awesome-icon icon="chevron-left" />
                        </button>
                        <button v-for="page in totalPages" :key="page" @click="currentPage = page"
                            class="px-3 py-1 rounded border"
                            :class="currentPage === page ? 'bg-primary text-white' : 'hover:bg-gray-100'">
                            {{ page }}
                        </button>
                        <button @click="currentPage++" :disabled="currentPage === totalPages"
                            class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                            <font-awesome-icon icon="chevron-right" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const router = useRouter()

const activeTab = ref('all')
const currentPage = ref(1)
const perPage = ref(10)

const tabs = ref([
    { value: 'all', label: 'All Orders', count: 23 },
    { value: 'processing', label: 'Processing', count: 5 },
    { value: 'shipped', label: 'Shipped', count: 3 },
    { value: 'completed', label: 'Completed', count: 15 },
    { value: 'cancelled', label: 'Cancelled', count: 0 }
])

const orders = ref([
    { id: '#2345', date: 'January 7, 2025', status: 'Processing', total: 250000, items: 7 },
    { id: '#2346', date: 'January 7, 2025', status: 'Completed', total: 430000, items: 6 },
    { id: '#2347', date: 'January 6, 2025', status: 'Shipped', total: 320000, items: 4 },
    { id: '#2348', date: 'January 5, 2025', status: 'Completed', total: 150000, items: 2 },
    // Add more orders as needed
])

const filteredOrders = computed(() => {
    if (activeTab.value === 'all') {
        return orders.value
    }
    return orders.value.filter(order =>
        order.status.toLowerCase() === activeTab.value
    )
})

const totalOrders = computed(() => filteredOrders.value.length)
const totalPages = computed(() => Math.ceil(totalOrders.value / perPage.value))

const getStatusClass = (status) => {
    const classes = {
        'Processing': 'bg-yellow-100 text-yellow-800',
        'Completed': 'bg-green-100 text-green-800',
        'Cancelled': 'bg-red-100 text-red-800',
        'Shipped': 'bg-blue-100 text-blue-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const viewOrder = (orderId) => {
    router.push(`/dashboard/orders/${orderId}`)
}

const trackOrder = (orderId) => {
    router.push(`/dashboard/track-order/${orderId}`)
}

const reorder = (orderId) => {
    // Implement reorder logic
    console.log('Reorder:', orderId)
}
</script>

<style scoped>
.badge {
    @apply bg-white bg-opacity-30 text-xs rounded-full px-2 py-0.5 ml-1;
}
</style>