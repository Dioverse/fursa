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
                        placeholder="Search users by name, email, or phone..." @input="debouncedSearch"
                        :disabled="loading">
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Advanced Filters Toggle -->
                <button @click="showAdvancedFilters = !showAdvancedFilters" class="btn-outline"
                    :class="{ 'bg-gray-50': showAdvancedFilters }">
                    <font-awesome-icon icon="filter" class="h-4 w-4 mr-2" />
                    Filters
                    <font-awesome-icon :icon="showAdvancedFilters ? 'chevron-up' : 'chevron-down'"
                        class="h-4 w-4 ml-2" />
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
                <!-- Role Filter -->
                <div>
                    <label class="form-label">Role</label>
                    <select v-model="localFilters.role" @change="emitUpdate" class="form-input" :disabled="loading">
                        <option value="">All Roles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="editor">Editor</option>
                        <option value="customer">Customer</option>
                        <option value="distributor">Distributor</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="form-label">Status</label>
                    <select v-model="localFilters.status" @change="emitUpdate" class="form-input" :disabled="loading">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
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
                    <input v-model="localFilters.date_to" @change="emitUpdate" type="date" class="form-input"
                        :disabled="loading">
                </div>
            </div>

            <!-- Additional Filters Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Sort By -->
                <div>
                    <label class="form-label">Sort By</label>
                    <select v-model="localFilters.sort_by" @change="emitUpdate" class="form-input" :disabled="loading">
                        <option value="created_at">Created Date</option>
                        <option value="updated_at">Updated Date</option>
                        <option value="name">Name</option>
                        <option value="email">Email</option>
                        <option value="role">Role</option>
                        <option value="status">Status</option>
                        <option value="last_login_at">Last Login</option>
                    </select>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="form-label">Sort Order</label>
                    <select v-model="localFilters.sort_order" @change="emitUpdate" class="form-input"
                        :disabled="loading">
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
                    Created Today
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

                <button @click="applyQuickFilter('active_users')"
                    class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                    :disabled="loading">
                    Active Only
                </button>

                <button @click="applyQuickFilter('pending_users')"
                    class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                    :disabled="loading">
                    Pending Only
                </button>

                <button @click="applyQuickFilter('admins')"
                    class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                    :disabled="loading">
                    Admins Only
                </button>
            </div>

            <!-- Active Filters Display -->
            <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Active Filters:</span>

                <div v-if="localFilters.role"
                    class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-md">
                    Role: {{ getRoleLabel(localFilters.role) }}
                    <button @click="clearFilter('role')" class="ml-1 text-blue-600 hover:text-blue-800">
                        <font-awesome-icon icon="times" class="h-3 w-3" />
                    </button>
                </div>

                <div v-if="localFilters.status"
                    class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-md">
                    Status: {{ getStatusLabel(localFilters.status) }}
                    <button @click="clearFilter('status')" class="ml-1 text-green-600 hover:text-green-800">
                        <font-awesome-icon icon="times" class="h-3 w-3" />
                    </button>
                </div>

                <div v-if="localFilters.date_from"
                    class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-md">
                    From: {{ formatDate(localFilters.date_from) }}
                    <button @click="clearFilter('date_from')" class="ml-1 text-purple-600 hover:text-purple-800">
                        <font-awesome-icon icon="times" class="h-3 w-3" />
                    </button>
                </div>

                <div v-if="localFilters.date_to"
                    class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-md">
                    To: {{ formatDate(localFilters.date_to) }}
                    <button @click="clearFilter('date_to')" class="ml-1 text-purple-600 hover:text-purple-800">
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
        if (key === 'sort_by' || key === 'sort_order') return false
        return localFilters[key] !== '' && localFilters[key] !== null
    })
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

        case 'active_users':
            localFilters.status = 'active'
            break

        case 'pending_users':
            localFilters.status = 'pending'
            break

        case 'admins':
            localFilters.role = 'admin'
            break
    }

    emitUpdate()
}

const clearFilter = (filterKey) => {
    localFilters[filterKey] = ''
    emitUpdate()
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