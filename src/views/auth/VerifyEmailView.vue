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
    const { id, hash, expires, signature } = route.query

    await axios.get(`${import.meta.env.VITE_API_BASE_URL}/email/verify/${id}/${hash}`, {
      params: { expires, signature },
      headers: { Authorization: `Bearer ${authStore.token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
 
    success.value = true
    setTimeout(() => {
      router.push('/dashboard')
    }, 2000)
  } catch (err) {
    error.value = "Verification link invalid or expired."
  } finally {
    loading.value = false
  }
})

</script>
