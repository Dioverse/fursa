<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-primary mb-4">Section 3: Distribution Capacity</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
           
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Prefered Region
                </label>
                <input v-model="form.preferedRegion" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Current Product Lines Distributed (if any)
                </label>
                <input v-model="form.currentProducts" type="text" placeholder="List current products"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Monthly Distribution Capacity (litres or cartons)
                </label>
                <input v-model="form.monthlyCapacity" type="text" placeholder="e.g., 10,000 litres"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Regions Currently Covered
                </label>
                <input v-model="form.regions" type="text" placeholder="e.g., Lagos, Abuja, Port Harcourt"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Number of Sales Staff
                </label>
                <input v-model="form.salesStaff" type="number" min="0"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Vehicle Details
                </label>
                <input v-model="form.website" type="url" placeholder="https://example.com"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Existing Warehouse or Storage Facility? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.hasWarehouse" type="radio" value="yes" class="text-primary focus:ring-primary">
                    <span>Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.hasWarehouse" type="radio" value="no" class="text-primary focus:ring-primary">
                    <span>No</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Do you have distribution vehicles?
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.hasVehicles" type="radio" value="yes" class="text-primary focus:ring-primary">
                    <span>Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.hasVehicles" type="radio" value="no" class="text-primary focus:ring-primary">
                    <span>No</span>
                </label>
            </div>
        </div>

        <div v-if="form.hasVehicles === 'yes'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                If yes, please state number and type:
            </label>
            <textarea v-model="form.vehicleDetails" rows="3" placeholder="e.g., 2 vans, 1 truck"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'


const form = reactive({
    preferedRegion: '',
    currentProducts: '',
    monthlyCapacity: '',
    regions: '',
    salesStaff: '',
    website: '',
    hasWarehouse: '',
    hasVehicles: '',
    vehicleDetails: ''
})

defineExpose({ form })
</script>