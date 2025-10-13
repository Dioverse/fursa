<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-primary mb-4">Section 3: Distribution Capacity</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Current Product Lines Distributed (if any)
                </label>
                <input v-model="form.current_product_lines" type="text" placeholder="e.g., Electronics, Home Appliances"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Monthly Distribution Capacity
                </label>
                <input v-model="form.monthly_capacity" type="text" placeholder="e.g., 1000 units"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Regions Currently Covered
                </label>
                <input v-model="form.regions_covered" type="text" placeholder="e.g., Lagos, Ogun, Oyo"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Number of Sales Staff
                </label>
                <input v-model="form.number_of_sales_staff" type="number" min="0"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Preferred Region
            </label>
            <input v-model="form.preferred_region" type="text" placeholder="e.g., Lagos"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Existing Warehouse or Storage Facility? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.has_warehouse" type="radio" :value="1" class="text-primary focus:ring-primary">
                    <span>Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.has_warehouse" type="radio" :value="0" class="text-primary focus:ring-primary">
                    <span>No</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Do you have distribution vehicles? <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.has_vehicles" type="radio" :value="1" class="text-primary focus:ring-primary">
                    <span>Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input v-model="form.has_vehicles" type="radio" :value="0" class="text-primary focus:ring-primary">
                    <span>No</span>
                </label>
            </div>
        </div>

        <div v-if="form.has_vehicles === 1">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                If yes, please state number and type:
            </label>
            <textarea v-model="form.vehicle_details" rows="3" placeholder="e.g., 2 Toyota Hilux, 1 Mercedes-Benz Sprinter"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Preferred States You Would Like to Cover: <span class="text-red-500">*</span>
            </label>
            <p class="text-sm text-gray-500 mb-3">Select all states where you intend to distribute products</p>
            <div class="border rounded-lg p-4 max-h-64 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <label v-for="state in nigerianStates" :key="state"
                        class="flex items-center gap-2 cursor-pointer p-2 hover:bg-gray-50 rounded">
                        <input v-model="form.preferred_states" type="checkbox" :value="state"
                            class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm">{{ state }}</span>
                    </label>
                </div>
            </div>
            <p v-if="form.preferred_states.length > 0" class="text-sm text-green-600 mt-2">
                <font-awesome-icon icon="check-circle" />
                {{ form.preferred_states.length }} state(s) selected
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'

const nigerianStates = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa',
    'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo',
    'Ekiti', 'Enugu', 'Gombe', 'Imo', 'Jigawa', 'Kaduna',
    'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
    'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo',
    'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara',
    'Abuja'
]

const form = reactive({
    current_product_lines: '',
    monthly_capacity: '',
    regions_covered: '',
    number_of_sales_staff: '',
    preferred_region: '',
    has_warehouse: null,
    has_vehicles: null,
    vehicle_details: '',
    preferred_states: []
})

defineExpose({ form })
</script>