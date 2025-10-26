<!-- BankingKYC -->
<template>
    <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.banking_kyc') }}</h3>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <font-awesome-icon icon="info-circle" class="text-blue-600 mt-1" />
                <div>
                    <p class="font-semibold text-blue-800">{{ $t('distributor.banking.info_title') }}</p>
                    <p class="text-blue-700 text-sm mt-1">
                        {{ $t('distributor.banking.info_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.banking.bank_name') }} <span class="text-red-500">*</span>
                </label>
                <select v-model="form.bank_name"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                    <option value="">{{ $t('distributor.banking.select_bank') }}</option>
                    <option v-for="bank in nigerianBanks" :key="bank" :value="bank">{{ bank }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.banking.account_number') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.account_number" type="text" maxlength="10" :placeholder="$t('distributor.banking.account_number_placeholder')"
                    @input="validateAccountNumber"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <p v-if="accountNumberError" class="text-red-500 text-sm mt-1">{{ accountNumberError }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.banking.account_name') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.account_name" type="text" :placeholder="$t('distributor.banking.account_name_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.banking.bvn') }} <span class="text-red-500">*</span>
                </label>
                <input v-model="form.bvn" type="text" maxlength="11" :placeholder="$t('distributor.banking.bvn_placeholder')"
                    @input="validateBVN"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required>
                <p v-if="bvnError" class="text-red-500 text-sm mt-1">{{ bvnError }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $t('distributor.banking.bvn_hint') }}</p>
            </div>
        </div>

        <div class="border-t pt-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                <font-awesome-icon icon="handshake" class="text-primary mr-2" />
                {{ $t('distributor.banking.partnership_info_title') }}
            </h4>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $t('distributor.banking.partnerships_label') }}
                </label>
                <textarea v-model="form.partnerships" rows="3"
                    :placeholder="$t('distributor.banking.partnerships_placeholder')"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                <p class="text-xs text-gray-500 mt-1">{{ $t('distributor.banking.partnerships_hint') }}</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <font-awesome-icon icon="shield-alt" class="text-yellow-600 mt-1" />
                <div>
                    <p class="font-semibold text-yellow-800">{{ $t('distributor.banking.privacy_notice_title') }}</p>
                    <p class="text-yellow-700 text-sm mt-1">
                        {{ $t('distributor.banking.privacy_notice_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'

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
const { t } = useI18n()

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
        accountNumberError.value = t('distributor.banking.account_number_error')
    } else {
        accountNumberError.value = ''
    }
}

const validateBVN = (event) => {
    const value = event.target.value
    form.bvn = value.replace(/\D/g, '')
    
    if (form.bvn && form.bvn.length !== 11) {
        bvnError.value = t('distributor.banking.bvn_error')
    } else {
        bvnError.value = ''
    }
}

defineExpose({ form })
</script>