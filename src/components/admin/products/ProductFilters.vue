<template>
  <div class="space-y-4">
    <!-- Search Bar -->
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <font-awesome-icon icon="search" class="h-5 w-5 text-gray-400" />
          </div>
          <input v-model="localFilters.search" type="text" class="form-input pl-10"
            placeholder="Search products by name, SKU, or description..." @input="debouncedSearch" :disabled="loading">
        </div>
      </div>

      <div class="flex items-center space-x-3">
        <!-- Advanced Filters Toggle -->
        <button @click="showAdvancedFilters = !showAdvancedFilters" class="btn-outline"
          :class="{ 'bg-gray-50': showAdvancedFilters }">
          <font-awesome-icon icon="filter" class="h-4 w-4 mr-2" />
          Filters
          <span v-if="activeFiltersCount > 0" class="ml-1 badge badge-primary text-xs">
            {{ activeFiltersCount }}
          </span>
          <font-awesome-icon :icon="showAdvancedFilters ? 'chevron-up' : 'chevron-down'" class="h-4 w-4 ml-2" />
        </button>

        <!-- Reset Filters -->
        <button @click="$emit('resetFilters')" class="btn-ghost" :disabled="loading || !hasActiveFilters">
          <font-awesome-icon icon="times" class="h-4 w-4 mr-2" />
          Clear
        </button>
      </div>
    </div>

    <!-- Advanced Filters -->
    <div v-if="showAdvancedFilters" class="bg-gray-50 rounded-lg p-4 space-y-4 animate-fade-in">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Category Filter -->
        <div>
          <label class="form-label">Category</label>
          <select v-model="localFilters.category_id" @change="emitUpdate" class="form-input" :disabled="loading">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>



        <!-- Status Filter -->
        <div>
          <label class="form-label">Status</label>
          <select v-model="localFilters.status" @change="emitUpdate" class="form-input" :disabled="loading">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <!-- Stock Status Filter -->
        <div>
          <label class="form-label">Stock Status</label>
          <select v-model="localFilters.stock_status" @change="emitUpdate" class="form-input" :disabled="loading">
            <option value="">All Stock Levels</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
            <option value="backorder">Backorder</option>
          </select>
        </div>
      </div>

      <!-- Price Range -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="form-label">Price From (₦)</label>
          <input v-model.number="localFilters.price_from" @change="emitUpdate" type="number" min="0" step="0.01"
            class="form-input" placeholder="0.00" :disabled="loading">
        </div>

        <div>
          <label class="form-label">Price To (₦)</label>
          <input v-model.number="localFilters.price_to" @change="emitUpdate" type="number" min="0" step="0.01"
            class="form-input" placeholder="999.99" :disabled="loading">
        </div>

        <!-- Date From -->
        <div>
          <label class="form-label">Created From</label>
          <input v-model="localFilters.date_from" @change="emitUpdate" type="date" class="form-input"
            :disabled="loading">
        </div>

        <!-- Date To -->
        <div>
          <label class="form-label">Created To</label>
          <input v-model="localFilters.date_to" @change="emitUpdate" type="date" class="form-input" :disabled="loading">
        </div>
      </div>

      <!-- Additional Options -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Featured Filter -->
        <div>
          <label class="form-label">Featured</label>
          <select v-model="localFilters.featured" @change="emitUpdate" class="form-input" :disabled="loading">
            <option value="">All Products</option>
            <option value="1">Featured Only</option>
            <option value="0">Not Featured</option>
          </select>
        </div>

        <!-- Sort By -->
        <div>
          <label class="form-label">Sort By</label>
          <select v-model="localFilters.sort_by" @change="emitUpdate" class="form-input" :disabled="loading">
            <option value="created_at">Created Date</option>
            <option value="updated_at">Updated Date</option>
            <option value="name">Name</option>
            <option value="price">Price</option>
            <option value="stock_quantity">Stock Quantity</option>
            <option value="sales_count">Sales Count</option>
            <option value="rating">Rating</option>
          </select>
        </div>

        <!-- Sort Order -->
        <div>
          <label class="form-label">Sort Order</label>
          <select v-model="localFilters.sort_order" @change="emitUpdate" class="form-input" :disabled="loading">
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>
        </div>

        <!-- Per Page -->
        <div>
          <label class="form-label">Per Page</label>
          <select v-model="localFilters.per_page" @change="emitUpdate" class="form-input" :disabled="loading">
            <option :value="10">10 per page</option>
            <option :value="25">25 per page</option>
            <option :value="50">50 per page</option>
            <option :value="100">100 per page</option>
          </select>
        </div>
      </div>

      <!-- Quick Filter Buttons -->
      <div class="flex flex-wrap gap-2">
        <span class="text-sm font-medium text-gray-700">Quick Filters:</span>

        <button @click="applyQuickFilter('today')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          Added Today
        </button>

        <button @click="applyQuickFilter('week')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          This Week
        </button>

        <button @click="applyQuickFilter('month')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          This Month
        </button>

        <button @click="applyQuickFilter('active')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          Active Only
        </button>

        <button @click="applyQuickFilter('featured')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          Featured Only
        </button>

        <button @click="applyQuickFilter('low_stock')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          Low Stock
        </button>

        <button @click="applyQuickFilter('out_of_stock')"
          class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          :disabled="loading">
          Out of Stock
        </button>
      </div>

      <!-- Active Filters Display -->
      <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2">
        <span class="text-sm font-medium text-gray-700">Active Filters:</span>

        <div v-if="localFilters.category_id"
          class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-md">
          Category: {{ getCategoryName(localFilters.category_id) }}
          <button @click="clearFilter('category_id')" class="ml-1 text-blue-600 hover:text-blue-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.brand_id"
          class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-md">
          Brand: {{ getBrandName(localFilters.brand_id) }}
          <button @click="clearFilter('brand_id')" class="ml-1 text-green-600 hover:text-green-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.status"
          class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-md">
          Status: {{ getStatusLabel(localFilters.status) }}
          <button @click="clearFilter('status')" class="ml-1 text-purple-600 hover:text-purple-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.stock_status"
          class="inline-flex items-center px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-md">
          Stock: {{ getStockStatusLabel(localFilters.stock_status) }}
          <button @click="clearFilter('stock_status')" class="ml-1 text-orange-600 hover:text-orange-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.price_from || localFilters.price_to"
          class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-md">
          Price: ${{ localFilters.price_from || '0' }} - ${{ localFilters.price_to || '∞' }}
          <button @click="clearPriceFilters" class="ml-1 text-yellow-600 hover:text-yellow-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.date_from"
          class="inline-flex items-center px-2 py-1 bg-pink-100 text-pink-800 text-xs rounded-md">
          From: {{ formatDate(localFilters.date_from) }}
          <button @click="clearFilter('date_from')" class="ml-1 text-pink-600 hover:text-pink-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.date_to"
          class="inline-flex items-center px-2 py-1 bg-pink-100 text-pink-800 text-xs rounded-md">
          To: {{ formatDate(localFilters.date_to) }}
          <button @click="clearFilter('date_to')" class="ml-1 text-pink-600 hover:text-pink-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>

        <div v-if="localFilters.featured"
          class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-md">
          {{ localFilters.featured === '1' ? 'Featured Only' : 'Not Featured' }}
          <button @click="clearFilter('featured')" class="ml-1 text-indigo-600 hover:text-indigo-800">
            <font-awesome-icon icon="times" class="h-3 w-3" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'

