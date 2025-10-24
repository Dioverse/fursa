<template>

  <div class="relative h-screen bg-gray-50 overflow-hidden">
    <!-- Sidebar overlay for mobile -->
    <div v-if="isMobileSidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
      @click="closeMobileSidebar"></div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50">
      <AdminSidebar :is-collapsed="isSidebarCollapsed" :is-mobile-open="isMobileSidebarOpen"
        @toggle-collapsed="toggleSidebarCollapsed" @close-mobile="closeMobileSidebar" />
    </div>

    <!-- Main content area -->
    <div :class="['flex flex-col h-screen min-h-0 transition-all duration-300',
      isSidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64']">
      <!-- Top header -->
      <AdminHeader @toggle-mobile-sidebar="toggleMobileSidebar" @toggle-sidebar="toggleSidebarCollapsed" />

      <!-- Main content -->
      <main class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden">
        <!-- Breadcrumb -->
        <AdminBreadcrumb v-if="showBreadcrumb" class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-3" />

        <!-- Page content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
          <!-- Page header if provided -->
          <!-- <div v-if="pageTitle || pageDescription" class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h1 v-if="pageTitle" class="text-2xl font-bold text-gray-900">
                  {{ pageTitle }}
                </h1>
                <p v-if="pageDescription" class="mt-1 text-sm text-gray-600">
                  {{ pageDescription }}
                </p>
              </div>
              <div v-if="$slots.actions" class="mt-4 sm:mt-0 sm:ml-4">
                <slot name="actions"></slot>
              </div>
            </div>
          </div> -->

          <!-- Router view for page content -->
          <div class="space-y-6">
            <router-view v-slot="{ Component, route }">
              <transition name="page-transition" mode="out-in" appear>
                <component :is="Component" :key="route.path" @update:title="updatePageTitle"
                  @update:description="updatePageDescription" />
              </transition>
            </router-view>
          </div>
        </div>
      </main>

      <!-- Footer -->
      <AdminFooter />
    </div>

    <!-- Loading overlay -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 shadow-strong">
        <div class="flex items-center space-x-3">
          <div class="loading-spinner w-5 h-5"></div>
          <span class="text-gray-900 font-medium">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useSiteStore } from '@/stores/site'
import AdminSidebar from '@/components/admin/layout/AdminSidebar.vue'
import AdminHeader from '@/components/admin/layout/AdminHeader.vue'
import AdminBreadcrumb from '@/components/admin/layout/AdminBreadcrumb.vue'
import AdminFooter from '@/components/admin/layout/AdminFooter.vue'

const route = useRoute()
const { isLoading } = useAuth()

// Layout state
const isSidebarCollapsed = ref(false)
const isMobileSidebarOpen = ref(false)
const pageTitle = ref('')
const pageDescription = ref('')

// Computed
const showBreadcrumb = computed(() => {
  return route.meta.breadcrumb !== false && route.name !== 'admin.dashboard'
})

// Methods
const toggleSidebarCollapsed = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  // Save preference to localStorage
  localStorage.setItem('sidebar-collapsed', isSidebarCollapsed.value.toString())
}

const toggleMobileSidebar = () => {
  isMobileSidebarOpen.value = !isMobileSidebarOpen.value
}

const closeMobileSidebar = () => {
  isMobileSidebarOpen.value = false
}

const updatePageTitle = (title) => {
  pageTitle.value = title
}

const updatePageDescription = (description) => {
  pageDescription.value = description
}

// Handle responsive behavior
const handleResize = () => {
  if (window.innerWidth >= 1024) {
    isMobileSidebarOpen.value = false
  }
}

// Handle escape key to close mobile sidebar
const handleKeydown = (event) => {
  if (event.key === 'Escape' && isMobileSidebarOpen.value) {
    closeMobileSidebar()
  }
}

// Watch route changes
watch(route, (newRoute) => {
  // Close mobile sidebar on route change
  closeMobileSidebar()

  // Reset page title and description
  pageTitle.value = newRoute.meta.title || ''
  pageDescription.value = newRoute.meta.description || ''

  // Update document title
  if (newRoute.meta.title) {
    document.title = `${newRoute.meta.title} | Fursa Energy Admin`
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  // Restore sidebar collapsed state
  const savedState = localStorage.getItem('sidebar-collapsed')
  if (savedState !== null) {
    isSidebarCollapsed.value = savedState === 'true'
  }

  // Add event listeners
  window.addEventListener('resize', handleResize)
  document.addEventListener('keydown', handleKeydown)

  // Handle responsive behavior on mount
  handleResize()

  // Preload site info (logo, name, tax)
  const siteStore = useSiteStore()
  siteStore.fetchSiteInfo().catch(() => {})
})

onUnmounted(() => {
  // Remove event listeners
  window.removeEventListener('resize', handleResize)
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
/* Page transition animations */
.page-transition-enter-active,
.page-transition-leave-active {
  transition: all 0.2s ease-in-out;
}

.page-transition-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-transition-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Custom scrollbar for main content */
main::-webkit-scrollbar {
  width: 6px;
}

main::-webkit-scrollbar-track {
  background: #f1f5f9;
}

main::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

main::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Ensure main content is not covered by sidebar */
@media (min-width: 1024px) {
  .lg\:ml-64 {
    margin-left: 16rem !important;
  }

  .lg\:ml-16 {
    margin-left: 4rem !important;
  }
}
</style>
