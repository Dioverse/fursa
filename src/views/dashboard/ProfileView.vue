<template>
  <DashboardLayout>
    <div class="space-y-6">
      <h1 class="lg:text-2xl md:text-xl text-lg font-bold">{{ $t('profile.title') }}</h1>

      <!-- READ-ONLY USER INFO -->
      <div class="bg-white rounded-lg shadow-md p-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3">
          <div v-for="(value, label) in userFields" :key="label">
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1 capitalize">
              {{ label }}
            </label>
            <input
              :value="value"
              type="text"
              disabled
              class="w-full bg-gray-50 px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg cursor-not-allowed"
            />
          </div>
        </div>
      </div>

      <!-- PASSWORD UPDATE FORM -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('profile.password.title') }}</h3>

        <form @submit.prevent="submitPassword" class="space-y-4">
          <div>
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
              {{ $t('profile.password.current') }}
            </label>
            <input
              v-model="passwordForm.current"
              type="password"
              placeholder="Enter current password"
              class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
              {{ $t('profile.password.new') }}
            </label>
            <input
              v-model="passwordForm.new"
              type="password"
              placeholder="Enter new password"
              class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-2">
              {{ $t('profile.password.confirm') }}
            </label>
            <input
              v-model="passwordForm.confirm"
              type="password"
              placeholder="Confirm new password"
              class="w-full px-3 py-2 lg:px-4 lg:py-3 border text-xs lg:text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>

          <div class="flex justify-end gap-4 pt-4 border-t mt-4">
            <button
              type="button"
              @click="resetPasswordForm"
              class="text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
              {{ $t('profile.buttons.cancel') }}
            </button>
            <button
              type="submit"
              class="text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition"
            >
              <font-awesome-icon icon="key" class="mr-2" />
              {{ $t('profile.buttons.save_changes') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { useToast } from 'vue-toastification'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import api from '@/services/api' // Axios instance

const toast = useToast()
const authStore = useAuthStore()
const { t } = useI18n()
const user = computed(() => authStore.user)

const loading = ref(false)
const error = ref(null)

const userFields = computed(() => ({
  'First Name': user.value.first_name,
  'Last Name': user.value.last_name,
  'Email': user.value.email,
  'Phone': user.value.phone
}))

const passwordForm = reactive({
  current: '',
  new: '',
  confirm: ''
})

// Update Password Only
const submitPassword = async () => {
  if (!passwordForm.current || !passwordForm.new || !passwordForm.confirm) {
    toast.error(t('profile.toasts.fill_all_password_fields'))
    return
  }

  if (passwordForm.new !== passwordForm.confirm) {
    toast.error(t('profile.toasts.passwords_no_match'))
    return
  }

  loading.value = true
  try {
    await api.post('/change-password', {
      current_password: passwordForm.current,
      new_password: passwordForm.new,
      new_password_confirmation: passwordForm.confirm
    })

    toast.success(t('profile.toasts.password_updated'))
    resetPasswordForm()
  } catch (err) {
    error.value = err.response?.data?.message || 'Password update failed'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const resetPasswordForm = () => {
  passwordForm.current = ''
  passwordForm.new = ''
  passwordForm.confirm = ''
}
</script>
