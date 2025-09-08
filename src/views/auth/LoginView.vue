<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-secondary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="flex justify-center mb-6">
          <div class="bg-primary-600 rounded-full p-3">
            <font-awesome-icon icon="shield" class="h-8 w-8 text-white" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Fursa Energy Admin
        </h1>
        <p class="text-gray-600">
          Sign in to access your dashboard
        </p>
      </div>

      <!-- Login Form -->
      <div class="bg-white rounded-xl shadow-strong border border-gray-200 overflow-hidden">
        <form @submit.prevent="handleLogin" class="p-8 space-y-6">
          <!-- Email Input -->
          <div>
            <label for="email" class="form-label">
              Email Address
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <font-awesome-icon icon="envelope" class="h-5 w-5 text-gray-400" />
              </div>
              <input id="email" v-model="form.email" type="email" autocomplete="email" required class="form-input pl-10"
                :class="{ 'form-input-error': errors.email }" placeholder="admin@fursaenergy.com"
                @input="clearError('email')">
            </div>
            <p v-if="errors.email" class="form-error">
              {{ errors.email }}
            </p>
          </div>

          <!-- Password Input -->
          <div>
            <label for="password" class="form-label">
              Password
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <font-awesome-icon icon="lock" class="h-5 w-5 text-gray-400" />
              </div>
              <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password" required class="form-input pl-10 pr-12"
                :class="{ 'form-input-error': errors.password }" placeholder="Enter your password"
                @input="clearError('password')">
              <button type="button" @click="togglePasswordVisibility"
                class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-700 transition-colors">
                <font-awesome-icon :icon="showPassword ? 'eye-slash' : 'eye'" class="h-5 w-5 text-gray-400" />
              </button>
            </div>
            <p v-if="errors.password" class="form-error">
              {{ errors.password }}
            </p>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember" v-model="form.remember" type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded transition-colors">
              <label for="remember" class="ml-2 block text-sm text-gray-700">
                Remember me
              </label>
            </div>

            <button type="button" @click="showForgotPassword = true"
              class="text-sm font-medium text-primary-600 hover:text-primary-500 transition-colors">
              Forgot password?
            </button>
          </div>

          <!-- Login Button -->
          <div>
            <button type="submit" :disabled="authStore.isLoading || !isFormValid"
              class="btn-primary w-full py-3 text-base font-semibold">
              <font-awesome-icon v-if="authStore.isLoading" icon="spinner" class="animate-spin mr-2" />
              <font-awesome-icon v-else icon="sign-in-alt" class="mr-2" />
              {{ authStore.isLoading ? 'Signing in...' : 'Sign In' }}
            </button>
          </div>
        </form>

        <!-- Security Notice -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
          <div class="flex items-center text-sm text-gray-600">
            <font-awesome-icon icon="shield" class="h-4 w-4 text-primary-500 mr-2" />
            Secure admin access with role-based permissions
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center mt-8 text-sm text-gray-600">
        © {{ currentYear }} Fursa Energy. All rights reserved.
      </div>
    </div>

    <!-- Forgot Password Modal -->
    <ForgotPasswordModal v-model:show="showForgotPassword" @success="handleForgotPasswordSuccess" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import ForgotPasswordModal from '@/components/auth/ForgotPasswordModal.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// Reactive data
const showPassword = ref(false)
const showForgotPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = reactive({
  email: '',
  password: ''
})

// Computed
const currentYear = computed(() => new Date().getFullYear())

const isFormValid = computed(() => {
  return form.email.trim() &&
    form.password.trim() &&
    form.email.includes('@') &&
    form.password.length >= 6
})

// Methods
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const clearError = (field) => {
  if (errors[field]) {
    errors[field] = ''
  }
}

const validateForm = () => {
  let isValid = true

  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })

  // Email validation
  if (!form.email.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  // Password validation
  if (!form.password.trim()) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
    isValid = false
  }

  return isValid
}

const handleLogin = async () => {
  if (!validateForm()) return

  const result = await authStore.login({
    user: form.email.trim(),
    password: form.password,
    // remember: form.remember
  })

  if (result.success) {
    // Handle redirect
    const redirect = route.query.redirect || '/admin/dashboard'
    router.push(redirect)
  } else {
    // Handle validation errors from server
    if (result.errors) {
      Object.keys(result.errors).forEach(key => {
        if (errors.hasOwnProperty(key)) {
          errors[key] = result.errors[key][0] // Take first error message
        }
      })
    }
  }
}

const handleForgotPasswordSuccess = () => {
  showForgotPassword.value = false
}

// Auto-fill demo credentials in development
onMounted(() => {
  if (import.meta.env.DEV) {
    form.email = 'arkdevlarry@gmail.com'
    form.password = 'password'
  }
})
</script>

<style scoped>
/* Add subtle animations */
.form-input {
  transition: all 0.2s ease-in-out;
}

.form-input:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

.btn-primary:active {
  transform: translateY(0);
}

/* Custom checkbox styling */
input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
}
</style>
