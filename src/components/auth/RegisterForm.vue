<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    First name:
                </label>
                <input v-model="form.first_name" type="text" placeholder="John"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Last name:
                </label>
                <input v-model="form.last_name" type="text" placeholder="Doe"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Email address:
            </label>
            <div class="relative">
                <input v-model="form.email" type="email" placeholder="Johndoe@gmail.com"
                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <font-awesome-icon icon="envelope" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Phone Number:
            </label>
            <div class="relative">
                <input v-model="form.phone" type="tel" placeholder="08111111111"
                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <font-awesome-icon icon="envelope" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Password:
            </label>
            <div class="relative">
                <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                    class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <font-awesome-icon icon="lock" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <font-awesome-icon :icon="showPassword ? 'eye-slash' : 'eye'" />
                </button>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Confirm Password:
            </label>
            <div class="relative">
                <input v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                    class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <font-awesome-icon icon="lock" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <font-awesome-icon :icon="showConfirmPassword ? 'eye-slash' : 'eye'" />
                </button>
            </div>
        </div>

        <div>
            <label class="flex items-start">
                <input v-model="form.terms" type="checkbox"
                    class="rounded border-gray-300 text-primary focus:ring-primary mt-1" required>
                <span class="ml-2 text-sm text-gray-600">
                    I accept the <a href="#" class="text-primary hover:underline">Terms and Conditions</a>,
                    and I agree to the <a href="#" class="text-primary hover:underline">Terms and Privacy policy</a>.
                </span>
            </label>
        </div>

        <BaseButton
        type="submit"
        variant="primary"
        size="lg"
        fullWidth
        icon="user-plus"
        :loading="authStore.loading"
        text="Sign Up"
        loadingText="Signing Up..."
        />
    </form>
</template>

<script setup>
import { ref, reactive } from 'vue'
import BaseButton from '@/components/common/BaseButton.vue'
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()

const emit = defineEmits(['submit'])

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    terms: false
})

const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const handleSubmit = async () => {
    if (form.password !== form.password_confirmation) {
        alert('Passwords do not match')
        return
    }

    loading.value = true
    await emit('submit', form)
    loading.value = false
}
</script>