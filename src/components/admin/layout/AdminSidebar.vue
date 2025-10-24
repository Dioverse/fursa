<template>
  <!-- Desktop Sidebar -->
  <div
    class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 bg-white border-r border-gray-200 transition-all duration-300 ease-in-out z-30"
    :class="[
      isCollapsed ? 'lg:w-16' : 'lg:w-64'
    ]">
    <!-- Logo Section -->
    <div class="flex items-center px-6 py-4 border-b border-gray-200" :class="{ 'px-4': isCollapsed }">
      <div class="flex items-center space-x-3">
        <div v-if="logo" class="flex-shrink-0">
          <img :src="logo" alt="Logo" class="h-8 w-auto" />
        </div>
        <div v-else class="bg-primary-600 rounded-lg p-2 flex-shrink-0">
          <font-awesome-icon icon="bolt" class="h-6 w-6 text-white" />
        </div>
        <div v-if="!isCollapsed" class="flex flex-col">
          <span class="text-lg font-bold text-gray-900">{{ siteName }}</span>
          <span class="text-xs text-gray-500 uppercase tracking-wide">Admin Panel</span>
        </div>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
      <div v-for="section in menuSections" :key="section.title" class="space-y-2">
        <!-- Section Title -->
        <div v-if="!isCollapsed && section.title"
          class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
          {{ section.title }}
        </div>

        <!-- Menu Items -->
        <div class="space-y-1">
          <router-link v-for="item in section.items" :key="item.name" :to="{ name: item.route }"
            v-slot="{ isActive, isExactActive }">
            <div class="sidebar-link group relative flex items-center gap-3" :class="[
              isActive || isExactActive ? 'sidebar-link-active' : 'sidebar-link-inactive'
            ]" :title="isCollapsed ? item.label : ''">
              <font-awesome-icon :icon="item.icon" class="h-5 w-5 flex-shrink-0" :class="[
                isActive || isExactActive
                  ? 'text-primary-600'
                  : 'text-gray-400 group-hover:text-gray-600'
              ]" />
              <span v-if="!isCollapsed" class="text-sm font-medium">
                {{ item.label }}
              </span>

              <!-- Badge for notifications -->
              <span v-if="item.badge && !isCollapsed" class="ml-auto badge badge-danger text-xs">
                {{ item.badge }}
              </span>

              <!-- Tooltip for collapsed sidebar -->
              <div v-if="isCollapsed"
                class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50 whitespace-nowrap">
                {{ item.label }}
                <div
                  class="absolute top-1/2 right-full transform -translate-y-1/2 border-4 border-transparent border-r-gray-900">
                </div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Collapse Button -->
    <div class="px-4 py-4 border-t border-gray-200">
      <button @click="$emit('toggleCollapsed')"
        class="w-full flex items-center justify-center px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors duration-200"
        :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
        <font-awesome-icon :icon="isCollapsed ? 'chevron-right' : 'chevron-left'" class="h-4 w-4" />
      </button>
    </div>
  </div>

  <!-- Mobile Sidebar -->
  <div
    class="lg:hidden fixed inset-y-0 left-0 bg-white border-r border-gray-200 w-64 transform transition-transform duration-300 ease-in-out z-50"
    :class="[
      isMobileOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
    <!-- Mobile Logo Section -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <div class="flex items-center space-x-3">
        <div v-if="logo" class="flex-shrink-0">
          <img :src="logo" alt="Logo" class="h-8 w-auto" />
        </div>
        <div v-else class="bg-primary-600 rounded-lg p-2">
          <font-awesome-icon icon="bolt" class="h-6 w-6 text-white" />
        </div>
        <div class="flex flex-col">
          <span class="text-lg font-bold text-gray-900">{{ siteName }}</span>
          <span class="text-xs text-gray-500 uppercase tracking-wide">Admin Panel</span>
        </div>
      </div>

      <!-- Close button -->
      <button @click="$emit('closeMobile')"
        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
        <font-awesome-icon icon="times" class="h-5 w-5" />
      </button>
    </div>

    <!-- Mobile Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
      <div v-for="section in menuSections" :key="section.title" class="space-y-2">
        <!-- Section Title -->
        <div v-if="section.title" class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
          {{ section.title }}
        </div>

        <!-- Menu Items -->
        <div class="space-y-1">
          <router-link v-for="item in section.items" :key="item.name" :to="{ name: item.route }"
            v-slot="{ isActive, isExactActive }" @click="$emit('closeMobile')">
            <div class="sidebar-link flex items-center gap-3" :class="[
              isActive || isExactActive ? 'sidebar-link-active' : 'sidebar-link-inactive'
            ]">
              <font-awesome-icon :icon="item.icon" class="h-5 w-5 flex-shrink-0" :class="[
                isActive || isExactActive
                  ? 'text-primary-600'
                  : 'text-gray-400'
              ]" />
              <span class="text-sm font-medium">
                {{ item.label }}
              </span>

              <!-- Badge for notifications -->
              <span v-if="item.badge" class="ml-auto badge badge-danger text-xs">
                {{ item.badge }}
              </span>
            </div>
          </router-link>
        </div>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useSiteStore } from '@/stores/site'
