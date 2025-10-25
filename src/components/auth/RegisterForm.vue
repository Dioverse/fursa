<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('auth.register.first_name_label') }}
                </label>
                <input v-model="form.first_name" type="text" :placeholder="$t('auth.register.first_name_placeholder')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('auth.register.last_name_label') }}
                </label>
                <input v-model="form.last_name" type="text" :placeholder="$t('auth.register.last_name_placeholder')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('auth.register.email_label') }}
            </label>
            <div class="relative">
                <input v-model="form.email" type="email" :placeholder="$t('auth.register.email_placeholder')"
                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <font-awesome-icon icon="envelope" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('auth.register.phone_label') }}
            </label>
            <div class="relative">
                <input v-model="form.phone" type="tel" :placeholder="$t('auth.register.phone_placeholder')"
                    inputmode="numeric"
                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    maxlength="11"
                    pattern="[0-9]*"
                    required
                    @input="form.phone = form.phone.replace(/[^0-9]/g, '')">
                <font-awesome-icon icon="envelope" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('auth.register.password_label') }}
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
                {{ $t('auth.register.confirm_password_label') }}
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
                <span class="ml-2 text-sm text-gray-600" v-html="$t('auth.register.terms_html')"></span>
            </label>
        </div>

    <BaseButton
        type="submit"
        variant="primary"
        size="lg"
        fullWidth
        icon="user-plus"
        :loading="authStore.loading"
    :text="$t('auth.register.submit')"
    :loadingText="$t('auth.register.loading')"
        />
    </form>
</template>

<script setup>
import { ref, reactive } from 'vue'
import BaseButton from '@/components/common/BaseButton.vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
const authStore = useAuthStore()
const { t } = useI18n()

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
        alert(t('auth.register.passwords_no_match'))
        return
    }

    loading.value = true
    await emit('submit', form)
    loading.value = false
}
</script>