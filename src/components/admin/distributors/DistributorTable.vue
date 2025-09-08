<!-- DistributorTable.vue -->
<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Loading State -->
    <div v-if="loading" class="p-6">
      <div class="animate-pulse space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center space-x-4">
          <div class="w-4 h-4 bg-gray-200 rounded"></div>
          <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="w-20 h-6 bg-gray-200 rounded"></div>
          <div class="w-24 h-6 bg-gray-200 rounded"></div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div v-else-if="distributors.length > 0" class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <!-- Select All Checkbox -->
            <th class="px-6 py-3 text-left">
              <input type="checkbox" :checked="allSelected" @change="toggleSelectAll"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" />
            </th>

            <!-- Distributor -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sort('name')">
              <div class="flex items-center space-x-1">
                <span>Distributor</span>
                <ChevronUpDownIcon class="w-4 h-4" />
              </div>
            </th>

            <!-- Business Info -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Business Info
            </th>

            <!-- Territory -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sort('territory')">
              <div class="flex items-center space-x-1">
                <span>Territory</span>
                <ChevronUpDownIcon class="w-4 h-4" />
              </div>
            </th>

            <!-- Status -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sort('status')">
              <div class="flex items-center space-x-1">
                <span>Status</span>
                <ChevronUpDownIcon class="w-4 h-4" />
              </div>
            </th>

            <!-- Performance -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sort('performance')">
              <div class="flex items-center space-x-1">
                <span>Performance</span>
                <ChevronUpDownIcon class="w-4 h-4" />
              </div>
            </th>

            <!-- Revenue -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sort('revenue')">
              <div class="flex items-center space-x-1">
                <span>Revenue</span>
                <ChevronUpDownIcon class="w-4 h-4" />
              </div>
            </th>

            <!-- Join Date -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sort('created_at')">
              <div class="flex items-center space-x-1">
                <span>Join Date</span>
                <ChevronUpDownIcon class="w-4 h-4" />
              </div>
            </th>

            <!-- Actions -->
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="distributor in distributors" :key="distributor.id"
            class="hover:bg-gray-50 transition-colors duration-150"
            :class="{ 'bg-blue-50': selected.includes(distributor.id) }">
            <!-- Checkbox -->
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="checkbox" :value="distributor.id" v-model="selectedLocal"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" />
            </td>

            <!-- Distributor Info -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img v-if="distributor.avatar" :src="distributor.avatar" :alt="distributor.name"
                    class="h-10 w-10 rounded-full object-cover" />
                  <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700">
                      {{ getInitials(distributor.first_name + ' ' + distributor.last_name) }}
                    </span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ distributor.first_name }} {{ distributor.last_name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ distributor.email }}
                  </div>
                  <div class="text-xs text-gray-400">
                    {{ distributor.phone }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Business Info -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">
                {{ distributor.distributor.business_name || 'N/A' }}
              </div>
              <div class="text-sm text-gray-500">
                {{ distributor.distributor.business_type || 'Individual' }}
              </div>
              <div class="text-xs text-gray-400">
                {{ distributor.distributor.years_in_business || 0 }}+ years exp
              </div>
            </td>

            <!-- Territory -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <MapPinIcon class="h-4 w-4 text-gray-400 mr-2" />
                <div>
                  <div class="text-sm text-gray-900">
                    {{ distributor.territory?.name || 'Unassigned' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ distributor.territory?.state }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClasses(distributor.status)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusText(distributor.status) }}
              </span>
            </td>

            <!-- Performance -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                  <div class="h-2 rounded-full transition-all duration-300"
                    :class="getPerformanceBarClass(distributor.performance_score)"
                    :style="{ width: `${distributor.performance_score || 0}%` }"></div>
                </div>
                <span class="text-sm text-gray-900">
                  {{ (distributor.performance_score || 0).toFixed(0) }}%
                </span>
              </div>
            </td>

            <!-- Revenue -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">
                {{ formatCurrency(distributor.total_revenue || 0) }}
              </div>
              <div class="text-xs text-gray-500">
                Last 30 days: {{ formatCurrency(distributor.monthly_revenue || 0) }}
              </div>
            </td>

            <!-- Join Date -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div>{{ formatDate(distributor.created_at) }}</div>
              <div class="text-xs text-gray-400">
                {{ getRelativeTime(distributor.created_at) }}
              </div>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end space-x-1">
                <!-- Quick View -->
                <button @click="$emit('view-details', distributor)"
                  class="text-primary-600 hover:text-primary-900 p-1 rounded hover:bg-primary-50" title="View Details">
                  <EyeIcon class="h-4 w-4" />
                </button>

                <!-- Edit -->
                <button @click="$emit('edit', distributor)"
                  class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-50" title="Edit">
                  <PencilIcon class="h-4 w-4" />
                </button>

                <!-- Status Actions -->
                <div v-if="distributor.status === 'pending'" class="flex space-x-1">
                  <button @click="$emit('approve', distributor)"
                    class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50" title="Approve">
                    <CheckIcon class="h-4 w-4" />
                  </button>
                  <button @click="$emit('reject', distributor)"
                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" title="Reject">
                    <XMarkIcon class="h-4 w-4" />
                  </button>
                </div>

                <div v-else-if="distributor.status === 'active'" class="flex space-x-1">
                  <button @click="$emit('suspend', distributor)"
                    class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50" title="Suspend">
                    <PauseIcon class="h-4 w-4" />
                  </button>
                </div>

                <div v-else-if="distributor.status === 'suspended'" class="flex space-x-1">
                  <button @click="$emit('activate', distributor)"
                    class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50" title="Activate">
                    <PlayIcon class="h-4 w-4" />
                  </button>
                </div>

                <!-- More Actions Dropdown -->
                <div class="relative" :ref="el => actionMenuRefs[distributor.id] = el">
                  <button @click="toggleActionMenu(distributor.id)"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-50">
                    <EllipsisVerticalIcon class="h-4 w-4" />
                  </button>

                  <!-- Dropdown Menu -->
                  <Transition enter-active-class="transition duration-200 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="activeActionMenu === distributor.id"
                      class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                      <div class="py-1">
                        <button @click="handleAction('send-email', distributor)"
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                          <EnvelopeIcon class="w-4 h-4 inline mr-2" />
                          Send Email
                        </button>
                        <button @click="handleAction('view-performance', distributor)"
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                          <ChartBarIcon class="w-4 h-4 inline mr-2" />
                          View Performance
                        </button>
                        <button @click="handleAction('change-territory', distributor)"
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                          <MapIcon class="w-4 h-4 inline mr-2" />
                          Change Territory
                        </button>
                        <div class="border-t border-gray-100"></div>
                        <button @click="handleAction('delete', distributor)"
                          class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                          <TrashIcon class="w-4 h-4 inline mr-2" />
                          Delete
                        </button>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <UsersIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No distributors found</h3>
      <p class="mt-1 text-sm text-gray-500">
        Get started by inviting a new distributor.
      </p>
      <div class="mt-6">
        <button @click="$emit('create')"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          <PlusIcon class="w-4 h-4 mr-2" />
          Invite Distributor
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { format, formatDistanceToNow } from 'date-fns'

