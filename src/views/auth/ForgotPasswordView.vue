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

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email address:
                    </label>
                    <div class="relative">
                        <input 
                            v-model="form.email" 
                            type="email" 
                            placeholder="Johndoe@gmail.com"
                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            :class="{ 'border-red-500': errors.email }"
                            required
                        >
                        <font-awesome-icon 
                            icon="envelope" 
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" 
                        />
                    </div>
                    <span v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</span>
                </div>

                <BaseButton
                    type="submit"
                    variant="primary"
                    size="lg"
                    fullWidth
                    :loading="authStore.loading"
                    text="Forgot Password"
                    loadingText="Loading ..."
                />
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
import { reactive } from 'vue'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import ForgotPasswordForm from '@/components/auth/ForgotPasswordForm.vue'
import { useAuthStore } from '@/stores/auth'
import BaseButton from '@/components/common/BaseButton.vue'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const emit = defineEmits(['submit'])

const form = reactive({
    email: ''
})

const errors = reactive({
    email: '',
})

const validateForm = () => {
    errors.email = ''

    if (!form.email) {
        errors.email = 'Email is required'
    } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Email is invalid'
    }

    return !errors.email
}


const handleSubmit = async (e) => {
  if (!validateForm()) return
  try {
    await authStore.forgotPassword(form.email)
    toast.success('If an account with that email exists, a password reset link is on its way!')
    router.push('/reset-password')
  } catch (error) {
    const msg = error.response?.data?.message || 'An error occurred. Please try again.'
    toast.error(msg)
  }
}


</script>