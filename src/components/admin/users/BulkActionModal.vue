<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="closeModal">
        <div class="bg-white rounded-xl shadow-strong max-w-md w-full animate-fade-in" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ modalTitle }}
                </h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"
                    :disabled="loading">
                    <font-awesome-icon icon="times" class="h-5 w-5" />
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                        :class="iconClass">
                        <font-awesome-icon :icon="actionIcon" class="h-5 w-5" :class="iconTextClass" />
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 mb-4">
                            {{ confirmationMessage }}
                        </p>

                        <!-- User List -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4 max-h-40 overflow-y-auto">
                            <div class="text-xs font-medium text-gray-500 mb-2">
                                Selected Users ({{ users.length }})
                            </div>
                            <div class="space-y-1">
                                <div v-for="user in users.slice(0, 5)" :key="user.id"
                                    class="flex items-center space-x-2 text-sm">
                                    <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-xs font-medium text-primary-600">
                                            {{ getUserInitials(user.name) }}
                                        </span>
                                    </div>
                                    <span class="text-gray-900">{{ user.name }}</span>
                                    <span class="text-gray-500">{{ user.email }}</span>
                                </div>

                                <div v-if="users.length > 5" class="text-xs text-gray-500 italic">
                                    and {{ users.length - 5 }} more...
                                </div>
                            </div>
                        </div>

                        <!-- Additional Form Fields -->
                        <div v-if="showAdditionalFields" class="space-y-4">
                            <!-- Role selection for role change -->
                            <div v-if="action === 'change_role'">
                                <label class="form-label">New Role</label>
                                <select v-model="additionalData.role" class="form-input" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="editor">Editor</option>
                                    <option value="customer">Customer</option>
                                    <option value="distributor">Distributor</option>
                                </select>
                            </div>

                            <!-- Reason for suspension -->
                            <div v-if="action === 'suspend'">
                                <label class="form-label">Reason for Suspension (Optional)</label>
                                <textarea v-model="additionalData.reason" class="form-input" rows="3"
                                    placeholder="Provide a reason for suspension..."></textarea>
                            </div>

                            <!-- Email notification option -->
                            <div v-if="['activate', 'deactivate', 'suspend'].includes(action)">
                                <label class="flex items-center">
                                    <input v-model="additionalData.notify_users" type="checkbox"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">
                                        Send email notification to users
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Warning for destructive actions -->
                        <div v-if="isDestructive" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <font-awesome-icon icon="exclamation-triangle" class="h-4 w-4 text-red-500 mt-0.5" />
                                <div class="text-sm text-red-700">
                                    <strong>Warning:</strong> This action cannot be undone.
                                    {{ destructiveWarning }}
                                </div>
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
import { ref, reactive, computed, watch } from 'vue'

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
    users: {
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
    role: '',
    reason: '',
    notify_users: true
})

// Computed
const modalTitle = computed(() => {
    const titles = {
        activate: 'Activate Users',
        deactivate: 'Deactivate Users',
        suspend: 'Suspend Users',
        delete: 'Delete Users',
        change_role: 'Change User Roles',
        export: 'Export Users'
    }
    return titles[props.action] || 'Bulk Action'
})

const confirmationMessage = computed(() => {
    const userCount = props.users.length
    const userText = userCount === 1 ? 'user' : 'users'

    const messages = {
        activate: `Are you sure you want to activate ${userCount} ${userText}? This will enable their access to the system.`,
        deactivate: `Are you sure you want to deactivate ${userCount} ${userText}? This will disable their access to the system.`,
        suspend: `Are you sure you want to suspend ${userCount} ${userText}? This will temporarily disable their access.`,
        delete: `Are you sure you want to permanently delete ${userCount} ${userText}? This action cannot be undone.`,
        change_role: `Are you sure you want to change the role for ${userCount} ${userText}?`,
        export: `Export ${userCount} ${userText} to a file?`
    }
    return messages[props.action] || `Perform bulk action on ${userCount} ${userText}?`
})

const actionIcon = computed(() => {
    const icons = {
        activate: 'user-check',
        deactivate: 'user-times',
        suspend: 'user-slash',
        delete: 'trash',
        change_role: 'user-cog',
        export: 'download'
    }
    return icons[props.action] || 'cogs'
})

const iconClass = computed(() => {
    const classes = {
        activate: 'bg-green-100',
        deactivate: 'bg-gray-100',
        suspend: 'bg-yellow-100',
        delete: 'bg-red-100',
        change_role: 'bg-blue-100',
        export: 'bg-purple-100'
    }
    return classes[props.action] || 'bg-gray-100'
})

const iconTextClass = computed(() => {
    const classes = {
        activate: 'text-green-600',
        deactivate: 'text-gray-600',
        suspend: 'text-yellow-600',
        delete: 'text-red-600',
        change_role: 'text-blue-600',
        export: 'text-purple-600'
    }
    return classes[props.action] || 'text-gray-600'
})

const actionButtonText = computed(() => {
    const texts = {
        activate: 'Activate Users',
        deactivate: 'Deactivate Users',
        suspend: 'Suspend Users',
        delete: 'Delete Users',
        change_role: 'Change Roles',
        export: 'Export Users'
    }
    return texts[props.action] || 'Confirm'
})

const isDestructive = computed(() => {
    return ['delete', 'suspend'].includes(props.action)
})

const destructiveWarning = computed(() => {
    const warnings = {
        delete: 'All user data, including their orders and activity history, will be permanently removed.',
        suspend: 'Suspended users will not be able to access their accounts until reactivated.'
    }
    return warnings[props.action] || ''
})

const showAdditionalFields = computed(() => {
    return ['change_role', 'suspend', 'activate', 'deactivate'].includes(props.action)
})

const canConfirm = computed(() => {
    if (props.action === 'change_role') {
        return additionalData.role !== ''
    }
    return true
})

// Methods
const getUserInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase()
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
    additionalData.role = ''
    additionalData.reason = ''
    additionalData.notify_users = true
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