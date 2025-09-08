<template>
  <div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-lg shadow-soft text-white overflow-hidden">
      <div class="px-6 py-8 sm:px-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2 text-white">
              Welcome back, {{ getUserDisplayName() }}!
            </h1>
            <p class="text-primary-100 text-base sm:text-lg">
              Here's what's happening with your business today.
            </p>
          </div>

          <div class="hidden sm:block">
            <div class="bg-white bg-opacity-20 rounded-lg p-4">
              <font-awesome-icon icon="chart-line" class="h-12 w-12 text-white opacity-80" />
            </div>
          </div>
        </div>

        <!-- Quick stats -->
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="bg-white bg-opacity-10 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold">{{ todayStats.orders }}</div>
            <div class="text-sm text-primary-100">Orders Today</div>
          </div>
          <div class="bg-white bg-opacity-10 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold">{{ todayStats.revenue }}</div>
            <div class="text-sm text-primary-100">Revenue Today</div>
          </div>
          <div class="bg-white bg-opacity-10 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold">{{ todayStats.users }}</div>
            <div class="text-sm text-primary-100">New Users</div>
          </div>
          <div class="bg-white bg-opacity-10 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold">{{ todayStats.products }}</div>
            <div class="text-sm text-primary-100">Products</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatsCard title="Total Revenue" :value="formatCurrency(stats.totalRevenue)" :change="stats.revenueChange"
        icon="dollar-sign" color="success" :loading="statsLoading" />

      <StatsCard title="Total Orders" :value="stats.totalOrders.toLocaleString()" :change="stats.ordersChange"
        icon="shopping-cart" color="primary" :loading="statsLoading" />

      <StatsCard title="Active Users" :value="stats.activeUsers.toLocaleString()" :change="stats.usersChange"
        icon="users" color="warning" :loading="statsLoading" />

      <StatsCard title="Distributors" :value="stats.distributors.toLocaleString()" :change="stats.distributorsChange"
        icon="truck" color="secondary" :loading="statsLoading" />
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Revenue Chart -->
      <div class="card">
        <div class="card-header">
          <h3 class="text-lg font-medium text-gray-900">Revenue Overview</h3>
          <div class="flex items-center space-x-2">
            <select v-model="revenueChartPeriod" @change="loadRevenueChart" class="text-sm border-gray-300 rounded-md">
              <option value="7days">Last 7 days</option>
              <option value="30days">Last 30 days</option>
              <option value="3months">Last 3 months</option>
              <option value="12months">Last 12 months</option>
            </select>
          </div>
        </div>
        <div class="card-body">
          <RevenueChart :key="revenueChartPeriod + '-' + revenueChartLoading" :data="revenueChartData"
            :loading="revenueChartLoading" :period="revenueChartPeriod" />
        </div>
      </div>

      <!-- Orders Status Chart -->
      <div class="card">
        <div class="card-header">
          <h3 class="text-lg font-medium text-gray-900">Orders by Status</h3>
        </div>
        <div class="card-body" style="min-height: 400px;">
          <OrdersStatusChart :key="ordersStatusLoading" :data="ordersStatusData" :loading="ordersStatusLoading" />
        </div>
      </div>
    </div>

    <!-- Recent Activity and Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Orders -->
      <div class="lg:col-span-2">
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
            <router-link :to="{ name: 'admin.orders' }" class="text-sm text-primary-600 hover:text-primary-800">
              View all
            </router-link>
          </div>
          <div class="card-body p-0">
            <RecentOrdersTable :orders="recentOrders" :loading="recentOrdersLoading" />
          </div>
        </div>
      </div>

      <!-- Quick Actions and Notifications -->
      <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
          </div>
          <div class="card-body">
            <div class="space-y-3">
              <router-link v-for="action in quickActions" :key="action.name" :to="{ name: action.route }"
                class="flex items-center p-3 text-sm text-gray-700 rounded-md hover:bg-gray-50 transition-colors group">
                <div class="flex-shrink-0 w-8 h-8 rounded-md flex items-center justify-center mr-3"
                  :class="action.bgColor">
                  <font-awesome-icon :icon="action.icon" class="h-4 w-4" :class="action.iconColor" />
                </div>
                <span class="group-hover:text-gray-900">{{ action.label }}</span>
              </router-link>
            </div>
          </div>
        </div>

        <!-- System Info -->
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-gray-900">System Info</h3>
          </div>
          <div class="card-body">
            <div class="space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Server Status</span>
                <span class="text-green-600 font-medium">Online</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Last Backup</span>
                <span class="text-gray-900">2 hours ago</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Storage Used</span>
                <span class="text-gray-900">2.3 GB / 10 GB</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full" style="width: 23%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import api from '@/services/api'
import StatsCard from '@/components/admin/dashboard/StatsCard.vue'
import RevenueChart from '@/components/admin/dashboard/RevenueChart.vue'
import OrdersStatusChart from '@/components/admin/dashboard/OrdersStatusChart.vue'
import RecentOrdersTable from '@/components/admin/dashboard/RecentOrdersTable.vue'

const { getUserDisplayName, hasPermission } = useAuth()


