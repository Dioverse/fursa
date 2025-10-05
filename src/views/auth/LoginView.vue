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
            <h2 class="text-3xl font-bold text-primary mb-8">Sign in to Fursa</h2>

            <LoginForm @submit="handleLogin" />

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or</span>
                </div>
            </div>

            <SocialLogin />

            <p class="text-center mt-8 text-gray-600">
                Don't have an account?
                <RouterLink to="/register" class="text-primary font-semibold hover:underline">
                    Register
                </RouterLink>
            </p>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import LoginForm from '@/components/auth/LoginForm.vue'
import SocialLogin from '@/components/auth/SocialLogin.vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const handleLogin = async (credentials) => {
    try {
        await authStore.login(credentials)
        toast.success('Login successful!')

        // Check if there's a redirect URL
        const redirectTo = router.currentRoute.value.query.redirect || '/dashboard'
        router.push(redirectTo)
    } catch (error) {
        toast.error(error.response?.data?.errors?.user[0] || 'Invalid login credentials. Please try again.')
    }
}

</script>