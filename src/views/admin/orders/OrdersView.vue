<template>
  <div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-sm text-gray-500">Total Orders</div>
        <div class="mt-2 text-2xl font-semibold">{{ totalOrders }}</div>
      </div>

      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-sm text-gray-500">Total Revenue</div>
        <div class="mt-2 text-2xl font-semibold">{{ formatCurrency(totalRevenue) }}</div>
      </div>

      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-sm text-gray-500">Pending</div>
        <div class="mt-2 text-2xl font-semibold">{{ statusCounts.pending || 0 }}</div>
      </div>

      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-sm text-gray-500">Completed</div>
        <div class="mt-2 text-2xl font-semibold">{{ statusCounts.completed || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm text-gray-600">Search</label>
          <input v-model="filters.q" @input="onSearchInput"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500"
            placeholder="Customer name, email or order id" />
        </div>

        <div>
          <label class="block text-sm text-gray-600">Status</label>
          <select v-model="filters.status"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
            <option value="refunded">Refunded</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">From</label>
          <input type="date" v-model="filters.from_date"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500" />
        </div>

        <div>
          <label class="block text-sm text-gray-600">To</label>
          <input type="date" v-model="filters.to_date"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500" />
        </div>
      </div>

      <div class="mt-4 flex items-center space-x-2">
        <button @click="applyFilters"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600">Apply</button>
        <button @click="clearFilters"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white">Clear</button>
      </div>
    </div>

    <!-- Orders list -->
    <OrdersList :orders="ordersStore.orders" :loading="ordersStore.loading" :pagination="ordersStore.pagination">
      <template #actions>
        <router-link to="{ name: 'admin.orders.create' }"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600">New
          Order</router-link>
      </template>

      <template #pagination>
        <TablePagination v-bind="ordersStore.pagination" @pageChange="handlePageChange" />
      </template>
    </OrdersList>
  </div>
</template>

<script setup>
import OrdersList from '@/components/admin/orders/OrdersList.vue'
import TablePagination from '@/components/common/TablePagination.vue'
import { useOrdersStore } from '@/stores/orders'
import { onMounted, reactive, computed } from 'vue'

const ordersStore = useOrdersStore()

// Filters
const filters = reactive({ q: '', status: '', from_date: '', to_date: '' })
let searchTimer = null

const fetchWithFilters = (page = 1) => {
  const params = { page }
  if (filters.q) params.q = filters.q
  if (filters.status) params.status = filters.status
  if (filters.from_date) params.from_date = filters.from_date
  if (filters.to_date) params.to_date = filters.to_date
  return ordersStore.fetchOrders(params).catch(() => { })
}

const applyFilters = () => fetchWithFilters(1)
const clearFilters = () => {
  filters.q = ''
  filters.status = ''
  filters.from_date = ''
  filters.to_date = ''
  fetchWithFilters(1)
}

const onSearchInput = () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    fetchWithFilters(1)
  }, 450)
}

const handlePageChange = (page) => {
  fetchWithFilters(page)
}

onMounted(() => {
  fetchWithFilters().catch(() => { })
})

// Derived stats from currently loaded orders (page data)
const totalOrders = computed(() => Number(ordersStore.pagination?.total ?? (Array.isArray(ordersStore.orders) ? ordersStore.orders.length : 0)))
const totalRevenue = computed(() => {
  if (!Array.isArray(ordersStore.orders)) return 0
  return ordersStore.orders.reduce((acc, o) => acc + (parseFloat(o?.total || o?.total_amount || 0) || 0), 0)
})
const statusCounts = computed(() => {
  const counts = { pending: 0, completed: 0, cancelled: 0 }
  if (!Array.isArray(ordersStore.orders)) return counts
  ordersStore.orders.forEach((o) => {
    const s = (o?.status || '').toLowerCase()
    if (s.includes('pending')) counts.pending++
    else if (s.includes('completed')) counts.completed++
    else if (s.includes('cancel')) counts.cancelled++
  })
  return counts
})

const formatCurrency = (v) => {
  if (v == null) return '0'
  return Number(v).toLocaleString(undefined, { style: 'currency', currency: 'NGN' })
}
</script>
