<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.contact_person') }}</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.full_name') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.contact_full_name" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.position_title') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.contact_position" type="text" :placeholder="$t('distributor.contact.position_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.mobile') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.contact_mobile" type="tel" :placeholder="$t('distributor.contact.mobile_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.means_of_id') }} <span class="text-red-500">*</span>
                </label>
                <select v-model="form.means_of_id"
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
                <input v-model="form.id_number" type="text" :placeholder="$t('distributor.contact.id_number_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.contact.years_in_business') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.years_in_business" type="number" placeholder="1" min="0"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'

const form = reactive({
    contact_full_name: '',
    contact_position: '',
    contact_mobile: '',
    means_of_id: '',
    id_number: '',
    years_in_business: ''
})

defineExpose({ form })
</script>