<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Users</h1>
        <p class="mt-1 text-sm text-gray-600">
          Manage user accounts, roles, and permissions
        </p>
      </div>

      <div class="mt-4 sm:mt-0 flex items-center space-x-3">
        <!-- Export Button -->
        <div class="relative" ref="exportDropdownRef">
          <button @click="toggleExportDropdown" class="btn-outline">
            <font-awesome-icon icon="download" class="h-4 w-4 mr-2" />
            Export
            <font-awesome-icon icon="chevron-down" class="h-4 w-4 ml-2" />
          </button>

          <!-- Export Dropdown -->
          <div v-if="showExportDropdown"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" @click.stop>
            <div class="py-1">
              <button @click="handleExport('csv')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="file-csv" class="h-4 w-4 mr-3 text-gray-400" />
                Export as CSV
              </button>
              <button @click="handleExport('xlsx')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="file-excel" class="h-4 w-4 mr-3 text-gray-400" />
                Export as Excel
              </button>
              <button @click="handleExport('pdf')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <font-awesome-icon icon="file-pdf" class="h-4 w-4 mr-3 text-gray-400" />
                Export as PDF
              </button>
            </div>
          </div>
        </div>

        <!-- Create User Button -->
        <router-link v-if="hasPermission('users.create')" :to="{ name: 'admin.users.create' }" class="btn-primary">
          <font-awesome-icon icon="plus" class="h-4 w-4 mr-2" />
          Add User
        </router-link>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatsCard title="Total Users" :value="usersStore.totalUsers.toLocaleString()" icon="users" color="primary"
        :loading="usersStore.isLoading && !usersStore.hasUsers" />

      <StatsCard title="Active Users" :value="usersStore.activeUsers.length.toLocaleString()" icon="user-check"
        color="success" :loading="usersStore.isLoading && !usersStore.hasUsers" />

      <StatsCard title="Pending Users" :value="usersStore.pendingUsers.length.toLocaleString()" icon="user-clock"
        color="warning" :loading="usersStore.isLoading && !usersStore.hasUsers" />

      <StatsCard title="Inactive Users" :value="usersStore.inactiveUsers.length.toLocaleString()" icon="user-times"
        color="danger" :loading="usersStore.isLoading && !usersStore.hasUsers" />
    </div>

    <!-- Filters and Search -->
    <div class="card">
      <div class="card-body">
        <UserFilters :filters="usersStore.filters" :loading="usersStore.isLoading" @update-filters="handleUpdateFilters"
          @reset-filters="handleResetFilters" @search="handleSearch" />
      </div>
    </div>

    <!-- Users Table -->
    <div class="card">
      <div class="card-header">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <h3 class="text-lg font-medium text-gray-900">
              Users List
            </h3>

            <!-- Selected Items Info -->
            <div v-if="selectedUsers.length > 0" class="text-sm text-gray-600">
              {{ selectedUsers.length }} user{{ selectedUsers.length !== 1 ? 's' : '' }} selected
            </div>
          </div>

          <!-- Bulk Actions -->
          <div v-if="selectedUsers.length > 0" class="flex items-center space-x-2">
            <div class="relative" ref="bulkActionsRef">
              <button @click="toggleBulkActions" class="btn-outline">
                <font-awesome-icon icon="cogs" class="h-4 w-4 mr-2" />
                Bulk Actions
                <font-awesome-icon icon="chevron-down" class="h-4 w-4 ml-2" />
              </button>

              <!-- Bulk Actions Dropdown -->
              <div v-if="showBulkActions"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                @click.stop>
                <div class="py-1">
                  <button @click="handleBulkAction('activate')"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <font-awesome-icon icon="user-check" class="h-4 w-4 mr-3 text-green-500" />
                    Activate Users
                  </button>
                  <button @click="handleBulkAction('deactivate')"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <font-awesome-icon icon="user-times" class="h-4 w-4 mr-3 text-red-500" />
                    Deactivate Users
                  </button>
                  <button @click="handleBulkAction('suspend')"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <font-awesome-icon icon="user-slash" class="h-4 w-4 mr-3 text-yellow-500" />
                    Suspend Users
                  </button>
                  <hr class="my-1">
                  <button @click="handleBulkAction('delete')"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                    <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                    Delete Users
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <UsersTable :users="usersStore.users" :loading="usersStore.isLoading" :selected="selectedUsers"
          :pagination="usersStore.pagination" @select="handleSelectUser" @select-all="handleSelectAll"
          @view="handleViewUser" @edit="handleEditUser" @delete="handleDeleteUser" @update-status="handleUpdateStatus"
          @impersonate="handleImpersonate" @send-password-reset="handleSendPasswordReset"
          @page-change="handlePageChange" @sort="handleSort" />
      </div>
    </div>

    <!-- Bulk Action Confirmation Modal -->
    <BulkActionModal v-model:show="showBulkActionModal" :action="bulkActionType" :users="selectedUsers"
      :loading="usersStore.isUpdating" @confirm="confirmBulkAction" />

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal v-model:show="showDeleteModal" :user="userToDelete" :loading="usersStore.isDeleting"
      @confirm="confirmDelete" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useUsersStore } from '@/stores/users'
