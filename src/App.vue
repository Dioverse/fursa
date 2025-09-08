<template>
  <div id="app" class="min-h-screen">
    <!-- Loading Screen -->
    <div v-if="isInitializing" class="fixed inset-0 bg-white flex items-center justify-center z-50">
      <div class="text-center">
        <div class="mb-4">
          <div class="bg-primary-600 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center">
            <font-awesome-icon icon="shield" class="h-8 w-8 text-white" />
          </div>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
          Fursa Energy Admin
        </h1>
        <div class="flex items-center justify-center space-x-2 text-gray-600">
          <div class="loading-spinner w-4 h-4"></div>
          <span>Loading...</span>
        </div>
      </div>
    </div>

    <!-- Main App Content -->
    <div v-else>
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'

// Composables
const { initialize } = useAuth()

// State
const isInitializing = ref(true)

// Initialize authentication on app mount
onMounted(async () => {
  try {
    await initialize()
  } catch (error) {
    console.error('App initialization failed:', error)
  } finally {
    isInitializing.value = false
  }
})
</script>

<style>
/* Global styles are imported in main.js via main.css */

/* Loading animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

.loading-spinner {
  animation: spin 1s linear infinite;
}
</style>