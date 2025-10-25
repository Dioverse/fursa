<template>
    <DashboardLayout>
        <div class="space-y-6">
            <h1 class="lg:text-2xl md:text-xl text-lg font-bold">{{ $t('profile.title') }}</h1>

            <div class="bg-white rounded-lg shadow-md p-6">
                <form @submit.prevent="updateProfile" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                {{ $t('profile.form.first_name') }}
                            </label>
                            <input v-model="form.firstName" type="text"
                                class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                {{ $t('profile.form.last_name') }}
                            </label>
                            <input v-model="form.lastName" type="text"
                                class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                {{ $t('profile.form.email') }}
                            </label>
                            <input v-model="form.email" type="email"
                                class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                {{ $t('profile.form.phone') }}
                            </label>
                            <input v-model="form.phone" type="tel"
                                class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">{{ $t('profile.password.title') }}</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                    {{ $t('profile.password.current') }}
                                </label>
                                <input v-model="passwordForm.current" type="password"
                                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                    {{ $t('profile.password.new') }}
                                </label>
                                <input v-model="passwordForm.new" type="password"
                                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
                                    {{ $t('profile.password.confirm') }}
                                </label>
                                <input v-model="passwordForm.confirm" type="password"
                                    class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button type="button" @click="resetForm"
                            class=" text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            {{ $t('profile.buttons.cancel') }}
                        </button>
                        <button type="submit"
                            class=" text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">
                            <font-awesome-icon icon="save" class="mr-2" />
                            {{ $t('profile.buttons.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useToast } from 'vue-toastification'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'


const toast = useToast()
const authStore = useAuthStore()
const user = computed(() => authStore.user)
const { t } = useI18n()

const form = reactive({
    firstName: user.value.first_name,
    lastName: user.value.last_name,
    email: user.value.email,
    phone: user.value.phone
})

const passwordForm = reactive({
    current: '',
    new: '',
    confirm: ''
})

const updateProfile = () => {
    // Validate password if changing
    if (passwordForm.new) {
        if (passwordForm.new !== passwordForm.confirm) {
            toast.error(t('profile.toasts.passwords_no_match'))
            return
        }
    }

    toast.success(t('profile.toasts.updated_success'))
}

const resetForm = () => {
    // Reset to current user values
    form.firstName = user.value.first_name
    form.lastName = user.value.last_name
    form.email = user.value.email
    form.phone = user.value.phone

    passwordForm.current = ''
    passwordForm.new = ''
    passwordForm.confirm = ''
}
</script>