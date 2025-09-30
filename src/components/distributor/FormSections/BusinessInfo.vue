<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-primary mb-4">Section 1: Business Information</h3>
            <div>
                <input type="hidden" v-name="firstName" v-model="form.firstName"></input>
                <input type="hidden" v-name="lastName" v-model="form.lastName"></input>
                <input type="hidden" v-name="email" v-model="form.email"></input>
                <input type="hidden" v-name="phone" v-model="form.phone"></input>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Company Name <span class="text-red-500">*</span>
                </label>
                <input v-model="form.companyName" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Registered Business Name (if different)
                </label>
                <input v-model="form.registeredName" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    RC Number <span class="text-red-500">*</span>
                </label>
                <input v-model="form.rcNumber" type="text"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input v-model="form.email" type="email"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Office Phone Number <span class="text-red-500">*</span>
                </label>
                <input v-model="form.phone" type="tel"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Company Type <span class="text-red-500">*</span>
                </label>
                <select v-model="form.companyType"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                    <option value="">Select Type</option>
                    <option value="limited">Limited Liability Company</option>
                    <option value="plc">Public Limited Company</option>
                    <option value="partnership">Partnership</option>
                    <option value="sole">Sole Proprietorship</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Website (if any)
                </label>
                <input v-model="form.website" type="url" placeholder="https://example.com"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Business Address <span class="text-red-500">*</span>
            </label>
            <textarea v-model="form.address" rows="3"
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
    firstName: user.value.first_name,
    lastName: user.value.last_name,
    email: user.value.email,
    phone: user.value.phone,
    companyName: '',
    registeredName: '',
    rcNumber: '',
    email: '',
    phone: '',
    companyType: '',
    website: '',
    address: ''
})

defineExpose({ form })
</script>