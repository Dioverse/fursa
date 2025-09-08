<!-- DistributorEditModal.vue -->
<template>
    <TransitionRoot as="template" :show="show">
        <Dialog as="div" class="relative z-50" @close="handleClose">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                            <!-- Header -->
                            <div class="bg-white px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                            Edit Distributor
                                        </DialogTitle>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Update distributor information and settings
                                        </p>
                                    </div>
                                    <button @click="handleClose"
                                        class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <XMarkIcon class="h-6 w-6" />
                                    </button>
                                </div>
                            </div>

                            <!-- Form -->
                            <form @submit.prevent="updateDistributor" class="px-6 py-6">
                                <!-- Tabs -->
                                <div class="border-b border-gray-200 mb-6">
                                    <nav class="-mb-px flex space-x-8">
                                        <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                                            type="button" :class="[
                                                activeTab === tab.id
                                                    ? 'border-primary-500 text-primary-600'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                                'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                                            ]">
                                            <component :is="tab.icon" class="w-5 h-5 mr-2 inline" />
                                            {{ tab.label }}
                                        </button>
                                    </nav>
                                </div>

                                <!-- Tab Content -->
                                <div class="space-y-6">
                                    <!-- Personal Information Tab -->
                                    <div v-show="activeTab === 'personal'" class="space-y-6">
                                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    First Name *
                                                </label>
                                                <input v-model="form.firstName" type="text" required
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.firstName }" />
                                                <p v-if="errors.firstName" class="mt-1 text-xs text-red-600">
                                                    {{ errors.firstName }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Last Name *
                                                </label>
                                                <input v-model="form.lastName" type="text" required
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.lastName }" />
                                                <p v-if="errors.lastName" class="mt-1 text-xs text-red-600">
                                                    {{ errors.lastName }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Email Address *
                                                </label>
                                                <input v-model="form.email" type="email" required
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    :class="{ 'border-red-300': errors.email }" />
                                                <p v-if="errors.email" class="mt-1 text-xs text-red-600">
                                                    {{ errors.email }}
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Phone Number
                                                </label>
                                                <input v-model="form.phone" type="tel"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Date of Birth
                                            </label>
                                            <input v-model="form.dateOfBirth" type="date"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Address
                                            </label>
                                            <textarea v-model="form.address" rows="3"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                placeholder="Enter full address..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Business Information Tab -->
                                    <div v-show="activeTab === 'business'" class="space-y-6">
                                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Business Name
                                                </label>
                                                <input v-model="form.businessName" type="text"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    placeholder="Leave empty for individual" />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Business Type
                                                </label>
                                                <select v-model="form.businessType"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                                    <option value="individual">Individual</option>
                                                    <option value="partnership">Partnership</option>
                                                    <option value="corporation">Corporation</option>
                                                    <option value="llc">LLC</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Tax ID / EIN
                                                </label>
                                                <input v-model="form.taxId" type="text"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    placeholder="XX-XXXXXXX" />
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    License Number
                                                </label>
                                                <input v-model="form.licenseNumber" type="text"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Years of Experience
                                            </label>
                                            <input v-model="form.yearsOfExperience" type="number" min="0" max="50"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Business Description
                                            </label>
                                            <textarea v-model="form.businessDescription" rows="3"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                placeholder="Describe your business background and expertise..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Territory & Commission Tab -->
                                    <div v-show="activeTab === 'territory'" class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Assigned Territory
                                            </label>
                                            <select v-model="form.territoryId"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                                <option value="">Select Territory</option>
                                                <option v-for="territory in territories" :key="territory.id"
                                                    :value="territory.id">
                                                    {{ territory.name }} - {{ territory.state }}
                                                </option>
                                            </select>
                                            <p class="mt-1 text-sm text-gray-500">
                                                Territory assignment determines the distributor's coverage area
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Commission Rate (%)
                                                </label>
                                                <div class="relative">
                                                    <input v-model="form.commissionRate" type="number" min="0" max="100"
                                                        step="0.1"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm pr-8" />
                                                    <div
                                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Sales Target (Monthly)
                                                </label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">$</span>
                                                    </div>
                                                    <input v-model="form.salesTarget" type="number" min="0"
                                                        class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Special Terms & Conditions
                                            </label>
                                            <textarea v-model="form.specialTerms" rows="3"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                placeholder="Any special terms, bonuses, or conditions..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Status & Settings Tab -->
                                    <div v-show="activeTab === 'settings'" class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Account Status
                                            </label>
                                            <select v-model="form.status"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="active">Active</option>
                                                <option value="suspended">Suspended</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>

                                        <!-- Permissions -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                                Permissions & Access
                                            </label>
                                            <div class="space-y-3">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input v-model="form.canViewReports" type="checkbox"
                                                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded" />
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label class="font-medium text-gray-700">View Sales
                                                            Reports</label>
                                                        <p class="text-gray-500">Allow access to sales and performance
                                                            reports</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input v-model="form.canManageOrders" type="checkbox"
                                                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded" />
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label class="font-medium text-gray-700">Manage Orders</label>
                                                        <p class="text-gray-500">Allow creating and modifying orders</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input v-model="form.canAccessTraining" type="checkbox"
                                                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded" />
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label class="font-medium text-gray-700">Access Training
                                                            Materials</label>
                                                        <p class="text-gray-500">Allow access to training resources and
                                                            materials</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input v-model="form.receiveNotifications" type="checkbox"
                                                            class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded" />
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label class="font-medium text-gray-700">Email
                                                            Notifications</label>
                                                        <p class="text-gray-500">Send email updates about orders,
                                                            commissions, and news</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Internal Notes
                                            </label>
                                            <textarea v-model="form.internalNotes" rows="4"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                placeholder="Internal notes about this distributor (not visible to distributor)..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Display -->
                                <div v-if="generalError" class="mt-6 rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                                            <p class="text-sm text-red-700 mt-1">{{ generalError }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="mt-8 flex justify-between">
                                    <button type="button" @click="resetForm"
                                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                        :disabled="loading">
                                        Reset Changes
                                    </button>

                                    <div class="flex space-x-3">
                                        <button type="button" @click="handleClose"
                                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                            :disabled="loading">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="loading || !isFormValid"
                                            class="rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span v-if="loading" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Updating...
                                            </span>
                                            <span v-else>Update Distributor</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useDistributorStore } from '@/stores/distributorStore'