// import { useAuth } from '@/composables/useAuth'

// Props
defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  },
  isMobileOpen: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['toggleCollapsed', 'closeMobile'])

// Composables
// const { hasPermission, hasAnyPermission } = useAuth()

// Menu configuration
const menuSections = computed(() => {
  const sections = [
    {
      title: 'Main',
      items: [
        {
          name: 'dashboard',
          label: 'Dashboard',
          icon: 'home',
          route: 'admin.dashboard'
        }
      ]
    }
  ]


  // Management section (all visible for now)
  const managementItems = [
    {
      name: 'users',
      label: 'Users',
      icon: 'users',
      route: 'admin.users',
      badge: null // You can add notification badges here
    },
    {
      name: 'products',
      label: 'Products',
      icon: 'box',
      route: 'admin.products'
    },
    {
      name: 'categories',
      label: 'Categories',
      icon: 'tags',
      route: 'admin.categories'
    },
    {
      name: 'distributors',
      label: 'Distributors',
      icon: 'truck',
      route: 'admin.distributors'
    },
    {
      name: 'orders',
      label: 'Orders',
      icon: 'shopping-cart',
      route: 'admin.orders'
    }
    ,
    {
      name: 'inventory-logs',
      label: 'Inventory Logs',
      icon: 'list',
      route: 'admin.inventory.logs'
    }
  ]
  sections.push({
    title: 'Management',
    items: managementItems
  })


  // Content & Settings section (all visible for now)
  const contentItems = [
    {
      name: 'content',
      label: 'Content',
      icon: 'file-text',
      route: 'admin.content'
    },
    {
      name: 'settings',
      label: 'Settings',
      icon: 'cog',
      route: 'admin.settings'
    },
    {
      name: 'analytics',
      label: 'Analytics',
      icon: 'chart-line',
      route: 'admin.analytics'
    }
  ]
  sections.push({
    title: 'System',
    items: contentItems
  })

  return sections
})

// Site info (name/logo)
const siteStore = useSiteStore()
const siteName = computed(() => siteStore.name)
const logo = computed(() => siteStore.logo)
</script>

<style scoped>
/* Custom scrollbar for sidebar */
nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}

/* Sidebar link transitions */
.sidebar-link {
  transition: all 0.2s ease-in-out;
}

.sidebar-link:hover {
  transform: translateX(2px);
}

.sidebar-link-active {
  background: linear-gradient(135deg, #f0a146 0%, #d8681d 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.25);
}

.sidebar-link-active:hover {
  transform: translateX(0);
  background: linear-gradient(135deg, #f0a146 0%, #d8681d 100%);
}
</style>
