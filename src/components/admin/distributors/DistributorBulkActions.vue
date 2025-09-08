<!-- DistributorBulkActions.vue -->
<template>
    <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <CheckCircleIcon class="h-5 w-5 text-primary-600 mr-2" />
                <span class="text-sm text-primary-700 font-medium">
                    {{ selectedCount }} distributor{{ selectedCount > 1 ? 's' : '' }} selected
                </span>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Quick Actions Dropdown -->
                <div class="relative" ref="dropdownRef">
                    <button @click="showQuickActions = !showQuickActions"
                        class="inline-flex items-center px-3 py-1 border border-primary-300 text-sm font-medium rounded text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <BoltIcon class="w-4 h-4 mr-1" />
                        Quick Actions
                        <ChevronDownIcon class="w-4 h-4 ml-1" />
                    </button>

                    <!-- Quick Actions Dropdown Menu -->
                    <Transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <div v-if="showQuickActions"
                            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <button @click="handleQuickAction('send-email')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <EnvelopeIcon class="w-4 h-4 inline mr-2" />
                                    Send Email
                                </button>
                                <button @click="handleQuickAction('export')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <DocumentArrowDownIcon class="w-4 h-4 inline mr-2" />
                                    Export Selected
                                </button>
                                <button @click="handleQuickAction('assign-territory')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <MapIcon class="w-4 h-4 inline mr-2" />
                                    Assign Territory
                                </button>
                                <button @click="handleQuickAction('schedule-meeting')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <CalendarIcon class="w-4 h-4 inline mr-2" />
                                    Schedule Meeting
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Primary Actions -->
                <button @click="$emit('bulk-approve')"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <CheckIcon class="w-4 h-4 mr-1" />
                    Approve
                </button>

                <button @click="$emit('bulk-suspend')"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <PauseIcon class="w-4 h-4 mr-1" />
                    Suspend
                </button>

                <button @click="handleReject"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <XMarkIcon class="w-4 h-4 mr-1" />
                    Reject
                </button>

                <!-- More Actions Dropdown -->
                <div class="relative" ref="moreDropdownRef">
                    <button @click="showMoreActions = !showMoreActions"
                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <EllipsisHorizontalIcon class="w-4 h-4" />
                    </button>

                    <!-- More Actions Dropdown Menu -->
                    <Transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <div v-if="showMoreActions"
                            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <button @click="handleMoreAction('activate')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <PlayIcon class="w-4 h-4 inline mr-2" />
                                    Activate
                                </button>
                                <button @click="handleMoreAction('deactivate')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <StopIcon class="w-4 h-4 inline mr-2" />
                                    Deactivate
                                </button>
                                <div class="border-t border-gray-100"></div>
                                <button @click="handleMoreAction('add-note')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <DocumentTextIcon class="w-4 h-4 inline mr-2" />
                                    Add Note
                                </button>
                                <button @click="handleMoreAction('update-commission')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">
                                    <CurrencyDollarIcon class="w-4 h-4 inline mr-2" />
                                    Update Commission
                                </button>
                                <div class="border-t border-gray-100"></div>
                                <button @click="handleMoreAction('delete')"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 hover:text-red-900"
                                    role="menuitem">
                                    <TrashIcon class="w-4 h-4 inline mr-2" />
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <!-- Action Status Message -->
        <div v-if="actionStatus.show" class="mt-3 p-3 rounded-md" :class="actionStatusClasses">
            <div class="flex">
                <div class="flex-shrink-0">
                    <component :is="actionStatus.type === 'success' ? CheckCircleIcon : ExclamationTriangleIcon"
                        :class="actionStatus.type === 'success' ? 'text-green-400' : 'text-yellow-400'"
                        class="h-5 w-5" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium"
                        :class="actionStatus.type === 'success' ? 'text-green-800' : 'text-yellow-800'">
                        {{ actionStatus.message }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="actionStatus.show = false" type="button"
                            :class="actionStatus.type === 'success' ? 'text-green-500 hover:bg-green-100' : 'text-yellow-500 hover:bg-yellow-100'"
                            class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

// Icons
import {
    CheckCircleIcon,
    CheckIcon,
    PauseIcon,
    XMarkIcon,
    EllipsisHorizontalIcon,
    ChevronDownIcon,
    BoltIcon,
    EnvelopeIcon,
    DocumentArrowDownIcon,
    MapIcon,
    CalendarIcon,
    PlayIcon,
    StopIcon,
    DocumentTextIcon,
    CurrencyDollarIcon,
    TrashIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    selectedCount: {
        type: Number,
        required: true
    }
})

