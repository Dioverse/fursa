<template>
    <header class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <!-- Left side -->
            <div class="flex items-center space-x-4">
                <!-- Mobile menu button -->
                <button @click="$emit('toggleMobileSidebar')"
                    class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
                    <font-awesome-icon icon="bars" class="h-5 w-5" />
                </button>

                <!-- Desktop sidebar toggle -->
                <button @click="$emit('toggleSidebar')"
                    class="hidden lg:block p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
                    <font-awesome-icon icon="bars" class="h-5 w-5" />
                </button>

                <!-- Page title for mobile -->
                <div class="lg:hidden">
                    <h1 class="text-lg font-semibold text-gray-900">
                        {{ currentPageTitle }}
                    </h1>
                </div>
            </div>

            <!-- Center - Search (hidden on mobile) -->
            <div class="hidden sm:block flex-1 max-w-lg mx-8">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <font-awesome-icon icon="search" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input v-model="searchQuery" type="search"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm"
                        placeholder="Search users, products, orders..." @keydown.enter="performSearch">
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Quick Actions Dropdown -->
                <div class="hidden sm:block relative" ref="quickActionsRef">
                    <button @click="toggleQuickActions"
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors relative">
                        <font-awesome-icon icon="plus" class="h-5 w-5" />
                    </button>

                    <!-- Quick Actions Menu -->
                    <div v-if="showQuickActions"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                        @click.stop>
                        <div class="py-1">
                            <button v-for="action in quickActions" :key="action.name" @click="handleQuickAction(action)"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-3 transition-colors">
                                <font-awesome-icon :icon="action.icon" class="h-4 w-4 text-gray-400" />
                                <span>{{ action.label }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="relative" ref="notificationsRef">
                    <button @click="toggleNotifications"
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors relative">
                        <font-awesome-icon icon="bell" class="h-5 w-5" />

                        <!-- Notification badge -->
                        <span v-if="unreadNotifications > 0"
                            class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                            {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
                        </span>
                    </button>

                    <!-- Notifications dropdown -->
                    <div v-if="showNotifications"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                        @click.stop>
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                                <button v-if="unreadNotifications > 0" @click="markAllAsRead"
                                    class="text-sm text-primary-600 hover:text-primary-800">
                                    Mark all as read
                                </button>
                            </div>
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500">
                                <font-awesome-icon icon="bell" class="h-8 w-8 text-gray-300 mb-2" />
                                <p>No notifications</p>
                            </div>

                            <div v-else class="divide-y divide-gray-200">
                                <div v-for="notification in notifications" :key="notification.id"
                                    @click="handleNotificationClick(notification)"
                                    class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                                    :class="{ 'bg-blue-50': !notification.read }">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                            :class="getNotificationIconClass(notification.type)">
                                            <font-awesome-icon :icon="getNotificationIcon(notification.type)"
                                                class="h-4 w-4 text-white" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ notification.title }}
                                            </p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ notification.message }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ formatNotificationTime(notification.created_at) }}
                                            </p>
                                        </div>
                                        <div v-if="!notification.read" class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-primary-500 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border-t border-gray-200">
                            <router-link to="/admin/notifications"
                                class="block text-sm text-center text-primary-600 hover:text-primary-800"
                                @click="showNotifications = false">
                                View all notifications
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative" ref="userMenuRef">
                    <button @click="toggleUserMenu"
                        class="flex items-center space-x-3 p-2 text-sm rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                        <!-- User avatar -->
                        <div class="flex-shrink-0">
                            <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name || 'User'"
                                class="h-8 w-8 rounded-full object-cover">
                            <div v-else
                                class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center text-white text-sm font-medium">
                                {{ userInitials }}
                            </div>
                        </div>

                        <!-- User info (hidden on mobile) -->
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-medium text-gray-900">
                                {{ user?.name || 'User' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ getRoleDisplayName(user?.role) }}
                            </p>
                        </div>

                        <font-awesome-icon icon="chevron-down" class="h-4 w-4 text-gray-400"
                            :class="{ 'transform rotate-180': showUserMenu }" />
                    </button>

                    <!-- User dropdown menu -->
                    <div v-if="showUserMenu"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                        @click.stop>
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-200">
                                <p class="font-medium text-gray-900">{{ user?.name || 'User' }}</p>
                                <p class="text-xs">{{ user?.email || '' }}</p>
                            </div>

                            <router-link to="/admin/profile"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                @click="showUserMenu = false">
                                <font-awesome-icon icon="user" class="h-4 w-4 mr-3 text-gray-400" />
                                Profile
                            </router-link>

                            <router-link to="/admin/settings"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                @click="showUserMenu = false">
                                <font-awesome-icon icon="cog" class="h-4 w-4 mr-3 text-gray-400" />
                                Settings
                            </router-link>

                            <div class="border-t border-gray-200 my-1"></div>

                            <button @click="handleLogout"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <font-awesome-icon icon="sign-out-alt" class="h-4 w-4 mr-3 text-gray-400" />
                                Sign out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { formatDistanceToNow } from 'date-fns'

