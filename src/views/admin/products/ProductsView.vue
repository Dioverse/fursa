<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <p class="mt-1 text-sm text-gray-600">
          Manage your product catalog, inventory, and pricing
        </p>
      </div>

      <div class="mt-4 sm:mt-0 flex items-center space-x-3">
        <!-- Import Button -->
        <div class="relative" ref="importDropdownRef">
          <button @click="toggleImportDropdown" class="btn-outline">
            <font-awesome-icon icon="upload" class="h-4 w-4 mr-2" />
            Import
            <font-awesome-icon icon="chevron-down" class="h-4 w-4 ml-2" />
          </button>

          <!-- Import Dropdown -->
          <div v-if="showImportDropdown"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" @click.stop>
            <div class="py-1">
              <label
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer flex items-center">
                <font-awesome-icon icon="file-csv" class="h-4 w-4 mr-3 text-gray-400" />
                Import CSV
                <input ref="csvImportInput" type="file" accept=".csv" class="hidden" @change="handleImport">
              </label>
              <label
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer flex items-center">
                <font-awesome-icon icon="file-excel" class="h-4 w-4 mr-3 text-gray-400" />
                Import Excel
                <input ref="excelImportInput" type="file" accept=".xlsx,.xls" class="hidden" @change="handleImport">
              </label>
            </div>
          </div>
        </div>

        <!-- Export Button -->
        <div class="relative" ref="exportDropdownRef">
          <button @click="toggleExportDropdown" class="btn-outline">
            <font-awesome-icon icon="download" class="h-4 w-4 mr-2" />
            Export
            <font-awesome-icon icon="chevron-down" class="h-4 w-4 ml-2" />
          </button>

          <!-- Export Dropdown -->
          <div v-if="showExportDropdown"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" @click.stop>
            <div class="py-1">
              <button @click="handleExport('csv')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="file-csv" class="h-4 w-4 mr-3 text-gray-400" />
                Export as CSV
              </button>
              <button @click="handleExport('xlsx')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="file-excel" class="h-4 w-4 mr-3 text-gray-400" />
                Export as Excel
              </button>
              <button @click="handleExport('pdf')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="file-pdf" class="h-4 w-4 mr-3 text-gray-400" />
                Export as PDF
              </button>
            </div>
          </div>
        </div>

        <!-- Create Product Button -->
        <button @click="handleCreateProduct" class="btn-primary">
          <font-awesome-icon icon="plus" class="h-4 w-4 mr-2" />
          Add Product
        </button>
      </div>
    </div>

    <!-- Product Form Drawer -->
    <ProductFormDrawer :show="showProductDrawer" :categories="productsStore.categories" :product="selectedProduct"
      :loading="productsStore.isCreating || productsStore.isUpdating" @close="closeProductDrawer"
      @submit="handleProductSubmit" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatsCard title="Total Products" :value="productsStore.productStats?.total?.toLocaleString() || '0'" icon="box"
        color="primary" :loading="productsStore.isLoading" />

      <StatsCard title="Active Products" :value="productsStore.productStats?.active?.toLocaleString() || '0'"
        icon="check" color="success" :loading="productsStore.isLoading" />

      <StatsCard title="Out of Stock" :value="productsStore.productStats?.out_of_stock?.toLocaleString() || '0'"
        icon="exclamation-triangle" color="danger" :loading="productsStore.isLoading" />

      <StatsCard title="Low Stock" :value="productsStore.productStats?.low_stock?.toLocaleString() || '0'"
        icon="exclamation-triangle" color="warning" :loading="productsStore.isLoading" />
    </div>

    <!-- Filters and Search -->
    <div class="card">
      <div class="card-body">
        <ProductFilters :filters="productsStore.filters" :categories="productsStore.categories"
          :brands="productsStore.brands" :loading="productsStore.isLoading" @update-filters="handleUpdateFilters"
          @reset-filters="handleResetFilters" @search="handleSearch" />
      </div>
    </div>

    <!-- View Toggle -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-gray-700">View:</span>
        <div class="flex border border-gray-300 rounded-md">
          <button @click="viewMode = 'grid'" :class="[
            'px-3 py-2 text-sm font-medium transition-colors',
            viewMode === 'grid'
              ? 'bg-primary-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-50'
          ]">
            <font-awesome-icon icon="th" class="h-4 w-4" />
          </button>
          <button @click="viewMode = 'table'" :class="[
            'px-3 py-2 text-sm font-medium transition-colors border-l border-gray-300',
            viewMode === 'table'
              ? 'bg-primary-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-50'
          ]">
            <font-awesome-icon icon="list" class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- Selected Items Info -->
      <div v-if="selectedProducts.length > 0" class="flex items-center space-x-4">
        <span class="text-sm text-gray-600">
          {{ selectedProducts.length }} product{{ selectedProducts.length !== 1 ? 's' : '' }} selected
        </span>

        <!-- Bulk Actions -->
        <div class="relative" ref="bulkActionsRef">
          <button @click="toggleBulkActions" class="btn-outline">
            <font-awesome-icon icon="cogs" class="h-4 w-4 mr-2" />
            Bulk Actions
            <font-awesome-icon icon="chevron-down" class="h-4 w-4 ml-2" />
          </button>

          <!-- Bulk Actions Dropdown -->
          <div v-if="showBulkActions"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" @click.stop>
            <div class="py-1">
              <button @click="handleBulkAction('activate')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="check-circle" class="h-4 w-4 mr-3 text-green-500" />
                Activate Products
              </button>
              <button @click="handleBulkAction('deactivate')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="times-circle" class="h-4 w-4 mr-3 text-red-500" />
                Deactivate Products
              </button>
              <button @click="handleBulkAction('feature')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="star" class="h-4 w-4 mr-3 text-yellow-500" />
                Feature Products
              </button>
              <button @click="handleBulkAction('unfeature')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="star" class="h-4 w-4 mr-3 text-gray-400" />
                Unfeature Products
              </button>
              <hr class="my-1">
              <button @click="handleBulkAction('delete')"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                Delete Products
              </button>
              <button @click="handleBulkAction('apply_discount')"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                Apply Discount
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Display -->
    <div class="card">
      <div class="card-body p-0">
        <!-- Grid View -->
        <ProductsGrid v-if="viewMode === 'grid'" :products="productsStore.products" :loading="productsStore.isLoading"
          :selected="selectedProducts" @select="handleSelectProduct" @select-all="handleSelectAll"
          @view="handleViewProduct" @edit="handleEditProduct" @delete="handleDeleteProduct"
          @duplicate="handleDuplicateProduct" @update-status="handleUpdateStatus" @update-stock="handleUpdateStock" />

        <!-- Table View -->
        <ProductsTable v-else :products="productsStore.products" :loading="productsStore.isLoading"
          :selected="selectedProducts" :pagination="productsStore.pagination" @select="handleSelectProduct"
          @select-all="handleSelectAll" @view="handleViewProduct" @edit="handleEditProduct"
          @delete="handleDeleteProduct" @duplicate="handleDuplicateProduct" @update-status="handleUpdateStatus"
          @update-stock="handleUpdateStock" @page-change="handlePageChange" @sort="handleSort" />
      </div>
    </div>

    <!-- Bulk Action Confirmation Modal -->
    <ProductBulkActionModal v-model:show="showBulkActionModal" :action="bulkActionType" :products="selectedProducts"
      :loading="productsStore.isUpdating" @confirm="confirmBulkAction" />

    <!-- Delete Confirmation Modal -->
    <ProductDeleteModal v-model:show="showDeleteModal" :product="productToDelete" :loading="productsStore.isDeleting"
      @confirm="confirmDelete" />

    <!-- Stock Update Modal -->
    <StockUpdateModal v-model:show="showStockModal" :product="productToUpdateStock" :loading="productsStore.isUpdating"
      @confirm="confirmStockUpdate" />

    <!-- Import Progress Modal -->
    <ImportProgressModal v-model:show="showImportModal" :progress="importProgress"
      :loading="productsStore.isCreating" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