// Props
const props = defineProps({
  filters: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  },
  brands: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['updateFilters', 'resetFilters', 'search'])

// Reactive data
const showAdvancedFilters = ref(false)
const localFilters = reactive({ ...props.filters })

// Computed
const hasActiveFilters = computed(() => {
  return Object.keys(localFilters).some(key => {
    if (key === 'sort_by' || key === 'sort_order' || key === 'per_page') return false
    return localFilters[key] !== '' && localFilters[key] !== null
  })
})

const activeFiltersCount = computed(() => {
  let count = 0
  Object.keys(localFilters).forEach(key => {
    if (key === 'sort_by' || key === 'sort_order' || key === 'per_page') return
    if (localFilters[key] !== '' && localFilters[key] !== null) {
      count++
    }
  })
  return count
})

// Methods
const emitUpdate = () => {
  emit('updateFilters', { ...localFilters })
}

const debouncedSearch = debounce(() => {
  emit('search')
}, 500)

const applyQuickFilter = (filterType) => {
  const today = new Date()
  const startOfDay = new Date(today)
  startOfDay.setHours(0, 0, 0, 0)

  switch (filterType) {
    case 'today':
      localFilters.date_from = startOfDay.toISOString().split('T')[0]
      localFilters.date_to = today.toISOString().split('T')[0]
      break

    case 'week':
      const startOfWeek = new Date(today)
      startOfWeek.setDate(today.getDate() - today.getDay())
      startOfWeek.setHours(0, 0, 0, 0)
      localFilters.date_from = startOfWeek.toISOString().split('T')[0]
      localFilters.date_to = today.toISOString().split('T')[0]
      break

    case 'month':
      const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
      localFilters.date_from = startOfMonth.toISOString().split('T')[0]
      localFilters.date_to = today.toISOString().split('T')[0]
      break

    case 'active':
      localFilters.status = 'active'
      break

    case 'featured':
      localFilters.featured = '1'
      break

    case 'low_stock':
      localFilters.stock_status = 'low_stock'
      break

    case 'out_of_stock':
      localFilters.stock_status = 'out_of_stock'
      break
  }

  emitUpdate()
}

const clearFilter = (filterKey) => {
  localFilters[filterKey] = ''
  emitUpdate()
}

const clearPriceFilters = () => {
  localFilters.price_from = ''
  localFilters.price_to = ''
  emitUpdate()
}

const getCategoryName = (categoryId) => {
  const category = props.categories.find(c => c.id == categoryId)
  return category ? category.name : 'Unknown'
}

const getBrandName = (brandId) => {
  const brand = props.brands.find(b => b.id == brandId)
  return brand ? brand.name : 'Unknown'
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

const getStockStatusLabel = (status) => {
  const labels = {
    in_stock: 'In Stock',
    low_stock: 'Low Stock',
    out_of_stock: 'Out of Stock',
    backorder: 'Backorder'
  }
  return labels[status] || status
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

// Debounce utility
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Watch for prop changes
watch(() => props.filters, (newFilters) => {
  Object.assign(localFilters, newFilters)
}, { deep: true })
</script>
