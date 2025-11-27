<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const success = ref(false)
const error = ref(null)

onMounted(async () => {
  try {
    const { id, hash, expires, signature } = route.query

    // Use apiClient instead of axios
    await apiClient.get(`/email/verify/${id}/${hash}`, {
      params: { expires, signature },
    })

    success.value = true
    setTimeout(() => {
      router.push('/dashboard')
    }, 2000)
  } catch (err) {
    console.error('Verification failed:', err)

    // Fallback in case global interceptor doesn’t handle it
    error.value =
      err.response?.data?.message || 'Verification link invalid or expired.'
  } finally {
    loading.value = false
  }
})

</script>
