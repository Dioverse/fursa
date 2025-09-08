<!-- DistributorDetailsModal.vue -->
<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-50" @close="$emit('update:show', false)">
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
              class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
              <!-- Loading State -->
              <div v-if="loading" class="px-6 py-8">
                <div class="animate-pulse">
                  <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 bg-gray-200 rounded-full"></div>
                    <div class="flex-1 space-y-2">
                      <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                      <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                  </div>
                  <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="h-20 bg-gray-200 rounded"></div>
                    <div class="h-20 bg-gray-200 rounded"></div>
                    <div class="h-20 bg-gray-200 rounded"></div>
                  </div>
                  <div class="space-y-3">
                    <div class="h-4 bg-gray-200 rounded"></div>
                    <div class="h-4 bg-gray-200 rounded"></div>
                    <div class="h-4 bg-gray-200 rounded"></div>
                  </div>
                </div>
              </div>

              <!-- Content -->
              <div v-else-if="distributor" class="px-6 py-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <img v-if="distributor.avatar" :src="distributor.avatar" :alt="distributor.name"
                        class="h-16 w-16 rounded-full object-cover" />
                      <div v-else class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-xl font-medium text-gray-700">
                          {{ getInitials(distributor.first_name + ' ' + distributor.last_name) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <DialogTitle as="h3" class="text-xl font-semibold text-gray-900">
                        {{ (distributor.first_name ? (distributor.first_name + ' ' + (distributor.last_name || '')) :
                          distributor.name) || getNested('contact_full_name') || 'No name' }}
                      </DialogTitle>
                      <p class="text-sm text-gray-500">{{ distributor.email || getNested('email') }}</p>
                      <div class="mt-1 flex items-center space-x-3">
                        <span :class="getStatusClasses(distributor.status)"
                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                          {{ getStatusText(distributor.status) }}
                        </span>
                        <span class="text-sm text-gray-500">
                          Joined {{ formatDate(distributor.created_at) }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <!-- <button @click="$emit('edit', distributor)"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <PencilIcon class="w-4 h-4 mr-2" />
                      Edit
                    </button> -->
                    <button @click="$emit('update:show', false)"
                      class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                      <XMarkIcon class="h-6 w-6" />
                    </button>
                  </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-4 mb-8">
                  <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                      <CurrencyDollarIcon class="h-6 w-6 text-blue-600" />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-blue-900">Total Revenue</p>
                        <p class="text-lg font-semibold text-blue-900">
                          ${{ formatCurrency(distributor.total_revenue || getNested('total_revenue') || 0) }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                      <ChartBarIcon class="h-6 w-6 text-green-600" />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-green-900">Performance</p>
                        <p class="text-lg font-semibold text-green-900">
                          {{ (distributor.performance_score || getNested('performance_score') || 0).toFixed(1) }}%
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                      <ShoppingCartIcon class="h-6 w-6 text-purple-600" />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-purple-900">Total Orders</p>
                        <p class="text-lg font-semibold text-purple-900">
                          {{ distributor.total_orders || getNested('total_orders') || 0 }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="bg-orange-50 rounded-lg p-4">
                    <div class="flex items-center">
                      <UsersIcon class="h-6 w-6 text-orange-600" />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-orange-900">Customers</p>
                        <p class="text-lg font-semibold text-orange-900">
                          {{ distributor.total_customers || getNested('total_customers') || 0 }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                  <nav class="-mb-px flex space-x-8">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
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
                <div class="mt-6">
                  <!-- Overview Tab -->
                  <div v-if="activeTab === 'overview'" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                      <!-- Personal Information -->
                      <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information
                        </h4>
                        <dl class="space-y-3">
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="text-sm text-gray-900">{{ (distributor.first_name ? (distributor.first_name + ' '
                              + (distributor.last_name || '')) : distributor.name) || getNested('contact_full_name') ||
                              'N/A' }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ distributor.email || getNested('email') || 'N/A' }}
                            </dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="text-sm text-gray-900">{{ distributor.phone || getNested('contact_mobile') ||
                              getNested('office_phone') || 'N/A' }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                            <dd class="text-sm text-gray-900">{{ distributor.date_of_birth ?
                              formatDate(distributor.date_of_birth) : 'N/A' }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="text-sm text-gray-900">
                              {{ distributor.address || 'N/A' }}
                            </dd>
                          </div>
                        </dl>
                      </div>

                      <!-- Business Information -->
                      <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Business Information
                        </h4>
                        <dl class="space-y-3">
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                            <dd class="text-sm text-gray-900">{{ getNested('company_name') ||
                              getNested('registered_name') || distributor.business_name || 'N/A' }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Company Type</dt>
                            <dd class="text-sm text-gray-900">{{ getNested('company_type') || distributor.business_type
                              || 'N/A' }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">RC Number</dt>
                            <dd class="text-sm text-gray-900">{{ getNested('rc_number') || 'N/A' }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Years in Business</dt>
                            <dd class="text-sm text-gray-900">{{ getNested('years_in_business') || 0 }} years</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Registered Address</dt>
                            <dd class="text-sm text-gray-900">{{ getNested('business_address') || 'N/A' }}</dd>
                          </div>
                        </dl>
                      </div>
                    </div>

                    <!-- Territory & Commission -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                      <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Territory &
                          Performance</h4>
                        <dl class="space-y-3">
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned Territory
                            </dt>
                            <dd class="text-sm text-gray-900">
                              {{ distributor.territory?.name || 'Unassigned' }}
                              <span v-if="distributor.territory?.state" class="text-gray-500">
                                ({{ distributor.territory.state }})
                              </span>
                            </dd>
                          </div>
                          <!-- <div>
                            <dt class="text-sm font-medium text-gray-500">Commission Rate
                            </dt>
                            <dd class="text-sm text-gray-900">{{ distributor.commission_rate
                              || 5 }}%</dd>
                          </div> -->
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Performance Score
                            </dt>
                            <dd class="text-sm text-gray-900">
                              <div class="flex items-center space-x-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                  <div class="h-2 rounded-full transition-all duration-300"
                                    :class="getPerformanceBarClass(distributor.performance_score)"
                                    :style="{ width: `${distributor.performance_score || 0}%` }">
                                  </div>
                                </div>
                                <span>{{ (distributor.performance_score || 0).toFixed(1)
                                }}%</span>
                              </div>
                            </dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Ranking</dt>
                            <dd class="text-sm text-gray-900">#{{ distributor.ranking ||
                              'N/A' }} in territory</dd>
                          </div>
                        </dl>
                      </div>

                      <!-- Account Status -->
                      <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Account Status</h4>
                        <dl class="space-y-3">
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Current Status
                            </dt>
                            <dd class="text-sm text-gray-900">
                              <span :class="getStatusClasses(distributor.status)"
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                {{ getStatusText(distributor.status) }}
                              </span>
                            </dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Join Date</dt>
                            <dd class="text-sm text-gray-900">{{
                              formatDate(distributor.created_at) }}</dd>
                          </div>
                          <div v-if="distributor.approved_at">
                            <dt class="text-sm font-medium text-gray-500">Approved Date</dt>
                            <dd class="text-sm text-gray-900">{{
                              formatDate(distributor.approved_at) }}</dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                            <dd class="text-sm text-gray-900">
                              {{ distributor.last_login ?
                                formatDate(distributor.last_login) : 'Never' }}
                            </dd>
                          </div>
                          <div>
                            <dt class="text-sm font-medium text-gray-500">Last Activity</dt>
                            <dd class="text-sm text-gray-900">
                              {{ distributor.last_activity ?
                                getRelativeTime(distributor.last_activity) : 'No activity'
                              }}
                            </dd>
                          </div>
                        </dl>
                      </div>
                    </div>
                  </div>

                  <!-- Performance Tab -->
                  <div v-else-if="activeTab === 'performance'" class="space-y-6">
                    <!-- Revenue Chart Placeholder -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                      <h4 class="text-lg font-medium text-gray-900 mb-4">Revenue Trend (Last 12
                        Months)</h4>
                      <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                        <p class="text-gray-500">Revenue chart would be rendered here</p>
                      </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                      <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Sales Performance
                        </h5>
                        <p class="text-2xl font-bold text-blue-600">{{
                          (distributor.performance_score || 0).toFixed(1) }}%</p>
                        <p class="text-sm text-gray-500">vs target</p>
                      </div>
                      <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Customer Satisfaction
                        </h5>
                        <p class="text-2xl font-bold text-green-600">{{
                          (distributor.customer_satisfaction || 0).toFixed(1) }}/5</p>
                        <p class="text-sm text-gray-500">average rating</p>
                      </div>
                      <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Order Fulfillment
                        </h5>
                        <p class="text-2xl font-bold text-purple-600">{{
                          (distributor.fulfillment_rate || 0).toFixed(1) }}%</p>
                        <p class="text-sm text-gray-500">on-time delivery</p>
                      </div>
                    </div>
                  </div>

                  <!-- Orders Tab -->
                  <div v-else-if="activeTab === 'orders'" class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                      <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h4 class="text-lg font-medium text-gray-900">Recent Orders</h4>
                      </div>
                      <div class="p-6">
                        <div class="text-center text-gray-500">
                          <ShoppingCartIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                          <p>Order history would be displayed here</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Documents Tab -->
                  <div v-else-if="activeTab === 'documents'" class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                      <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h4 class="text-lg font-medium text-gray-900">Documents & Certifications
                        </h4>
                      </div>
                      <div class="p-6">
                        <div class="space-y-3">
                          <p class="text-sm text-gray-500">Below are the uploaded documents for this distributor. Click
                            any item to open it in a new tab.</p>

                          <ul class="mt-4 divide-y divide-gray-100 border rounded-md">
                            <li v-for="doc in getDocuments()" :key="doc.key"
                              class="flex items-center justify-between p-3">
                              <div class="flex items-start space-x-3">
                                <DocumentTextIcon class="h-6 w-6 text-gray-500 mt-1" />
                                <div>
                                  <p class="text-sm font-medium text-gray-900">{{ doc.label }}</p>
                                  <p v-if="doc.url" class="text-xs text-gray-500">{{ doc.filename }}</p>
                                  <p v-else class="text-xs text-gray-400">Not provided</p>
                                </div>
                              </div>
                              <div>
                                <a v-if="doc.url" :href="doc.url" target="_blank" rel="noopener noreferrer"
                                  class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-700 bg-primary-50 border border-transparent rounded-md hover:bg-primary-100">
                                  Open
                                </a>
                              </div>
                            </li>
                            <li v-if="getDocuments().filter(d => d.url).length === 0"
                              class="p-4 text-center text-sm text-gray-500">No documents available</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Notes Tab -->
                  <div v-else-if="activeTab === 'notes'" class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                      <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                        <h4 class="text-lg font-medium text-gray-900">Notes & Comments</h4>
                        <button
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                          <PlusIcon class="w-4 h-4 mr-2" />
                          Add Note
                        </button>
                      </div>
                      <div class="p-6">
                        <div class="text-center text-gray-500">
                          <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                          <p>Notes and comments would be displayed here</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Error State -->
              <div v-else class="px-6 py-8 text-center">
                <ExclamationTriangleIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">Distributor Not Found</h3>
                <p class="text-sm text-gray-500">The requested distributor could not be loaded.</p>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { format, formatDistanceToNow } from 'date-fns'

// Icons
import {
  XMarkIcon,
  // PencilIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  ShoppingCartIcon,
  UsersIcon,
  DocumentTextIcon,
  ExclamationTriangleIcon,
  PlusIcon,
  UserIcon,
  ChatBubbleLeftRightIcon
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
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['update:show', 'edit'])

// Helper to read nested distributor.* fields
const getNested = (key) => {
  try {
    return props.distributor?.distributor?.[key]
  } catch {
    return undefined
  }
}

// Local state
const activeTab = ref('overview')

const tabs = [
  { id: 'overview', label: 'Overview', icon: UserIcon },
  { id: 'performance', label: 'Performance', icon: ChartBarIcon },
  { id: 'orders', label: 'Orders', icon: ShoppingCartIcon },
  { id: 'documents', label: 'Documents', icon: DocumentTextIcon },
  { id: 'notes', label: 'Notes', icon: ChatBubbleLeftRightIcon }
]

// Methods
const getInitials = (name) => {
  return name?.split(' ').map(n => n[0]).join('').toUpperCase() || 'N/A'
}

const getStatusText = (status) => {
  const statusMap = {
    pending: 'Pending',
    approved: 'Approved',
    active: 'Active',
    suspended: 'Suspended',
    rejected: 'Rejected'
  }
  return statusMap[status] || status
}

const getStatusClasses = (status) => {
  const classMap = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-blue-100 text-blue-800',
    active: 'bg-green-100 text-green-800',
    suspended: 'bg-red-100 text-red-800',
    rejected: 'bg-gray-100 text-gray-800'
  }
  return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getPerformanceBarClass = (score) => {
  if (score >= 90) return 'bg-green-500'
  if (score >= 70) return 'bg-blue-500'
  if (score >= 50) return 'bg-yellow-500'
  return 'bg-red-500'
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const formatDate = (date) => {
  return format(new Date(date), 'MMM dd, yyyy')
}

const getRelativeTime = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

// Documents helper: map known distributor.distributor fields to user-friendly labels
const buildFileUrl = (path) => {
  if (!path) return null
  const base = import.meta.env.FILE_BASE_PATH || ''
  // If path already looks like a full URL, return it
  if (/^https?:\/\//i.test(path)) return path
  // Trim slashes appropriately
  return `${base.replace(/\/$/, '')}/${path.replace(/^\//, '')}`
}

const getDocuments = () => {
  const nested = props.distributor?.distributor || {}
  const docsMap = [
    ['cac_certificate', 'CAC Certificate'],
    ['form_co7', 'Form CO7'],
    ['memart', 'Memart'],
    ['utility_bill', 'Utility Bill'],
    ['tin_certificate', 'TIN Certificate'],
    ['id_of_contact', 'Contact ID'],
    ['referee_letter', 'Referee Letter'],
    ['signature', 'Signature']
  ]

  return docsMap.map(([key, label]) => {
    const value = nested[key]
    // support arrays or single strings
    let filename = ''
    let url = null
    if (Array.isArray(value) && value.length > 0) {
      filename = value[0].split('/').pop()
      url = buildFileUrl(value[0])
    } else if (typeof value === 'string' && value.length) {
      filename = value.split('/').pop()
      url = buildFileUrl(value)
    }
    return { key, label, filename, url }
  })
}
</script>
