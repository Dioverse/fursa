<template>
  <div class="overflow-hidden">
    <div v-if="loading" class="p-6">
      <div class="space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center space-x-4">
          <div class="loading-skeleton w-12 h-12 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="loading-skeleton h-4 w-24"></div>
            <div class="loading-skeleton h-3 w-32"></div>
          </div>
          <div class="loading-skeleton h-6 w-16 rounded-full"></div>
        </div>
      </div>
    </div>

    <div v-else-if="!orders || orders.length === 0" class="p-12 text-center text-gray-500">
      <font-awesome-icon icon="shopping-cart" class="h-12 w-12 text-gray-300 mb-4" />
      <p>No recent orders</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Order
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Customer
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Amount
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition-colors duration-200">
            <!-- Order ID -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="text-sm font-medium text-gray-900">
                  {{ order.id }}
                </div>
              </div>
            </td>

            <!-- Customer -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8">
                  <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-xs font-medium text-primary-600">
                      {{ getCustomerInitials(order.customer) }}
                    </span>
                  </div>
                </div>
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900">
                    {{ order.customer }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ order.email }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Amount -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ formatCurrency(order.total) }}
              </div>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusClass(order.status)">
                <div class="w-1.5 h-1.5 rounded-full mr-1.5" :class="getStatusDotClass(order.status)">
                </div>
                {{ getStatusLabel(order.status) }}
              </span>
            </td>

            <!-- Date -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(order.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end space-x-2">
                <button @click="viewOrder(order.id)" class="text-primary-600 hover:text-primary-900 transition-colors"
                  title="View Order">
                  <font-awesome-icon icon="eye" class="h-4 w-4" />
                </button>

                <button @click="editOrder(order.id)" class="text-gray-600 hover:text-gray-900 transition-colors"
                  title="Edit Order">
                  <font-awesome-icon icon="edit" class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { formatDistanceToNow } from 'date-fns'

// Props
const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Composables
const router = useRouter()

// Methods
const getCustomerInitials = (name) => {
  return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase()
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const formatDate = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusDotClass = (status) => {
  const classes = {
    pending: 'bg-yellow-400',
    processing: 'bg-blue-400',
    shipped: 'bg-purple-400',
    delivered: 'bg-green-400',
    cancelled: 'bg-red-400'
  }
  return classes[status] || 'bg-gray-400'
}

const viewOrder = (orderId) => {
  router.push({
    name: 'admin.orders.detail',
    params: { id: orderId }
  })
}

const editOrder = (orderId) => {
  router.push({
    name: 'admin.orders.edit',
    params: { id: orderId }
  })
}
</script>
