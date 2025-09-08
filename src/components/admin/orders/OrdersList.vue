<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="text-lg font-medium text-gray-900">Orders</h3>
      <div>
        <slot name="actions" />
      </div>
    </div>

    <div class="p-4">
      <div v-if="loading" class="text-sm text-gray-500">Loading orders...</div>
      <div v-else>
        <table class="min-w-full text-left text-sm">
          <thead>
            <tr class="text-xs text-gray-500">
              <th class="py-2">#</th>
              <th class="py-2">Customer</th>
              <th class="py-2">Email</th>
              <th class="py-2">Total</th>
              <th class="py-2">Status</th>
              <th class="py-2">Placed</th>
              <th class="py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id" class="border-t border-gray-100">
              <td class="py-2">{{ order.id }}</td>
              <td class="py-2">{{ order.customer || order.customer_name || (order.user && (order.user.first_name + ' ' +
                order.user.last_name)) }}</td>
              <td class="py-2">{{ order.email || (order.user && order.user.email) }}</td>
              <td class="py-2">{{ formatCurrency(order.total || order.total_amount) }}</td>
              <td class="py-2"><span class="inline-block px-2 py-1 text-xs rounded"
                  :class="statusClass(order.status)">{{ order.status }}</span></td>
              <td class="py-2">{{ formatDate(order.created_at) }}</td>
              <td class="py-2">
                <slot name="row-actions" :order="order">
                  <router-link :to="{ name: 'admin.orders.detail', params: { id: order.id } }"
                    class="text-primary-600">View</router-link>
                </slot>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="mt-4">
          <slot name="pagination" :pagination="pagination" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ orders: { type: Array, default: () => [] }, loading: { type: Boolean, default: false }, pagination: { type: Object, default: () => ({}) } })

const formatCurrency = (v) => {
  if (v == null) return '0'
  return Number(v).toLocaleString(undefined, { style: 'currency', currency: 'NGN' })
}

const formatDate = (d) => {
  if (!d) return '-'
  const dt = new Date(d)
  return dt.toLocaleString()
}

const statusClass = (status) => {
  if (!status) return 'bg-gray-100 text-gray-700'
  switch (status.toLowerCase()) {
    case 'pending': return 'bg-yellow-100 text-yellow-800'
    case 'completed': return 'bg-green-100 text-green-800'
    case 'cancelled': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-700'
  }
}
</script>
