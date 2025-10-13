<template>
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-primary mb-4">Section 5: Banking & KYC Information</h3>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <font-awesome-icon icon="info-circle" class="text-blue-600 mt-1" />
                <div>
                    <p class="font-semibold text-blue-800">Banking Information Required</p>
                    <p class="text-blue-700 text-sm mt-1">
                        Please provide accurate banking details for payment processing and verification purposes.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bank Name <span class="text-red-500">*</span>
                </label>
                <select v-model="form.bank_name"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                    <option value="">Select Your Bank</option>
                    <option v-for="bank in nigerianBanks" :key="bank" :value="bank">{{ bank }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Account Number <span class="text-red-500">*</span>
                </label>
                <input v-model="form.account_number" type="text" maxlength="10" placeholder="0123456789"
                    @input="validateAccountNumber"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <p v-if="accountNumberError" class="text-red-500 text-sm mt-1">{{ accountNumberError }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Account Name <span class="text-red-500">*</span>
                </label>
                <input v-model="form.account_name" type="text" placeholder="As registered with bank"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    BVN of Contact Person <span class="text-red-500">*</span>
                </label>
                <input v-model="form.bvn" type="text" maxlength="11" placeholder="12345678901"
                    @input="validateBVN"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <p v-if="bvnError" class="text-red-500 text-sm mt-1">{{ bvnError }}</p>
                <p class="text-xs text-gray-500 mt-1">Your Bank Verification Number (11 digits)</p>
            </div>
        </div>

        <div class="border-t pt-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                <font-awesome-icon icon="handshake" class="text-primary mr-2" />
                Partnership Information
            </h4>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Current Partnerships with Oil & Gas Companies (if any)
                </label>
                <textarea v-model="form.partnerships" rows="3"
                    placeholder="List the Oil & Gas companies you currently partner with (e.g., Samsung, LG)"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                <p class="text-xs text-gray-500 mt-1">Leave empty if you have no current partnerships</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <font-awesome-icon icon="shield-alt" class="text-yellow-600 mt-1" />
                <div>
                    <p class="font-semibold text-yellow-800">Data Privacy Notice</p>
                    <p class="text-yellow-700 text-sm mt-1">
                        All banking and personal information provided will be kept confidential and used solely
                        for verification and payment processing purposes in accordance with applicable data
                        protection regulations.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

const nigerianBanks = [
    'Access Bank',
    'Citibank Nigeria',
    'Ecobank Nigeria',
    'Fidelity Bank',
    'First Bank',
    'First City Monument Bank (FCMB)',
    'Guaranty Trust Bank (GTBank)',
    'Heritage Bank',
    'Keystone Bank',
    'Polaris Bank',
    'Providus Bank',
    'Stanbic IBTC Bank',
    'Standard Chartered Bank',
    'Sterling Bank',
    'SunTrust Bank',
    'Union Bank of Nigeria',
    'United Bank for Africa (UBA)',
    'Unity Bank',
    'Wema Bank',
    'Zenith Bank',
    'Kuda Bank',
    'Moniepoint',
    'OPay',
    'PalmPay'
]

const accountNumberError = ref('')
const bvnError = ref('')

const form = reactive({
    bank_name: '',
    account_number: '',
    account_name: '',
    bvn: '',
    partnerships: ''
})

const validateAccountNumber = (event) => {
    const value = event.target.value
    form.account_number = value.replace(/\D/g, '')
    
    if (form.account_number && form.account_number.length !== 10) {
        accountNumberError.value = 'Account number must be exactly 10 digits'
    } else {
        accountNumberError.value = ''
    }
}

const validateBVN = (event) => {
    const value = event.target.value
    form.bvn = value.replace(/\D/g, '')
    
    if (form.bvn && form.bvn.length !== 11) {
        bvnError.value = 'BVN must be exactly 11 digits'
    } else {
        bvnError.value = ''
    }
}

defineExpose({ form })
</script>