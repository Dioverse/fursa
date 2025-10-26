<!-- ContactPerson with Store -->
<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.contact_person') }}</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.full_name') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.contactPerson.contact_full_name" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.position_title') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.contactPerson.contact_position" type="text" :placeholder="$t('distributor.contact.position_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.mobile') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.contactPerson.contact_mobile" type="tel" :placeholder="$t('distributor.contact.mobile_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.means_of_id') }} <span class="text-red-500">*</span>
                </label>
                <select v-model="distributorStore.formData.contactPerson.means_of_id"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                    <option value="">{{ $t('distributor.contact.select_id_type') }}</option>
                    <option value="National ID (NIN)">{{ $t('distributor.contact.id_types.nin') }}</option>
                    <option value="Driver's License">{{ $t('distributor.contact.id_types.drivers_license') }}</option>
                    <option value="International Passport">{{ $t('distributor.contact.id_types.passport') }}</option>
                    <option value="Voter's Card">{{ $t('distributor.contact.id_types.voters_card') }}</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.id_number') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.contactPerson.id_number" type="text" :placeholder="$t('distributor.contact.id_number_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.years_in_business') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="distributorStore.formData.contactPerson.years_in_business" type="number" placeholder="1" min="0"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <!-- ID Document Upload -->
        <div class="border-t pt-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                <font-awesome-icon icon="id-card" class="text-primary mr-2" />
                ID Document Upload <span class="text-red-500">*</span>
            </h4>
            <p class="text-sm text-gray-600 mb-4">Upload a clear scan or photo of the ID selected above (NIN, Driver's License, Passport, or Voter's Card).</p>
            
            <div class="border-2 border-dashed rounded-lg p-6"
                :class="{ 'border-red-300 bg-red-50': idUploadError }">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-medium">{{ distributorStore.formData.contactPerson.means_of_id || 'Select ID Type First' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                    <label class="cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" 
                            @change="handleIdUpload"
                            class="hidden"
                            :disabled="!distributorStore.formData.contactPerson.means_of_id">
                        <div :class="{ 'opacity-50 cursor-not-allowed': !distributorStore.formData.contactPerson.means_of_id }"
                            class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded hover:bg-gray-200 transition whitespace-nowrap">
                            <font-awesome-icon icon="upload" />
                            <span>Upload ID</span>
                        </div>
                    </label>
                </div>

                <div v-if="distributorStore.formData.contactPerson.id_of_contact" class="text-sm text-green-600 mt-4">
                    <font-awesome-icon icon="check-circle" />
                    {{ distributorStore.formData.contactPerson.id_of_contact.name }}
                </div>
                <div v-if="idUploadError" class="text-sm text-red-600 mt-4">
                    <font-awesome-icon icon="exclamation-circle" />
                    {{ idUploadError }}
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
const idUploadError = ref('')

const MAX_FILE_SIZE = 5 * 1024 * 1024 // 5MB

const handleIdUpload = (event) => {
    const file = event.target.files[0]
    idUploadError.value = ''

    if (!file) {
        distributorStore.formData.contactPerson.id_of_contact = null
        return
    }

    // Validate file size
    if (file.size > MAX_FILE_SIZE) {
        idUploadError.value = `File size must be less than 5MB. Your file is ${(file.size / 1024 / 1024).toFixed(2)}MB`
        distributorStore.formData.contactPerson.id_of_contact = null
        return
    }

    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowedTypes.includes(file.type)) {
        idUploadError.value = 'Only PDF, JPG, and PNG files are allowed'
        distributorStore.formData.contactPerson.id_of_contact = null
        return
    }

    distributorStore.formData.contactPerson.id_of_contact = file
    toast.info('ID document uploaded successfully')
}

defineExpose({ form: distributorStore.formData.contactPerson })
</script>