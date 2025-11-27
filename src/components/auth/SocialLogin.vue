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
      <span>
        {{ isLoading ? t('auth.social.signing_in') : t('auth.social.continue_google') }}
      </span>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'

const { t } = useI18n()
const auth = useAuthStore()
const toast = useToast()
const router = useRouter()
const isLoading = ref(false)

const handleGoogleLogin = async () => {
  isLoading.value = true
  try {
    google.accounts.id.initialize({
      client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
      callback: async (response) => {
        await sendIdTokenToBackend(response.credential)
      },
      ux_mode: "popup", // recommended for FedCM
      use_fedcm_for_prompt: true, // enable FedCM
    })

    google.accounts.id.prompt()
  } catch (error) {
    console.error("Google login failed:", error)
  } finally {
    isLoading.value = false
  }
}

const sendIdTokenToBackend = async (idToken) => {
  try {
    await auth.googleSSO(idToken)
    toast.success(t('auth.login.success'))
    // Store token or redirect user
    const redirectTo = router.currentRoute.value.query.redirect || '/dashboard'
    router.push(redirectTo)
  } catch (error) {
    toast.error(error.response?.data?.errors?.user?.[0] || t('auth.login.error_invalid'))
    console.error('Backend login error:', error.response?.data || error)
  }
}
</script>
