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
            <h2 class="text-3xl font-bold text-primary mb-8">Forgot Password</h2>

            <form action="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password:
                    </label>
                    <div class="relative">
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            :class="{ 'border-red-500': errors.password }" required>
                        <font-awesome-icon icon="lock" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <font-awesome-icon :icon="showPassword ? 'eye-slash' : 'eye'" />
                        </button>
                    </div>
                    <span v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password:
                    </label>
                    <div class="relative">
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            :class="{ 'border-red-500': errors.password }" required>
                        <font-awesome-icon icon="lock" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <font-awesome-icon :icon="showPassword ? 'eye-slash' : 'eye'" />
                        </button>
                    </div>
                    <span v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</span>
                </div>       
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or</span>
                </div>
            </div>

            <p class="text-center mt-8 text-gray-600">
                Go back to
                <RouterLink to="/login" class="text-primary font-semibold hover:underline">
                    Login
                </RouterLink>
            </p>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import ForgotPasswordForm from '@/components/auth/ForgotPasswordForm.vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const handleSubmit = async (credentials) => {
    try {
        await authStore.forgotPassword(credentials)
        toast.success('An email has been sent with password reset instructions!')
        const redirectTo = router.currentRoute.value.query.redirect || '/login'
        router.push(redirectTo)
    } catch (error) {
        toast.error(error.response?.data?.errors?.user[0] || 'An error occured. Please try again.')
    }
}

</script>