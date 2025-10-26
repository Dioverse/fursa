<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h1 class="text-3xl font-bold text-center mb-8">{{ $t('distributor.title') }}</h1>

                    <!-- Step Indicator -->
                    <StepIndicator :current-step="currentStep" :steps="steps" />

                    <!-- Form Content -->
                    <!-- <form @submit.prevent="handleSubmit" class="mt-8"> -->
                    <form @submit.prevent="handleSubmit" novalidate class="mt-8">
                        <!-- Step 1: Business Information -->
                        <div v-show="currentStep === 1">
                            <BusinessInfo ref="step1Ref" />
                        </div>

                        <!-- Step 2: Contact Person -->
                        <div v-show="currentStep === 2">
                            <ContactPerson ref="step2Ref" />
                        </div>

                        <!-- Step 3: Distribution Capacity -->
                        <div v-show="currentStep === 3">
                            <DistributionCapacity ref="step3Ref" />
                        </div>

                        <!-- Step 4: Product Focus -->
                        <div v-show="currentStep === 4">
                            <ProductFocus ref="step4Ref" />
                        </div>

                        <!-- Step 5: Banking & KYC -->
                        <div v-show="currentStep === 5">
                            <BankingKYC ref="step5Ref" />
                        </div>

                        <!-- Step 6: Review & Submit -->
                        <div v-show="currentStep === 6">
                            <ReviewSubmit ref="reviewSubmitRef" :form-data="formData" />
                        </div>

                        <!-- Validation Error Display -->
                        <div v-if="validationError" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <font-awesome-icon icon="exclamation-triangle" class="text-red-600 mt-1" />
                                <div>
                                    <p class="font-semibold text-red-800">{{ $t('distributor.validation.error_title') }}</p>
                                    <p class="text-red-700 text-sm mt-1">{{ validationError }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button v-if="currentStep > 1" @click="previousStep" type="button"
                                class="px-6 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <font-awesome-icon icon="arrow-left" class="mr-2" />
                                {{ $t('distributor.buttons.previous') }}
                            </button>
                            <div v-else></div>

                            <button v-if="currentStep < 6" @click="nextStep" type="button"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">
                                {{ $t('distributor.buttons.next') }}
                                <font-awesome-icon icon="arrow-right" class="ml-2" />
                            </button>

                            <button type="button" @click="debugFormData"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                                <font-awesome-icon v-if="submitting" icon="spinner" spin class="mr-2" />
                                <font-awesome-icon v-else icon="check" class="mr-2" />
                                debug
                            </button>

                            <button v-if="currentStep === 6" type="submit" :disabled="submitting"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                                <font-awesome-icon v-if="submitting" icon="spinner" spin class="mr-2" />
                                <font-awesome-icon v-else icon="check" class="mr-2" />
                                {{ $t('distributor.buttons.submit_application') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
// In Distributor.vue, replace the imports and initial setup:

import { ref, reactive, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'
import { useDistributorFormStore } from '@/stores/distributorForm'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import StepIndicator from '@/components/distributor/StepIndicator.vue'
import BusinessInfo from '@/components/distributor/FormSections/BusinessInfo.vue'
import ContactPerson from '@/components/distributor/FormSections/ContactPerson.vue'
import DistributionCapacity from '@/components/distributor/FormSections/DistributionCapacity.vue'
import ProductFocus from '@/components/distributor/FormSections/ProductFocus.vue'
import BankingKYC from '@/components/distributor/FormSections/BankingKYC.vue'
import ReviewSubmit from '@/components/distributor/FormSections/ReviewSubmit.vue'

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()
const distributorStore = useDistributorFormStore()
const { t } = useI18n()

const currentStep = ref(1)
const submitting = ref(false)
const validationError = ref('')
const reviewSubmitRef = ref(null)

const steps = computed(() => ([
    { number: 1, label: t('distributor.steps.business_info') },
    { number: 2, label: t('distributor.steps.contact_person') },
    { number: 3, label: t('distributor.steps.distribution') },
    { number: 4, label: t('distributor.steps.product_focus') },
    { number: 5, label: t('distributor.steps.banking_kyc') },
    { number: 6, label: t('distributor.steps.review_submit') }
]))

const step1Ref = ref(null)
const step2Ref = ref(null)
const step3Ref = ref(null)
const step4Ref = ref(null)
const step5Ref = ref(null)

// Use the store's formData
const formData = distributorStore.formData

// Debug function
const debugFormData = () => {
    console.log('=== DEBUGGING FORM DATA ===')
    
    console.log('\n1. Business Info:')
    console.log(formData.businessInfo)
    if (formData.businessInfo.documents?.utility_bill) {
        console.log(`   ✓ Utility Bill: ${formData.businessInfo.documents.utility_bill.name} (${formData.businessInfo.documents.utility_bill.size} bytes)`)
    } else {
        console.log('   ✗ Utility Bill: NOT FOUND')
    }
    
    console.log('\n2. Contact Person:')
    console.log(formData.contactPerson)
    if (formData.contactPerson.id_of_contact) {
        console.log(`   ✓ ID Document: ${formData.contactPerson.id_of_contact.name} (${formData.contactPerson.id_of_contact.size} bytes)`)
    } else {
        console.log('   ✗ ID Document: NOT FOUND')
    }
    
    console.log('\n3. Distribution Capacity:')
    console.log(formData.distributionCapacity)
    
    console.log('\n4. Product Focus:')
    const docs = formData.productFocus.documents || {}
    console.log('   Documents:', {
        cac: docs.cac ? `${docs.cac.name} (${docs.cac.size} bytes)` : 'NOT FOUND',
        form_c07: docs.form_c07 ? `${docs.form_c07.name} (${docs.form_c07.size} bytes)` : 'NOT FOUND',
        memart: docs.memart ? `${docs.memart.name} (${docs.memart.size} bytes)` : 'NOT FOUND',
        tin: docs.tin ? `${docs.tin.name} (${docs.tin.size} bytes)` : 'NOT FOUND',
        referee: docs.referee ? `${docs.referee.name} (${docs.referee.size} bytes)` : 'NOT FOUND',
        signature: docs.signature ? `${docs.signature.name} (${docs.signature.size} bytes)` : 'NOT FOUND'
    })
    
    console.log('\n5. Banking KYC:')
    console.log(formData.bankingKYC)
    
    console.log('\n=== END DEBUG ===')
}

const validateCurrentStep = () => {
    validationError.value = ''
    
    switch (currentStep.value) {
        case 1:
            const businessInfo = formData.businessInfo
            if (!businessInfo?.company_name?.trim()) {
                validationError.value = t('distributor.validation.company_name_required')
                return false
            }
            if (!businessInfo?.rc_number?.trim()) {
                validationError.value = t('distributor.validation.rc_number_required')
                return false
            }
            if (!businessInfo?.email || !businessInfo.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                validationError.value = t('distributor.validation.email_required')
                return false
            }
            if (!businessInfo?.office_phone?.trim()) {
                validationError.value = t('distributor.validation.office_phone_required')
                return false
            }
            if (!businessInfo?.company_type) {
                validationError.value = t('distributor.validation.company_type_required')
                return false
            }
            if (!businessInfo?.business_address?.trim()) {
                validationError.value = t('distributor.validation.business_address_required')
                return false
            }
            if (!businessInfo?.documents?.utility_bill) {
                validationError.value = 'Utility bill is required'
                return false
            }
            break

        case 2:
            const contactPerson = formData.contactPerson
            if (!contactPerson?.contact_full_name?.trim()) {
                validationError.value = t('distributor.validation.contact_full_name_required')
                return false
            }
            if (!contactPerson?.contact_position?.trim()) {
                validationError.value = t('distributor.validation.position_required')
                return false
            }
            if (!contactPerson?.contact_mobile?.trim()) {
                validationError.value = t('distributor.validation.mobile_required')
                return false
            }
            if (!contactPerson?.means_of_id) {
                validationError.value = t('distributor.validation.id_type_required')
                return false
            }
            if (!contactPerson?.id_number?.trim()) {
                validationError.value = t('distributor.validation.id_number_required')
                return false
            }
            if (!contactPerson?.years_in_business) {
                validationError.value = t('distributor.validation.years_in_business_required')
                return false
            }
            if (!contactPerson?.id_of_contact) {
                validationError.value = 'ID document is required'
                return false
            }
            break

        case 3:
            const distCapacity = formData.distributionCapacity
            if (distCapacity?.has_warehouse === null || distCapacity?.has_warehouse === undefined) {
                validationError.value = t('distributor.validation.has_warehouse_required')
                return false
            }
            if (distCapacity?.has_vehicles === null || distCapacity?.has_vehicles === undefined) {
                validationError.value = t('distributor.validation.has_vehicles_required')
                return false
            }
            if (!distCapacity?.preferred_states || distCapacity.preferred_states.length === 0) {
                validationError.value = t('distributor.validation.preferred_states_required')
                return false
            }
            break

        case 4:
            const productFocus = formData.productFocus
            if (!productFocus?.product_categories || productFocus.product_categories.length === 0) {
                validationError.value = t('distributor.validation.product_categories_required')
                return false
            }
            if (productFocus?.product_categories?.includes('other') && !productFocus?.other_specify?.trim()) {
                validationError.value = t('distributor.validation.other_specify_required')
                return false
            }
            if (productFocus?.has_technical_knowledge === null || productFocus?.has_technical_knowledge === undefined) {
                validationError.value = t('distributor.validation.technical_knowledge_required')
                return false
            }
            if (productFocus?.willing_to_train === null || productFocus?.willing_to_train === undefined) {
                validationError.value = t('distributor.validation.willing_to_train_required')
                return false
            }
            if (!productFocus?.distribution_start_time?.trim()) {
                validationError.value = t('distributor.validation.distribution_start_time_required')
                return false
            }
            // Validate all documents are uploaded
            const docs = productFocus?.documents || {}
            const requiredDocs = ['cac', 'form_c07', 'memart', 'tin', 'referee', 'signature']
            const hasAllDocs = requiredDocs.every(doc => docs[doc])
            if (!hasAllDocs) {
                const missingDocs = requiredDocs.filter(doc => !docs[doc]).join(', ')
                validationError.value = `Missing documents: ${missingDocs}`
                return false
            }
            break

        case 5:
            const bankingKYC = formData.bankingKYC
            if (!bankingKYC?.bank_name) {
                validationError.value = t('distributor.validation.bank_name_required')
                return false
            }
            if (!bankingKYC?.account_number || bankingKYC.account_number.length !== 10) {
                validationError.value = t('distributor.validation.account_number_required')
                return false
            }
            if (!bankingKYC?.account_name?.trim()) {
                validationError.value = t('distributor.validation.account_name_required')
                return false
            }
            if (!bankingKYC?.bvn || bankingKYC.bvn.length !== 11) {
                validationError.value = t('distributor.validation.bvn_required')
                return false
            }
            break

        case 6:
            const reviewData = reviewSubmitRef.value
            if (!reviewData?.agreed) {
                validationError.value = t('distributor.validation.must_agree_terms')
                return false
            }
            if (!reviewData?.password?.trim()) {
                validationError.value = t('distributor.validation.password_required')
                return false
            }
            if (reviewData.password.length < 8) {
                validationError.value = t('distributor.validation.password_min')
                return false
            }
            if (reviewData.password !== reviewData.passwordConfirmation) {
                validationError.value = t('distributor.validation.passwords_no_match')
                return false
            }
            break
    }
    
    return true
}

const nextStep = () => {
    if (validateCurrentStep()) {
        if (currentStep.value < 6) {
            currentStep.value++
        }
    }
}

const previousStep = () => {
    validationError.value = ''
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const handleSubmit = async () => {
    debugFormData()
    
    if (!validateCurrentStep()) {
        return
    }

    submitting.value = true

    try {
        // Use FormData to handle file uploads
        const formDataObj = new FormData()

        // User basic info
        formDataObj.append('first_name', authStore.user?.first_name || '')
        formDataObj.append('last_name', authStore.user?.last_name || '')
        formDataObj.append('email', authStore.user?.email || '')
        formDataObj.append('phone', authStore.user?.phone || '')
        formDataObj.append('role', 'distributor')
        
        // Password
        formDataObj.append('password', reviewSubmitRef.value.password)
        formDataObj.append('password_confirmation', reviewSubmitRef.value.passwordConfirmation)

        // Business Information
        Object.keys(formData.businessInfo).forEach(key => {
            const value = formData.businessInfo[key]
            
            // Handle documents separately
            if (key === 'documents') return
            
            if (value !== null && value !== undefined && value !== '') {
                formDataObj.append(key, value)
            }
        })

        // Utility bill from businessInfo documents
        if (formData.businessInfo.documents?.utility_bill instanceof File) {
            formDataObj.append('utility_bill', formData.businessInfo.documents.utility_bill)
        }

        // Contact Person
        Object.keys(formData.contactPerson).forEach(key => {
            const value = formData.contactPerson[key]
            
            // Handle ID file separately
            if (key === 'id_of_contact') return
            
            if (value !== null && value !== undefined && value !== '') {
                formDataObj.append(key, value)
            }
        })

        // Contact ID document
        if (formData.contactPerson.id_of_contact instanceof File) {
            formDataObj.append('id_of_contact', formData.contactPerson.id_of_contact)
        }

        // Distribution Capacity
        Object.keys(formData.distributionCapacity).forEach(key => {
            const value = formData.distributionCapacity[key]
            if (value !== null && value !== undefined && value !== '') {
                if (Array.isArray(value)) {
                    value.forEach(v => formDataObj.append(`${key}[]`, v))
                } else {
                    formDataObj.append(key, value)
                }
            }
        })

        // Product Focus
        Object.keys(formData.productFocus).forEach(key => {
            const value = formData.productFocus[key]
            
            // Skip documents and display arrays
            if (key === 'documents' || key === 'product_categories_display') return
            
            if (value !== null && value !== undefined && value !== '') {
                if (Array.isArray(value)) {
                    value.forEach(v => formDataObj.append(`${key}[]`, v))
                } else {
                    formDataObj.append(key, value)
                }
            }
        })

        // Product Focus documents
        const fileFieldMap = {
            'cac': 'cac_certificate',
            'form_c07': 'form_co7',
            'memart': 'memart',
            'tin': 'tin_certificate',
            'referee': 'referee_letter',
            'signature': 'signature'
        }

        const productDocs = formData.productFocus.documents || {}
        Object.entries(fileFieldMap).forEach(([docKey, fieldName]) => {
            const file = productDocs[docKey]
            if (file && file instanceof File) {
                formDataObj.append(fieldName, file)
            }
        })

        // Additional notes
        formDataObj.append('notes', reviewSubmitRef.value.additionalNotes || '')

        // Banking & KYC
        Object.keys(formData.bankingKYC).forEach(key => {
            const value = formData.bankingKYC[key]
            if (value !== null && value !== undefined && value !== '') {
                formDataObj.append(key, value)
            }
        })

        // Log FormData contents for debugging
        console.log('=== FormData Contents ===')
        for (let [key, value] of formDataObj.entries()) {
            if (value instanceof File) {
                console.log(`✓ ${key}: File - ${value.name} (${(value.size / 1024).toFixed(2)} KB)`)
            } else {
                console.log(`✓ ${key}:`, value)
            }
        }
        console.log('========================')

        // Send request
        if (authStore.token) {
            await authStore.distributorApplication(formDataObj)
        } else {
            await authStore.register(formDataObj)
        }
        
        toast.success(t('distributor.toasts.submit_success'))
        distributorStore.resetFormData()
        router.push('/dashboard')
    } catch (error) {
        console.error('Submission error:', error)
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstErrorKey = Object.keys(errors)[0]
            const firstError = errors[firstErrorKey][0]
            toast.error(`${firstErrorKey}: ${firstError}`)
        } else {
            toast.error(error.response?.data?.message || t('distributor.toasts.submit_failed_generic'))
        }
    } finally {
        submitting.value = false
    }
}

</script>