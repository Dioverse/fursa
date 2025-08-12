<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Recent Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button @click="viewOrder(order.id)" class="text-primary hover:text-opacity-80 transition">
                                <font-awesome-icon icon="eye" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            <button class="text-primary hover:underline flex items-center gap-2 mx-auto">
                <span>Load More</span>
                <font-awesome-icon icon="arrow-right" />
            </button>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
})

const router = useRouter()

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
</script>