// Icons
import {
  ChevronUpDownIcon,
  EyeIcon,
  PencilIcon,
  CheckIcon,
  XMarkIcon,
  PauseIcon,
  PlayIcon,
  EllipsisVerticalIcon,
  MapPinIcon,
  UsersIcon,
  PlusIcon,
  EnvelopeIcon,
  ChartBarIcon,
  MapIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  distributors: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  selected: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits([
  'update:selected',
  'view-details',
  'edit',
  'approve',
  'reject',
  'suspend',
  'activate',
  'delete',
  'create',
  'sort'
])

// Local state
const selectedLocal = computed({
  get: () => props.selected,
  set: (value) => emit('update:selected', value)
})

const allSelected = computed(() => {
  return props.distributors.length > 0 && props.selected.length === props.distributors.length
})

const activeActionMenu = ref(null)
const actionMenuRefs = ref({})

// Methods
const toggleSelectAll = () => {
  if (allSelected.value) {
    emit('update:selected', [])
  } else {
    emit('update:selected', props.distributors.map(d => d.id))
  }
}

const sort = (field) => {
  emit('sort', field)
}

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
  if (!amount) return '₦0.00'
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const formatDate = (date) => {
  return format(new Date(date), 'MMM dd, yyyy')
}

const getRelativeTime = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const toggleActionMenu = (distributorId) => {
  if (activeActionMenu.value === distributorId) {
    activeActionMenu.value = null
  } else {
    activeActionMenu.value = distributorId
  }
}

const handleAction = (action, distributor) => {
  activeActionMenu.value = null

  switch (action) {
    case 'send-email':
      // Handle send email
      console.log('Send email to', distributor.email)
      break
    case 'view-performance':
      // Handle view performance
      console.log('View performance for', distributor.name)
      break
    case 'change-territory':
      // Handle change territory
      console.log('Change territory for', distributor.name)
      break
    case 'delete':
      emit('delete', distributor)
      break
    default:
      break
  }
}

const handleClickOutside = (event) => {
  const clickedOutside = Object.values(actionMenuRefs.value).every(ref => {
    return !ref || !ref.contains(event.target)
  })

  if (clickedOutside) {
    activeActionMenu.value = null
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
