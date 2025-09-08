<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-secondary-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <div class="bg-primary-600 rounded-full p-3">
                        <font-awesome-icon icon="key" class="h-8 w-8 text-white" />
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Reset Password
                </h1>
                <p class="text-gray-600">
                    Enter your new password below
                </p>
            </div>

            <!-- Reset Form -->
            <div class="bg-white rounded-xl shadow-strong border border-gray-200 overflow-hidden">
                <form @submit.prevent="handleReset" class="p-8 space-y-6">
                    <!-- Token validation message -->
                    <div v-if="!isValidToken" class="text-center">
                        <div class="bg-danger-50 border border-danger-200 rounded-lg p-4">
                            <font-awesome-icon icon="exclamation-triangle"
                                class="h-8 w-8 text-danger-500 mx-auto mb-3" />
                            <h3 class="text-lg font-medium text-danger-900 mb-2">Invalid Reset Link</h3>
                            <p class="text-danger-700 text-sm mb-4">
                                This password reset link is invalid or has expired.
                            </p>
                            <router-link to="/login" class="btn-primary">
                                Back to Login
                            </router-link>
                        </div>
                    </div>

                    <!-- Reset form -->
                    <template v-else>
                        <!-- New Password Input -->
                        <div>
                            <label for="password" class="form-label">
                                New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <font-awesome-icon icon="lock" class="h-5 w-5 text-gray-400" />
                                </div>
                                <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                    required class="form-input pl-10 pr-12"
                                    :class="{ 'form-input-error': errors.password }" placeholder="Enter new password"
                                    @input="clearError('password')">
                                <button type="button" @click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-700 transition-colors">
                                    <font-awesome-icon :icon="showPassword ? 'eye-slash' : 'eye'"
                                        class="h-5 w-5 text-gray-400" />
                                </button>
                            </div>
                            <p v-if="errors.password" class="form-error">
                                {{ errors.password }}
                            </p>
                            <div class="form-help">
                                Password must be at least 8 characters long
                            </div>
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <label for="password_confirmation" class="form-label">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <font-awesome-icon icon="lock" class="h-5 w-5 text-gray-400" />
                                </div>
                                <input id="password_confirmation" v-model="form.password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'" required
                                    class="form-input pl-10 pr-12"
                                    :class="{ 'form-input-error': errors.password_confirmation }"
                                    placeholder="Confirm new password" @input="clearError('password_confirmation')">
                                <button type="button" @click="toggleConfirmPasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-700 transition-colors">
                                    <font-awesome-icon :icon="showConfirmPassword ? 'eye-slash' : 'eye'"
                                        class="h-5 w-5 text-gray-400" />
                                </button>
                            </div>
                            <p v-if="errors.password_confirmation" class="form-error">
                                {{ errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div v-if="form.password" class="space-y-2">
                            <div class="text-sm font-medium text-gray-700">Password Strength:</div>
                            <div class="flex space-x-1">
                                <div v-for="i in 4" :key="i" class="h-2 flex-1 rounded-full"
                                    :class="getStrengthBarClass(i)"></div>
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ getStrengthText() }}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" :disabled="authStore.isLoading || !isFormValid"
                                class="btn-primary w-full py-3 text-base font-semibold">
                                <font-awesome-icon v-if="authStore.isLoading" icon="spinner"
                                    class="animate-spin mr-2" />
                                <font-awesome-icon v-else icon="key" class="mr-2" />
                                {{ authStore.isLoading ? 'Resetting...' : 'Reset Password' }}
                            </button>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <router-link to="/login"
                                class="text-sm font-medium text-primary-600 hover:text-primary-500 transition-colors">
                                <font-awesome-icon icon="arrow-left" class="mr-1" />
                                Back to Login
                            </router-link>
                        </div>
                    </template>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-sm text-gray-600">
                © {{ currentYear }} Fursa Energy. All rights reserved.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const toast = useToast()

// Reactive data
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const isValidToken = ref(true)
const isCheckingToken = ref(true)

const form = reactive({
    token: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const errors = reactive({
    password: '',
    password_confirmation: ''
})

// Computed
const currentYear = computed(() => new Date().getFullYear())

const isFormValid = computed(() => {
    return form.password.trim() &&
        form.password_confirmation.trim() &&
        form.password.length >= 8 &&
        form.password === form.password_confirmation
})

const passwordStrength = computed(() => {
    const password = form.password
    let strength = 0

    if (password.length >= 8) strength++
    if (/[a-z]/.test(password)) strength++
    if (/[A-Z]/.test(password)) strength++
    if (/[0-9]/.test(password)) strength++
    if (/[^A-Za-z0-9]/.test(password)) strength++

    return Math.min(strength, 4)
})

// Methods
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
}

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value
}

const clearError = (field) => {
    if (errors[field]) {
        errors[field] = ''
    }
}

const getStrengthBarClass = (index) => {
    const strength = passwordStrength.value
    if (index <= strength) {
        if (strength <= 1) return 'bg-danger-500'
        if (strength <= 2) return 'bg-warning-500'
        if (strength <= 3) return 'bg-primary-500'
        return 'bg-success-500'
    }
    return 'bg-gray-200'
}

const getStrengthText = () => {
    const strength = passwordStrength.value
    if (strength <= 1) return 'Weak'
    if (strength <= 2) return 'Fair'
    if (strength <= 3) return 'Good'
    return 'Strong'
}

const validateForm = () => {
    let isValid = true

    // Reset errors
    Object.keys(errors).forEach(key => {
        errors[key] = ''
    })

    // Password validation
    if (!form.password.trim()) {
        errors.password = 'Password is required'
        isValid = false
    } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters'
        isValid = false
    }

    // Password confirmation validation
    if (!form.password_confirmation.trim()) {
        errors.password_confirmation = 'Password confirmation is required'
        isValid = false
    } else if (form.password !== form.password_confirmation) {
        errors.password_confirmation = 'Passwords do not match'
        isValid = false
    }

    return isValid
}

const handleReset = async () => {
    if (!validateForm()) return

    const result = await authStore.resetPassword({
        token: form.token,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation
    })

    if (result.success) {
        toast.success('Password reset successfully! Please login with your new password.')
        router.push('/login')
    } else {
        // Handle validation errors from server
        if (result.errors) {
            Object.keys(result.errors).forEach(key => {
                if (errors.hasOwnProperty(key)) {
                    errors[key] = result.errors[key][0]
                }
            })
        }
    }
}

const checkTokenValidity = async () => {
    const token = route.query.token
    const email = route.query.email

    if (!token || !email) {
        isValidToken.value = false
        isCheckingToken.value = false
        return
    }

    form.token = token
    form.email = email

    try {
        // You could add an API call here to verify the token
        // For now, we'll assume it's valid if both parameters exist
        isValidToken.value = true
    } catch (error) {
        isValidToken.value = false
        console.error('Token validation failed:', error)
    } finally {
        isCheckingToken.value = false
    }
}

// Lifecycle
onMounted(() => {
    checkTokenValidity()
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
</style>