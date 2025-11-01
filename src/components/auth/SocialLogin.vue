<template>
  <div class="space-y-3">
    <button
      @click="handleGoogleLogin"
      class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
    >
      <font-awesome-icon :icon="['fab', 'google']" class="text-red-500" />
      <span>{{ t('auth.social.continue_google') }}</span>
    </button>

    <!-- Apple button placeholder -->
    <!-- <button
      @click="handleAppleLogin"
      class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
    >
      <font-awesome-icon :icon="['fab', 'apple']" class="text-black" />
      <span>{{ t('auth.social.continue_apple') }}</span>
    </button> -->
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'

const toast = useToast()
const auth = useAuthStore();
const { t } = useI18n()

const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID // set this in your .env

let googleClient = null

// Handle Google login click
const handleGoogleLogin = () => {
  if (!googleClient) {
    toast.error('Google not initialized')
    return
  }

  // Show popup login
  googleClient.prompt((notification) => {
    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
      console.warn('Google login cancelled or popup blocked')
    }
  })
}

// Backend auth logic
const handleCredentialResponse = async (response) => {
  const id_token = response?.credential
  if (!id_token) {
    toast.error('Google login failed: No credential received')
    return
  }

  try {
    const object = await auth.googleSSO(id_token, 'login')
    console.log(object);

    // toast.success(`${t('auth.logged_in_as')} ${user.name}`)
    toast.success(t('auth.login.success'))
    // optionally emit event or redirect
  } catch (err) {
    console.error('Google login error:', err)
    toast.error(err.response?.data?.message || 'Google login failed')
  }
}

// Load Google script dynamically
const loadGoogleScript = () => {
  return new Promise((resolve, reject) => {
    if (window.google && window.google.accounts) return resolve()

    const script = document.createElement('script')
    script.src = 'https://accounts.google.com/gsi/client'
    script.async = true
    script.defer = true
    script.onload = resolve
    script.onerror = reject
    document.head.appendChild(script)
  })
}

// Initialize Google Identity client
onMounted(async () => {
  try {
    await loadGoogleScript()

    googleClient = window.google.accounts.id
    googleClient.initialize({
      client_id: GOOGLE_CLIENT_ID,
      callback: handleCredentialResponse,
      auto_select: false,
      cancel_on_tap_outside: false,
    })
  } catch (error) {
    console.error('Failed to load Google script:', error)
    toast.error('Failed to initialize Google login')
  }
})

const handleAppleLogin = () => {
  toast.info(t('auth.social.coming_soon'))
}
</script>
