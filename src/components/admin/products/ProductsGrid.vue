<template>
  <div class="p-6">
    <!-- Loading State -->
    <div v-if="loading && !products.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="i in 8" :key="i" class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="loading-skeleton h-48 w-full"></div>
        <div class="p-4 space-y-3">
          <div class="loading-skeleton h-4 w-3/4"></div>
          <div class="loading-skeleton h-3 w-1/2"></div>
          <div class="loading-skeleton h-6 w-1/3"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!products.length" class="text-center py-12">
      <font-awesome-icon icon="box" class="h-12 w-12 text-gray-300 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
      <p class="text-gray-500">Try adjusting your search criteria or create a new product.</p>
    </div>

    <!-- Products Grid -->
    <div v-else>
      <!-- Select All Controls -->
      <div v-if="products.length > 0" class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <input type="checkbox" :checked="isAllSelected" :indeterminate="isIndeterminate" @change="handleSelectAll"
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
          <span class="text-sm text-gray-600">
            Select all products
            <span v-if="selected.length > 0" class="font-medium">
              ({{ selected.length }} selected)
            </span>
          </span>
        </div>
      </div>

      <!-- Grid Layout -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="product in products" :key="product.id"
          class="group bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-medium transition-all duration-200 cursor-pointer"
          :class="{ 'ring-2 ring-primary-500 bg-primary-50': isSelected(product) }">
          <!-- Product Image -->
          <div class="relative aspect-w-16 aspect-h-12">
            <img v-if="product.image || product.images?.[0]"
              :src="productPrimaryImageUrl(product)" :alt="product.name"
              class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-200"
              @click="handleView(product)">
            <div v-else class="w-full h-48 bg-gray-100 flex items-center justify-center" @click="handleView(product)">
              <font-awesome-icon icon="image" class="h-12 w-12 text-gray-400" />
            </div>

            <!-- Selection Checkbox -->
            <div class="absolute top-3 left-3">
              <input type="checkbox" :checked="isSelected(product)" @change="handleSelect(product)" @click.stop
                class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-2 border-white rounded shadow-lg">
            </div>

            <!-- Product Badges -->
            <div class="absolute top-3 right-3 flex flex-col space-y-1">
              <div v-if="product.is_featured" class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                <font-awesome-icon icon="star" class="mr-1" />
                Featured
              </div>

              <div v-if="getStockBadge(product)" class="text-xs px-2 py-1 rounded-full"
                :class="getStockBadge(product).class">
                {{ getStockBadge(product).text }}
              </div>
            </div>

            <!-- Quick Actions Overlay -->
            <div
              class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center space-x-2">
              <button @click="handleView(product)"
                class="bg-white text-gray-800 p-2 rounded-full hover:bg-gray-100 transition-colors"
                title="View Details">
                <font-awesome-icon icon="eye" class="h-4 w-4" />
              </button>

              <button @click="handleEdit(product)"
                class="bg-white text-gray-800 p-2 rounded-full hover:bg-gray-100 transition-colors"
                title="Edit Product">
                <font-awesome-icon icon="edit" class="h-4 w-4" />
              </button>

              <button @click="handleUpdateStock(product)"
                class="bg-white text-gray-800 p-2 rounded-full hover:bg-gray-100 transition-colors"
                title="Update Stock">
                <font-awesome-icon icon="boxes" class="h-4 w-4" />
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-4">
            <div class="mb-2">
              <h3 class="text-sm font-medium text-gray-900 truncate cursor-pointer hover:text-primary-600"
                @click="handleView(product)">
                {{ product.name }}
              </h3>
              <p class="text-xs text-gray-500 mt-1">
                SKU: {{ product.sku || 'N/A' }}
              </p>
            </div>

            <!-- Category & Brand -->
            <div class="flex items-center justify-between mb-3">
              <span v-if="product.category"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ product.category.name }}
              </span>
              <span v-else class="text-xs text-gray-400">No category</span>

              <span v-if="product.brand" class="text-xs text-gray-500">
                {{ product.brand.name }}
              </span>
            </div>

            <!-- Price -->
            <div class="mb-3">
              <div class="flex items-center space-x-2">
                <span class="text-lg font-bold text-gray-900">
                  {{ formatCurrency(product.price) }}
                </span>
                <span v-if="product.compare_price && product.compare_price > product.price"
                  class="text-sm text-gray-500 line-through">
                  {{ formatCurrency(product.compare_price) }}
                </span>
              </div>

              <div v-if="product.compare_price && product.compare_price > product.price"
                class="text-xs text-green-600 font-medium">
                Save {{ formatCurrency(product.compare_price - product.price) }}
              </div>
            </div>

            <!-- Stock Status -->
            <div class="mb-4">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full" :class="getStockStatusColor(product)"></div>
                <span class="text-sm text-gray-600">
                  {{ product.stock_quantity || 0 }} in stock
                </span>
                <button @click="handleUpdateStock(product)" class="text-gray-400 hover:text-gray-600"
                  title="Update Stock">
                  <font-awesome-icon icon="edit" class="h-3 w-3" />
                </button>
              </div>
            </div>

            <!-- Status & Actions -->
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <select :value="product.status" @change="handleStatusChange(product, $event.target.value)" @click.stop
                  class="text-xs border-0 bg-transparent font-medium rounded-full px-2 py-1 focus:ring-2 focus:ring-primary-500"
                  :class="getStatusClass(product.status)" :disabled="loading">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                  <!-- <option value="draft">Draft</option>
                                    <option value="archived">Archived</option> -->
                </select>
              </div>

              <!-- Action Menu -->
              <div class="relative" :ref="el => actionMenuRefs[product.id] = el">
                <button @click="toggleActionMenu(product.id)"
                  class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                  <font-awesome-icon icon="ellipsis-v" class="h-4 w-4" />
                </button>

                <!-- Action Dropdown -->
                <div v-if="activeActionMenu === product.id"
                  class="absolute right-0 bottom-full mb-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                  @click.stop>
                  <div class="py-1">
                    <button @click="handleView(product)"
                      class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="eye" class="h-3 w-3 mr-2 text-gray-400" />
                      View
                    </button>

                    <button @click="handleEdit(product)"
                      class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="edit" class="h-3 w-3 mr-2 text-gray-400" />
                      Edit
                    </button>


                    <hr class="my-1">

                    <button @click="handleDelete(product)"
                      class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 flex items-center">
                      <font-awesome-icon icon="trash" class="h-3 w-3 mr-2" />
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
              <div class="flex items-center justify-between">
                <span>Created {{ formatRelativeDate(product.created_at) }}</span>
                <div class="flex items-center space-x-2">
                  <span v-if="product.rating" class="flex items-center">
                    <font-awesome-icon icon="star" class="h-3 w-3 text-yellow-400 mr-1" />
                    {{ product.rating }}
                  </span>
                  <span v-if="product.sales_count" title="Sales Count">
                    {{ product.sales_count }} sold
                  </span>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { formatDistanceToNow } from 'date-fns'
