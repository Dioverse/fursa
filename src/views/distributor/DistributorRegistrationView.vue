<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h1 class="text-3xl font-bold text-center mb-8">Distributor Registration</h1>

                    <!-- Step Indicator -->
                    <StepIndicator :current-step="currentStep" :steps="steps" />

                    <!-- Form Content -->
                    <form @submit.prevent="handleSubmit" class="mt-8">
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
                                    <p class="font-semibold text-red-800">Validation Error</p>
                                    <p class="text-red-700 text-sm mt-1">{{ validationError }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button v-if="currentStep > 1" @click="previousStep" type="button"
                                class="px-6 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <font-awesome-icon icon="arrow-left" class="mr-2" />
                                Previous
                            </button>
                            <div v-else></div>

                            <button v-if="currentStep < 6" @click="nextStep" type="button"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">
                                Next
                                <font-awesome-icon icon="arrow-right" class="ml-2" />
                            </button>

                            <button v-if="currentStep === 6" type="submit" :disabled="submitting"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                                <font-awesome-icon v-if="submitting" icon="spinner" spin class="mr-2" />
                                <font-awesome-icon v-else icon="check" class="mr-2" />
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'
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

const currentStep = ref(1)
const submitting = ref(false)
const validationError = ref('')
const reviewSubmitRef = ref(null)

const steps = [
    { number: 1, label: 'Business Info' },
    { number: 2, label: 'Contact Person' },
    { number: 3, label: 'Distribution' },
    { number: 4, label: 'Product Focus' },
    { number: 5, label: 'Banking & KYC' },
    { number: 6, label: 'Review & Submit' }
]

const step1Ref = ref(null)
const step2Ref = ref(null)
const step3Ref = ref(null)
const step4Ref = ref(null)
const step5Ref = ref(null)

const formData = reactive({
    businessInfo: {},
    contactPerson: {},
    distributionCapacity: {},
    productFocus: {},
    bankingKYC: {}
})

const validateCurrentStep = () => {
    validationError.value = ''
    
    switch (currentStep.value) {
        case 1:
            const businessInfo = step1Ref.value?.form
            if (!businessInfo?.company_name) {
                validationError.value = 'Company Name is required'
                return false
            }
            if (!businessInfo?.rc_number) {
                validationError.value = 'RC Number is required'
                return false
            }
            if (!businessInfo?.email || !businessInfo.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                validationError.value = 'Valid Email Address is required'
                return false
            }
            if (!businessInfo?.office_phone) {
                validationError.value = 'Office Phone Number is required'
                return false
            }
            if (!businessInfo?.company_type) {
                validationError.value = 'Company Type is required'
                return false
            }
            if (!businessInfo?.business_address) {
                validationError.value = 'Business Address is required'
                return false
            }
            break

        case 2:
            const contactPerson = step2Ref.value?.form
            if (!contactPerson?.contact_full_name) {
                validationError.value = 'Contact Person Full Name is required'
                return false
            }
            if (!contactPerson?.contact_position) {
                validationError.value = 'Position/Title is required'
                return false
            }
            if (!contactPerson?.contact_mobile) {
                validationError.value = 'Mobile Number is required'
                return false
            }
            if (!contactPerson?.means_of_id) {
                validationError.value = 'ID Type is required'
                return false
            }
            if (!contactPerson?.id_number) {
                validationError.value = 'ID Number is required'
                return false
            }
            if (!contactPerson?.years_in_business) {
                validationError.value = 'Years in Business is required'
                return false
            }
            break

        case 3:
            const distCapacity = step3Ref.value?.form
            if (distCapacity?.has_warehouse === null) {
                validationError.value = 'Please specify if you have a warehouse'
                return false
            }
            if (distCapacity?.has_vehicles === null) {
                validationError.value = 'Please specify if you have distribution vehicles'
                return false
            }
            if (!distCapacity?.preferred_states || distCapacity.preferred_states.length === 0) {
                validationError.value = 'Please select at least one preferred state'
                return false
            }
            break

        case 4:
            const productFocus = step4Ref.value?.form
            if (!productFocus?.product_categories || productFocus.product_categories.length === 0) {
                validationError.value = 'Please select at least one product category'
                return false
            }
            if (productFocus?.has_technical_knowledge === null) {
                validationError.value = 'Please specify if you have technical knowledge'
                return false
            }
            if (productFocus?.willing_to_train === null) {
                validationError.value = 'Please specify if you are willing to take training'
                return false
            }
            if (!productFocus?.distribution_start_time) {
                validationError.value = 'Please specify when you can commence distribution'
                return false
            }
            break

        case 5:
            const bankingKYC = step5Ref.value?.form
            if (!bankingKYC?.bank_name) {
                validationError.value = 'Bank Name is required'
                return false
            }
            if (!bankingKYC?.account_number || bankingKYC.account_number.length !== 10) {
                validationError.value = 'Valid 10-digit Account Number is required'
                return false
            }
            if (!bankingKYC?.account_name) {
                validationError.value = 'Account Name is required'
                return false
            }
            if (!bankingKYC?.bvn || bankingKYC.bvn.length !== 11) {
                validationError.value = 'Valid 11-digit BVN is required'
                return false
            }
            break

        case 6:
            const reviewData = reviewSubmitRef.value
            if (!reviewData?.agreed) {
                validationError.value = 'You must agree to the terms and conditions'
                return false
            }
            if (!reviewData?.password) {
                validationError.value = 'Password is required'
                return false
            }
            if (reviewData.password.length < 8) {
                validationError.value = 'Password must be at least 8 characters'
                return false
            }
            if (reviewData.password !== reviewData.passwordConfirmation) {
                validationError.value = 'Passwords do not match'
                return false
            }
            break
    }
    
    return true
}

const nextStep = () => {
    if (validateCurrentStep()) {
        // Save current step data
        switch (currentStep.value) {
            case 1:
                formData.businessInfo = step1Ref.value?.form || {}
                break
            case 2:
                formData.contactPerson = step2Ref.value?.form || {}
                break
            case 3:
                formData.distributionCapacity = step3Ref.value?.form || {}
                break
            case 4:
                formData.productFocus = step4Ref.value?.form || {}
                break
            case 5:
                formData.bankingKYC = step5Ref.value?.form || {}
                break
        }

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
    if (!validateCurrentStep()) {
        return
    }

    submitting.value = true

    try {
        const payload = {
            // User basic info
            first_name: authStore.user?.first_name,
            last_name: authStore.user?.last_name,
            email: authStore.user?.email,
            phone: authStore.user?.phone,
            role: 'customer',
            
            // Password
            password: reviewSubmitRef.value.password,
            password_confirmation: reviewSubmitRef.value.passwordConfirmation,

            // Business Information
            ...formData.businessInfo,

            // Contact Person
            ...formData.contactPerson,

            // Distribution Capacity
            ...formData.distributionCapacity,

            // Product Focus
            ...formData.productFocus,

            // Banking & KYC
            ...formData.bankingKYC,

            // Additional notes
            notes: reviewSubmitRef.value.additionalNotes || ''
        }

        // Convert arrays properly for Laravel
        if (payload.preferred_states && Array.isArray(payload.preferred_states)) {
            payload['preferred_states[]'] = payload.preferred_states
            delete payload.preferred_states
        }

        if (payload.product_categories && Array.isArray(payload.product_categories)) {
            payload['product_categories[]'] = payload.product_categories
            delete payload.product_categories
        }

        await authStore.register(payload)
        toast.success('Your distributor application has been submitted successfully!')
        router.push('/dashboard')
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstError = Object.values(errors)[0][0]
            toast.error(firstError)
        } else {
            toast.error(error.response?.data?.message || "Failed to submit application. Please try again.")
        }
    } finally {
        submitting.value = false
    }
}
</script>