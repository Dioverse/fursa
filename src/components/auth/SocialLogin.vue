<template>
    <div class="space-y-3">
        <button @click="handleGoogleLogin"
            class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <font-awesome-icon :icon="['fab', 'google']" class="text-red-500" />
            <span>Continue with Google</span>
        </button>

        <button @click="handleAppleLogin"
            class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <font-awesome-icon :icon="['fab', 'apple']" class="text-black" />
            <span>Continue with Apple</span>
        </button>
    </div>
</template>

<script setup>
import { useToast } from 'vue-toastification'

const toast = useToast()

const handleGoogleLogin = () => {
    toast.info('Google login coming soon!')
}

const handleAppleLogin = () => {
    toast.info('Apple login coming soon!')
}
</script>