// Emits
const emit = defineEmits([
    'bulk-approve',
    'bulk-suspend',
    'bulk-reject',
    'bulk-activate',
    'bulk-deactivate',
    'bulk-delete',
    'bulk-export',
    'bulk-email',
    'bulk-assign-territory',
    'bulk-schedule-meeting',
    'bulk-add-note',
    'bulk-update-commission'
])

// Local state
const showQuickActions = ref(false)
const showMoreActions = ref(false)
const dropdownRef = ref(null)
const moreDropdownRef = ref(null)

const actionStatus = ref({
    show: false,
    type: 'success', // 'success' | 'warning' | 'error'
    message: ''
})

// Computed
const actionStatusClasses = computed(() => {
    return {
        'bg-green-50 border border-green-200': actionStatus.value.type === 'success',
        'bg-yellow-50 border border-yellow-200': actionStatus.value.type === 'warning',
        'bg-red-50 border border-red-200': actionStatus.value.type === 'error'
    }
})

// Methods
const handleReject = () => {
    if (confirm(`Are you sure you want to reject ${props.selectedCount} distributor${props.selectedCount > 1 ? 's' : ''}?`)) {
        emit('bulk-reject')
        showActionStatus('success', `${props.selectedCount} distributor${props.selectedCount > 1 ? 's' : ''} rejected successfully`)
    }
}

const handleQuickAction = (action) => {
    showQuickActions.value = false

    switch (action) {
        case 'send-email':
            emit('bulk-email')
            showActionStatus('success', 'Email composer opened')
            break
        case 'export':
            emit('bulk-export')
            showActionStatus('success', 'Export started')
            break
        case 'assign-territory':
            emit('bulk-assign-territory')
            showActionStatus('success', 'Territory assignment dialog opened')
            break
        case 'schedule-meeting':
            emit('bulk-schedule-meeting')
            showActionStatus('success', 'Meeting scheduler opened')
            break
        default:
            break
    }
}

const handleMoreAction = (action) => {
    showMoreActions.value = false

    switch (action) {
        case 'activate':
            emit('bulk-activate')
            showActionStatus('success', `${props.selectedCount} distributor${props.selectedCount > 1 ? 's' : ''} activated`)
            break
        case 'deactivate':
            emit('bulk-deactivate')
            showActionStatus('warning', `${props.selectedCount} distributor${props.selectedCount > 1 ? 's' : ''} deactivated`)
            break
        case 'add-note':
            emit('bulk-add-note')
            showActionStatus('success', 'Note editor opened')
            break
        case 'update-commission':
            emit('bulk-update-commission')
            showActionStatus('success', 'Commission update dialog opened')
            break
        case 'delete':
            if (confirm(`Are you sure you want to delete ${props.selectedCount} distributor${props.selectedCount > 1 ? 's' : ''}? This action cannot be undone.`)) {
                emit('bulk-delete')
                showActionStatus('success', `${props.selectedCount} distributor${props.selectedCount > 1 ? 's' : ''} deleted`)
            }
            break
        default:
            break
    }
}

const showActionStatus = (type, message) => {
    actionStatus.value = {
        show: true,
        type,
        message
    }

    // Auto-hide after 5 seconds
    setTimeout(() => {
        actionStatus.value.show = false
    }, 5000)
}

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        showQuickActions.value = false
    }
    if (moreDropdownRef.value && !moreDropdownRef.value.contains(event.target)) {
        showMoreActions.value = false
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