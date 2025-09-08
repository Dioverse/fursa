<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="closeModal">
        <div class="bg-white rounded-xl shadow-strong max-w-lg w-full animate-fade-in" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Delete Product
                </h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"
                    :disabled="loading">
                    <font-awesome-icon icon="times" class="h-5 w-5" />
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <!-- Warning Icon -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <font-awesome-icon icon="exclamation-triangle" class="h-6 w-6 text-red-600" />
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 mb-4">
                            Are you sure you want to permanently delete this product? This action cannot be undone.
                        </p>

                        <!-- Product Info -->
                        <div v-if="product" class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img v-if="product.image || product.images?.[0]"
                                        :src="product.image || product.images?.[0]" :alt="product.name"
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
                                    <div class="flex items-center space-x-3 mt-2">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="getStatusClass(product.status)">
                                            {{ getStatusLabel(product.status) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ formatCurrency(product.price) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ product.stock_quantity || 0 }} in stock
                                        </span>
                                    </div>
                                    <div v-if="product.category" class="mt-1">
                                        <span class="text-xs text-gray-500">
                                            Category: {{ product.category.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Impact Analysis -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-red-800 mb-3 flex items-center">
                                <font-awesome-icon icon="exclamation-triangle" class="h-4 w-4 mr-2" />
                                What will be deleted:
                            </h4>
                            <ul class="text-sm text-red-700 space-y-2">
                                <li class="flex items-start">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2 mt-0.5" />
                                    <span>Product information and description</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2 mt-0.5" />
                                    <span>All product images and media files</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2 mt-0.5" />
                                    <span>Product variants and specifications</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2 mt-0.5" />
                                    <span>Inventory and stock history</span>
                                </li>
                                <li class="flex items-start">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2 mt-0.5" />
                                    <span>Sales analytics and performance data</span>
                                </li>
                                <li v-if="product?.reviews_count > 0" class="flex items-start">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2 mt-0.5" />
                                    <span>{{ product.reviews_count }} customer reviews</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Order Impact Warning -->
                        <div v-if="product?.orders_count > 0"
                            class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <font-awesome-icon icon="shopping-cart" class="h-4 w-4 text-yellow-600 mr-2 mt-0.5" />
                                <div class="text-sm text-yellow-800">
                                    <strong>Important:</strong> This product has been ordered {{ product.orders_count }}
                                    times.
                                    Deleting it may affect order history and reporting. Consider deactivating instead.
                                </div>
                            </div>
                        </div>

                        <!-- Alternative Actions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-3 flex items-center">
                                <font-awesome-icon icon="lightbulb" class="h-4 w-4 mr-2" />
                                Consider these alternatives:
                            </h4>
                            <div class="space-y-2">
                                <button @click="handleDeactivate"
                                    class="w-full text-left text-sm text-blue-700 hover:text-blue-800 flex items-center p-2 hover:bg-blue-100 rounded-md transition-colors"
                                    :disabled="loading">
                                    <font-awesome-icon icon="eye-slash" class="h-3 w-3 mr-3" />
                                    Deactivate product (hide from customers, preserve data)
                                </button>

                                <button @click="handleArchive"
                                    class="w-full text-left text-sm text-blue-700 hover:text-blue-800 flex items-center p-2 hover:bg-blue-100 rounded-md transition-colors"
                                    :disabled="loading">
                                    <font-awesome-icon icon="archive" class="h-3 w-3 mr-3" />
                                    Archive product (move to archived status)
                                </button>

                                <button @click="handleOutOfStock"
                                    class="w-full text-left text-sm text-blue-700 hover:text-blue-800 flex items-center p-2 hover:bg-blue-100 rounded-md transition-colors"
                                    :disabled="loading">
                                    <font-awesome-icon icon="boxes" class="h-3 w-3 mr-3" />
                                    Mark as out of stock (temporarily unavailable)
                                </button>
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

                <button @click="confirmDelete" :disabled="loading" class="btn-danger">
                    <font-awesome-icon v-if="loading" icon="spinner" class="animate-spin mr-2" />
                    <font-awesome-icon v-else icon="trash" class="mr-2" />
                    {{ loading ? 'Deleting...' : 'Delete Product' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

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
const emit = defineEmits(['update:show', 'confirm', 'deactivate', 'archive', 'outOfStock'])

// Methods
const formatCurrency = (amount) => {
    if (!amount) return '$0.00'
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount)
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

const closeModal = () => {
    if (!props.loading) {
        emit('update:show', false)
    }
}

const confirmDelete = () => {
    if (!props.loading) {
        emit('confirm')
    }
}

const handleDeactivate = () => {
    emit('deactivate', props.product)
    closeModal()
}

const handleArchive = () => {
    emit('archive', props.product)
    closeModal()
}

const handleOutOfStock = () => {
    emit('outOfStock', props.product)
    closeModal()
}

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
/* Fade in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}
</style>