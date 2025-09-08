<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="closeModal">
        <div class="bg-white rounded-xl shadow-strong max-w-md w-full animate-fade-in" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Delete User
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
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <font-awesome-icon icon="exclamation-triangle" class="h-5 w-5 text-red-600" />
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 mb-4">
                            Are you sure you want to permanently delete this user? This action cannot be undone.
                        </p>

                        <!-- User Info -->
                        <div v-if="user" class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <img v-if="user.avatar" :src="user.avatar" :alt="user.name"
                                        class="h-10 w-10 rounded-full object-cover">
                                    <div v-else
                                        class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary-600">
                                            {{ getUserInitials(user.name) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ user.name }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ user.email }}
                                    </p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ getRoleLabel(user.role) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="getStatusClass(user.status)">
                                            {{ getStatusLabel(user.status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consequences -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-red-800 mb-2">
                                What will be deleted:
                            </h4>
                            <ul class="text-sm text-red-700 space-y-1">
                                <li class="flex items-center">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2" />
                                    User account and profile information
                                </li>
                                <li class="flex items-center">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2" />
                                    Order history and transactions
                                </li>
                                <li class="flex items-center">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2" />
                                    Account preferences and settings
                                </li>
                                <li class="flex items-center">
                                    <font-awesome-icon icon="check" class="h-3 w-3 mr-2" />
                                    Associated files and documents
                                </li>
                            </ul>
                        </div>

                        <!-- Alternative Actions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">
                                Consider these alternatives:
                            </h4>
                            <div class="space-y-2">
                                <button @click="handleDeactivate"
                                    class="w-full text-left text-sm text-blue-700 hover:text-blue-800 flex items-center"
                                    :disabled="loading">
                                    <font-awesome-icon icon="user-times" class="h-3 w-3 mr-2" />
                                    Deactivate user instead (preserves data)
                                </button>
                                <button @click="handleSuspend"
                                    class="w-full text-left text-sm text-blue-700 hover:text-blue-800 flex items-center"
                                    :disabled="loading">
                                    <font-awesome-icon icon="user-slash" class="h-3 w-3 mr-2" />
                                    Suspend user temporarily
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
                    {{ loading ? 'Deleting...' : 'Delete User' }}
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
    user: {
        type: Object,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    }
})

// Emits
const emit = defineEmits(['update:show', 'confirm', 'deactivate', 'suspend'])

// Methods
const getUserInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase()
}

const getRoleLabel = (role) => {
    const labels = {
        super_admin: 'Super Admin',
        admin: 'Admin',
        manager: 'Manager',
        editor: 'Editor',
        customer: 'Customer',
        distributor: 'Distributor'
    }
    return labels[role] || role
}

const getStatusLabel = (status) => {
    const labels = {
        active: 'Active',
        inactive: 'Inactive',
        pending: 'Pending',
        suspended: 'Suspended'
    }
    return labels[status] || status
}

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        suspended: 'bg-red-100 text-red-800'
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
    emit('deactivate', props.user)
    closeModal()
}

const handleSuspend = () => {
    emit('suspend', props.user)
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