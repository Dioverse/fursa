<template>
  <div class="overflow-hidden">
    <!-- {{ products }} -->
    <!-- Loading State -->
    <div v-if="loading && !products.length" class="p-6">
      <div class="space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center space-x-4">
          <div class="loading-skeleton w-4 h-4"></div>
          <div class="loading-skeleton w-16 h-16 rounded"></div>
          <div class="flex-1 space-y-2">
            <div class="loading-skeleton h-4 w-40"></div>
            <div class="loading-skeleton h-3 w-24"></div>
          </div>
          <div class="loading-skeleton h-6 w-20 rounded-full"></div>
          <div class="loading-skeleton h-8 w-20"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!products.length" class="p-12 text-center text-gray-500">
      <font-awesome-icon icon="box" class="h-12 w-12 text-gray-300 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
      <p>Try adjusting your search criteria or create a new product.</p>
    </div>

    <!-- Products Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <!-- Select All Checkbox -->
            <th class="px-6 py-3 text-left">
              <input type="checkbox" :checked="isAllSelected" :indeterminate="isIndeterminate" @change="handleSelectAll"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            </th>

            <!-- Product Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('name')">
              <div class="flex items-center space-x-1">
                <span>Product</span>
                <font-awesome-icon v-if="sortBy === 'name'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Category Column -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Category
            </th>

            <!-- Price Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('price')">
              <div class="flex items-center space-x-1">
                <span>Price</span>
                <font-awesome-icon v-if="sortBy === 'price'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Stock Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('stock_quantity')">
              <div class="flex items-center space-x-1">
                <span>Stock</span>
                <font-awesome-icon v-if="sortBy === 'stock_quantity'"
                  :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'" class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Status Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('status')">
              <div class="flex items-center space-x-1">
                <span>Status</span>
                <font-awesome-icon v-if="sortBy === 'status'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Created Date Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('created_at')">
              <div class="flex items-center space-x-1">
                <span>Created</span>
                <font-awesome-icon v-if="sortBy === 'created_at'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Actions Column -->
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition-colors duration-200"
            :class="{ 'bg-blue-50': isSelected(product) }">
            <!-- Select Checkbox -->
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="checkbox" :checked="isSelected(product)" @change="handleSelect(product)"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            </td>

            <!-- Product Info -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                  <img v-if="product.image || product.images?.[0]"
                    :src="product.image || `import.meta.env.FILE_BASE_PATH${product.images?.[0].path}`"
                    :alt="product.name" class="h-16 w-16 rounded-lg object-cover border border-gray-200">
                  <div v-else
                    class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                    <font-awesome-icon icon="image" class="h-6 w-6 text-gray-400" />
                  </div>
                </div>
                <div class="ml-4">
                  <div class="flex items-center space-x-2">
                    <div class="text-sm font-medium text-gray-900">
                      {{ product.name }}
                    </div>
                    <div v-if="product.is_featured === '1'" class="text-yellow-500">
                      <font-awesome-icon icon="star" class="h-4 w-4" title="Featured" />
                    </div>
                  </div>
                  <div class="text-sm text-gray-500">
                    SKU: {{ product.sku || 'N/A' }}
                  </div>
                  <div v-if="product.brand" class="text-xs text-gray-400">
                    {{ product.brand.name }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Category -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="product.category"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ product.category.name }}
              </span>
              <span v-else class="text-gray-400 text-sm">Uncategorized</span>
            </td>

            <!-- Price -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ formatCurrency(product.base_price) }}
              </div>
              <div class="text-xs text-blue-500">
                {{ formatCurrency(product.distributor_price) }}
              </div>
            </td>

            <!-- Stock -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-2 h-2 rounded-full mr-2" :class="getStockStatusColor(product)"></div>
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ product.stock_quantity || 0 }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ getStockStatusText(product) }}
                  </div>
                </div>
                <button @click="handleUpdateStock(product)" class="ml-2 text-gray-400 hover:text-gray-600"
                  title="Update Stock">
                  <font-awesome-icon icon="edit" class="h-3 w-3" />
                </button>
              </div>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="relative">
                <select :value="product.status" @change="handleStatusChange(product, $event.target.value)"
                  class="text-xs border-0 bg-transparent font-medium rounded-full px-2.5 py-0.5 focus:ring-2 focus:ring-primary-500"
                  :class="getStatusClass(product.status)" :disabled="loading">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                  <!-- <option value="draft">Draft</option>
                  <option value="archived">Archived</option> -->
                </select>
              </div>
            </td>

            <!-- Created Date -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(product.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="relative" :ref="el => actionMenuRefs[product.id] = el">
                <button @click="toggleActionMenu(product.id)"
                  class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                  <font-awesome-icon icon="ellipsis-v" class="h-5 w-5" />
                </button>

                <!-- Action Dropdown -->
                <div v-if="activeActionMenu === product.id"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                  @click.stop>
                  <div class="py-1">
                    <button @click="handleView(product)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="eye" class="h-4 w-4 mr-3 text-gray-400" />
                      View Details
                    </button>

                    <button @click="handleEdit(product)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="edit" class="h-4 w-4 mr-3 text-gray-400" />
                      Edit Product
                    </button>



                    <button @click="handleUpdateStock(product)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="boxes" class="h-4 w-4 mr-3 text-gray-400" />
                      Update Stock
                    </button>

                    <hr class="my-1">

                    <button @click="handleDelete(product)"
                      class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                      <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                      Delete Product
                    </button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="bg-white px-6 py-3 border-t border-gray-200">
      <TablePagination :current-page="pagination.current_page" :last-page="pagination.last_page"
        :per-page="pagination.per_page" :total="pagination.total" :from="pagination.from" :to="pagination.to"
        @page-change="handlePageChange" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { formatDistanceToNow, format } from 'date-fns'
import TablePagination from '@/components/common/TablePagination.vue'

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
  },
  pagination: {
    type: Object,
    default: () => ({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0
    })
  }
})

