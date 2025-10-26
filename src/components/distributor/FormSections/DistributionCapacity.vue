<!-- DistributionCapacity with Store -->
<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.distribution_capacity') }}</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.distribution.current_product_lines') }}
                </label>
                <input v-model="distributorStore.formData.distributionCapacity.current_product_lines" type="text" :placeholder="$t('distributor.distribution.current_product_lines_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.distribution.monthly_capacity') }}
                </label>
                <input v-model="distributorStore.formData.distributionCapacity.monthly_capacity" type="text" :placeholder="$t('distributor.distribution.monthly_capacity_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.distribution.regions_covered') }}
                </label>
                <input v-model="distributorStore.formData.distributionCapacity.regions_covered" type="text" :placeholder="$t('distributor.distribution.regions_covered_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.distribution.sales_staff') }}
                </label>
                <input v-model="distributorStore.formData.distributionCapacity.number_of_sales_staff" type="number" min="0"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.distribution.preferred_region') }}
            </label>
            <input v-model="distributorStore.formData.distributionCapacity.preferred_region" type="text" :placeholder="$t('distributor.distribution.preferred_region_placeholder')"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.distribution.has_warehouse') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.distributionCapacity.has_warehouse" type="radio" :value="1" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.distributionCapacity.has_warehouse" type="radio" :value="0" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                {{ $t('distributor.distribution.has_vehicles') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.distributionCapacity.has_vehicles" type="radio" :value="1" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.yes') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="distributorStore.formData.distributionCapacity.has_vehicles" type="radio" :value="0" class="text-primary focus:ring-primary">
                    <span>{{ $t('distributor.common.no') }}</span>
                </label>
            </div>
        </div>

        <div v-if="distributorStore.formData.distributionCapacity.has_vehicles === 1">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.distribution.vehicle_details_label') }}
            </label>
            <textarea v-model="distributorStore.formData.distributionCapacity.vehicle_details" rows="3" :placeholder="$t('distributor.distribution.vehicle_details_placeholder')"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('distributor.distribution.preferred_states_label') }} <span class="text-red-500">*</span>
            </label>
            <p class="text-sm text-gray-500 mb-3">{{ $t('distributor.distribution.preferred_states_hint') }}</p>
            <div class="border rounded-lg p-4 max-h-64 overflow-y-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    <label v-for="state in nigerianStates" :key="state"
                        class="flex items-center gap-2 cursor-pointer p-2 hover:bg-gray-50 rounded">
                        <input v-model="distributorStore.formData.distributionCapacity.preferred_states" type="checkbox" :value="state"
                            class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm">{{ state }}</span>
                    </label>
                </div>
            </div>
            <p v-if="distributorStore.formData.distributionCapacity.preferred_states.length > 0" class="text-sm text-green-600 mt-2">
                <font-awesome-icon icon="check-circle" />
                {{ $t('distributor.distribution.states_selected', { count: distributorStore.formData.distributionCapacity.preferred_states.length }) }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { useDistributorFormStore } from '@/stores/distributorForm'

const distributorStore = useDistributorFormStore()

const nigerianStates = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa',
    'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo',
    'Ekiti', 'Enugu', 'Gombe', 'Imo', 'Jigawa', 'Kaduna',
    'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
    'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo',
    'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara',
    'Abuja'
]

defineExpose({ form: distributorStore.formData.distributionCapacity })
</script>