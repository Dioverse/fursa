<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Email address:
            </label>
            <div class="relative">
                <input v-model="form.user" type="email" placeholder="Johndoe@gmail.com"
                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    :class="{ 'border-red-500': errors.user }" required>
                <font-awesome-icon icon="envelope" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            </div>
            <span v-if="errors.user" class="text-red-500 text-sm mt-1">{{ errors.user }}</span>
        </div>

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

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input v-model="form.remember" type="checkbox"
                    class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            <RouterLink to="/forgot-password" class="text-sm text-primary hover:underline">
                Forgot password?
            </RouterLink>
        </div>

        <!-- <BaseButton type="submit" variant="primary" size="lg" fullWidth :loading="loading" icon="sign-in-alt">
            Sign In
        </BaseButton> -->
        <BaseButton
        type="submit"
        variant="primary"
        size="lg"
        fullWidth
        icon="user-plus"
        :loading="authStore.loading"
        text="Sign In"
        loadingText="Signing In..."
        />
    </form>
</template>

<script setup>
import { ref, reactive } from 'vue'
import BaseButton from '@/components/common/BaseButton.vue'

const emit = defineEmits(['submit'])

const form = reactive({
    user: '',
    password: '',
    remember: false
})

const errors = reactive({
    user: '',
    password: ''
})

const loading = ref(false)
const showPassword = ref(false)

const validateForm = () => {
    errors.user = ''
    errors.password = ''

    if (!form.user) {
        errors.user = 'User is required'
    } else if (!/\S+@\S+\.\S+/.test(form.user)) {
        errors.user = 'User is invalid'
    }

    if (!form.password) {
        errors.password = 'Password is required'
    } else if (form.password.length < 6) {
        errors.password = 'Password must be at least 6 characters'
    }

    return !errors.user && !errors.password
}

const handleSubmit = async () => {
    if (!validateForm()) return

    loading.value = true
    await emit('submit', form)
    loading.value = false
}
</script>