import { productPrimaryImageUrl } from '@/utils/fileUrl'

// Props
const props = defineProps({
  products: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  selected: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits([
  'select', 'selectAll', 'view', 'edit', 'delete', 'duplicate',
  'updateStatus', 'updateStock'
])

// Reactive data
const activeActionMenu = ref(null)
const actionMenuRefs = ref({})

// Computed
const isAllSelected = computed(() => {
  return props.products.length > 0 && props.selected.length === props.products.length
})

const isIndeterminate = computed(() => {
  return props.selected.length > 0 && props.selected.length < props.products.length
})

// Methods
const isSelected = (product) => {
  return props.selected.some(selected => selected.id === product.id)
}

const handleSelect = (product) => {
  emit('select', product)
}

const handleSelectAll = (event) => {
  emit('selectAll', event.target.checked)
}

const handleView = (product) => {
  activeActionMenu.value = null
  emit('view', product)
}

const handleEdit = (product) => {
  activeActionMenu.value = null
  emit('edit', product)
}

const handleDelete = (product) => {
  activeActionMenu.value = null
  emit('delete', product)
}



const handleUpdateStock = (product) => {
  activeActionMenu.value = null
  emit('updateStock', product)
}

const handleStatusChange = (product, newStatus) => {
  if (newStatus !== product.status) {
    emit('updateStatus', product, newStatus)
  }
}

const toggleActionMenu = (productId) => {
  activeActionMenu.value = activeActionMenu.value === productId ? null : productId
}

const formatCurrency = (amount) => {
  if (!amount) return '₦0.00'
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const formatRelativeDate = (date) => {
  if (!date) return 'N/A'
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const getStatusClass = (status) => {
  const classes = {
    1: 'bg-green-100 text-green-800',
    0: 'bg-gray-100 text-gray-800',
    // draft: 'bg-yellow-100 text-yellow-800',
    // archived: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStockStatusColor = (product) => {
  const quantity = product.stock_quantity || 0
  const lowThreshold = product.low_stock_threshold || 10

  if (quantity === 0) return 'bg-red-500'
  if (quantity <= lowThreshold) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getStockBadge = (product) => {
  const quantity = product.stock_quantity || 0
  const lowThreshold = product.low_stock_threshold || 10

  if (quantity === 0) {
    return {
      text: 'Out of Stock',
      class: 'bg-red-500 text-white'
    }
  }

  if (quantity <= lowThreshold) {
    return {
      text: 'Low Stock',
      class: 'bg-yellow-500 text-white'
    }
  }

  return null
}

// Click outside handler
const handleClickOutside = (event) => {
  const isClickInsideMenu = Object.values(actionMenuRefs.value).some(ref => {
    return ref && ref.contains(event.target)
  })

  if (!isClickInsideMenu) {
    activeActionMenu.value = null
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Loading state animations */
@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.5;
  }
}

.loading-skeleton {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  background-color: #f3f4f6;
}

/* Aspect ratio utility for consistent image sizes */
.aspect-w-16 {
  position: relative;
  padding-bottom: 75%;
  /* 4:3 Aspect Ratio */
}

.aspect-w-16>* {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

/* Card hover effects */
.group:hover {
  transform: translateY(-2px);
}

/* Custom select styling */
select option {
  background: white;
  color: black;
}

/* Smooth transitions */
.group .absolute {
  animation: slideUp 0.2s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
