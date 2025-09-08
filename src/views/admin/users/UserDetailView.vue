<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="usersStore.isLoading && !user" class="animate-pulse">
      <div class="bg-white rounded-lg shadow-soft p-6">
        <div class="flex items-center space-x-4">
          <div class="loading-skeleton w-24 h-24 rounded-full"></div>
          <div class="flex-1 space-y-3">
            <div class="loading-skeleton h-6 w-48"></div>
            <div class="loading-skeleton h-4 w-32"></div>
            <div class="loading-skeleton h-4 w-24"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Not Found -->
    <div v-else-if="!user && !usersStore.isLoading" class="text-center py-12">
      <font-awesome-icon icon="user-times" class="h-16 w-16 text-gray-300 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">User not found</h3>
      <p class="text-gray-500 mb-4">The user you're looking for doesn't exist or has been deleted.</p>
      <router-link :to="{ name: 'admin.users' }" class="btn-primary">
        <font-awesome-icon icon="arrow-left" class="mr-2" />
        Back to Users
      </router-link>
    </div>

    <!-- User Details -->
    <div v-else-if="user" class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center space-x-4">
          <router-link :to="{ name: 'admin.users' }" class="btn-ghost p-2">
            <font-awesome-icon icon="arrow-left" class="h-5 w-5" />
          </router-link>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ user.name || `${user.first_name} ${user.last_name}` }}</h1>
            <div class="flex items-center space-x-3 mt-1">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getRoleClass(user.role)">
                <font-awesome-icon :icon="getRoleIcon(user.role)" class="h-3 w-3 mr-1" />
                {{ getRoleLabel(user.role) }}
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusClass(user.status)">
                <div class="w-1.5 h-1.5 rounded-full mr-1.5" :class="getStatusDotClass(user.status)">
                </div>
                {{ getStatusLabel(user.status) }}
              </span>
            </div>
          </div>
        </div>

        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
          <button v-if="hasPermission('users.edit')" @click="handleEdit" class="btn-outline">
            <font-awesome-icon icon="edit" class="h-4 w-4 mr-2" />
            Edit User
          </button>

          <div class="relative" ref="actionsDropdownRef">
            <button @click="toggleActionsDropdown" class="btn-outline">
              <font-awesome-icon icon="ellipsis-v" class="h-4 w-4" />
            </button>

            <!-- Actions Dropdown -->
            <div v-if="showActionsDropdown"
              class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" @click.stop>
              <div class="py-1">
                <button @click="handleSendPasswordReset"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <font-awesome-icon icon="key" class="h-4 w-4 mr-3 text-gray-400" />
                  Reset Password
                </button>

                <button v-if="user.role !== 'super_admin'" @click="handleImpersonate"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <font-awesome-icon icon="user-secret" class="h-4 w-4 mr-3 text-gray-400" />
                  Impersonate User
                </button>

                <button v-if="user.role !== 'super_admin'" @click="handleToggleBan"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <font-awesome-icon icon="user-lock" class="h-4 w-4 mr-3 text-gray-400" />
                  {{ user.ban ? 'Unban User' : 'Ban User' }}
                </button>

                <hr class="my-1">

                <button @click="handleDelete" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                  <font-awesome-icon icon="trash" class="h-4 w-4 mr-3" />
                  Delete User
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Information Cards -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic Information -->
          <div class="card">
            <div class="card-header">
              <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            </div>
            <div class="card-body">
              <div class="flex items-start space-x-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                  <img v-if="user.avatar" :src="user.avatar" :alt="user.name || `${user.first_name} ${user.last_name}`"
                    class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg">
                  <div v-else
                    class="h-24 w-24 rounded-full bg-primary-100 flex items-center justify-center border-4 border-white shadow-lg">
                    <span class="text-2xl font-medium text-primary-600">
                      {{ getUserInitials(user.name || `${user.first_name} ${user.last_name}`) }}
                    </span>
                  </div>
                </div>

                <!-- Details -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="text-sm font-medium text-gray-500">Full Name</label>
                    <p class="text-sm text-gray-900">{{ user.first_name }} {{ user.last_name }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-sm text-gray-900">{{ user.email }}</p>
                  </div>
                  <div v-if="user.phone">
                    <label class="text-sm font-medium text-gray-500">Phone</label>
                    <p class="text-sm text-gray-900">{{ user.phone }}</p>
                  </div>
                  <div v-if="user.date_of_birth">
                    <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                    <p class="text-sm text-gray-900">{{ formatDate(user.date_of_birth) }}</p>
                  </div>
                  <div v-if="user.gender">
                    <label class="text-sm font-medium text-gray-500">Gender</label>
                    <p class="text-sm text-gray-900 capitalize">{{ user.gender }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500">Account Status</label>
                    <div class="flex items-center space-x-2 mt-1">
                      <select v-if="hasPermission('users.edit')" :value="user.status"
                        @change="handleStatusChange($event.target.value)"
                        class="text-xs border-0 bg-transparent font-medium rounded-full px-2 py-1 focus:ring-2 focus:ring-primary-500"
                        :class="getStatusClass(user.status)">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                      </select>
                      <span v-else :class="getStatusClass(user.status)"
                        class="px-2 py-1 rounded-full text-xs font-medium">
                        {{ getStatusLabel(user.status) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Address Information -->
          <div v-if="user.addresses?.length > 0" class="card">
            <div class="card-header">
              <h3 class="text-lg font-medium text-gray-900">Addresses</h3>
            </div>
            <div class="card-body">
              <div class="space-y-4">
                <div v-for="address in user.addresses" :key="address.id" class="p-4 border border-gray-200 rounded-lg">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-2 mb-2">
                        <h4 class="text-sm font-medium text-gray-900">{{ address.type ||
                          'Address' }}</h4>
                        <span v-if="address.is_default" class="badge badge-primary text-xs">Default</span>
                      </div>
                      <p class="text-sm text-gray-600">
                        {{ address.street }}<br>
                        {{ address.city }}, {{ address.state }} {{ address.postal_code }}<br>
                        {{ address.country }}
                      </p>
                    </div>
                    <font-awesome-icon icon="map-marker-alt" class="h-4 w-4 text-gray-400" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="card">
            <div class="card-header">
              <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            </div>
            <div class="card-body">
              <div v-if="userActivity?.length > 0" class="space-y-4">
                <div v-for="activity in userActivity" :key="activity.id" class="flex items-start space-x-3">
                  <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                    :class="getActivityIconClass(activity.type)">
                    <font-awesome-icon :icon="getActivityIcon(activity.type)" class="h-4 w-4" />
                  </div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-900">{{ activity.description }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                <font-awesome-icon icon="clock" class="h-8 w-8 text-gray-300 mb-2" />
                <p>No recent activity</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Quick Stats -->
          <div class="card">
            <div class="card-header">
              <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
            </div>
            <div class="card-body">
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <font-awesome-icon icon="calendar-alt" class="h-4 w-4 text-gray-400" />
                    <span class="text-sm text-gray-600">Member Since</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900">
                    {{ formatDate(user.created_at) }}
                  </span>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <font-awesome-icon icon="sign-in-alt" class="h-4 w-4 text-gray-400" />
                    <span class="text-sm text-gray-600">Last Login</span>
                  </div>
                  <span class="text-sm font-medium text-gray-900">
                    {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                  </span>
                </div>

                <div v-if="userStats" class="space-y-2 pt-4 border-t border-gray-200">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Orders</span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ userStats.orders_count || 0 }}
                    </span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Spent</span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ formatCurrency(userStats.total_spent || 0) }}
                    </span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Average Order</span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ formatCurrency(userStats.average_order || 0) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Settings -->
          <div class="card">
            <div class="card-header">
              <h3 class="text-lg font-medium text-gray-900">Account Settings</h3>
            </div>
            <div class="card-body">
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Email Verified</span>
                  <div class="flex items-center space-x-1">
                    <font-awesome-icon :icon="user.email_verified_at ? 'check-circle' : 'times-circle'"
                      :class="user.email_verified_at ? 'text-green-500' : 'text-red-500'" class="h-4 w-4" />
                    <span class="text-sm font-medium"
                      :class="user.email_verified_at ? 'text-green-600' : 'text-red-600'">
                      {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                    </span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">2FA Enabled</span>
                  <div class="flex items-center space-x-1">
                    <font-awesome-icon :icon="user.two_factor_enabled ? 'shield-alt' : 'shield'"
                      :class="user.two_factor_enabled ? 'text-green-500' : 'text-gray-400'" class="h-4 w-4" />
                    <span class="text-sm font-medium"
                      :class="user.two_factor_enabled ? 'text-green-600' : 'text-gray-600'">
                      {{ user.two_factor_enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Marketing Emails</span>
                  <div class="flex items-center space-x-1">
                    <font-awesome-icon :icon="user.marketing_emails ? 'check' : 'times'"
                      :class="user.marketing_emails ? 'text-green-500' : 'text-gray-400'" class="h-4 w-4" />
                    <span class="text-sm font-medium"
                      :class="user.marketing_emails ? 'text-green-600' : 'text-gray-600'">
                      {{ user.marketing_emails ? 'Subscribed' : 'Unsubscribed' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Permissions -->
          <div v-if="user.permissions?.length > 0" class="card">
            <div class="card-header">
              <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
            </div>
            <div class="card-body">
              <div class="space-y-2">
                <div v-for="permission in user.permissions" :key="permission" class="flex items-center space-x-2">
                  <font-awesome-icon icon="check" class="h-3 w-3 text-green-500" />
                  <span class="text-sm text-gray-700">{{ formatPermission(permission) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders History -->
      <div v-if="userOrders?.length > 0" class="card">
        <div class="card-header">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Order History</h3>
            <router-link :to="{ name: 'admin.orders', query: { user_id: user.id } }"
              class="text-sm text-primary-600 hover:text-primary-800">
              View All Orders
            </router-link>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in userOrders.slice(0, 5)" :key="order.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ order.order_number }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(order.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="badge" :class="getOrderStatusClass(order.status)">
                      {{ getOrderStatusLabel(order.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ formatCurrency(order.total) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <router-link :to="{ name: 'admin.orders.detail', params: { id: order.id } }"
                      class="text-primary-600 hover:text-primary-900">
                      View
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal v-model:show="showDeleteModal" :user="user" :loading="usersStore.isDeleting"
      @confirm="confirmDelete" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useUsersStore } from '@/stores/users'
import { format } from 'date-fns'
import DeleteConfirmationModal from '@/components/admin/users/DeleteConfirmationModal.vue'

// Composables
const route = useRoute()
const router = useRouter()
const { hasPermission } = useAuth()
const usersStore = useUsersStore()

// Reactive data
const showActionsDropdown = ref(false)
const showDeleteModal = ref(false)
const actionsDropdownRef = ref(null)

// Computed
const user = computed(() => usersStore.currentUser)
const userStats = computed(() => user.value?.stats)
const userActivity = computed(() => user.value?.recent_activity || [])
const userOrders = computed(() => user.value?.orders || [])

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

const getStatusDotClass = (status) => {
  const classes = {
    active: 'bg-green-400',
    inactive: 'bg-gray-400',
    pending: 'bg-yellow-400',
    suspended: 'bg-red-400'
  }
  return classes[status] || 'bg-gray-400'
}

const getActivityIcon = (type) => {
  const icons = {
    login: 'sign-in-alt',
    logout: 'sign-out-alt',
    order: 'shopping-cart',
    profile_update: 'user-edit',
    password_change: 'key',
    email_verified: 'envelope-check'
  }
  return icons[type] || 'clock'
}

const getActivityIconClass = (type) => {
  const classes = {
    login: 'bg-green-100 text-green-600',
    logout: 'bg-gray-100 text-gray-600',
    order: 'bg-blue-100 text-blue-600',
    profile_update: 'bg-yellow-100 text-yellow-600',
    password_change: 'bg-purple-100 text-purple-600',
    email_verified: 'bg-green-100 text-green-600'
  }
  return classes[type] || 'bg-gray-100 text-gray-600'
}

const getOrderStatusClass = (status) => {
  const classes = {
    pending: 'badge-warning',
    processing: 'badge-primary',
    shipped: 'badge-secondary',
    delivered: 'badge-success',
    cancelled: 'badge-danger'
  }
  return classes[status] || 'badge-secondary'
}

const getOrderStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return labels[status] || status
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return format(new Date(date), 'MMM d, yyyy')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const formatPermission = (permission) => {
  return permission.replace(/[._]/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const toggleActionsDropdown = () => {
  showActionsDropdown.value = !showActionsDropdown.value
}

const handleEdit = () => {
  router.push({ name: 'admin.users.edit', params: { id: user.value.id } })
}

const handleStatusChange = async (newStatus) => {
  await usersStore.updateUserStatus(user.value.id, newStatus, user.value.role)
}

const handleToggleBan = async () => {
  const newStatus = user.value.ban ? 'unban' : 'ban'
  if (confirm(`Are you sure you want to ${newStatus} ${user.value.name || user.value.first_name + ' ' + user.value.last_name}?`)) {
    await usersStore.toggleUserBan(user.value.id)
  }
}

const handleSendPasswordReset = async () => {
  if (confirm(`Send password reset email to ${user.value.email}?`)) {
    await usersStore.sendPasswordReset(user.value.id)
  }
  showActionsDropdown.value = false
}

const handleImpersonate = async () => {
  if (confirm(`Are you sure you want to impersonate ${user.value.name || user.value.first_name + ' ' + user.value.last_name}?`)) {
    await usersStore.impersonateUser(user.value.id)
    router.push('/')
  }
  showActionsDropdown.value = false
}

const handleDelete = () => {
  showDeleteModal.value = true
  showActionsDropdown.value = false
}

const confirmDelete = async () => {
  const result = await usersStore.deleteUser(user.value.id)
  if (result.success) {
    router.push({ name: 'admin.users' })
  }
}

const loadUserData = async () => {
  const userId = route.params.id
  if (userId) {
    await usersStore.fetchUser(userId)
  }
}

// Click outside handler
const handleClickOutside = (event) => {
  if (actionsDropdownRef.value && !actionsDropdownRef.value.contains(event.target)) {
    showActionsDropdown.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadUserData()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  usersStore.clearCurrentUser()
})
</script>
