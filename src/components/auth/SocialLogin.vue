// 1. Install Google SDK in main.js/main.ts
import { createApp } from 'vue'
import App from './App.vue'

const app = createApp(App)

// Initialize Google SDK
window.onload = () => {
  google.accounts.id.initialize({
    client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
    callback: handleCredentialResponse,
  })
}

app.mount('#app')

// ============================================
// 2. Social Auth Component (Updated)
// ============================================
// File: components/SocialAuth.vue

<template>
  <div class="space-y-3">
    <button 
      @click="handleGoogleLogin"
      :disabled="isLoading"
      class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <font-awesome-icon 
        v-if="!isLoading"
        :icon="['fab', 'google']" 
        class="text-red-500" 
      />
      <span v-if="isLoading" class="animate-spin">⏳</span>
      <span>{{ isLoading ? t('common.loading') : t('auth.social.continue_google') }}</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const toast = useToast()
const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()
const isLoading = ref(false)

interface GoogleCredentialResponse {
  credential: string
}

const handleGoogleLogin = async () => {
  try {
    isLoading.value = true

    // Trigger Google Sign-In dialog
    google.accounts.id.renderButton(
      document.createElement('div'),
      { theme: 'outline', size: 'large' }
    )

    // Programmatically trigger sign-in
    google.accounts.id.prompt((notification) => {
      if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
        // Fallback: trigger standard popup
        google.accounts.id.renderButton(
          document.getElementById('google-signin-btn') || document.body,
          { prompt: 'select_account' }
        )
      }
    })
  } catch (error) {
    console.error('Google Sign-In error:', error)
    toast.error(t('auth.social.error'))
    isLoading.value = false
  }
}

// Handle Google credential response (called by Google SDK)
window.handleCredentialResponse = async (response: GoogleCredentialResponse) => {
  try {
    isLoading.value = true

    // Send credential to backend
    const apiResponse = await fetch('/api/auth/google', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        token: response.credential,
      }),
    })

    if (!apiResponse.ok) {
      const errorData = await apiResponse.json()
      throw new Error(errorData.message || t('auth.social.error'))
    }

    const data = await apiResponse.json()

    // Store auth tokens and user data
    authStore.setUser(data.user)
    authStore.setToken(data.token)
    
    // Optional: store refresh token
    if (data.refresh_token) {
      localStorage.setItem('refresh_token', data.refresh_token)
    }

    toast.success(t('auth.login.success'))

    // Redirect to dashboard
    router.push({ name: 'dashboard' })
  } catch (error) {
    console.error('Backend authentication error:', error)
    toast.error(error instanceof Error ? error.message : t('auth.social.error'))
  } finally {
    isLoading.value = false
  }
}

// Make it globally available for Google SDK
Object.assign(window, { handleCredentialResponse })
</script>

// ============================================
// 3. .env.local Configuration
// ============================================
VITE_GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com
VITE_API_URL=http://localhost:8000

// ============================================
// 4. HTML Head (index.html)
// ============================================
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>