import StatsCard from '@/components/admin/dashboard/StatsCard.vue'
import UserFilters from '@/components/admin/users/UserFilters.vue'
import UsersTable from '@/components/admin/users/UsersTable.vue'
import BulkActionModal from '@/components/admin/users/BulkActionModal.vue'
import DeleteConfirmationModal from '@/components/admin/users/DeleteConfirmationModal.vue'

// Composables
const router = useRouter()
const { hasPermission } = useAuth()
const usersStore = useUsersStore()

// Reactive data
const selectedUsers = ref([])
const showExportDropdown = ref(false)
const showBulkActions = ref(false)
const showBulkActionModal = ref(false)
const showDeleteModal = ref(false)
const bulkActionType = ref('')
const userToDelete = ref(null)

// Refs for dropdown handling
const exportDropdownRef = ref(null)
const bulkActionsRef = ref(null)

// Methods
const handleUpdateFilters = (newFilters) => {
  usersStore.updateFilters(newFilters)
  handleSearch()
}

const handleResetFilters = () => {
  usersStore.resetFilters()
  selectedUsers.value = []
  loadUsers()
}

const handleSearch = () => {
  selectedUsers.value = []
  loadUsers()
}

const handleSelectUser = (user) => {
  const index = selectedUsers.value.findIndex(u => u.id === user.id)
  if (index > -1) {
    selectedUsers.value.splice(index, 1)
  } else {
    selectedUsers.value.push(user)
  }
}

const handleSelectAll = (selected) => {
  if (selected) {
    selectedUsers.value = [...usersStore.users]
  } else {
    selectedUsers.value = []
  }
}

const handleViewUser = (user) => {
  router.push({ name: 'admin.users.detail', params: { id: user.id } })
}

const handleEditUser = (user) => {
  router.push({ name: 'admin.users.edit', params: { id: user.id } })
}

const handleDeleteUser = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const handleUpdateStatus = async (user, status) => {
  await usersStore.updateUserStatus(user.id, status, user.role)
}

const handleImpersonate = async (user) => {
  if (confirm(`Are you sure you want to impersonate ${user.name}?`)) {
    await usersStore.impersonateUser(user.id)
    // Optionally redirect to user dashboard
    router.push('/')
  }
}

const handleSendPasswordReset = async (user) => {
  if (confirm(`Send password reset email to ${user.email}?`)) {
    await usersStore.sendPasswordReset(user.id)
  }
}

const handlePageChange = (page) => {
  selectedUsers.value = []
  loadUsers(page)
}

const handleSort = (sortBy, sortOrder) => {
  usersStore.updateFilters({ sort_by: sortBy, sort_order: sortOrder })
  handleSearch()
}

const toggleExportDropdown = () => {
  showExportDropdown.value = !showExportDropdown.value
  showBulkActions.value = false
}

const toggleBulkActions = () => {
  showBulkActions.value = !showBulkActions.value
  showExportDropdown.value = false
}

const handleExport = async (format) => {
  showExportDropdown.value = false
  await usersStore.exportUsers(format)
}

const handleBulkAction = (action) => {
  bulkActionType.value = action
  showBulkActionModal.value = true
  showBulkActions.value = false
}

const confirmBulkAction = async (additionalData = {}) => {
  const userIds = selectedUsers.value.map(user => user.id)
  const result = await usersStore.bulkAction(bulkActionType.value, userIds, additionalData)

  if (result.success) {
    selectedUsers.value = []
    showBulkActionModal.value = false
  }
}

const confirmDelete = async () => {
  if (userToDelete.value) {
    const result = await usersStore.deleteUser(userToDelete.value.id)
    if (result.success) {
      showDeleteModal.value = false
      userToDelete.value = null
    }
  }
}

const loadUsers = async (page = 1) => {
  await usersStore.fetchUsers(page)
}

// Click outside handler
const handleClickOutside = (event) => {
  if (exportDropdownRef.value && !exportDropdownRef.value.contains(event.target)) {
    showExportDropdown.value = false
  }
  if (bulkActionsRef.value && !bulkActionsRef.value.contains(event.target)) {
    showBulkActions.value = false
  }
}

// Watchers
watch(() => usersStore.filters.search, (newSearch, oldSearch) => {
  // Debounce search
  if (newSearch !== oldSearch) {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
      handleSearch()
    }, 500)
  }
})

let searchTimeout = null

// Lifecycle
onMounted(() => {
  loadUsers()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
})
</script>