// Icons
import {
    XMarkIcon,
    ExclamationTriangleIcon,
    UserIcon,
    BuildingOfficeIcon,
    MapIcon,
    CogIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    distributor: {
        type: Object,
        default: null
    },
    territories: {
        type: Array,
        default: () => []
    }
})

// Emits
const emit = defineEmits(['update:show', 'updated'])

// Store
const distributorStore = useDistributorStore()

// Local state
const loading = ref(false)
const generalError = ref('')
const activeTab = ref('personal')
const hasUnsavedChanges = ref(false)

const tabs = [
    { id: 'personal', label: 'Personal Info', icon: UserIcon },
    { id: 'business', label: 'Business Info', icon: BuildingOfficeIcon },
    { id: 'territory', label: 'Territory & Commission', icon: MapIcon },
    { id: 'settings', label: 'Settings', icon: CogIcon }
]

const form = reactive({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    dateOfBirth: '',
    address: '',
    businessName: '',
    businessType: 'individual',
    taxId: '',
    licenseNumber: '',
    yearsOfExperience: '',
    businessDescription: '',
    territoryId: '',
    commissionRate: '',
    salesTarget: '',
    specialTerms: '',
    status: 'active',
    canViewReports: true,
    canManageOrders: true,
    canAccessTraining: true,
    receiveNotifications: true,
    internalNotes: ''
})

const errors = reactive({
    firstName: '',
    lastName: '',
    email: ''
})

// Computed
const isFormValid = computed(() => {
    return form.firstName && form.lastName && form.email && !Object.values(errors).some(error => error)
})

