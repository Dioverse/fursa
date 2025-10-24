<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Distributor Management</h1>
        <p class="mt-1 text-sm text-gray-600">
          Manage distributors, applications, territories, and performance
        </p>
      </div>
      <div class="mt-4 sm:mt-0 flex space-x-3">
        <button @click="exportDistributors"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          <ArrowDownTrayIcon class="w-4 h-4 mr-2" />
          Export Report
        </button>
        <button @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          <PlusIcon class="w-4 h-4 mr-2" />
          Create Distributor
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <DistributorStatsCards :stats="distributorStore.stats" />

    <!-- Filters -->
    <DistributorFilters v-model:filters="filters" :territories="distributorStore.territories"
      @apply-filters="applyFilters" @clear-filters="clearFilters" />

    <!-- Bulk Actions -->
    <DistributorBulkActions v-if="selectedDistributors.length > 0" :selected-count="selectedDistributors.length"
      @bulk-approve="bulkApprove" @bulk-suspend="bulkSuspend" @bulk-reject="bulkReject" />

    <!-- Distributors Table -->
    <DistributorTable :distributors="distributorStore.distributors" :loading="distributorStore.loading"
      v-model:selected="selectedDistributors" @view-details="viewDistributorDetails" @edit="editDistributor"
      @approve="approveDistributor" @reject="rejectDistributor" @suspend="suspendDistributor"
      @activate="activateDistributor" @delete="deleteDistributor" />

    <!-- Pagination -->
    <Pagination :current-page="pagination.currentPage" :total-pages="pagination.totalPages"
      :total-items="pagination.totalItems" :per-page="pagination.perPage" @page-change="handlePageChange"
      @per-page-change="handlePerPageChange" />

    <!-- Modals -->
    <DistributorInviteModal v-model:show="showCreateModal" @created="handleInviteSent" />

    <DistributorDetailsModal v-model:show="showDetailsModal" :distributor="selectedDistributor" />

    <DistributorEditModal v-model:show="showEditModal" :distributor="selectedDistributor"
      @updated="handleDistributorUpdated" />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useDistributorStore } from '@/stores/distributorStore'
import { useNotificationStore } from '@/stores/notification'
// import { debounce } from 'lodash'

