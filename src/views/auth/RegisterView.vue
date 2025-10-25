<template>
    <AuthLayout>
        <template #sidebar>
            <div class="text-center">
                <img src="/images/logo.png" alt="Fursa Energy" class="w-32 mx-auto mb-6" />
                <h1 class="text-4xl font-bold mb-4">{{ $t('auth.register.sidebar_title') }}</h1>
                <p class="text-lg opacity-90">{{ $t('auth.register.sidebar_subtitle') }}</p>
            </div>
        </template>

        <div class="w-full max-w-md mx-auto">
            <h2 class="text-3xl font-bold text-primary mb-8">{{ $t('auth.register.title') }}</h2>

            <RegisterForm @submit="handleRegister" />

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">{{ $t('auth.common.or') }}</span>
                </div>
            </div>

            <SocialLogin />

            <p class="text-center mt-8 text-gray-600">
                {{ $t('auth.register.have_account') }}
                <RouterLink to="/login" class="text-primary font-semibold hover:underline">
                    {{ $t('auth.register.login_link') }}
                </RouterLink>
            </p>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/layouts/AuthLayout.vue'
import RegisterForm from '@/components/auth/RegisterForm.vue'
import SocialLogin from '@/components/auth/SocialLogin.vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()
const { t } = useI18n()

const handleRegister = async (userData) => {
    try {
        await authStore.register(userData)
        toast.success(t('auth.register.success'))
        router.push('/login')
    } catch (error) {
        if (error.response?.status === 422) {
        const errors = error.response.data.errors;

        // Grab first error message
        const firstError = Object.values(errors)[0][0];
        toast.error(firstError);
        } else {
        toast.error(error.response?.data?.message || t('auth.register.failed_generic'));
        }
    }
}
</script>

<style>
    .loader {
        border: 2px solid #f3f3f3;
        border-top: 2px solid white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>