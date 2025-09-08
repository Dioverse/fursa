<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
    @click.self="closeModal">
    <div class="bg-white rounded-xl shadow-strong max-w-lg w-full animate-fade-in" @click.stop>
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Update Stock
        </h3>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors" :disabled="loading">
          <font-awesome-icon icon="times" class="h-5 w-5" />
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <!-- Product Info -->
        <div v-if="product" class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <img v-if="product.image || product.images?.[0]"
                :src="product.image || `import.meta.env.FILE_BASE_PATH${product.images?.[0].path}`" :alt="product.name"
                class="h-16 w-16 rounded-lg object-cover border border-gray-200">
              <div v-else
                class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                <font-awesome-icon icon="image" class="h-6 w-6 text-gray-400" />
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-medium text-gray-900 truncate">
                {{ product.name }}
              </h4>
              <p class="text-sm text-gray-500 mt-1">
                SKU: {{ product.sku || 'N/A' }}
              </p>
              <div class="flex items-center space-x-2 mt-2">
                <div class="w-2 h-2 rounded-full" :class="getStockStatusColor(product.stock_quantity)">
                </div>
                <span class="text-sm font-medium text-gray-900">
                  Current Stock: {{ product.stock_quantity || 0 }}
                </span>
                <span class="text-xs text-gray-500">
                  ({{ getStockStatusText(product.stock_quantity) }})
                </span>
              </div>
              <div v-if="product.low_stock_threshold" class="text-xs text-gray-500 mt-1">
                Low stock threshold: {{ product.low_stock_threshold }}
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Update Form -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Operation Type -->
          <div>
            <label class="form-label">Operation</label>
            <div class="grid grid-cols-3 gap-3">
              <label v-for="operation in stockOperations" :key="operation.value" class="relative cursor-pointer">
                <input v-model="formData.operation" :value="operation.value" type="radio" class="sr-only" required>
                <div class="border-2 rounded-lg p-3 text-center transition-all" :class="formData.operation === operation.value
                  ? 'border-primary-500 bg-primary-50 text-primary-900'
                  : 'border-gray-300 hover:border-gray-400'">
                  <font-awesome-icon :icon="operation.icon" class="h-5 w-5 mb-2" />
                  <div class="text-sm font-medium">{{ operation.label }}</div>
                  <div class="text-xs text-gray-500">{{ operation.description }}</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Quantity Input -->
          <div>
            <label class="form-label">
              {{ getQuantityLabel() }}
            </label>
            <div class="relative">
              <input v-model.number="formData.quantity" type="number" min="0"
                :max="formData.operation === 'subtract' ? product?.stock_quantity : undefined" class="form-input pr-20"
                :placeholder="getQuantityPlaceholder()" required>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-sm text-gray-500">units</span>
              </div>
            </div>
            <div v-if="formData.operation === 'subtract'" class="form-help">
              Maximum: {{ product?.stock_quantity || 0 }} units (current stock)
            </div>
          </div>

          <!-- New Stock Preview -->
          <div v-if="newStockQuantity !== null" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm font-medium text-blue-900">New Stock Level</div>
                <div class="text-xs text-blue-700">After this operation</div>
              </div>
              <div class="text-right">
                <div class="text-lg font-bold text-blue-900">
                  {{ newStockQuantity }}
                </div>
                <div class="text-xs" :class="getNewStockStatusClass(newStockQuantity)">
                  {{ getStockStatusText(newStockQuantity) }}
                </div>
              </div>
            </div>

            <!-- Stock change indicator -->
            <div class="mt-2 pt-2 border-t border-blue-200 text-xs text-blue-700">
              {{ (product?.stock_quantity || 0) }} → {{ newStockQuantity }}
              <span :class="getStockChangeClass()">
                ({{ getStockChangeText() }})
              </span>
            </div>
          </div>

          <!-- Low Stock Warning -->
          <div v-if="showLowStockWarning" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <div class="flex items-start space-x-2">
              <font-awesome-icon icon="exclamation-triangle" class="h-4 w-4 text-yellow-600 mt-0.5" />
              <div class="text-sm text-yellow-800">
                <strong>Low Stock Warning:</strong> The new stock level will be below the threshold
                ({{ product?.low_stock_threshold || 10 }} units).
              </div>
            </div>
          </div>

          <!-- Out of Stock Warning -->
          <div v-if="newStockQuantity === 0" class="bg-red-50 border border-red-200 rounded-lg p-3">
            <div class="flex items-start space-x-2">
              <font-awesome-icon icon="exclamation-circle" class="h-4 w-4 text-red-600 mt-0.5" />
              <div class="text-sm text-red-800">
                <strong>Out of Stock:</strong> This product will be marked as out of stock
                and hidden from customers.
              </div>
            </div>
          </div>

          <!-- Reason -->
          <!-- <div>
                        <label class="form-label">Reason (Optional)</label>
                        <select v-model="formData.reason_type" class="form-input mb-2">
                            <option value="">Select reason</option>
                            <option value="restock">Restock/Purchase</option>
                            <option value="sale">Sale/Order</option>
                            <option value="damage">Damaged/Defective</option>
                            <option value="theft">Theft/Loss</option>
                            <option value="return">Customer Return</option>
                            <option value="adjustment">Inventory Adjustment</option>
                            <option value="transfer">Transfer</option>
                            <option value="other">Other</option>
                        </select>

                        <textarea v-model="formData.reason_note" class="form-input" rows="3"
                            placeholder="Additional notes about this stock change..."></textarea>
                    </div> -->

          <!-- Track Changes Option -->
          <!-- <div>
                        <label class="flex items-center">
                            <input v-model="formData.track_change" type="checkbox"
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">
                                Track this change in stock history
                            </span>
                        </label>
                        <div class="form-help">
                            Enable to maintain a detailed audit trail of stock movements
                        </div>
                    </div> -->

          <!-- Low Stock Threshold Update -->
          <div class="border-t border-gray-200 pt-4">
            <label class="flex items-center mb-2">
              <input v-model="showThresholdUpdate" type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
              <span class="ml-2 text-sm font-medium text-gray-700">
                Update low stock threshold
              </span>
            </label>

            <div v-if="showThresholdUpdate" class="ml-6">
              <input v-model.number="formData.low_stock_threshold" type="number" min="0" class="form-input w-32"
                placeholder="10">
              <div class="form-help">
                Alert when stock falls below this level
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
        <button @click="closeModal" type="button" class="btn-outline" :disabled="loading">
          Cancel
        </button>

        <button @click="handleSubmit" :disabled="!canSubmit || loading" class="btn-primary">
          <font-awesome-icon v-if="loading" icon="spinner" class="animate-spin mr-2" />
          <font-awesome-icon v-else icon="check" class="mr-2" />
          {{ loading ? 'Updating...' : 'Update Stock' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  product: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update:show', 'confirm'])

// Reactive data
const showThresholdUpdate = ref(false)
const formData = reactive({
  operation: '',
  quantity: '',
  reason_type: '',
  reason_note: '',
  track_change: true,
  low_stock_threshold: ''
})

// Stock operations
const stockOperations = [
  {
    value: '1',
    label: 'Add Stock',
    description: 'Increase quantity',
    icon: 'plus'
  },
  {
    value: '2',
    label: 'Remove Stock',
    description: 'Decrease quantity',
    icon: 'minus'
  },
  {
    value: '3',
    label: 'Set Stock',
    description: 'Set exact amount',
    icon: 'equals'
  }
]

// Computed
const canSubmit = computed(() => {
  return formData.operation && formData.quantity !== '' && formData.quantity >= 0
})

const newStockQuantity = computed(() => {
  if (!props.product || !formData.operation || formData.quantity === '') return null

  const current = props.product.stock_quantity || 0
  const quantity = Number(formData.quantity)

  switch (formData.operation) {
    case '1':
      return current + quantity
    case '2':
      return Math.max(0, current - quantity)
    case '3':
      return quantity
    default:
      return null
  }
})

const showLowStockWarning = computed(() => {
  if (newStockQuantity.value === null) return false
  const threshold = props.product?.low_stock_threshold || 10
  return newStockQuantity.value > 0 && newStockQuantity.value <= threshold
})

// Methods
const getQuantityLabel = () => {
  switch (formData.operation) {
    case '1':
      return 'Quantity to Add'
    case '2':
      return 'Quantity to Remove'
    case '3':
      return 'New Stock Quantity'
    default:
      return 'Quantity'
  }
}

const getQuantityPlaceholder = () => {
  switch (formData.operation) {
    case '1':
      return 'Enter quantity to add'
    case '2':
      return 'Enter quantity to remove'
    case '3':
      return 'Enter new stock level'
    default:
      return 'Enter quantity'
  }
}

const getStockStatusColor = (quantity) => {
  const qty = quantity || 0
  const threshold = props.product?.low_stock_threshold || 10

  if (qty === 0) return 'bg-red-500'
  if (qty <= threshold) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getStockStatusText = (quantity) => {
  const qty = quantity || 0
  const threshold = props.product?.low_stock_threshold || 10

  if (qty === 0) return 'Out of Stock'
  if (qty <= threshold) return 'Low Stock'
  return 'In Stock'
}

const getNewStockStatusClass = (quantity) => {
  const qty = quantity || 0
  const threshold = props.product?.low_stock_threshold || 10

  if (qty === 0) return 'text-red-600'
  if (qty <= threshold) return 'text-yellow-600'
  return 'text-green-600'
}

const getStockChangeClass = () => {
  if (newStockQuantity.value === null) return ''

  const current = props.product?.stock_quantity || 0
  const change = newStockQuantity.value - current

  if (change > 0) return 'text-green-600'
  if (change < 0) return 'text-red-600'
  return 'text-gray-600'
}

const getStockChangeText = () => {
  if (newStockQuantity.value === null) return ''

  const current = props.product?.stock_quantity || 0
  const change = newStockQuantity.value - current

  if (change > 0) return `+${change}`
  if (change < 0) return `${change}`
  return 'no change'
}

const closeModal = () => {
  if (!props.loading) {
    emit('update:show', false)
  }
}

const handleSubmit = () => {
  if (!canSubmit.value || props.loading) return

  const data = {
    operation: formData.operation,
    quantity: Number(formData.quantity),
    update_threshold: showThresholdUpdate.value,
    track_change: formData.track_change
  }

  if (showThresholdUpdate.value && formData.low_stock_threshold !== '') {
    data.low_stock_threshold = Number(formData.low_stock_threshold)
  }

  emit('confirm', data)


  // reason_type: formData.reason_type,
  // reason_note: formData.reason_note,
}

const resetForm = () => {
  formData.operation = ''
  formData.quantity = ''
  // formData.reason_type = ''
  // formData.reason_note = ''
  formData.track_change = true
  formData.low_stock_threshold = ''
  showThresholdUpdate.value = false
}

// Watchers
watch(() => props.show, (newValue) => {
  if (newValue) {
    resetForm()
    if (props.product?.low_stock_threshold) {
      formData.low_stock_threshold = props.product.low_stock_threshold
    }
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

<style scoped>
/* Radio button custom styling */
input[type="radio"]:checked+div {
  transform: scale(1.02);
}

/* Fade in animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s ease-in-out;
}
</style>
