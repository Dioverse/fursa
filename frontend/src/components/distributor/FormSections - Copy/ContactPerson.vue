<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-primary mb-4">Section 2: Contact Person</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                
                <input v-model="form.fullName" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Position/Title <span class="text-red-500">*</span>
                </label>
                <input v-model="form.position" type="text" placeholder="e.g., Sales Manager"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input v-model="form.mobile" type="tel" placeholder="+234 XXX XXX XXXX"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Means of ID <span class="text-red-500">*</span>
                </label>
                <select v-model="form.idType"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                    <option value="">Select ID Type</option>
                    <option value="nin">National ID (NIN)</option>
                    <option value="drivers">Driver's License</option>
                    <option value="passport">International Passport</option>
                    <option value="voters">Voter's Card</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ID Number <span class="text-red-500">*</span>
                </label>
                <input v-model="form.idNumber" type="text" placeholder="Enter your ID number"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Year(s) in Business <span class="text-red-500">*</span>
                </label>
                <input v-model="form.businessYear" type="number" placeholder="1"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

    </div>
</template>

<script setup>
import { reactive } from 'vue'


const form = reactive({
    fullName: '',
    position: '',
    mobile: '',
    idType: '',
    idNumber: '',
    businessYear: ''
})

defineExpose({ form })
</script>