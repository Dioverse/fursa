<template>
    <AuthLayout>
        <template #sidebar>
            <div class="text-center">
                <!-- <img src="/images/logo.png" alt="Fursa Energy" class="w-32 mx-auto mb-6" /> -->
                <!-- <h1 class="text-4xl font-bold mb-4">Welcome Back!</h1>
                <p class="text-lg opacity-90">Welcome back to fursa. Log in to browse our exciting offers</p> -->
            </div>
        </template>

        <div class="w-full max-w-md mx-auto">
            <h2 class="text-3xl font-bold text-primary mb-8">Verify Your Account</h2>

            <form @submit="handleSubmit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Enter OTP
                    </label>
                    <div class="relative">
                        <input v-model="form.otp" type="number" placeholder="Enter OTP"
                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            :class="{ 'border-red-500': errors.otp }" required>
                        <font-awesome-icon icon="envelope" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    </div>
                    <span v-if="errors.otp" class="text-red-500 text-sm mt-1">{{ errors.otp }}</span>
                </div>

                <BaseButton type="submit" variant="primary" size="lg" fullWidth :loading="loading" icon="sign-in-alt">
                    Verify Email
                </BaseButton>
            </form>

        </div>
    </AuthLayout>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'
import BaseButton from '@/components/common/BaseButton.vue'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const handleSubmit = async (credentials) => {
    try {
        await authStore.login(credentials)
        toast.success('Login successful!')

        // Check if there's a redirect URL
        const redirectTo = router.currentRoute.value.query.redirect || '/dashboard'
        router.push(redirectTo)
    } catch (error) {
        // console.error('Login error:', error.response?.data?.errors?.user[0] || error.message)
        toast.error(error.response?.data?.errors?.user[0] || 'Invalid login credentials. Please try again.')
    }
}
</script>