// Methods
const populateForm = () => {
    if (!props.distributor) return

    const d = props.distributor
    form.firstName = d.first_name || d.name?.split(' ')[0] || ''
    form.lastName = d.last_name || d.name?.split(' ').slice(1).join(' ') || ''
    form.email = d.email || ''
    form.phone = d.phone || ''
    form.dateOfBirth = d.date_of_birth || ''
    form.address = d.address || ''
    form.businessName = d.business_name || ''
    form.businessType = d.business_type || 'individual'
    form.taxId = d.tax_id || ''
    form.licenseNumber = d.license_number || ''
    form.yearsOfExperience = d.years_of_experience || ''
    form.businessDescription = d.business_description || ''
    form.territoryId = d.territory_id || ''
    form.commissionRate = d.commission_rate || ''
    form.salesTarget = d.sales_target || ''
    form.specialTerms = d.special_terms || ''
    form.status = d.status || 'active'
    form.canViewReports = d.permissions?.can_view_reports ?? true
    form.canManageOrders = d.permissions?.can_manage_orders ?? true
    form.canAccessTraining = d.permissions?.can_access_training ?? true
    form.receiveNotifications = d.preferences?.receive_notifications ?? true
    form.internalNotes = d.internal_notes || ''

    hasUnsavedChanges.value = false
}

const resetForm = () => {
    populateForm()
    Object.keys(errors).forEach(key => errors[key] = '')
    generalError.value = ''
    hasUnsavedChanges.value = false
}

const validateForm = () => {
    // Clear previous errors
    Object.keys(errors).forEach(key => errors[key] = '')
    generalError.value = ''

    // Validate required fields
    if (!form.firstName.trim()) {
        errors.firstName = 'First name is required'
    }

    if (!form.lastName.trim()) {
        errors.lastName = 'Last name is required'
    }

    if (!form.email.trim()) {
        errors.email = 'Email is required'
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address'
    }

    return !Object.values(errors).some(error => error)
}

const updateDistributor = async () => {
    if (!validateForm()) return

    loading.value = true
    generalError.value = ''

    try {
        const updateData = {
            first_name: form.firstName,
            last_name: form.lastName,
            email: form.email,
            phone: form.phone,
            date_of_birth: form.dateOfBirth || null,
            address: form.address,
            business_name: form.businessName,
            business_type: form.businessType,
            tax_id: form.taxId,
            license_number: form.licenseNumber,
            years_of_experience: form.yearsOfExperience || null,
            business_description: form.businessDescription,
            territory_id: form.territoryId || null,
            commission_rate: form.commissionRate || null,
            sales_target: form.salesTarget || null,
            special_terms: form.specialTerms,
            status: form.status,
            permissions: {
                can_view_reports: form.canViewReports,
                can_manage_orders: form.canManageOrders,
                can_access_training: form.canAccessTraining
            },
            preferences: {
                receive_notifications: form.receiveNotifications
            },
            internal_notes: form.internalNotes
        }

        await distributorStore.updateDistributor(props.distributor.id, updateData)

        hasUnsavedChanges.value = false
        emit('updated')
        emit('update:show', false)
    } catch (error) {
        generalError.value = error.response?.data?.message || 'Failed to update distributor. Please try again.'
    } finally {
        loading.value = false
    }
}

const handleClose = () => {
    if (hasUnsavedChanges.value) {
        if (confirm('You have unsaved changes. Are you sure you want to close without saving?')) {
            emit('update:show', false)
            hasUnsavedChanges.value = false
        }
    } else {
        emit('update:show', false)
    }
}

// Watch for form changes
watch(form, () => {
    hasUnsavedChanges.value = true
}, { deep: true })

// Watch for email changes to validate in real-time
watch(() => form.email, (newEmail) => {
    if (newEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newEmail)) {
        errors.email = 'Please enter a valid email address'
    } else {
        errors.email = ''
    }
})

// Populate form when distributor changes
watch(() => props.distributor, (newDistributor) => {
    if (newDistributor) {
        populateForm()
    }
}, { immediate: true })

// Reset when modal is opened
watch(() => props.show, (show) => {
    if (show) {
        activeTab.value = 'personal'
        resetForm()
    }
})
</script>