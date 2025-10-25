<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.business_info') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.company_name') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.company_name" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.registered_name') }}
                </label>
                <input v-model="form.registered_name" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.rc_number') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.rc_number" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.email') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.email" type="email"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.office_phone') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.office_phone" type="tel"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.company_type') }} <span class="text-red-500">*</span>
                </label>
                <select v-model="form.company_type"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                    <option value="">{{ $t('distributor.business.select_type') }}</option>
                    <option value="Private Limited">{{ $t('distributor.business.types.private_limited') }}</option>
                    <option value="Public Limited">{{ $t('distributor.business.types.public_limited') }}</option>
                    <option value="Partnership">{{ $t('distributor.business.types.partnership') }}</option>
                    <option value="Sole Proprietorship">{{ $t('distributor.business.types.sole_proprietorship') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.website') }}
                </label>
                <input v-model="form.website" type="url" :placeholder="$t('distributor.business.website_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.business.business_address') }} <span class="text-red-500">*</span>
            </label>
            <textarea v-model="form.business_address" rows="3"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                required></textarea>
        </div>
    </div>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const form = reactive({
    company_name: '',
    registered_name: '',
    rc_number: '',
    email: user.value?.email || '',
    office_phone: user.value?.phone || '',
    company_type: '',
    website: '',
    business_address: ''
})

defineExpose({ form })
</script>