// Props & Emits
defineEmits(['toggleMobileSidebar', 'toggleSidebar'])

// Composables
const route = useRoute()
const router = useRouter()
const { user, userInitials, logout, getRoleDisplayName, hasPermission } = useAuth()

// Reactive data
const searchQuery = ref('')
const showQuickActions = ref(false)
const showNotifications = ref(false)
const showUserMenu = ref(false)
const notifications = ref([
    {
        id: 1,
        type: 'order',
        title: 'New Order Received',
        message: 'Order #1234 has been placed by John Doe',
        read: false,
        created_at: new Date(Date.now() - 1000 * 60 * 30) // 30 minutes ago
    },
    {
        id: 2,
        type: 'user',
        title: 'New User Registration',
        message: 'Jane Smith has registered as a distributor',
        read: false,
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 2) // 2 hours ago
    },
    {
        id: 3,
        type: 'system',
        title: 'System Update',
        message: 'System maintenance completed successfully',
        read: true,
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 24) // 1 day ago
    }
])

// Refs for dropdowns
const quickActionsRef = ref(null)
const notificationsRef = ref(null)
const userMenuRef = ref(null)

// Computed
const currentPageTitle = computed(() => {
    return route.meta?.title || 'Dashboard'
})

const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read).length
})

const quickActions = computed(() => {
    const actions = []

    if (hasPermission('users.create')) {
        actions.push({
            name: 'new-user',
            label: 'New User',
            icon: 'user-plus',
            route: 'admin.users.create'
        })
    }

    if (hasPermission('products.create')) {
        actions.push({
            name: 'new-product',
            label: 'New Product',
            icon: 'plus-circle',
            route: 'admin.products.create'
        })
    }

    if (hasPermission('orders.create')) {
        actions.push({
            name: 'new-order',
            label: 'New Order',
            icon: 'shopping-cart',
            route: 'admin.orders.create'
        })
    }

    return actions
})

// Methods
const toggleQuickActions = () => {
    showQuickActions.value = !showQuickActions.value
    showNotifications.value = false
    showUserMenu.value = false
}

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value
    showQuickActions.value = false
    showUserMenu.value = false
}

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value
    showQuickActions.value = false
    showNotifications.value = false
}

const closeAllDropdowns = () => {
    showQuickActions.value = false
    showNotifications.value = false
    showUserMenu.value = false
}

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({
            name: 'admin.search',
            query: { q: searchQuery.value.trim() }
        })
    }
}

const handleQuickAction = (action) => {
    showQuickActions.value = false
    router.push({ name: action.route })
}

const handleNotificationClick = (notification) => {
    // Mark as read
    notification.read = true

    // Navigate to relevant page based on notification type
    switch (notification.type) {
        case 'order':
            router.push({ name: 'admin.orders' })
            break
        case 'user':
            router.push({ name: 'admin.users' })
            break
        case 'product':
            router.push({ name: 'admin.products' })
            break
        default:
            router.push({ name: 'admin.dashboard' })
    }

    showNotifications.value = false
}

const markAllAsRead = () => {
    notifications.value.forEach(notification => {
        notification.read = true
    })
}

const getNotificationIcon = (type) => {
    const icons = {
        order: 'shopping-cart',
        user: 'user',
        product: 'box',
        system: 'cog',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    }
    return icons[type] || 'bell'
}

const getNotificationIconClass = (type) => {
    const classes = {
        order: 'bg-success-500',
        user: 'bg-primary-500',
        product: 'bg-warning-500',
        system: 'bg-gray-500',
        warning: 'bg-danger-500',
        info: 'bg-blue-500'
    }
    return classes[type] || 'bg-gray-500'
}

const formatNotificationTime = (date) => {
    return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const handleLogout = async () => {
    closeAllDropdowns()
    await logout()
}

// Click outside handler
const handleClickOutside = (event) => {
    if (
        quickActionsRef.value &&
        !quickActionsRef.value.contains(event.target)
    ) {
        showQuickActions.value = false
    }

    if (
        notificationsRef.value &&
        !notificationsRef.value.contains(event.target)
    ) {
        showNotifications.value = false
    }

    if (
        userMenuRef.value &&
        !userMenuRef.value.contains(event.target)
    ) {
        showUserMenu.value = false
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