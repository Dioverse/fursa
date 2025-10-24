<template>
  <div class="bg-white shadow rounded-lg overflow-hidden" ref="printArea">
    <!-- Header -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-3">
          <span>Order #{{ order?.order_id || order?.id || '-' }}</span>
          <span v-if="order" class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full border"
            :class="statusPillClass(order.status)">{{ (order.status || '').toUpperCase() }}</span>
        </h3>
        <p class="text-xs text-gray-500 mt-1">
          Placed: {{ formatDate(order?.created_at) }}
          <span v-if="order?.payment_method"> • Payment: {{ order.payment_method }}</span>
          <span v-if="order?.delivery_date"> • Delivery: {{ formatDate(order.delivery_date) }}</span>
        </p>
      </div>

  <div class="flex items-center gap-3 no-print">
        <!-- Status changer -->
        <div class="flex items-center gap-2">
          <select v-model="localStatus" class="border rounded-md px-2 py-1 text-sm">
            <option disabled value="">Change status…</option>
            <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
          </select>
          <button @click="saveStatus" :disabled="!localStatus || savingStatus" type="button"
            class="inline-flex items-center px-3 py-1.5 border border-primary-600 rounded-md text-sm text-white bg-primary-600 hover:bg-primary-700">
            Update
          </button>
        </div>

        <!-- Print button -->
        <button @click="printOrder" type="button"
          class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
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
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="p-6 text-sm text-gray-500">Loading order details…</div>

    <!-- Content -->
    <div v-else class="p-4">
      <div v-if="!order" class="text-sm text-gray-500">No order selected</div>
      <div v-else class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Left: Customer and addresses -->
        <div class="space-y-4 lg:col-span-1">
          <div class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Customer</h4>
            <div class="text-sm text-gray-900">{{ customerName }}</div>
            <div class="text-sm text-gray-500">{{ customerEmail }}</div>
            <div v-if="order?.phone" class="text-sm text-gray-500">{{ order.phone }}</div>
          </div>

          <div class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Shipping Address</h4>
            <p class="text-sm text-gray-900">{{ shippingAddress }}</p>
            <p class="text-sm text-gray-500">{{ shippingCityState }}</p>
          </div>

          <div class="rounded-lg border border-gray-100 p-4" v-if="billingAddress">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Billing Address</h4>
            <p class="text-sm text-gray-900">{{ billingAddress }}</p>
            <p class="text-sm text-gray-500">{{ billingCityState }}</p>
          </div>
        </div>

        <!-- Middle: Items -->
        <div class="lg:col-span-2 space-y-4">
          <div class="rounded-lg border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50/60">Items</div>
            <div class="p-4 overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="text-xs text-gray-500">
                    <th class="py-2 pr-2">Product</th>
                    <th class="py-2 px-2">Qty</th>
                    <th class="py-2 px-2">Unit Price</th>
                    <th class="py-2 pl-2 text-right">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items" :key="item.id" class="border-t border-gray-100">
                    <td class="py-2 pr-2">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden border">
                          <img v-if="item.thumb" :src="item.thumb" alt="" class="w-full h-full object-cover" />
                          <span v-else class="text-xs text-gray-500">No image</span>
                        </div>
                        <div>
                          <div class="text-gray-900">{{ item.name }}</div>
                          <div class="text-xs text-gray-500">SKU: {{ item.sku || '—' }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="py-2 px-2">{{ item.qty }}</td>
                    <td class="py-2 px-2">{{ formatCurrency(item.unit) }}</td>
                    <td class="py-2 pl-2 text-right">{{ formatCurrency(item.total) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="rounded-lg border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <h4 class="text-sm font-medium text-gray-700 mb-2">Status</h4>
              <div class="text-sm text-gray-900">Current: {{ order.status || '—' }}</div>
              <div class="text-xs text-gray-500">Placed: {{ formatDate(order.created_at) }}</div>
              <div v-if="order?.updated_at" class="text-xs text-gray-500">Updated: {{ formatDate(order.updated_at) }}</div>
            </div>
            <div class="sm:text-right">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Summary</h4>
              <div class="text-sm text-gray-900">Subtotal: {{ formatCurrency(totals.subtotal) }}</div>
              <div class="text-sm text-gray-900">Shipping: {{ formatCurrency(totals.shipping) }}</div>
              <div v-if="totals.discount" class="text-sm text-gray-900">Discount: -{{ formatCurrency(totals.discount) }}</div>
              <div class="mt-1 pt-2 border-t text-base font-semibold">Total: {{ formatCurrency(totals.total) }}</div>
            </div>
          </div>

          <!-- Timeline / history -->
          <div v-if="history.length" class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Status History</h4>
            <ul class="space-y-2">
              <li v-for="h in history" :key="h.id" class="text-sm flex items-center justify-between">
                <div>
                  <span class="font-medium">{{ h.status }}</span>
                  <span v-if="h.note" class="text-gray-500"> — {{ h.note }}</span>
                </div>
                <div class="text-xs text-gray-500">{{ formatDate(h.created_at) }}</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, defineEmits } from 'vue'

const emit = defineEmits(['update-delivery', 'update-status'])

const props = defineProps({
  order: { type: [Object, null], default: null },
  loading: { type: Boolean, default: false },
})

const deliveryDate = ref(null)
const savingDelivery = ref(false)
const savingStatus = ref(false)
const localStatus = ref('')

const statusOptions = ['pending', 'processing', 'completed', 'cancelled', 'refunded']

watch(
  () => props.order,
  (val) => {
    deliveryDate.value = val?.delivery_date ? String(val.delivery_date).split('T')[0] : val?.delivery_date || null
    localStatus.value = val?.status || ''
  },
  { immediate: true },
)

const customerName = computed(() => {
  const o = props.order || {}
  return o.customer || o.customer_name || (o.user && [o.user.first_name, o.user.last_name].filter(Boolean).join(' ')) || '—'
})

const customerEmail = computed(() => {
  const o = props.order || {}
  return o.email || (o.user && o.user.email) || '—'
})

const shippingAddress = computed(() => {
  const a = props.order?.shipping_address
  return a?.address || props.order?.address || 'N/A'
})

const shippingCityState = computed(() => {
  const a = props.order?.shipping_address
  return [a?.city, a?.state].filter(Boolean).join(', ')
})

const billingAddress = computed(() => {
  const a = props.order?.billing_address
  return a?.address || ''
})

const billingCityState = computed(() => {
  const a = props.order?.billing_address
  return [a?.city, a?.state].filter(Boolean).join(', ')
})

const items = computed(() => {
  const arr = props.order?.items || props.order?.order_items || []
  return arr.map((i) => {
    const qty = i.quantity ?? i.qty ?? 1
    const unit = Number(i.price ?? i.unit_price ?? i.total_price ?? 0)
    const total = qty * unit
    const thumb = i.image || i.thumbnail || i.product?.image || null
    const name = i.name || i.title || i.product?.name || 'Item'
    return { id: i.id || `${name}-${unit}`, name, qty, unit, total, sku: i.sku || i.product?.sku, thumb }
  })
})

const totals = computed(() => {
  const o = props.order || {}
  const subtotal = Number(o.subtotal ?? o.sub_total ?? 0)
  const shipping = Number(o.shipping ?? o.shipping_fee ?? 0)
  const discount = Number(o.discount ?? o.discount_total ?? 0)
  const total = Number(o.total ?? o.total_amount ?? subtotal + shipping - discount)
  return { subtotal, shipping, discount, total }
})

const history = computed(() => {
  const h = props.order?.status_history || props.order?.order_status_history || []
  return Array.isArray(h) ? h : []
})

const formatCurrency = (v) => {
  if (v == null || isNaN(Number(v))) return '-'
  return Number(v).toLocaleString(undefined, { style: 'currency', currency: 'NGN' })
}

const statusPillClass = (status) => {
  const s = String(status || '').toLowerCase()
  const base = 'border rounded-full px-2 py-0.5'
  const map = {
    pending: 'bg-yellow-50 text-yellow-800 border-yellow-200',
    processing: 'bg-blue-50 text-blue-800 border-blue-200',
    completed: 'bg-green-50 text-green-800 border-green-200',
    cancelled: 'bg-red-50 text-red-800 border-red-200',
    refunded: 'bg-purple-50 text-purple-800 border-purple-200',
  }
  return `${base} ${map[s] || 'bg-gray-50 text-gray-800 border-gray-200'}`
}

const printArea = ref(null)

const printOrder = () => {
  // Print the same div content with current styles
  const el = printArea.value
  if (!el) return

  const w = window.open('', '_blank', 'noopener,noreferrer')
  if (!w) return

  const doc = w.document
  doc.open()
  doc.write('<html><head><title>Order Print</title>')
  // Copy existing stylesheets and style tags
  Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).forEach((node) => {
    doc.write(node.outerHTML)
  })
  // Hide elements marked no-print in the printed doc
  doc.write('<style>@media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }</style>')
  doc.write('</head><body>')
  doc.write(el.outerHTML)
  doc.write('</body></html>')
  doc.close()

  w.focus()
  // Delay a bit to allow styles to load
  setTimeout(() => {
    w.print()
    w.close()
  }, 300)
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

const saveStatus = async () => {
  if (!localStatus.value) return
  savingStatus.value = true
  try {
    emit('update-status', localStatus.value)
  } finally {
    savingStatus.value = false
  }
}

const formatDate = (d) => {
  if (!d) return '-'
  const dt = new Date(d)
  return dt.toLocaleString()
}
</script>
