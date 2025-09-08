<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="closeModal">
        <div class="bg-white rounded-xl shadow-strong max-w-md w-full animate-fade-in" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Reset Password
                </h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <font-awesome-icon icon="times" class="h-5 w-5" />
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="handleSubmit" class="p-6">
                <div class="mb-6">
                    <div class="flex justify-center mb-4">
                        <div class="bg-primary-100 rounded-full p-3">
                            <font-awesome-icon icon="key" class="h-6 w-6 text-primary-600" />
                        </div>
                    </div>
                    <p class="text-gray-600 text-center text-sm mb-6">
                        Enter your email address and we'll send you instructions to reset your password.
                    </p>

                    <!-- Email Input -->
                    <div>
                        <label for="reset-email" class="form-label">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <font-awesome-icon icon="envelope" class="h-5 w-5 text-gray-400" />
                            </div>
                            <input id="reset-email" v-model="email" type="email" required class="form-input pl-10"
                                :class="{ 'form-input-error': error }" placeholder="Enter your email address"
                                :disabled="isLoading">
                        </div>
                        <p v-if="error" class="form-error">
                            {{ error }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <button type="button" @click="closeModal" class="btn-outline flex-1" :disabled="isLoading">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!isFormValid || isLoading" class="btn-primary flex-1">
                        <font-awesome-icon v-if="isLoading" icon="spinner" class="animate-spin mr-2" />
                        <font-awesome-icon v-else icon="paper-plane" class="mr-2" />
                        {{ isLoading ? 'Sending...' : 'Send Reset Link' }}
                    </button>
                </div>
            </form>

            <!-- Success Message -->
            <div v-if="showSuccess" class="p-6 border-t border-gray-200 bg-success-50">
                <div class="flex items-center text-success-800">
                    <font-awesome-icon icon="check-circle" class="h-5 w-5 mr-2" />
                    <p class="text-sm">
                        Password reset instructions have been sent to your email address.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'

// Props & Emits
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:show', 'success'])

// Store
const authStore = useAuthStore()

// Reactive data
const email = ref('')
const error = ref('')
const isLoading = ref(false)
const showSuccess = ref(false)

// Computed
const isFormValid = computed(() => {
    return email.value.trim() &&
        email.value.includes('@') &&
        email.value.includes('.')
})

// Methods
const closeModal = () => {
    if (!isLoading.value) {
        emit('update:show', false)
    }
}

const resetForm = () => {
    email.value = ''
    error.value = ''
    showSuccess.value = false
}

const handleSubmit = async () => {
    if (!isFormValid.value || isLoading.value) return

    error.value = ''
    isLoading.value = true

    try {
        const result = await authStore.forgotPassword(email.value.trim())

        if (result.success) {
            showSuccess.value = true
            setTimeout(() => {
                emit('success')
                closeModal()
            }, 2000)
        } else {
            error.value = result.error
        }
    } finally {
        isLoading.value = false
    }
}

// Watchers
watch(() => props.show, (newValue) => {
    if (newValue) {
        resetForm()
    }
})

// Handle escape key
const handleKeydown = (event) => {
    if (event.key === 'Escape' && props.show && !isLoading.value) {
        closeModal()
    }
}

// Add event listener
if (typeof window !== 'undefined') {
    document.addEventListener('keydown', handleKeydown)
}
</script>