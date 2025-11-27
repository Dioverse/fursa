<template>
  <AuthLayout>
    <div class="w-full max-w-md mx-auto text-center py-12">
      <h2 class="text-3xl font-bold text-primary mb-4">Verify Your Account</h2>
      <p class="mb-6 text-gray-600">
        A fresh verification link has been sent to your email address.
        Please check your inbox and click the link to continue.
      </p>

      <BaseButton :loading="loading" @click="resendVerification" variant="primary" size="lg" fullWidth icon="envelope">
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
import { useRouter } from 'vue-router'
import apiClient from '@/services/api'

const toast = useToast()
const authStore = useAuthStore()
const loading = ref(false)
const router = useRouter();

const resendVerification = async () => {
  loading.value = true
  try {
    await apiClient.post('/email/verification-notification')

    toast.success('Verification link has been resent to your email!')
  } catch (err) {
    const message =
      err.response?.data?.message ||
      err.message ||
      'Failed to resend verification email.'
    toast.error(message)

    // Handle specific cases
    if (err.response?.status === 400) {
      // Update user if present in response
      if (err.response.data?.user) {
        authStore.user = err.response.data.user
      }
      // Redirect to dashboard
      router.push({ name: 'dashboard' })
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  resendVerification()
})
</script>