// import { useAuth } from '@/composables/useAuth'
import { useProductsStore } from '@/stores/products'
import StatsCard from '@/components/admin/dashboard/StatsCard.vue'
import ProductFilters from '@/components/admin/products/ProductFilters.vue'
import ProductsGrid from '@/components/admin/products/ProductsGrid.vue'
import ProductsTable from '@/components/admin/products/ProductsTable.vue'
import ProductBulkActionModal from '@/components/admin/products/ProductBulkActionModal.vue'
import ProductDeleteModal from '@/components/admin/products/ProductDeleteModal.vue'
import StockUpdateModal from '@/components/admin/products/StockUpdateModal.vue'
import ImportProgressModal from '@/components/admin/products/ImportProgressModal.vue'
import ProductFormDrawer from '@/components/admin/products/ProductFormDrawer.vue'

// Composables
const router = useRouter()
// const { hasPermission } = useAuth()
const productsStore = useProductsStore()

// Reactive data
const viewMode = ref('table') // 'grid' or 'table'
const selectedProducts = ref([])
const showImportDropdown = ref(false)
const showExportDropdown = ref(false)
const showBulkActions = ref(false)
const showBulkActionModal = ref(false)
const showDeleteModal = ref(false)
const showStockModal = ref(false)
const showImportModal = ref(false)
const showProductDrawer = ref(false)
const bulkActionType = ref('')
const productToDelete = ref(null)
const productToUpdateStock = ref(null)
const selectedProduct = ref(null)
const importProgress = ref({ current: 0, total: 0, message: '' })