// Reactive data
const statsLoading = ref(true)
const revenueChartLoading = ref(true)
const ordersStatusLoading = ref(true)
const recentOrdersLoading = ref(true)
const revenueChartPeriod = ref('30days')

const stats = ref({
  totalRevenue: 0,
  revenueChange: 0,
  totalOrders: 0,
  ordersChange: 0,
  activeUsers: 0,
  usersChange: 0,
  distributors: 0,
  distributorsChange: 0
})

const todayStats = ref({
  orders: 0,
  revenue: '₦0',
  users: 0,
  products: 0
})

const revenueChartData = ref([])
const ordersStatusData = ref([])
const recentOrders = ref([])

// Computed
const quickActions = computed(() => {
  const actions = []

  if (hasPermission('orders.create')) {
    actions.push({
      name: 'new-order',
      label: 'Create Order',
      icon: 'plus-circle',
      route: 'admin.orders.create',
      bgColor: 'bg-primary-100',
      iconColor: 'text-primary-600'
    })
  }

  if (hasPermission('products.create')) {
    actions.push({
      name: 'new-product',
      label: 'Add Product',
      icon: 'box',
      route: 'admin.products.create',
      bgColor: 'bg-success-100',
      iconColor: 'text-success-600'
    })
  }

  if (hasPermission('users.create')) {
    actions.push({
      name: 'new-user',
      label: 'Add User',
      icon: 'user-plus',
      route: 'admin.users.create',
      bgColor: 'bg-warning-100',
      iconColor: 'text-warning-600'
    })
  }

  actions.push({
    name: 'analytics',
    label: 'View Analytics',
    icon: 'chart-bar',
    route: 'admin.analytics',
    bgColor: 'bg-secondary-100',
    iconColor: 'text-secondary-600'
  })

  return actions
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN'
  }).format(amount)
}

const fetchDashboardData = async () => {
  try {
    statsLoading.value = true
    revenueChartLoading.value = true
    ordersStatusLoading.value = true
    recentOrdersLoading.value = true

    const response = await api.get('/admin-dashboard')
    const data = response.data.data

    // Update today stats
    todayStats.value = {
      orders: parseInt(data.ordersToday) || 0,
      revenue: data.revenueToday ? formatCurrency(parseFloat(data.revenueToday)) : '₦0',
      users: parseInt(data.newUsersToday) || 0,
      products: parseInt(data.productsCount) || 0
    }

    // Update main stats
    stats.value = {
      totalRevenue: parseFloat(data.totalRevenue) || 0,
      revenueChange: parseFloat(data.totalRevenueChange) || 0,
      totalOrders: parseInt(data.totalOrders) || 0,
      ordersChange: parseFloat(data.totalOrdersChange) || 0,
      activeUsers: parseInt(data.activeUsers) || 0,
      usersChange: parseFloat(data.activeUsersChange) || 0,
      distributors: parseInt(data.distributorsCount) || 0,
      distributorsChange: parseFloat(data.distributorsChange) || 0
    }

    // Update revenue chart data
    if (data.revenue_overview) {
      revenueChartData.value = data.revenue_overview.months.map((month, index) => ({
        date: month,
        revenue: parseFloat(data.revenue_overview.values[index]) || 0
      }))
    }

    // Update orders status data
    if (data.orders_by_status) {
      const statusColors = {
        'pending': '#f59e0b',
        'out for delivery': '#3b82f6',
        'delivered': '#10b981',
        'cancelled': '#ef4444'
      }

      ordersStatusData.value = Object.entries(data.orders_by_status).map(([status, count]) => ({
        status: status.charAt(0).toUpperCase() + status.slice(1),
        count: parseInt(count) || 0,
        color: statusColors[status] || '#6b7280'
      }))
    }

    // Update recent orders - transform API data to match component expectations
    if (data.recentOrders && Array.isArray(data.recentOrders)) {
      recentOrders.value = data.recentOrders.map(order => ({
        id: order.order_id,
        customer: order.user ? `${order.user.first_name} ${order.user.last_name}` : 'Unknown Customer',
        email: order.user ? order.user.email : 'N/A',
        total: parseFloat(order.total_amount),
        status: order.status,
        created_at: new Date(order.created_at)
      }))
    }

  } catch (error) {
    console.error('Failed to load dashboard data:', error)

    // Error is already handled by the API service interceptor
    // You can add additional error handling here if needed
  } finally {
    statsLoading.value = false
    revenueChartLoading.value = false
    ordersStatusLoading.value = false
    recentOrdersLoading.value = false
  }
}

const loadRevenueChart = async () => {
  // Since the API provides the revenue overview data with months,
  // we don't need a separate function as the data is already loaded
  // This function is kept for compatibility but doesn't need to do anything
  // console.log('Revenue chart data already loaded from API')
}

// Lifecycle
onMounted(async () => {
  await fetchDashboardData()
})
</script>

<style scoped>
/* Custom animations for dashboard cards */
.card {
  animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hover effects for quick actions */
.group:hover {
  transform: translateX(2px);
  transition: transform 0.2s ease-in-out;
}

/* Gradient text for welcome section */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Custom scrollbar for cards */
.card-body::-webkit-scrollbar {
  width: 4px;
}

.card-body::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.card-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.card-body::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
