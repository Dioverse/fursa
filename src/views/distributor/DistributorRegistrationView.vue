<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h1 class="text-3xl font-bold text-center mb-8">{{ $t('distributor.title') }}</h1>

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
import { ref, reactive, computed } from 'vue'
import { useI18n } from 'vue-i18n'
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
                validationError.value = t('distributor.validation.company_name_required')
                return false
            }
            if (!businessInfo?.rc_number) {
                validationError.value = t('distributor.validation.rc_number_required')
                return false
            }
            if (!businessInfo?.email || !businessInfo.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                validationError.value = t('distributor.validation.email_required')
                return false
            }
            if (!businessInfo?.office_phone) {
                validationError.value = t('distributor.validation.office_phone_required')
                return false
            }
            if (!businessInfo?.company_type) {
                validationError.value = t('distributor.validation.company_type_required')
                return false
            }
            if (!businessInfo?.business_address) {
                validationError.value = t('distributor.validation.business_address_required')
                return false
            }
            break

        case 2:
            const contactPerson = step2Ref.value?.form
            if (!contactPerson?.contact_full_name) {
                validationError.value = t('distributor.validation.contact_full_name_required')
                return false
            }
            if (!contactPerson?.contact_position) {
                validationError.value = t('distributor.validation.position_required')
                return false
            }
            if (!contactPerson?.contact_mobile) {
                validationError.value = t('distributor.validation.mobile_required')
                return false
            }
            if (!contactPerson?.means_of_id) {
                validationError.value = t('distributor.validation.id_type_required')
                return false
            }
            if (!contactPerson?.id_number) {
                validationError.value = t('distributor.validation.id_number_required')
                return false
            }
            if (!contactPerson?.years_in_business) {
                validationError.value = t('distributor.validation.years_in_business_required')
                return false
            }
            break

        case 3:
            const distCapacity = step3Ref.value?.form
            if (distCapacity?.has_warehouse === null) {
                validationError.value = t('distributor.validation.has_warehouse_required')
                return false
            }
            if (distCapacity?.has_vehicles === null) {
                validationError.value = t('distributor.validation.has_vehicles_required')
                return false
            }
            if (!distCapacity?.preferred_states || distCapacity.preferred_states.length === 0) {
                validationError.value = t('distributor.validation.preferred_states_required')
                return false
            }
            break

        case 4:
            const productFocus = step4Ref.value?.form
            if (!productFocus?.product_categories || productFocus.product_categories.length === 0) {
                validationError.value = t('distributor.validation.product_categories_required')
                return false
            }
            if (productFocus?.product_categories?.includes('other') && !productFocus?.other_specify) {
                validationError.value = t('distributor.validation.other_specify_required')
                return false
            }
            if (productFocus?.has_technical_knowledge === null) {
                validationError.value = t('distributor.validation.technical_knowledge_required')
                return false
            }
            if (productFocus?.willing_to_train === null) {
                validationError.value = t('distributor.validation.willing_to_train_required')
                return false
            }
            if (!productFocus?.distribution_start_time) {
                validationError.value = t('distributor.validation.distribution_start_time_required')
                return false
            }
            break

        case 5:
            const bankingKYC = step5Ref.value?.form
            if (!bankingKYC?.bank_name) {
                validationError.value = t('distributor.validation.bank_name_required')
                return false
            }
            if (!bankingKYC?.account_number || bankingKYC.account_number.length !== 10) {
                validationError.value = t('distributor.validation.account_number_required')
                return false
            }
            if (!bankingKYC?.account_name) {
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
            if (!reviewData?.password) {
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
            role: 'distributor',
            
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

        if (authStore.token) { await authStore.distributorApplication(payload) }
        else { await authStore.register(payload) }
        toast.success(t('distributor.toasts.submit_success'))
        router.push('/dashboard')
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstError = Object.values(errors)[0][0]
            toast.error(firstError)
        } else {
            toast.error(error.response?.data?.message || t('distributor.toasts.submit_failed_generic'))
        }
    } finally {
        submitting.value = false
    }
}
</script>