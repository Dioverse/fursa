<template>
  <div class="flex flex-col items-center justify-center h-screen">
    <h1 class="text-2xl font-bold mb-4">Verifying your email...</h1>
    <p v-if="loading">Please wait...</p>
    <p v-if="error" class="text-red-600">{{ error }}</p>
    <p v-if="success" class="text-green-600">Email verified successfully! Redirecting...</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const success = ref(false)
const error = ref(null)

onMounted(async () => {
  try {
    const { id, hash } = route.params
    const { expires, signature } = route.query

    await axios.get(`${import.meta.env.VITE_API_BASE_URL}/email/verify/${id}/${hash}`, {
      params: { expires, signature },
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    success.value = true
    setTimeout(() => {
      router.push('/dashboard')
    }, 2000)
  } catch (err) {
    error.value = "Verification failed or link expired."
  } finally {
    loading.value = false
  }
})
</script>
