<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
    @click.self="closeModal">
    <div class="bg-white rounded-xl shadow-strong max-w-2xl w-full max-h-screen overflow-y-auto animate-fade-in"
      @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ modalTitle }}
        </h3>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors" :disabled="loading">
          <font-awesome-icon icon="times" class="h-5 w-5" />
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <div class="flex items-start space-x-4">
          <!-- Icon -->
          <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" :class="iconClass">
            <font-awesome-icon :icon="actionIcon" class="h-5 w-5" :class="iconTextClass" />
          </div>

          <!-- Content -->
          <div class="flex-1">
            <p class="text-sm text-gray-700 mb-4">
              {{ confirmationMessage }}
            </p>

            <!-- Product List -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4 max-h-60 overflow-y-auto">
              <div class="text-xs font-medium text-gray-500 mb-3">
                Selected Products ({{ products.length }})
              </div>
              <div class="grid grid-cols-1 gap-3">
                <div v-for="product in products.slice(0, 10)" :key="product.id"
                  class="flex items-center space-x-3 bg-white rounded-md p-3">
                  <div class="flex-shrink-0">
                    <img v-if="product.image || product.images?.[0]"
                      :src="product.image || `import.meta.env.FILE_BASE_PATH${product.images?.[0].path}`"
                      :alt="product.name" class="w-12 h-12 rounded-md object-cover">
                    <div v-else class="w-12 h-12 rounded-md bg-gray-100 flex items-center justify-center">
                      <font-awesome-icon icon="image" class="h-4 w-4 text-gray-400" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ product.name }}
                    </p>
                    <p class="text-xs text-gray-500">
                      SKU: {{ product.sku || 'N/A' }}
                    </p>
                    <div class="flex items-center space-x-2 mt-1">
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(product.status)">
                        {{ getStatusLabel(product.status) }}
                      </span>
                      <span class="text-xs text-gray-500">
                        {{ formatCurrency(product.price) }}
                      </span>
                    </div>
                  </div>
                </div>

                <div v-if="products.length > 10" class="text-xs text-gray-500 italic text-center py-2">
                  and {{ products.length - 10 }} more products...
                </div>
              </div>
            </div>

            <!-- Additional Form Fields -->
            <div v-if="showAdditionalFields" class="space-y-4">
              <!-- Status change -->
              <div v-if="action === 'change_status'">
                <label class="form-label">New Status</label>
                <select v-model="additionalData.status" class="form-input" required>
                  <option value="">Select Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="draft">Draft</option>
                  <option value="archived">Archived</option>
                </select>
              </div>

              <!-- Category assignment -->
              <div v-if="action === 'assign_category'">
                <label class="form-label">New Category</label>
                <select v-model="additionalData.category_id" class="form-input" required>
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <!-- Price adjustment -->
              <div v-if="action === 'adjust_price'">
                <label class="form-label">Price Adjustment</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div>
                    <select v-model="additionalData.adjustment_type" class="form-input" required>
                      <option value="">Select Type</option>
                      <option value="fixed_amount">Fixed Amount</option>
                      <option value="percentage">Percentage</option>
                      <option value="set_price">Set Price</option>
                    </select>
                  </div>
                  <div>
                    <select v-model="additionalData.adjustment_operation" class="form-input"
                      v-if="additionalData.adjustment_type !== 'set_price'" required>
                      <option value="">Select Operation</option>
                      <option value="increase">Increase</option>
                      <option value="decrease">Decrease</option>
                    </select>
                  </div>
                  <div>
                    <input v-model.number="additionalData.adjustment_value" type="number" step="0.01" min="0"
                      class="form-input" :placeholder="getAdjustmentPlaceholder()" required>
                  </div>
                </div>
                <div class="form-help">
                  {{ getAdjustmentDescription() }}
                </div>
              </div>

              <!-- Stock adjustment -->
              <div v-if="action === 'adjust_stock'">
                <label class="form-label">Stock Adjustment</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <select v-model="additionalData.stock_operation" class="form-input" required>
                      <option value="">Select Operation</option>
                      <option value="add">Add Stock</option>
                      <option value="subtract">Subtract Stock</option>
                      <option value="set">Set Stock</option>
                    </select>
                  </div>
                  <div>
                    <input v-model.number="additionalData.stock_quantity" type="number" min="0" class="form-input"
                      placeholder="Quantity" required>
                  </div>
                </div>
                <div>
                  <label class="form-label">Reason (Optional)</label>
                  <textarea v-model="additionalData.stock_reason" class="form-input" rows="2"
                    placeholder="Reason for stock adjustment..."></textarea>
                </div>
              </div>

              <!-- Discount application -->
              <div v-if="action === 'apply_discount'">
                <label class="form-label">Discount Settings</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <select v-model="additionalData.discount_type" class="form-input" required>
                      <option value="">Select Type</option>
                      <option value="percentage">Percentage</option>
                      <option value="fixed_amount">Fixed Amount</option>
                    </select>
                  </div>
                  <div>
                    <input v-model.number="additionalData.discount_value" type="number" step="0.01" min="0"
                      class="form-input" :placeholder="additionalData.discount_type === 'percentage' ? '10' : '5.00'"
                      required>
                  </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                  <div>
                    <label class="form-label">Start Date</label>
                    <input v-model="additionalData.discount_start_date" type="date" class="form-input" required>
                  </div>
                  <div>
                    <label class="form-label">End Date</label>
                    <input v-model="additionalData.discount_end_date" type="date" class="form-input" required>
                  </div>
                </div>
              </div>

              <!-- Notification option -->
              <div v-if="['activate', 'deactivate', 'feature', 'unfeature'].includes(action)">
                <label class="flex items-center">
                  <input v-model="additionalData.notify_customers" type="checkbox"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                  <span class="ml-2 text-sm text-gray-700">
                    Notify customers about changes
                  </span>
                </label>
              </div>
            </div>

            <!-- Warning for destructive actions -->
            <div v-if="isDestructive" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
              <div class="flex items-start space-x-3">
                <font-awesome-icon icon="exclamation-triangle" class="h-5 w-5 text-red-500 mt-0.5" />
                <div class="text-sm text-red-700">
                  <strong>Warning:</strong> This action cannot be undone.
                  {{ destructiveWarning }}
                </div>
              </div>
            </div>

            <!-- Summary for price/stock adjustments -->
            <div v-if="showSummary" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
              <h4 class="text-sm font-medium text-blue-900 mb-2">
                <font-awesome-icon icon="info-circle" class="mr-1" />
                Action Summary
              </h4>
              <div class="text-sm text-blue-800">
                {{ getSummaryText() }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
        <button @click="closeModal" class="btn-outline" :disabled="loading">
          Cancel
        </button>

        <button @click="confirmAction" :disabled="!canConfirm || loading" :class="[
          isDestructive ? 'btn-danger' : 'btn-primary'
        ]">
          <font-awesome-icon v-if="loading" icon="spinner" class="animate-spin mr-2" />
          <font-awesome-icon v-else :icon="actionIcon" class="mr-2" />
          {{ loading ? 'Processing...' : actionButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  action: {
    type: String,
    required: true
  },
  products: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update:show', 'confirm'])

// Reactive data
const additionalData = reactive({
  status: '',
  category_id: '',
  adjustment_type: '',
  adjustment_operation: '',
  adjustment_value: '',
  stock_operation: '',
  stock_quantity: '',
  stock_reason: '',
  discount_type: '',
  discount_value: '',
  discount_start_date: '',
  discount_end_date: '',
  notify_customers: false
})

// Computed
const modalTitle = computed(() => {
  const titles = {
    activate: 'Activate Products',
    deactivate: 'Deactivate Products',
    delete: 'Delete Products',
    feature: 'Feature Products',
    unfeature: 'Unfeature Products',
    change_status: 'Change Product Status',
    assign_category: 'Assign Category',
    adjust_price: 'Adjust Prices',
    adjust_stock: 'Adjust Stock',
    apply_discount: 'Apply Discount'
  }
  return titles[props.action] || 'Bulk Action'
})

const confirmationMessage = computed(() => {
  const productCount = props.products.length
  const productText = productCount === 1 ? 'product' : 'products'

  const messages = {
    activate: `Are you sure you want to activate ${productCount} ${productText}? This will make them visible to customers.`,
    deactivate: `Are you sure you want to deactivate ${productCount} ${productText}? This will hide them from customers.`,
    delete: `Are you sure you want to permanently delete ${productCount} ${productText}? This action cannot be undone.`,
    feature: `Are you sure you want to feature ${productCount} ${productText}? This will highlight them on your store.`,
    unfeature: `Are you sure you want to unfeature ${productCount} ${productText}?`,
    change_status: `Change the status for ${productCount} ${productText}?`,
    assign_category: `Assign a new category to ${productCount} ${productText}?`,
    adjust_price: `Adjust prices for ${productCount} ${productText}?`,
    adjust_stock: `Adjust stock levels for ${productCount} ${productText}?`,
    apply_discount: `Apply discount to ${productCount} ${productText}?`
  }
  return messages[props.action] || `Perform bulk action on ${productCount} ${productText}?`
})

const actionIcon = computed(() => {
  const icons = {
    activate: 'check-circle',
    deactivate: 'times-circle',
    delete: 'trash',
    feature: 'star',
    unfeature: 'star',
    change_status: 'edit',
    assign_category: 'tag',
    adjust_price: 'dollar-sign',
    adjust_stock: 'boxes',
    apply_discount: 'percentage'
  }
  return icons[props.action] || 'cogs'
})

const iconClass = computed(() => {
  const classes = {
    activate: 'bg-green-100',
    deactivate: 'bg-gray-100',
    delete: 'bg-red-100',
    feature: 'bg-yellow-100',
    unfeature: 'bg-gray-100',
    change_status: 'bg-blue-100',
    assign_category: 'bg-purple-100',
    adjust_price: 'bg-green-100',
    adjust_stock: 'bg-blue-100',
    apply_discount: 'bg-orange-100'
  }
  return classes[props.action] || 'bg-gray-100'
})

const iconTextClass = computed(() => {
  const classes = {
    activate: 'text-green-600',
    deactivate: 'text-gray-600',
    delete: 'text-red-600',
    feature: 'text-yellow-600',
    unfeature: 'text-gray-600',
    change_status: 'text-blue-600',
    assign_category: 'text-purple-600',
    adjust_price: 'text-green-600',
    adjust_stock: 'text-blue-600',
    apply_discount: 'text-orange-600'
  }
  return classes[props.action] || 'text-gray-600'
})

const actionButtonText = computed(() => {
  const texts = {
    activate: 'Activate Products',
    deactivate: 'Deactivate Products',
    delete: 'Delete Products',
    feature: 'Feature Products',
    unfeature: 'Unfeature Products',
    change_status: 'Change Status',
    assign_category: 'Assign Category',
    adjust_price: 'Adjust Prices',
    adjust_stock: 'Adjust Stock',
    apply_discount: 'Apply Discount'
  }
  return texts[props.action] || 'Confirm'
})

const isDestructive = computed(() => {
  return ['delete'].includes(props.action)
})

const destructiveWarning = computed(() => {
  const warnings = {
    delete: 'All selected product data will be permanently removed.  Except for product in use which will be archived.'
  }
  return warnings[props.action] || ''
})

const showAdditionalFields = computed(() => {
  return [
    'change_status', 'assign_category', 'adjust_price',
    'adjust_stock', 'apply_discount'
  ].includes(props.action)
})

const showSummary = computed(() => {
  return ['adjust_price', 'adjust_stock', 'apply_discount'].includes(props.action) &&
    canConfirm.value
})

const canConfirm = computed(() => {
  switch (props.action) {
    case 'change_status':
      return additionalData.status !== ''
    case 'assign_category':
      return additionalData.category_id !== ''
    case 'adjust_price':
      return additionalData.adjustment_type !== '' &&
        additionalData.adjustment_value !== '' &&
        (additionalData.adjustment_type === 'set_price' || additionalData.adjustment_operation !== '')
    case 'adjust_stock':
      return additionalData.stock_operation !== '' &&
        additionalData.stock_quantity !== ''
    case 'apply_discount':
      return additionalData.discount_type !== '' &&
        additionalData.discount_value !== '' &&
        additionalData.discount_start_date !== '' &&
        additionalData.discount_end_date !== ''
    default:
      return true
  }
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    draft: 'bg-yellow-100 text-yellow-800',
    archived: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    active: 'Active',
    inactive: 'Inactive',
    draft: 'Draft',
    archived: 'Archived'
  }
  return labels[status] || status
}

const getAdjustmentPlaceholder = () => {
  const type = additionalData.adjustment_type
  if (type === 'percentage') return '10'
  if (type === 'fixed_amount') return '5.00'
  if (type === 'set_price') return '29.99'
  return 'Value'
}

const getAdjustmentDescription = () => {
  const { adjustment_type, adjustment_operation, adjustment_value } = additionalData

  if (!adjustment_type || !adjustment_value) return ''

  if (adjustment_type === 'set_price') {
    return `All selected products will have their price set to ${formatCurrency(adjustment_value)}`
  }

  if (adjustment_type === 'percentage' && adjustment_operation) {
    const op = adjustment_operation === 'increase' ? 'increased' : 'decreased'
    return `All selected products will have their price ${op} by ${adjustment_value}%`
  }

  if (adjustment_type === 'fixed_amount' && adjustment_operation) {
    const op = adjustment_operation === 'increase' ? 'increased' : 'decreased'
    return `All selected products will have their price ${op} by ${formatCurrency(adjustment_value)}`
  }

  return ''
}

const getSummaryText = () => {
  const count = props.products.length
  const productText = count === 1 ? 'product' : 'products' // Pluralization

  switch (props.action) {
    case 'adjust_price':
      return `${getAdjustmentDescription()} (${count} ${productText})`

    case 'adjust_stock':
      {
        const { stock_operation: op, stock_quantity: qty } = additionalData
        const action = {
          add: 'Add',
          subtract: 'Subtract',
          set: 'Set'
        }[op] || 'Modify'

        const preposition = op === 'set' ? 'for' : op === 'add' ? 'to' : 'from'
        return `${action} ${qty} units ${preposition} ${count} ${productText}`
      }

    case 'apply_discount':
      {
        const { discount_type, discount_value, discount_start_date, discount_end_date } = additionalData
        const discount = discount_type === 'percentage'
          ? `${discount_value}%`
          : formatCurrency(discount_value)
        return `Apply ${discount} discount to ${count} ${productText} (${discount_start_date} - ${discount_end_date})`
      }

    default:
      return `Action on ${count} ${productText}` // More informative default
  }
}

const closeModal = () => {
  if (!props.loading) {
    emit('update:show', false)
  }
}

const confirmAction = () => {
  if (canConfirm.value && !props.loading) {
    emit('confirm', { ...additionalData })
  }
}

const resetData = () => {
  Object.keys(additionalData).forEach(key => {
    if (typeof additionalData[key] === 'boolean') {
      additionalData[key] = false
    } else {
      additionalData[key] = ''
    }
  })
}

// Watchers
watch(() => props.show, (newValue) => {
  if (newValue) {
    resetData()
  }
})

// Handle escape key
const handleKeydown = (event) => {
  if (event.key === 'Escape' && props.show && !props.loading) {
    closeModal()
  }
}

// Add event listener
if (typeof window !== 'undefined') {
  document.addEventListener('keydown', handleKeydown)
}
</script>