// Refs for dropdown handling
const importDropdownRef = ref(null)
const exportDropdownRef = ref(null)
const bulkActionsRef = ref(null)
const csvImportInput = ref(null)
const excelImportInput = ref(null)

// Methods
const handleUpdateFilters = (newFilters) => {
  productsStore.updateFilters(newFilters)
  handleSearch()
}

const handleResetFilters = () => {
  productsStore.resetFilters()
  selectedProducts.value = []
  loadProducts()
}

const handleSearch = () => {
  selectedProducts.value = []
  loadProducts()
}

const handleSelectProduct = (product) => {
  const index = selectedProducts.value.findIndex(p => p.id === product.id)
  if (index > -1) {
    selectedProducts.value.splice(index, 1)
  } else {
    selectedProducts.value.push(product)
  }
}

const handleSelectAll = (selected) => {
  if (selected) {
    selectedProducts.value = [...productsStore.products]
  } else {
    selectedProducts.value = []
  }
}

const handleViewProduct = (product) => {
  router.push({ name: 'admin.products.detail', params: { id: product.id } })
}

const handleCreateProduct = () => {
  selectedProduct.value = null
  showProductDrawer.value = true
}

const handleEditProduct = (product) => {
  selectedProduct.value = product
  showProductDrawer.value = true
}

const closeProductDrawer = () => {
  showProductDrawer.value = false
  selectedProduct.value = null
}

// const handleProductSubmit = async (formData) => {
//   try {
//     console.log('Parent received FormData:', formData)

//     // Debug: Log all FormData entries
//     console.log('=== Parent FormData Contents ===')
//     for (let [key, value] of formData.entries()) {
//       console.log(`${key}:`, value instanceof File ? `File: ${value.name}` : value)
//     }
//     console.log('Editing product:', selectedProduct.value)

//     if (selectedProduct.value) {
//       await productsStore.updateProduct(selectedProduct.value.id, formData)
//     } else {
//       console.log('Creating new product with FormData:', formData)
//       await productsStore.createProduct(formData)
//     }

//     closeProductDrawer()
//     await loadProducts()
//   } catch (error) {
//     console.error('Error submitting product:', error)
//     alert('Failed to save product. Please try again.')
//   }
// }

const handleProductSubmit = async (submissionData) => {
  try {
    console.log('=== Parent Component Submission ===')
    console.log('Received submission data:', submissionData)

    const { formData, isEdit, productId } = submissionData

    // Debug FormData contents
    console.log('=== Parent FormData Debug ===')
    for (let [key, value] of formData.entries()) {
      console.log(`${key}:`, value instanceof File ? `File: ${value.name}` : value)
    }

    let result

    if (isEdit && productId) {
      console.log('Updating product:', productId)
      result = await productsStore.updateProduct(productId, formData)
    } else {
      console.log('Creating new product')
      result = await productsStore.createProduct(formData)
    }

    console.log('Operation result:', result)

    if (result && result.success) {
      closeProductDrawer()
      await loadProducts() // Refresh the product list
    } else {
      console.error('Operation failed:', result)
      // Handle specific error cases
      if (result && result.errors && Object.keys(result.errors).length > 0) {
        const errorMessages = Object.values(result.errors).flat().join(', ')
        alert(`Validation errors: ${errorMessages}`)
      } else {
        alert(result?.error || 'Failed to save product. Please try again.')
      }
    }

  } catch (error) {
    console.error('Error in handleProductSubmit:', error)
    alert('An unexpected error occurred. Please try again.')
  }
}


