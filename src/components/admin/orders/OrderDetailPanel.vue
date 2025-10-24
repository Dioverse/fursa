<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="text-lg font-medium text-gray-900">Order #{{ order?.order_id || order?.id }}</h3>
      <div class="flex items-center space-x-3">
        <!-- Print button -->
        <button @click="printOrder" type="button"
          class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
          <!-- Eye/print icon svg -->
          <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h4l2-2h2l2 2h4a2 2 0 012 2v12a2 2 0 01-2 2z" />
          </svg>
          Print
        </button>

        <!-- Delivery date input -->
        <div class="flex items-center space-x-2">
          <input v-model="deliveryDate" type="date" class="border rounded-md px-2 py-1 text-sm" />
          <button @click="saveDeliveryDate" :disabled="savingDelivery || !deliveryDate" type="button"
            class="inline-flex items-center px-3 py-1.5 border border-primary-600 rounded-md text-sm text-white bg-primary-600 hover:bg-primary-700">
            Save
          </button>
        </div>

        <div>
          <slot name="actions" :order="order" />
        </div>
      </div>
    </div>

    <div class="p-4">
      <div v-if="!order" class="text-sm text-gray-500">No order selected</div>
      <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <h4 class="text-sm font-medium text-gray-700">Customer</h4>
          <div class="text-sm text-gray-900">{{ order.customer || order.customer_name || (order.user &&
            (order.user.first_name + ' ' + order.user.last_name)) }}</div>
          <div class="text-sm text-gray-500">{{ order.email || (order.user && order.user.email) }}</div>
        </div>

        <div>
          <h4 class="text-sm font-medium text-gray-700">Totals</h4>
          <div class="text-sm text-gray-900">Subtotal: {{ formatCurrency(order.subtotal || order.sub_total ||
            order.total || 0) }}</div>
          <div class="text-sm text-gray-900">Shipping: {{ formatCurrency(order.shipping || 0) }}</div>
          <div class="text-sm text-gray-900">Total: {{ formatCurrency(order.total || order.total_amount || 0)
            }}</div>
        </div>

        <div class="sm:col-span-2">
          <h4 class="text-sm font-medium text-gray-700">Items</h4>
          <ul class="divide-y divide-gray-100">
            <li v-for="item in order.items || order.order_items || []" :key="item.id"
              class="py-2 flex items-center justify-between">
              <div>
                <div class="text-sm text-gray-900">{{ item.name || item.title }}</div>
                <div class="text-xs text-gray-500">Qty: {{ item.quantity || item.qty }} • {{
                  formatCurrency(item.price || item.unit_price || item.total_price) }}</div>
              </div>
              <div class="text-sm text-gray-900">{{ formatCurrency((item.quantity || item.qty || 1) *
                (item.price || item.unit_price || 0)) }}</div>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="text-sm font-medium text-gray-700">Status</h4>
          <div class="text-sm text-gray-900">{{ order.status }}</div>
          <div class="text-sm text-gray-500">Placed: {{ formatDate(order.created_at) }}</div>
        </div>

        <div>
          <h4 class="text-sm font-medium text-gray-700">Shipping Address</h4>
          <div class="text-sm text-gray-900">{{ order.shipping_address?.address || order.address || 'N/A' }}
          </div>
          <div class="text-sm text-gray-500">{{ order.shipping_address?.city || '' }} {{
            order.shipping_address?.state || '' }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineEmits } from 'vue'

const emit = defineEmits(['update-delivery'])

const props = defineProps({ order: { type: [Object, null], default: null } })

const deliveryDate = ref(null)
const savingDelivery = ref(false)

watch(() => props.order, (val) => {
  deliveryDate.value = val?.delivery_date ? val.delivery_date.split('T')[0] : val?.delivery_date || null
}, { immediate: true })

const formatCurrency = (v) => {
  if (v == null) return '-'
  return Number(v).toLocaleString(undefined, { style: 'currency', currency: 'NGN' })
}

const printOrder = () => {
  // Simple print: open a new window with order HTML and call print
  if (!props.order) return
  const html = `
    <html>
      <head>
  <title>Order ${props.order.order_id || props.order.id}</title>
        <style>body{font-family: Arial, sans-serif; padding: 20px;} .h{font-weight:700}</style>
      </head>
      <body>
  <h1>Order ${props.order.order_id || props.order.id}</h1>
  <p>Customer: ${props.order.customer || props.order.customer_name || (props.order.user && (props.order.user.first_name + ' ' + props.order.user.last_name)) || ''}</p>
  <p>Total: ${formatCurrency(props.order.total || props.order.total_amount || 0)}</p>
        <hr />
        <h3>Items</h3>
        <ul>
          ${(props.order.items || props.order.order_items || []).map(i => `<li>${i.name || i.title} — Qty: ${i.quantity || i.qty || 1} — ${formatCurrency((i.quantity || i.qty || 1) * (i.price || i.unit_price || 0))}</li>`).join('')}
        </ul>
      </body>
    </html>
  `
  const w = window.open('', '_blank')
  if (!w) return
  w.document.write(html)
  w.document.close()
  w.focus()
  w.print()
  // do not automatically close; let the browser manage
}

const saveDeliveryDate = async () => {
  if (!deliveryDate.value) return
  savingDelivery.value = true
  try {
    // Emit event so parent/view can perform API call via store
    emit('update-delivery', deliveryDate.value)
  } finally {
    savingDelivery.value = false
  }
}

const formatDate = (d) => {
  if (!d) return '-'
  const dt = new Date(d)
  return dt.toLocaleString()
}
</script>
