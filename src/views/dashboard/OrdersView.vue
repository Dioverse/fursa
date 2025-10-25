<template>
  <DashboardLayout>
<<<<<<< Updated upstream
    <div class="space-y-6">
      <h1 class="lg:text-2xl md:text-xl text-lg font-bold">{{ $t('orders.title') }}</h1>
=======
    <div class="w-full space-y-6 text-sm md:text-md">
      <h1 class="lg:text-2xl md:text-xl text-lg font-bold">My Orders</h1>
>>>>>>> Stashed changes

      <!-- Filter Tabs -->
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex gap-4 overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="setActiveTab(tab.value)"
            class="px-4 py-2 rounded-lg whitespace-nowrap transition flex items-center gap-2"
            :class="activeTab === tab.value ? 'bg-primary text-white' : 'bg-gray-100 hover:bg-gray-200'"
          >
            {{ tab.label }}
            <span class="ml-1 bg-white text-gray-700 text-xs px-2 py-0.5 rounded-full">
              {{ tab.count }}
            </span>
          </button>
        </div>
      </div>

      <!-- Orders List -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table v-if="filteredOrders.length > 0" class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $t('orders.table.order_id') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $t('orders.table.date') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $t('orders.table.status') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $t('orders.table.total') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $t('orders.table.actions') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="(order, index) in paginatedOrders"
                :key="order.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ (currentPage - 1) * perPage + index + 1 }}
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                  {{ order.id }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatDate(order.created_at) }}
                </td>
                <td class="px-6 py-4">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusClass(order.status)"
                  >
                    {{ getStatusText(order.status) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="text-sm text-gray-900">
                    ₦{{ Number(order.total_amount).toFixed(2) }}
                  </span>
                  <span class="text-xs text-gray-500 block">
                    {{ $t('orders.for_items', { count: order.order_items_count || 0 }) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium space-x-3">
                  <button @click="viewOrder(order.order_id)" class="text-primary hover:text-opacity-80" :title="$t('orders.actions.view')">
                    <font-awesome-icon icon="eye" />
                  </button>
                  <button @click="trackOrder(order.order_id)" class="text-blue-600 hover:text-blue-800" :title="$t('orders.actions.track')">
                    <font-awesome-icon icon="map-marker-alt" />
                  </button>
                  <button
                    v-if="order.status.toLowerCase() === 'completed'"
                    @click="reorder(order.id)"
                    class="text-green-600 hover:text-green-800"
                    :title="$t('orders.actions.reorder')"
                  >
                    <font-awesome-icon icon="redo" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty State -->
<<<<<<< Updated upstream
          <div v-else class="p-6 text-center text-gray-500">
            {{ $t('orders.empty') }}
=======
          <div v-else class="flex justify-center items-center p-6 text-center text-gray-500 min-h-[200px]">
            No orders found for this filter.
>>>>>>> Stashed changes
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalOrders > 0" class="px-6 py-4 border-t flex items-center justify-between">
          <div class="text-sm text-gray-600">
            {{ $t('orders.pagination.summary', { from: (currentPage - 1) * perPage + 1, to: Math.min(currentPage * perPage, totalOrders), total: totalOrders }) }}
          </div>
          <div class="flex gap-2">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50"
            >
              <font-awesome-icon icon="chevron-left" />
            </button>
            <button
              v-for="page in totalPages"
              :key="page"
              @click="currentPage = page"
              class="px-3 py-1 rounded border"
              :class="currentPage === page ? 'bg-primary text-white' : 'hover:bg-gray-100'"
            >
              {{ page }}
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50"
            >
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
import { useI18n } from 'vue-i18n'

const router = useRouter()
const { t, locale } = useI18n()
const activeTab = ref('all')
const currentPage = ref(1)
const perPage = ref(10)
const orders = ref([])
const loading = ref(false)
const error = ref(null)

// Tabs (reactive to language changes)
const tabs = computed(() => ([
  { value: 'all', label: t('orders.tabs.all'), count: counts.value.all },
  { value: 'processing', label: t('orders.tabs.processing'), count: counts.value.processing },
  { value: 'shipped', label: t('orders.tabs.shipped'), count: counts.value.shipped },
  { value: 'completed', label: t('orders.tabs.completed'), count: counts.value.completed },
  { value: 'cancelled', label: t('orders.tabs.cancelled'), count: counts.value.cancelled }
]))

// Store per-tab counts separately so labels re-render cleanly
const counts = ref({ all: 0, processing: 0, shipped: 0, completed: 0, cancelled: 0 })

// Fetch Orders
const fetchOrders = async () => {
  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/orders`, {
      headers: { Authorization: `Bearer ${token}` }
    })

    if (!response.ok) throw new Error('Failed to fetch orders')

    const data = await response.json()
    orders.value = data.data || []

    // update tab counts
    const map = { processing: 0, shipped: 0, completed: 0, cancelled: 0 }
    for (const o of orders.value) {
      const key = (o.status || '').toLowerCase()
      if (map[key] !== undefined) map[key]++
    }
    counts.value = {
      all: orders.value.length,
      processing: map.processing,
      shipped: map.shipped,
      completed: map.completed,
      cancelled: map.cancelled,
    }
  } catch (err) {
    console.error(err)
    error.value = err.message
  } finally {
    loading.value = false
  }
}

// Filters
const filteredOrders = computed(() =>
  activeTab.value === 'all'
    ? orders.value
    : orders.value.filter(order => order.status.toLowerCase() === activeTab.value)
)

const totalOrders = computed(() => filteredOrders.value.length)

const totalPages = computed(() => Math.ceil(totalOrders.value / perPage.value))

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredOrders.value.slice(start, start + perPage.value)
})

// Helpers
const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  const loc = locale.value || 'en'
  return date.toLocaleDateString(loc, {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
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

const getStatusText = (status) => {
  const key = (status || '').toLowerCase()
  const map = {
    processing: t('orders.status.processing'),
    completed: t('orders.status.completed'),
    cancelled: t('orders.status.cancelled'),
    shipped: t('orders.status.shipped'),
  }
  return map[key] || status
}

// Actions
const setActiveTab = (tab) => {
  activeTab.value = tab
  currentPage.value = 1
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const viewOrder = (id) => router.push(`/dashboard/orders/${id}`)
const trackOrder = (id) => router.push(`/dashboard/track-order/${id}`)
const reorder = (id) => console.log('Reorder:', id)

// Lifecycle
onMounted(fetchOrders)
</script>
