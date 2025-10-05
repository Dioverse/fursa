<template>
  <AuthLayout>
    <div class="w-full max-w-md mx-auto text-center py-12">
      <h2 class="text-3xl font-bold text-primary mb-4">Verify Your Account</h2>
      <p class="mb-6 text-gray-600">
        A fresh verification link has been sent to your email address.  
        Please check your inbox and click the link to continue.
      </p>

      <BaseButton
        :loading="loading"
        @click="resendVerification"
        variant="primary"
        size="lg"
        fullWidth
        icon="envelope"
      >
        Resend Verification Email
      </BaseButton>
    </div>
  </AuthLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import axios from 'axios'

const toast = useToast()
const authStore = useAuthStore()
const loading = ref(false)

const resendVerification = async () => {
  loading.value = true
  try {
    await axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/email/verification-notification`,
      {},
      { headers: { Authorization: `Bearer ${authStore.token}` } }
    )
    toast.success('Verification link has been resent to your email!')
  } catch (err) {
    toast.error('Failed to resend verification email.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  resendVerification()
})
</script>
