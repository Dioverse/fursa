<template>
    <div class="space-y-3">
        <button @click="handleGoogleLogin"
            class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <font-awesome-icon :icon="['fab', 'google']" class="text-red-500" />
            <span>{{ $t('auth.social.continue_google') }}</span>
        </button>

        <!-- <button @click="handleAppleLogin"
            class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <font-awesome-icon :icon="['fab', 'apple']" class="text-black" />
            <span>{{ $t('auth.social.continue_apple') }}</span>
        </button> -->
    </div>
</template>

<script setup>
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'

const toast = useToast()
const { t } = useI18n()

const handleGoogleLogin = () => {
    toast.info(t('auth.social.coming_soon'))
}

const handleAppleLogin = () => {
    toast.info(t('auth.social.coming_soon'))
}
</script>