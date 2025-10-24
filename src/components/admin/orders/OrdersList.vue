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
              <td class="py-2">{{ order.order_id }}</td>
              <td class="py-2">{{ (order.user && (order.user.first_name + ' ' + order.user.last_name)) || 'John Doe' }}
              </td>
              <td class="py-2">{{ order.email || (order.user && order.user.email) || 'email@example.com' }}</td>
              <td class="py-2">{{ formatCurrency(order.total || order.total_amount) }}</td>
              <td class="py-2"><span class="text-capitalize inline-block px-2 py-1 text-xs rounded"
                  :class="statusClass(order.status)">{{ order.status }}</span></td>
              <td class="py-2">{{ formatDate(order.created_at) }}</td>
              <td class="py-2">
                <slot name="row-actions" :order="order">
                  <router-link :to="{ name: 'admin.orders.detail', params: { id: order.id } }"
                    class="inline-flex items-center">
                    <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                      aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="sr-only">View order {{ order.order_id }}</span>
                  </router-link>
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
    case 'delivered': return 'bg-green-100 text-green-800'
    case 'cancelled': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-700'
  }
}
</script>
