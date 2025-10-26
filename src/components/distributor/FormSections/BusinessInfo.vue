<!-- BusinessInfo -->
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
                <input v-model="form.website" type="url" 
                    :placeholder="$t('distributor.business.website_placeholder')"
                    @blur="validateWebsite"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    :class="{ 'border-red-500': websiteError }">
                <p v-if="websiteError" class="text-red-500 text-sm mt-1">{{ websiteError }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $t('distributor.business.website_format_hint') }}</p>
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

        <!-- Utility Bill Upload -->
        <div class="border-t pt-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                <font-awesome-icon icon="file-pdf" class="text-primary mr-2" />
                Utility Bill <span class="text-red-500">*</span>
            </h4>
            <p class="text-sm text-gray-600 mb-4">Upload a recent utility bill (water, electricity, or gas) as proof of business address.</p>
            
            <div class="border-2 border-dashed rounded-lg p-6"
                :class="{ 'border-red-300 bg-red-50': utilityBillError }">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-medium">Utility Bill Document</p>
                        <p class="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                    <label class="cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" 
                            @change="handleUtilityBillUpload"
                            class="hidden">
                        <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition whitespace-nowrap">
                            <font-awesome-icon icon="upload" />
                            <span>Upload Bill</span>
                        </div>
                    </label>
                </div>

                <div v-if="form.utility_bill" class="text-sm text-green-600 mt-4">
                    <font-awesome-icon icon="check-circle" />
                    {{ form.utility_bill.name }}
                </div>
                <div v-if="utilityBillError" class="text-sm text-red-600 mt-4">
                    <font-awesome-icon icon="exclamation-circle" />
                    {{ utilityBillError }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
const websiteError = ref('')

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

const validateWebsite = () => {
    if (!form.website) {
        websiteError.value = ''
        return
    }
    
    try {
        // Allow empty or valid URLs
        new URL(form.website.startsWith('http') ? form.website : `https://${form.website}`)
        websiteError.value = ''
    } catch (e) {
        websiteError.value = 'Please enter a valid URL (e.g., https://example.com)'
    }
}

defineExpose({ form })
</script>