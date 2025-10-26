<!-- BusinessInfo with Store -->
<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.business_info') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.company_name') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.businessInfo.company_name" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.registered_name') }}
                </label>
                <input v-model="distributorStore.formData.businessInfo.registered_name" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.rc_number') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.businessInfo.rc_number" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.email') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.businessInfo.email" type="email"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.office_phone') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.businessInfo.office_phone" type="tel"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.business.company_type') }} <span class="text-red-500">*</span>
                </label>
                <select v-model="distributorStore.formData.businessInfo.company_type"
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
                <input v-model="distributorStore.formData.businessInfo.website" type="url" 
                    :placeholder="$t('distributor.business.website_placeholder')"
                    @blur="validateWebsite"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    :class="{ 'border-red-500': websiteError }">
                <p v-if="websiteError" class="text-red-500 text-sm mt-1">{{ websiteError }}</p>
                <p class="text-xs text-gray-500 mt-1">Optional - include https://</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.business.business_address') }} <span class="text-red-500">*</span>
            </label>
            <textarea v-model="distributorStore.formData.businessInfo.business_address" rows="3"
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
                :class="{ 'border-red-300 bg-red-50': uploadErrors.utility_bill }">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-medium">Utility Bill Document</p>
                        <p class="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                    <label class="cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" 
                            @change="handleUtilityBillUpload"
                            class="hidden">
                        <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition whitespace-nowrap ml-4">
                            <font-awesome-icon icon="upload" />
                            <span>Upload Bill</span>
                        </div>
                    </label>
                </div>

                <div v-if="distributorStore.formData.businessInfo.documents.utility_bill" class="text-sm text-green-600 mt-4">
                    <font-awesome-icon icon="check-circle" />
                    {{ distributorStore.formData.businessInfo.documents.utility_bill.name }}
                </div>
                <div v-if="uploadErrors.utility_bill" class="text-sm text-red-600 mt-4">
                    <font-awesome-icon icon="exclamation-circle" />
                    {{ uploadErrors.utility_bill }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useDistributorFormStore } from '@/stores/distributorForm'
import { useToast } from 'vue-toastification'

const distributorStore = useDistributorFormStore()
const toast = useToast()
const websiteError = ref('')
const uploadErrors = ref({
    utility_bill: ''
})

const MAX_FILE_SIZE = 5 * 1024 * 1024 // 5MB

const validateWebsite = () => {
    const website = distributorStore.formData.businessInfo.website
    if (!website) {
        websiteError.value = ''
        return
    }
    
    try {
        new URL(website.startsWith('http') ? website : `https://${website}`)
        websiteError.value = ''
    } catch (e) {
        websiteError.value = 'Please enter a valid URL (e.g., https://example.com)'
    }
}

const handleUtilityBillUpload = (event) => {
    const file = event.target.files[0]
    uploadErrors.value.utility_bill = ''

    if (!file) {
        delete distributorStore.formData.businessInfo.documents.utility_bill
        return
    }

    // Validate file size
    if (file.size > MAX_FILE_SIZE) {
        uploadErrors.value.utility_bill = `File size must be less than 5MB. Your file is ${(file.size / 1024 / 1024).toFixed(2)}MB`
        return
    }

    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowedTypes.includes(file.type)) {
        uploadErrors.value.utility_bill = 'Only PDF, JPG, and PNG files are allowed'
        return
    }

    distributorStore.formData.businessInfo.documents.utility_bill = file
    toast.info('Utility bill uploaded successfully')
}

defineExpose({ form: distributorStore.formData.businessInfo })
</script>