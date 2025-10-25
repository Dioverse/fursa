//OrderDetailPanel.vue
<template>
  <div class="bg-white shadow rounded-lg overflow-hidden" ref="printArea">
    <!-- Header -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-4">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-3 flex-wrap">
          <span>Order #{{ order?.order_id || order?.id || '-' }}</span>
          <span v-if="order" class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full border"
            :class="statusPillClass(order.status)">{{ (order.status || '').toUpperCase() }}</span>
        </h3>
        <p class="text-xs text-gray-500 mt-1">
          Placed: {{ formatDate(order?.created_at) }}
          <span v-if="paymentStatus"> • Payment: {{ paymentStatus }}</span>
          <span v-if="order?.delivery_days"> • Delivery: {{ order.delivery_days }}</span>
        </p>
      </div>

      <div class="flex items-center gap-3 no-print flex-wrap">
        <!-- Status changer -->
        <div class="flex items-center gap-2">
          <select v-model="localStatus" class="border rounded-md px-2 py-1 text-sm">
            <option disabled value="">Change status…</option>
            <option v-for="s in allowedStatuses" :key="s" :value="s">{{ toUcwords(s) }}</option>
          </select>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="notifyCustomer" type="checkbox" checked class="rounded border-gray-300" />
            <span class="text-sm text-gray-600">Notify</span>
          </label>
          <button @click="saveStatus" :disabled="!localStatus || savingStatus" type="button"
            class="inline-flex items-center px-3 py-1.5 border border-primary-600 rounded-md text-sm text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50">
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
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="p-6 text-sm text-gray-500">Loading order details…</div>

    <!-- Content -->
    <div v-else class="p-4">
      <div v-if="!order" class="text-sm text-gray-500">No order selected</div>
      <div v-else class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Left Column: Customer, Addresses, Payment -->
        <div class="space-y-4 lg:col-span-1">
          <!-- Customer Info -->
          <div class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Customer</h4>
            <div class="space-y-1">
              <div class="text-sm font-medium text-gray-900">{{ customerName }}</div>
              <div class="text-sm text-gray-500">{{ customerEmail }}</div>
              <div v-if="shippingPhone" class="text-sm text-gray-500">{{ shippingPhone }}</div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Shipping Address</h4>
            <div class="space-y-1">
              <div class="text-sm font-medium text-gray-900">{{ shippingFullName }}</div>
              <div class="text-sm text-gray-900">{{ shippingAddressLine }}</div>
              <div class="text-sm text-gray-500">{{ shippingCity }}, {{ shippingState }}</div>
              <div class="text-sm text-gray-500">{{ shippingPostalCode }} {{ shippingCountry }}</div>
            </div>
          </div>

          <!-- Payment Info -->
          <div class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Payment</h4>
            <div class="space-y-2">
              <div class="flex justify-between items-start">
                <span class="text-sm text-gray-600">Method:</span>
                <span class="text-sm font-medium text-gray-900 capitalize">{{ paymentMethod }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-sm text-gray-600">Status:</span>
                <span class="text-sm font-medium capitalize" :class="paymentStatusClass">{{ paymentStatus }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-sm text-gray-600">Gateway:</span>
                <span class="text-sm text-gray-500 capitalize">{{ paymentGateway }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-sm text-gray-600">Ref:</span>
                <span class="text-xs text-gray-500 text-right break-all">{{ transactionReference }}</span>
              </div>
              <div class="flex justify-between items-start pt-2 border-t">
                <span class="text-sm text-gray-600">Paid:</span>
                <span class="text-sm text-gray-500">{{ formatDate(paymentPaidAt) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Items & Summary -->
        <div class="lg:col-span-2 space-y-4">
          <!-- Items Table -->
          <div class="rounded-lg border border-gray-100">
            <div class="px-4 py-3 border-b bg-gray-50/60 font-medium text-sm">Order Items</div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="text-xs text-gray-600 border-b bg-gray-50/30">
                    <th class="py-2 px-3 text-left">Product</th>
                    <th class="py-2 px-3 text-center">Qty</th>
                    <th class="py-2 px-3 text-right">Unit Price</th>
                    <th class="py-2 px-3 text-right">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items" :key="item.id" class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="py-3 px-3">
                      <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center overflow-hidden border flex-shrink-0">
                          <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.name" class="w-full h-full object-cover" />
                          <span v-else class="text-xs text-gray-400">No img</span>
                        </div>
                        <div class="min-w-0">
                          <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                          <div class="text-xs text-gray-500">SKU: {{ item.sku }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-3 text-center text-gray-900">{{ item.quantity }}</td>
                    <td class="py-3 px-3 text-right text-gray-900">{{ formatCurrency(item.unit_price) }}</td>
                    <td class="py-3 px-3 text-right font-medium text-gray-900">{{ formatCurrency(item.total) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Order Summary & Status -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Status Section -->
            <div class="rounded-lg border border-gray-100 p-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Order Status</h4>
              <div class="space-y-2">
                <div>
                  <div class="text-xs text-gray-600 mb-1">Current Status</div>
                  <div class="text-sm font-semibold text-gray-900 capitalize">{{ order.status }}</div>
                </div>
                <div class="pt-2 border-t">
                  <div class="text-xs text-gray-600 mb-1">Created</div>
                  <div class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</div>
                </div>
                <div>
                  <div class="text-xs text-gray-600 mb-1">Last Updated</div>
                  <div class="text-xs text-gray-500">{{ formatDate(order.updated_at) }}</div>
                </div>
              </div>
            </div>

            <!-- Price Summary -->
            <div class="rounded-lg border border-gray-100 p-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Summary</h4>
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Subtotal:</span>
                  <span class="text-gray-900">{{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Shipping:</span>
                  <span class="text-gray-900">{{ formatCurrency(shippingCost) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Tax:</span>
                  <span class="text-gray-900">{{ formatCurrency(tax) }}</span>
                </div>
                <div class="pt-2 border-t flex justify-between">
                  <span class="font-semibold text-gray-900">Total:</span>
                  <span class="font-bold text-lg text-gray-900">{{ formatCurrency(totalAmount) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Status History Timeline -->
          <div v-if="history.length" class="rounded-lg border border-gray-100 p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Status Timeline</h4>
            <div class="space-y-3">
              <div v-for="(h, idx) in history" :key="h.id" class="flex gap-3">
                <div class="flex flex-col items-center">
                  <div class="w-3 h-3 rounded-full bg-primary-600 border-2 border-white"></div>
                  <div v-if="idx < history.length - 1" class="w-0.5 h-12 bg-gray-200 mt-2"></div>
                </div>
                <div class="flex-1 pt-0.5">
                  <div class="text-sm font-medium text-gray-900 capitalize">{{ h.status }}</div>
                  <div class="text-xs text-gray-500">{{ formatDate(h.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { getImageUrl, toUcwords } from '@/utils/helpers'
import { ref, watch, computed, defineEmits } from 'vue'

const emit = defineEmits(['update-delivery', 'update-status'])

const props = defineProps({
  order: { type: [Object, null], default: null },
  loading: { type: Boolean, default: false },
  allowedStatuses: { type: Array, default: () => [] },
})

const savingDelivery = ref(false)
const savingStatus = ref(false)
const localStatus = ref('')
const notifyCustomer = ref(false)

const allowedStatusOptions = computed(() => props.allowedStatuses)

watch(
  () => props.order,
  (val) => {
    if (val) {
      localStatus.value = val?.status || ''
    }
  },
  { immediate: true },
)

// Parse shipping address (comes as JSON string)
const parseShippingAddress = computed(() => {
  const addr = props.order?.shipping_address
  if (!addr) return {}
  if (typeof addr === 'string') {
    try {
      return JSON.parse(addr)
    } catch {
      return {}
    }
  }
  return addr
})

// Customer Info
const customerName = computed(() => {
  const user = props.order?.user
  if (user) {
    return [user.first_name, user.last_name].filter(Boolean).join(' ') || '—'
  }
  return '—'
})

const customerEmail = computed(() => props.order?.user?.email || '—')

// Shipping Address
const shippingFullName = computed(() => parseShippingAddress.value.full_name || '—')
const shippingPhone = computed(() => parseShippingAddress.value.phone || '')
const shippingAddressLine = computed(() => parseShippingAddress.value.address_line_one || '—')
const shippingCity = computed(() => parseShippingAddress.value.city || '')
const shippingState = computed(() => parseShippingAddress.value.state || '')
const shippingPostalCode = computed(() => parseShippingAddress.value.postal_code || '')
const shippingCountry = computed(() => parseShippingAddress.value.country || '')

// Payment Info
const paymentStatus = computed(() => props.order?.payment?.status || '—')
const paymentStatusClass = computed(() => {
  const status = paymentStatus.value.toLowerCase()
  const map = {
    successful: 'text-green-700 font-semibold',
    pending: 'text-yellow-700 font-semibold',
    failed: 'text-red-700 font-semibold',
  }
  return map[status] || 'text-gray-700'
})
const paymentMethod = computed(() => props.order?.payment?.payment_method || '—')
const paymentGateway = computed(() => props.order?.payment?.payment_gateway || '—')
const transactionReference = computed(() => props.order?.trans_ref || props.order?.payment?.transaction_reference || '—')
const paymentPaidAt = computed(() => props.order?.payment?.paid_at || '')

// Order Items
const items = computed(() => {
  const arr = props.order?.order_item || []
  return arr.map((item) => {
    const qty = Number(item.quantity || 1)
    const unitPrice = Number(item.unit_price || 0)
    const total = qty * unitPrice
    return {
      id: item.id,
      name: item.product?.name || 'Item',
      quantity: qty,
      unit_price: unitPrice,
      total,
      sku: item.product?.sku || '—',
      image: item.product?.first_image?.path || null,
    }
  })
})

// Totals
const subtotal = computed(() => items.value.reduce((sum, item) => sum + item.total, 0))
const shippingCost = computed(() => Number(props.order?.shipping_cost || 0))
const tax = computed(() => Number(props.order?.tax || 0))
const totalAmount = computed(() => Number(props.order?.total_amount || 0))

// History
const history = computed(() => {
  const h = props.order?.status_hstry || []
  return Array.isArray(h) ? h.sort((a, b) => new Date(a.created_at) - new Date(b.created_at)) : []
})

// Utility Functions
const buildImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path}`
}

const formatCurrency = (v) => {
  if (v == null || isNaN(Number(v))) return '—'
  return Number(v).toLocaleString('en-NG', { style: 'currency', currency: 'NGN' })
}

const formatDate = (d) => {
  if (!d) return '—'
  try {
    const dt = new Date(d)
    return dt.toLocaleString('en-NG', { dateStyle: 'medium', timeStyle: 'short' })
  } catch {
    return '—'
  }
}

const statusPillClass = (status) => {
  const s = String(status || '').toLowerCase()
  const base = 'border rounded-full px-3 py-0.5 text-xs font-medium'
  const map = {
    pending: 'bg-yellow-50 text-yellow-800 border-yellow-200',
    processing: 'bg-blue-50 text-blue-800 border-blue-200',
    shipping: 'bg-indigo-50 text-indigo-800 border-indigo-200',
    shipped: 'bg-indigo-50 text-indigo-800 border-indigo-200',
    'out for delivery': 'bg-orange-50 text-orange-800 border-orange-200',
    delivered: 'bg-green-50 text-green-800 border-green-200',
    completed: 'bg-green-50 text-green-800 border-green-200',
    cancelled: 'bg-red-50 text-red-800 border-red-200',
    refunded: 'bg-purple-50 text-purple-800 border-purple-200',
    confirmed: 'bg-green-50 text-green-800 border-green-200',
  }
  return `${base} ${map[s] || 'bg-gray-50 text-gray-800 border-gray-200'}`
}

// Print function
const printArea = ref(null)

const printOrder = () => {
  const el = printArea.value
  if (!el) return

  const w = window.open('', '_blank', 'noopener,noreferrer')
  if (!w) return

  const doc = w.document
  doc.open()
  doc.write('<html><head><title>Order #' + (props.order?.order_id || props.order?.id) + '</title>')
  Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).forEach((node) => {
    doc.write(node.outerHTML)
  })
  doc.write('<style>@media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }</style>')
  doc.write('</head><body>')
  doc.write(el.outerHTML)
  doc.write('</body></html>')
  doc.close()

  w.focus()
  setTimeout(() => {
    w.print()
    w.close()
  }, 300)
}

// Save functions
const saveStatus = async () => {
  if (!localStatus.value) return
  savingStatus.value = true
  try {
    emit('update-status', { status: localStatus.value, notify: notifyCustomer.value })
  } finally {
    savingStatus.value = false
  }
}

const formatDate_export = (d) => {
  if (!d) return '—'
  try {
    const dt = new Date(d)
    return dt.toLocaleString('en-NG', { dateStyle: 'medium', timeStyle: 'short' })
  } catch {
    return '—'
  }
}
</script>