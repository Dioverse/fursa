<template>
    <div class="space-y-6">
        <!-- Loading State -->
        <div v-if="productsStore.isLoading && !product" class="animate-pulse">
            <div class="bg-white rounded-lg shadow-soft p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="loading-skeleton h-96 rounded-lg"></div>
                    <div class="space-y-4">
                        <div class="loading-skeleton h-8 w-3/4"></div>
                        <div class="loading-skeleton h-4 w-1/2"></div>
                        <div class="loading-skeleton h-6 w-1/4"></div>
                        <div class="loading-skeleton h-20 w-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Not Found -->
        <div v-else-if="!product && !productsStore.isLoading" class="text-center py-12">
            <font-awesome-icon icon="box" class="h-16 w-16 text-gray-300 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">Product not found</h3>
            <p class="text-gray-500 mb-4">The product you're looking for doesn't exist or has been deleted.</p>
            <router-link :to="{ name: 'admin.products' }" class="btn-primary">
                <font-awesome-icon icon="arrow-left" class="mr-2" />
                Back to Products
            </router-link>
        </div>

        <!-- Product Details -->
        <div v-else-if="product" class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <router-link :to="{ name: 'admin.products' }" class="btn-ghost p-2">
                        <font-awesome-icon icon="arrow-left" class="h-5 w-5" />
                    </router-link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-sm text-gray-500">SKU: {{ product.sku || 'N/A' }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="getStatusClass(product.status)">
                                <div class="w-1.5 h-1.5 rounded-full mr-1.5" :class="getStatusDotClass(product.status)">
                                </div>
                                {{ getStatusLabel(product.status) }}
                            </span>
                            <span v-if="product.is_featured" class="text-yellow-500" title="Featured Product">
                                <font-awesome-icon icon="star" class="h-4 w-4" />
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <button v-if="hasPermission('products.edit')" @click="handleEdit" class="btn-outline">
                        <font-awesome-icon icon="edit" class="h-4 w-4 mr-2" />
                        Edit Product
                    </button>

                    <div class="relative" ref="actionsDropdownRef">
                        <button @click="toggleActionsDropdown" class="btn-outline">
                            <font-awesome-icon icon="ellipsis-v" class="h-4 w-4" />
                        </button>

                        <!-- Actions Dropdown -->
                        <div v-if="showActionsDropdown"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                            @click.stop>
                            <div class="py-1">
                                <button @click="handleDuplicate"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <font-awesome-icon icon="copy" class="h-4 w-4 mr-3 text-gray-400" />
                                    Duplicate Product
                                </button>

                                <button @click="handleUpdateStock"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <font-awesome-icon icon="boxes" class="h-4 w-4 mr-3 text-gray-400" />
                                    Update Stock
                                </button>

                                <button @click="handleViewFrontend"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <font-awesome-icon icon="external-link-alt" class="h-4 w-4 mr-3 text-gray-400" />
                                    View on Website
                                </button>

                                <hr class="my-1">

                                <button @click="handleDelete"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                                    Delete Product
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Images and Basic Info -->
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Images -->
                                <div>
                                    <!-- Main Image -->
                                    <div class="aspect-w-1 aspect-h-1 mb-4">
                                        <img :src="currentImage" :alt="product.name"
                                            class="w-full h-96 object-cover rounded-lg border border-gray-200">
                                    </div>

                                    <!-- Thumbnail Images -->
                                    <div v-if="product.images?.length > 1" class="grid grid-cols-4 gap-2">
                                        <button v-for="(image, index) in product.images" :key="index"
                                            @click="currentImage = image"
                                            class="aspect-w-1 aspect-h-1 rounded-md border-2 transition-colors"
                                            :class="currentImage === image ? 'border-primary-500' : 'border-gray-300'">
                                            <img :src="image" :alt="`${product.name} ${index + 1}`"
                                                class="w-full h-20 object-cover rounded-md">
                                        </button>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="space-y-6">
                                    <!-- Price -->
                                    <div>
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span class="text-3xl font-bold text-gray-900">
                                                {{ formatCurrency(product.price) }}
                                            </span>
                                            <span v-if="product.compare_price && product.compare_price > product.price"
                                                class="text-lg text-gray-500 line-through">
                                                {{ formatCurrency(product.compare_price) }}
                                            </span>
                                        </div>
                                        <div v-if="product.compare_price && product.compare_price > product.price"
                                            class="text-green-600 font-medium">
                                            Save {{ formatCurrency(product.compare_price - product.price) }}
                                            ({{ Math.round(((product.compare_price - product.price) /
                                            product.compare_price) * 100) }}% off)
                                        </div>
                                    </div>

                                    <!-- Stock Status -->
                                    <div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <div class="w-3 h-3 rounded-full"
                                                :class="getStockStatusColor(product.stock_quantity)"></div>
                                            <span class="text-lg font-medium"
                                                :class="getStockStatusTextClass(product.stock_quantity)">
                                                {{ getStockStatusText(product.stock_quantity) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            {{ product.stock_quantity || 0 }} units available
                                            <span v-if="product.low_stock_threshold" class="ml-2">
                                                (Low stock alert: {{ product.low_stock_threshold }})
                                            </span>
                                        </p>
                                    </div>

                                    <!-- Category and Brand -->
                                    <div class="flex items-center space-x-4">
                                        <div v-if="product.category">
                                            <span class="text-sm text-gray-500">Category:</span>
                                            <span
                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ product.category.name }}
                                            </span>
                                        </div>
                                        <div v-if="product.brand">
                                            <span class="text-sm text-gray-500">Brand:</span>
                                            <span class="ml-2 font-medium text-gray-900">{{ product.brand.name }}</span>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                                        <div v-if="product.description" class="prose prose-sm max-w-none text-gray-600">
                                            <div v-html="product.description"></div>
                                        </div>
                                        <p v-else class="text-gray-500 italic">No description available</p>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="flex space-x-3">
                                        <button @click="handleUpdateStock" class="btn-outline flex-1">
                                            <font-awesome-icon icon="boxes" class="h-4 w-4 mr-2" />
                                            Update Stock
                                        </button>
                                        <button @click="toggleFeatured"
                                            :class="product.is_featured ? 'btn-warning' : 'btn-outline'">
                                            <font-awesome-icon icon="star" class="h-4 w-4 mr-2" />
                                            {{ product.is_featured ? 'Unfeature' : 'Feature' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Specifications -->
                    <div v-if="product.specifications?.length > 0" class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">Specifications</h3>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-for="spec in product.specifications" :key="spec.id || spec.name"
                                    class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-sm font-medium text-gray-600">{{ spec.name }}</span>
                                    <span class="text-sm text-gray-900">{{ spec.value }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Variants -->
                    <div v-if="product.variants?.length > 0" class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">Variants</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div v-for="variant in product.variants" :key="variant.id"
                                    class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <img v-if="variant.image" :src="variant.image" :alt="variant.name"
                                            class="w-12 h-12 rounded-md object-cover border border-gray-200">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ variant.name }}</h4>
                                            <p class="text-sm text-gray-500">SKU: {{ variant.sku }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ formatCurrency(variant.price)
                                            }}</div>
                                        <div class="text-sm text-gray-500">{{ variant.stock_quantity || 0 }} in stock
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Information -->
                    <div v-if="product.seo_title || product.seo_description" class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">SEO Information</h3>
                        </div>
                        <div class="card-body space-y-4">
                            <div v-if="product.seo_title">
                                <label class="text-sm font-medium text-gray-500">SEO Title</label>
                                <p class="text-sm text-gray-900 mt-1">{{ product.seo_title }}</p>
                            </div>
                            <div v-if="product.seo_description">
                                <label class="text-sm font-medium text-gray-500">SEO Description</label>
                                <p class="text-sm text-gray-900 mt-1">{{ product.seo_description }}</p>
                            </div>
                            <div v-if="product.seo_keywords">
                                <label class="text-sm font-medium text-gray-500">Keywords</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span v-for="keyword in product.seo_keywords.split(',')" :key="keyword.trim()"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ keyword.trim() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">Product Stats</h3>
                        </div>
                        <div class="card-body space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Created</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ formatDate(product.created_at) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Last Updated</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ formatDate(product.updated_at) }}
                                </span>
                            </div>
                            <div v-if="productStats" class="space-y-2 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Total Sales</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ productStats.total_sales || 0 }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Revenue</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ formatCurrency(productStats.total_revenue || 0) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Views</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ productStats.total_views || 0 }}
                                    </span>
                                </div>
                                <div v-if="productStats.average_rating" class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Rating</span>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex text-yellow-400">
                                            <font-awesome-icon v-for="i in 5" :key="i" icon="star"
                                                :class="i <= productStats.average_rating ? 'text-yellow-400' : 'text-gray-300'"
                                                class="h-3 w-3" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">
                                            ({{ productStats.reviews_count || 0 }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Controls -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">Status Controls</h3>
                        </div>
                        <div class="card-body space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-2 block">Product Status</label>
                                <select v-if="hasPermission('products.edit')" :value="product.status"
                                    @change="handleStatusChange($event.target.value)" class="form-input">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <div v-else class="text-sm text-gray-900">{{ getStatusLabel(product.status) }}</div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Featured Product</label>
                                <button v-if="hasPermission('products.edit')" @click="toggleFeatured" :class="product.is_featured
                                    ? 'bg-yellow-500 hover:bg-yellow-600'
                                    : 'bg-gray-300 hover:bg-gray-400'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                    <span :class="product.is_featured ? 'translate-x-5' : 'translate-x-0'"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span v-else class="text-sm text-gray-900">
                                    {{ product.is_featured ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Management -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">Inventory</h3>
                        </div>
                        <div class="card-body space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Current Stock</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">
                                        {{ product.stock_quantity || 0 }}
                                    </span>
                                    <button @click="handleUpdateStock" class="text-primary-600 hover:text-primary-800"
                                        title="Update Stock">
                                        <font-awesome-icon icon="edit" class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <div v-if="product.track_inventory">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Low Stock Threshold</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ product.low_stock_threshold || 0 }}
                                    </span>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300"
                                        :class="getStockLevelClass(product.stock_quantity, product.low_stock_threshold)"
                                        :style="{ width: `${getStockPercentage(product.stock_quantity)}%` }"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Track Inventory</span>
                                <span :class="product.track_inventory ? 'text-green-600' : 'text-gray-600'">
                                    {{ product.track_inventory ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Related Products -->
                    <div v-if="relatedProducts?.length > 0" class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-medium text-gray-900">Related Products</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-3">
                                <router-link v-for="related in relatedProducts.slice(0, 3)" :key="related.id"
                                    :to="{ name: 'admin.products.detail', params: { id: related.id } }"
                                    class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50 transition-colors">
                                    <img v-if="related.image" :src="related.image" :alt="related.name"
                                        class="w-12 h-12 rounded-md object-cover border border-gray-200">
                                    <div v-else
                                        class="w-12 h-12 rounded-md bg-gray-100 flex items-center justify-center border border-gray-200">
                                        <font-awesome-icon icon="image" class="h-4 w-4 text-gray-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ related.name }}</p>
                                        <p class="text-sm text-gray-500">{{ formatCurrency(related.price) }}</p>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <ProductDeleteModal v-model:show="showDeleteModal" :product="product" :loading="productsStore.isDeleting"
            @confirm="confirmDelete" />

        <StockUpdateModal v-model:show="showStockModal" :product="product" :loading="productsStore.isUpdating"
            @confirm="handleStockUpdate" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useProductsStore } from '@/stores/products'
import { format } from 'date-fns'
import ProductDeleteModal from '@/components/admin/products/ProductDeleteModal.vue'
import StockUpdateModal from '@/components/admin/products/StockUpdateModal.vue'

// Composables
const route = useRoute()
const router = useRouter()
const { hasPermission } = useAuth()
const productsStore = useProductsStore()

// Reactive data
const showActionsDropdown = ref(false)
const showDeleteModal = ref(false)
const showStockModal = ref(false)
const actionsDropdownRef = ref(null)
const currentImage = ref('')

// Computed
const product = computed(() => productsStore.currentProduct)
const productStats = computed(() => product.value?.stats)
const relatedProducts = computed(() => product.value?.related_products || [])

// Methods
const formatCurrency = (amount) => {
    if (!amount) return '$0.00'
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount)
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return format(new Date(date), 'MMM d, yyyy')
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

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        draft: 'bg-yellow-100 text-yellow-800',
        archived: 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusDotClass = (status) => {
    const classes = {
        active: 'bg-green-500',
        inactive: 'bg-gray-500',
        draft: 'bg-yellow-500',
        archived: 'bg-red-500'
    }
    return classes[status] || 'bg-gray-500'
}

const getStockStatusColor = (quantity) => {
    const qty = quantity || 0
    const threshold = product.value?.low_stock_threshold || 10

    if (qty === 0) return 'bg-red-500'
    if (qty <= threshold) return 'bg-yellow-500'
    return 'bg-green-500'
}

const getStockStatusText = (quantity) => {
    const qty = quantity || 0
    const threshold = product.value?.low_stock_threshold || 10

    if (qty === 0) return 'Out of Stock'
    if (qty <= threshold) return 'Low Stock'
    return 'In Stock'
}
const getStockStatusTextClass = (quantity) => {
    const qty = quantity || 0
    const threshold = product.value?.low_stock_threshold || 10

    if (qty === 0) return 'text-red-600'
    if (qty <= threshold) return 'text-yellow-600'
    return 'text-green-600'
}
const getStockLevelClass = (quantity, threshold) => {
    const qty = quantity || 0
    if (qty === 0) return 'bg-red-500'
    if (qty <= threshold) return 'bg-yellow-500'
    return 'bg-green-500'
}
const getStockPercentage = (quantity) => {
    const qty = quantity || 0
    const threshold = product.value?.low_stock_threshold || 10
    return Math.min((qty / threshold) * 100, 100)
}
const toggleFeatured = () => {
    if (!hasPermission('products.edit')) return
    productsStore.toggleFeatured(product.value.id)
}
const handleEdit = () => {
    if (!hasPermission('products.edit')) return
    router.push({ name: 'admin.products.edit', params: { id: product.value.id } })
}
const handleViewFrontend = () => {
    if (!product.value?.slug) return
    window.open(`/products/${product.value.slug}`, '_blank')
}
const handleDuplicate = () => {
    if (!hasPermission('products.create')) return
    productsStore.duplicateProduct(product.value.id)
}
const handleUpdateStock = () => {
    if (!hasPermission('products.edit')) return
    showStockModal.value = true
}
const handleStatusChange = (status) => {
    if (!hasPermission('products.edit')) return
    productsStore.updateProductStatus(product.value.id, status)
}
const handleDelete = () => {
    if (!hasPermission('products.delete')) return
    showDeleteModal.value = true
}
const confirmDelete = () => {
    productsStore.deleteProduct(product.value.id)
    showDeleteModal.value = false
}
const handleStockUpdate = (newStock) => {
    productsStore.updateProductStock(product.value.id, newStock)
    showStockModal.value = false
}
// Lifecycle hooks
onMounted(() => {
    const productId = route.params.id
    if (productId) {
        productsStore.fetchProduct(productId)
    }
    currentImage.value = product.value?.images?.[0] || ''
})
onUnmounted(() => {
    productsStore.resetCurrentProduct()
})
</script>