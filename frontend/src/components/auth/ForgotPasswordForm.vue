<template>
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
            icon="email-plus"
            :loading="authStore.loading"
            text="Forgot Password"
            loadingText="Loading ..."
        />
    </form>
</template>

<script setup>
import { reactive } from 'vue'
import BaseButton from '@/components/common/BaseButton.vue'
import { useAuthStore } from '@/stores/auth'

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

const handleSubmit = () => {
    if (!validateForm()) return
    emit('submit', form)
}
</script>