// Emits
const emit = defineEmits([
  'select', 'selectAll', 'view', 'edit', 'delete', 'duplicate',
  'updateStatus', 'updateStock', 'pageChange', 'sort'
])

// Reactive data
const activeActionMenu = ref(null)
const actionMenuRefs = ref({})
const sortBy = ref('created_at')
const sortOrder = ref('desc')

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

const handleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
  emit('sort', sortBy.value, sortOrder.value)
}

const handleStatusChange = (product, newStatus) => {
  if (newStatus !== product.status) {
    emit('updateStatus', product, newStatus)
  }
}

const toggleActionMenu = (productId) => {
  activeActionMenu.value = activeActionMenu.value === productId ? null : productId
}

const handleView = (product) => {
  activeActionMenu.value = null
  emit('view', product)
}

const handleEdit = (product) => {
  console.log('Editing product:', product)
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

const handlePageChange = (page) => {
  emit('pageChange', page)
}

const formatCurrency = (amount) => {
  if (!amount) return '₦0.00'
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const formatDate = (date) => {
  if (!date) return 'N/A'

  const dateObj = new Date(date)
  const now = new Date()
  const diffInHours = (now - dateObj) / (1000 * 60 * 60)

  if (diffInHours < 24) {
    return formatDistanceToNow(dateObj, { addSuffix: true })
  } else {
    return format(dateObj, 'MMM d, yyyy')
  }
}

const getStatusClass = (status) => {
  const classes = {
    1: 'bg-green-100 text-green-800 border-green-200',
    0: 'bg-gray-100 text-gray-800 border-gray-200',
    // draft: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    // archived: 'bg-red-100 text-red-800 border-red-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const getStockStatusColor = (product) => {
  const quantity = product.stock_quantity || 0
  const lowThreshold = product.low_stock_threshold || 10

  if (quantity === 0) return 'bg-red-500'
  if (quantity <= lowThreshold) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getStockStatusText = (product) => {
  const quantity = product.stock_quantity || 0
  const lowThreshold = product.low_stock_threshold || 10

  if (quantity === 0) return 'Out of Stock'
  if (quantity <= lowThreshold) return 'Low Stock'
  return 'In Stock'
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
/* Custom styling for select dropdown to look like badges */
select option {
  background: white;
  color: black;
}

/* Animation for table rows */
tbody tr {
  transition: all 0.2s ease-in-out;
}

tbody tr:hover {
  transform: translateX(2px);
}

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

/* Smooth transitions for action menus */
.absolute {
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Checkbox indeterminate state */
input[type="checkbox"]:indeterminate {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m4 8h8'/%3e%3c/svg%3e");
}

/* Product image hover effect */
.flex-shrink-0 img:hover {
  transform: scale(1.05);
  transition: transform 0.2s ease-in-out;
}
</style>
