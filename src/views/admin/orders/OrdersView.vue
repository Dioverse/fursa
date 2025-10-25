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
        <div class="mt-2 text-2xl font-semibold">{{ statusCounts.confirmed || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm text-gray-600">Search by Name</label>
          <input v-model="filters.order_user_search" @input="onSearchInput"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500"
            placeholder="Order ID, customer last name..." />
        </div>

        <div>
          <label class="block text-sm text-gray-600">Status</label>
          <select v-model="filters.status"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="processing">Processing</option>
            <option value="shipping">Shipping</option>
            <option value="shipped">Shipped</option>
            <option value="out for delivery">Out for Delivery</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
            <option value="failed">Failed</option>
            <option value="expired">Expired</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Min Amount (₦)</label>
          <input type="number" v-model="filters.min_amount" @input="debouncedSearch"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500"
            placeholder="0" min="0" />
        </div>

        <div>
          <label class="block text-sm text-gray-600">Max Amount (₦)</label>
          <input type="number" v-model="filters.max_amount" @input="debouncedSearch"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500"
            placeholder="No limit" min="0" />
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
        <div>
          <label class="block text-sm text-gray-600">Sort By</label>
          <select v-model="filters.sort_by"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <option value="created_at">Date Created</option>
            <option value="order_id">Order ID</option>
            <option value="total_amount">Amount</option>
            <option value="status">Status</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Order</label>
          <select v-model="filters.sort_order"
            class="mt-1 block w-full rounded-md border-gray-200 shadow-sm focus:ring-primary-500 focus:border-primary-500">
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>
        </div>
      </div>

      <div class="mt-4 flex items-center space-x-2">
        <button @click="applyFilters"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Apply</button>
        <button @click="clearFilters"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</button>
      </div>
    </div>

    <!-- Orders list -->
    <OrdersList :orders="ordersStore.orders" :loading="ordersStore.loading" :pagination="ordersStore.pagination">
      <template #actions>
        <button type="button" disabled
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-500 bg-white cursor-not-allowed"
          title="Order creation coming soon">
          New Order
        </button>
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

// Filters matching backend API
const filters = reactive({
  order_user_search: '',
  status: '',
  min_amount: '',
  max_amount: '',
  sort_by: 'created_at',
  sort_order: 'desc'
})

let searchTimer = null

const fetchWithFilters = (page = 1) => {
  const params = { page }
  if (filters.order_user_search) params.order_user_search = filters.order_user_search
  if (filters.status) params.status = filters.status
  if (filters.min_amount) params.min_amount = filters.min_amount
  if (filters.max_amount) params.max_amount = filters.max_amount
  if (filters.sort_by) params.sort_by = filters.sort_by
  if (filters.sort_order) params.sort_order = filters.sort_order
  return ordersStore.fetchOrders(params).catch(() => { })
}

const applyFilters = () => fetchWithFilters(1)

const clearFilters = () => {
  filters.order_user_search = ''
  filters.status = ''
  filters.min_amount = ''
  filters.max_amount = ''
  filters.sort_by = 'created_at'
  filters.sort_order = 'desc'
  fetchWithFilters(1)
}

const debouncedSearch = () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    fetchWithFilters(1)
  }, 500)
}

const onSearchInput = () => {
  debouncedSearch()
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
  return ordersStore.orders.reduce((acc, o) => acc + (parseFloat(o?.total_amount || o?.total || 0) || 0), 0)
})
const statusCounts = computed(() => {
  const counts = { pending: 0, confirmed: 0, cancelled: 0, delivered: 0 }
  if (!Array.isArray(ordersStore.orders)) return counts
  ordersStore.orders.forEach((o) => {
    const s = (o?.status || '').toLowerCase()
    if (s === 'pending') counts.pending++
    else if (s === 'confirmed' || s === 'processing' || s === 'shipped') counts.confirmed++
    else if (s === 'cancelled') counts.cancelled++
    else if (s === 'delivered') counts.delivered++
  })
  return counts
})

const formatCurrency = (v) => {
  if (v == null) return '₦0'
  return Number(v).toLocaleString('en-NG', { style: 'currency', currency: 'NGN' })
}
</script>