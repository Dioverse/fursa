<template>
  <DashboardLayout>
    <div class="space-y-6">
      <h1 class="lg:text-2xl md:text-xl text-lg font-bold">Track Order</h1>

      <!-- Loading -->
      <div v-if="loading" class="p-6 text-center text-gray-500">
        Loading order tracking...
      </div>

      <!-- Error -->
      <div v-else-if="error" class="p-6 text-center text-red-500">
        {{ error }}
      </div>

      <!-- Order Details -->
      <div v-else class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div>
              <h2 class="text-lg font-semibold">Order #{{ order.id }}</h2>
              <p class="text-sm text-gray-600">
                Placed on {{ formatDate(order.created_at) }}
              </p>
            </div>
            <div>
              <span
                class="px-3 py-1 rounded-full text-xs font-medium"
                :class="getStatusClass(order.status)"
              >
                {{ order.status }}
              </span>
            </div>
          </div>
          <div class="mt-4 text-sm text-gray-700">
            <p><strong>Total:</strong> ₦{{ formatAmount(order.total_amount, 2) }}</p>
            <p><strong>Items:</strong> {{ order.order_items_count || 0 }}</p>
          </div>
        </div>

        <!-- Tracking Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold mb-4">Tracking Updates</h3>
          <ol class="relative border-l border-gray-200">
            <li v-for="(step, index) in tracking" :key="index" class="mb-10 ml-6">
              <!-- Circle -->
              <span
                class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full ring-8 ring-white"
                :class="step.completed ? 'bg-green-500' : 'bg-gray-300'"
              >
                <font-awesome-icon
                  v-if="step.completed"
                  icon="check"
                  class="text-white text-xs"
                />
              </span>

              <!-- Content -->
              <h4 class="font-medium text-gray-900">
                {{ step.status }}
              </h4>
              <p class="text-sm text-gray-500">{{ formatDate(step.timestamp) }}</p>
              <p v-if="step.note" class="text-sm text-gray-600">{{ step.note }}</p>
            </li>
          </ol>

          <div v-if="tracking.length === 0" class="text-gray-500 text-sm">
            No tracking updates available yet.
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { formatAmount } from '@/utils/helpers'

const route = useRoute()
const orderId = route.params.id

const order = ref({})
const tracking = ref([])
const loading = ref(false)
const error = ref(null)

const fetchTracking = async () => {
  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/orders/${orderId}/track`,
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )

    if (!response.ok) throw new Error('Failed to fetch tracking details')

    const data = await response.json()
    order.value = data.order || {}
    tracking.value = data.tracking || []
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusClass = (status) => {
  const classes = {
    processing: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    shipped: 'bg-blue-100 text-blue-800'
  }
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

onMounted(fetchTracking)
</script>