// Icons
import {
  PlusIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'

// Components
import DistributorStatsCards from '@/components/admin/distributors/DistributorStatsCards.vue'
import DistributorFilters from '@/components/admin/distributors/DistributorFilters.vue'
import DistributorBulkActions from '@/components/admin/distributors/DistributorBulkActions.vue'
import DistributorTable from '@/components/admin/distributors/DistributorTable.vue'
import Pagination from '@/components/common/TablePagination.vue'
import DistributorInviteModal from '@/components/admin/distributors/DistributorInviteModal.vue'
import DistributorDetailsModal from '@/components/admin/distributors/DistributorDetailsModal.vue'
import DistributorEditModal from '@/components/admin/distributors/DistributorEditModal.vue'

// Stores
const distributorStore = useDistributorStore()
const notificationStore = useNotificationStore()

// Reactive data
const selectedDistributors = ref([])
const selectedDistributor = ref(null)
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const showEditModal = ref(false)

const filters = reactive({
  search: '',
  status: '',
  territory: '',
  performance: '',
  dateRange: ''
})

const pagination = reactive({
  currentPage: 1,
  totalPages: 1,
  totalItems: 0,
  perPage: 10
})

// Debounced search
// const debouncedSearch = debounce(() => {
//   applyFilters()
// }, 300)

// Watch for search changes
watch(() => filters.search, () => {
  // debouncedSearch()
})

// Methods
const applyFilters = () => {
  pagination.currentPage = 1
  loadDistributors()
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  applyFilters()
}

const loadDistributors = async () => {
  try {
    await distributorStore.fetchDistributors({
      ...filters,
      page: pagination.currentPage,
      per_page: pagination.perPage // backend expects 'per_page'
    })

    pagination.totalPages = distributorStore.pagination.totalPages
    pagination.totalItems = distributorStore.pagination.totalItems
  } catch (error) {
    notificationStore.error('Error', 'Failed to load distributors', error)
  }
}

const handlePageChange = (page) => {
  pagination.currentPage = page
  loadDistributors()
}

const handlePerPageChange = (perPage) => {
  pagination.perPage = perPage
  pagination.currentPage = 1
  loadDistributors()
}

const viewDistributorDetails = (distributor) => {
  selectedDistributor.value = distributor
  showDetailsModal.value = true
}

const editDistributor = (distributor) => {
  selectedDistributor.value = distributor
  showEditModal.value = true
}

const approveDistributor = async (distributor) => {
  try {
    await distributorStore.approveDistributor(distributor.id)
    notificationStore.success('Success', 'Distributor approved successfully')
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to approve distributor', error)
  }
}

const rejectDistributor = async (distributor) => {
  try {
    await distributorStore.rejectDistributor(distributor.id)
    notificationStore.success('Success', 'Distributor rejected successfully')
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to reject distributor', error)
  }
}

const suspendDistributor = async (distributor) => {
  try {
    await distributorStore.suspendDistributor(distributor.id)
    notificationStore.success('Success', 'Distributor suspended successfully')
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to suspend distributor', error)
  }
}

const activateDistributor = async (distributor) => {
  try {
    await distributorStore.activateDistributor(distributor.id)
    notificationStore.success('Success', 'Distributor activated successfully')
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to activate distributor', error)
  }
}

const deleteDistributor = async (distributor) => {
  try {
    const confirmed = window.confirm('Are you sure you want to delete this distributor? This action cannot be undone.')
    if (!confirmed) return

    await distributorStore.deleteDistributor(distributor.id)
    notificationStore.success('Success', 'Distributor deleted successfully')
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to delete distributor', error)
  }
}

const bulkApprove = async () => {
  try {
    const distributorIds = selectedDistributors.value.map(d => d.id)
    await distributorStore.bulkApprove(distributorIds)
    notificationStore.success('Success', `${selectedDistributors.value.length} distributors approved successfully`)
    selectedDistributors.value = []
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to approve distributors', error)
  }
}

const bulkSuspend = async () => {
  try {
    const distributorIds = selectedDistributors.value.map(d => d.id)
    await distributorStore.bulkSuspend(distributorIds)
    notificationStore.success('Success', `${selectedDistributors.value.length} distributors suspended successfully`)
    selectedDistributors.value = []
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to suspend distributors', error)
  }
}

const bulkReject = async () => {
  const confirmed = window.confirm('Are you sure you want to reject the selected distributors?')
  if (!confirmed) return

  try {
    const distributorIds = selectedDistributors.value.map(d => d.id)
    await distributorStore.bulkReject(distributorIds)
    notificationStore.success('Success', `${selectedDistributors.value.length} distributors rejected successfully`)
    selectedDistributors.value = []
    loadDistributors()
  } catch (error) {
    notificationStore.error('Error', 'Failed to reject distributors', error)
  }
}

const exportDistributors = async () => {
  try {
    await distributorStore.exportDistributors(filters)
    notificationStore.success('Success', 'Distributors exported successfully')
  } catch (error) {
    notificationStore.error('Error', 'Failed to export distributors', error)
  }
}

const handleInviteSent = () => {
  notificationStore.success('Success', 'Distributor created successfully')
  showCreateModal.value = false
  loadDistributors()
}

const handleDistributorUpdated = () => {
  notificationStore.success('Success', 'Distributor updated successfully')
  showEditModal.value = false
  loadDistributors()
}

// Lifecycle
onMounted(() => {
  loadDistributors()
  distributorStore.fetchTerritories()
  distributorStore.fetchStats()
})
</script>
