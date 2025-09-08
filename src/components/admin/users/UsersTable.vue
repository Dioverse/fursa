<template>
  <div class="overflow-hidden">
    <!-- Loading State -->
    <div v-if="loading && !users.length" class="p-6">
      <div class="space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center space-x-4">
          <div class="loading-skeleton w-4 h-4"></div>
          <div class="loading-skeleton w-12 h-12 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="loading-skeleton h-4 w-32"></div>
            <div class="loading-skeleton h-3 w-24"></div>
          </div>
          <div class="loading-skeleton h-6 w-16 rounded-full"></div>
          <div class="loading-skeleton h-8 w-20"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!users.length" class="p-12 text-center text-gray-500">
      <font-awesome-icon icon="users" class="h-12 w-12 text-gray-300 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
      <p>Try adjusting your search criteria or create a new user.</p>
    </div>

    <!-- Users Table -->
    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <!-- Select All Checkbox -->
            <th class="px-6 py-3 text-left">
              <input type="checkbox" :checked="isAllSelected" :indeterminate="isIndeterminate" @change="handleSelectAll"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            </th>

            <!-- User Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('name')">
              <div class="flex items-center space-x-1">
                <span>User</span>
                <font-awesome-icon v-if="sortBy === 'name'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Role Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('role')">
              <div class="flex items-center space-x-1">
                <span>Role</span>
                <font-awesome-icon v-if="sortBy === 'role'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Status Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('status')">
              <div class="flex items-center space-x-1">
                <span>Status</span>
                <font-awesome-icon v-if="sortBy === 'status'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Last Login Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('last_login_at')">
              <div class="flex items-center space-x-1">
                <span>Last Login</span>
                <font-awesome-icon v-if="sortBy === 'last_login_at'"
                  :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'" class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Created Date Column -->
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="handleSort('created_at')">
              <div class="flex items-center space-x-1">
                <span>Created</span>
                <font-awesome-icon v-if="sortBy === 'created_at'" :icon="sortOrder === 'asc' ? 'sort-up' : 'sort-down'"
                  class="h-3 w-3" />
                <font-awesome-icon v-else icon="sort" class="h-3 w-3 text-gray-400" />
              </div>
            </th>

            <!-- Actions Column -->
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200"
            :class="{ 'bg-blue-50': isSelected(user) }">
            <!-- Select Checkbox -->
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="checkbox" :checked="isSelected(user)" @change="handleSelect(user)"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
            </td>

            <!-- User Info -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img v-if="user.avatar" :src="user.avatar" :alt="user.first_name"
                    class="h-10 w-10 rounded-full object-cover">
                  <div v-else class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-sm font-medium text-primary-600">
                      {{ getUserInitials(user.first_name + ' ' + user.last_name) }}
                    </span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.first_name }} {{ user.last_name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ user.email }}
                  </div>
                  <div v-if="user.phone" class="text-xs text-gray-400">
                    {{ user.phone }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Role -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getRoleClass(user.role)">
                <font-awesome-icon :icon="getRoleIcon(user.role)" class="h-3 w-3 mr-1" />
                {{ getRoleLabel(user.role) }}
              </span>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="relative">
                <select :value="user.status" @change="handleStatusChange(user, $event.target.value)"
                  class="text-xs border-0 bg-transparent font-medium rounded-full px-2.5 py-0.5 focus:ring-2 focus:ring-primary-500"
                  :class="getStatusClass(user.status)" :disabled="loading">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option v-if="user.role === 'distributor'" value="approved">Approved</option>
                  <option v-if="user.role === 'distributor'" value="rejected">Rejected</option>
                  <option value="pending">Pending</option>
                  <option value="suspended">Suspended</option>
                </select>
              </div>
            </td>

            <!-- Last Login -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div v-if="user.last_login_at">
                {{ formatDate(user.last_login_at) }}
              </div>
              <span v-else class="text-gray-400 italic">Never</span>
            </td>

            <!-- Created Date -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="relative" :ref="el => actionMenuRefs[user.id] = el">
                <button @click="toggleActionMenu(user.id)"
                  class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                  <font-awesome-icon icon="ellipsis-v" class="h-5 w-5" />
                </button>

                <!-- Action Dropdown -->
                <div v-if="activeActionMenu === user.id"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                  @click.stop>
                  <div class="py-1">
                    <button @click="handleView(user)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="eye" class="h-4 w-4 mr-3 text-gray-400" />
                      View Details
                    </button>

                    <button @click="handleEdit(user)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="edit" class="h-4 w-4 mr-3 text-gray-400" />
                      Edit User
                    </button>

                    <button @click="handleSendPasswordReset(user)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="key" class="h-4 w-4 mr-3 text-gray-400" />
                      Reset Password
                    </button>

                    <!-- <button v-if="user.role !== 'super_admin'" @click="handleImpersonate(user)"
                      class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                      <font-awesome-icon icon="user-secret" class="h-4 w-4 mr-3 text-gray-400" />
                      Impersonate
                    </button> -->

                    <hr class="my-1">

                    <button @click="handleDelete(user)"
                      class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                      <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                      Delete User
                    </button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="bg-white px-6 py-3 border-t border-gray-200">
      <TablePagination :current-page="pagination.current_page" :last-page="pagination.last_page"
        :per-page="pagination.per_page" :total="pagination.total" :from="pagination.from" :to="pagination.to"
        @page-change="handlePageChange" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { formatDistanceToNow, format } from 'date-fns'
import TablePagination from '@/components/common/TablePagination.vue'
// import { a } from 'vitest/dist/chunks/suite.d.FvehnV49'

// Props
const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  selected: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object,
    default: () => ({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0
    })
  }
})

