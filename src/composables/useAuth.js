import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

export function useAuth() {
  const router = useRouter()
  const authStore = useAuthStore()
  const toast = useToast()

  const isAuthenticated = computed(() => authStore.isAuthenticated)
  const user = computed(() => authStore.user)
  const loading = computed(() => authStore.loading)

  async function login(credentials) {
    try {
      await authStore.login(credentials)
      toast.success('Welcome back!')
      router.push('/dashboard')
    } catch (error) {
      toast.error(error.message || 'Login failed')
      throw error
    }
  }

  async function register(userData) {
    try {
      await authStore.register(userData)
      toast.success('Registration successful! Please login.')
      router.push('/login')
    } catch (error) {
      toast.error(error.message || 'Registration failed')
      throw error
    }
  }

  function logout() {
    authStore.logout()
    router.push('/')
  }

  function requireAuth() {
    if (!isAuthenticated.value) {
      router.push('/login')
      return false
    }
    return true
  }

  return {
    isAuthenticated,
    user,
    loading,
    login,
    register,
    logout,
    requireAuth,
  }
}