const handleDeleteProduct = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const handleDuplicateProduct = async (product) => {
  if (confirm(`Duplicate ${product.name}?`)) {
    await productsStore.duplicateProduct(product.id)
  }
}

const handleUpdateStatus = async (product, status) => {
  await productsStore.updateProductStatus(product.id, status)
}

const handleUpdateStock = (product) => {
  productToUpdateStock.value = product
  showStockModal.value = true
}

const handlePageChange = (page) => {
  selectedProducts.value = []
  loadProducts(page)
}

const handleSort = (sortBy, sortOrder) => {
  productsStore.updateFilters({ sort_by: sortBy, sort_order: sortOrder })
  handleSearch()
}

// Dropdown handlers
const toggleImportDropdown = () => {
  showImportDropdown.value = !showImportDropdown.value
  showExportDropdown.value = false
  showBulkActions.value = false
}

const toggleExportDropdown = () => {
  showExportDropdown.value = !showExportDropdown.value
  showImportDropdown.value = false
  showBulkActions.value = false
}

const toggleBulkActions = () => {
  showBulkActions.value = !showBulkActions.value
  showImportDropdown.value = false
  showExportDropdown.value = false
}

// Import/Export handlers
const handleImport = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  showImportDropdown.value = false
  showImportModal.value = true

  await productsStore.importProducts(file)
  showImportModal.value = false

  // Reset file input
  event.target.value = ''
}

const handleExport = async (format) => {
  showExportDropdown.value = false
  await productsStore.exportProducts(format)
}

// Bulk actions
const handleBulkAction = (action) => {
  bulkActionType.value = action
  showBulkActionModal.value = true
  showBulkActions.value = false
}

const confirmBulkAction = async (additionalData = {}) => {
  const productIds = selectedProducts.value.map(product => product.id)
  const result = await productsStore.bulkAction(bulkActionType.value, productIds, additionalData)

  if (result.success) {
    selectedProducts.value = []
    showBulkActionModal.value = false
  }
}

const confirmDelete = async () => {
  if (productToDelete.value) {
    const result = await productsStore.deleteProduct(productToDelete.value.id)
    if (result.success) {
      showDeleteModal.value = false
      productToDelete.value = null
    }
  }
}

const confirmStockUpdate = async (stockData) => {
  if (productToUpdateStock.value) {
    const result = await productsStore.updateStock(productToUpdateStock.value.id, stockData)
    if (result.success) {
      showStockModal.value = false
      productToUpdateStock.value = null
    }
  }
}

const loadProducts = async (page = 1) => {
  await productsStore.fetchProducts(page)
}

const loadInitialData = async () => {
  await Promise.all([
    productsStore.fetchCategories(),
    // productsStore.fetchBrands(),
    // productsStore.fetchStatistics(),
    loadProducts()
  ])
}

// Click outside handler
const handleClickOutside = (event) => {
  if (importDropdownRef.value && !importDropdownRef.value.contains(event.target)) {
    showImportDropdown.value = false
  }
  if (exportDropdownRef.value && !exportDropdownRef.value.contains(event.target)) {
    showExportDropdown.value = false
  }
  if (bulkActionsRef.value && !bulkActionsRef.value.contains(event.target)) {
    showBulkActions.value = false
  }
}

// Watchers
let searchTimeout = null
watch(() => productsStore.filters.search, (newSearch, oldSearch) => {
  if (newSearch !== oldSearch) {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
      handleSearch()
    }, 500)
  }
})

// Lifecycle
onMounted(() => {
  loadInitialData()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
})
</script>