// Emits
const emit = defineEmits([
  'select', 'selectAll', 'view', 'edit', 'delete',
  'updateStatus', 'impersonate', 'sendPasswordReset',
  'pageChange', 'sort'
])

// Reactive data
const activeActionMenu = ref(null)
const actionMenuRefs = ref({})
const sortBy = ref('created_at')
const sortOrder = ref('desc')

// Computed
const isAllSelected = computed(() => {
  return props.users.length > 0 && props.selected.length === props.users.length
})

const isIndeterminate = computed(() => {
  return props.selected.length > 0 && props.selected.length < props.users.length
})

// Methods
const isSelected = (user) => {
  return props.selected.some(selected => selected.id === user.id)
}

const handleSelect = (user) => {
  emit('select', user)
}

const handleSelectAll = (event) => {
  emit('selectAll', event.target.checked)
}

const handleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
  emit('sort', sortBy.value, sortOrder.value)
}

const handleStatusChange = (user, newStatus) => {
  if (newStatus !== user.status) {
    emit('updateStatus', user, newStatus)
  }
}

const toggleActionMenu = (userId) => {
  activeActionMenu.value = activeActionMenu.value === userId ? null : userId
}

const handleView = (user) => {
  activeActionMenu.value = null
  emit('view', user)
}

const handleEdit = (user) => {
  activeActionMenu.value = null
  emit('edit', user)
}

const handleDelete = (user) => {
  activeActionMenu.value = null
  emit('delete', user)
}

// const handleImpersonate = (user) => {
//   activeActionMenu.value = null
//   emit('impersonate', user)
// }

const handleSendPasswordReset = (user) => {
  activeActionMenu.value = null
  emit('sendPasswordReset', user)
}

const handlePageChange = (page) => {
  emit('pageChange', page)
}

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

const getRoleClass = (role) => {
  const classes = {
    super_admin: 'bg-purple-100 text-purple-800',
    admin: 'bg-red-100 text-red-800',
    manager: 'bg-blue-100 text-blue-800',
    editor: 'bg-green-100 text-green-800',
    customer: 'bg-gray-100 text-gray-800',
    distributor: 'bg-yellow-100 text-yellow-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const getRoleIcon = (role) => {
  const icons = {
    super_admin: 'crown',
    admin: 'shield-alt',
    manager: 'user-tie',
    editor: 'edit',
    customer: 'user',
    distributor: 'truck'
  }
  return icons[role] || 'user'
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800 border-green-200',
    approved: 'bg-blue-100 text-blue-800 border-blue-200',
    rejected: 'bg-red-100 text-red-800 border-red-200',
    inactive: 'bg-gray-100 text-gray-800 border-gray-200',
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    suspended: 'bg-red-100 text-red-800 border-red-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const formatDate = (date) => {
  if (!date) return 'N/A'

  const dateObj = new Date(date)
  const now = new Date()
  const diffInHours = (now - dateObj) / (1000 * 60 * 60)

  if (diffInHours < 24) {
    return formatDistanceToNow(dateObj, { addSuffix: true })
  } else {
    return format(dateObj, 'MMM d, yyyy')
  }
}

// Click outside handler
const handleClickOutside = (event) => {
  const isClickInsideMenu = Object.values(actionMenuRefs.value).some(ref => {
    return ref && ref.contains(event.target)
  })

  if (!isClickInsideMenu) {
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

<style scoped>
/* Custom styling for select dropdown to look like badges */
select option {
  background: white;
  color: black;
}

/* Animation for table rows */
tbody tr {
  transition: all 0.2s ease-in-out;
}

tbody tr:hover {
  transform: translateX(2px);
}

/* Loading state animations */
@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.5;
  }
}

.loading-skeleton {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  background-color: #f3f4f6;
}

/* Smooth transitions for action menus */
.absolute {
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Checkbox indeterminate state */
input[type="checkbox"]:indeterminate {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m4 8h8'/%3e%3c/svg%3e");
}
</style>
