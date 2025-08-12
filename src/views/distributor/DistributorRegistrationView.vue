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

                        <!-- Step 5: Review & Submit -->
                        <div v-show="currentStep === 5">
                            <ReviewSubmit :form-data="formData" />
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button v-if="currentStep > 1" @click="previousStep" type="button"
                                class="px-6 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <font-awesome-icon icon="arrow-left" class="mr-2" />
                                Previous
                            </button>
                            <div v-else></div>

                            <button v-if="currentStep < 5" @click="nextStep" type="button"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition">
                                Next
                                <font-awesome-icon icon="arrow-right" class="ml-2" />
                            </button>

                            <button v-if="currentStep === 5" type="submit" :disabled="submitting"
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
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import StepIndicator from '@/components/distributor/StepIndicator.vue'
import BusinessInfo from '@/components/distributor/FormSections/BusinessInfo.vue'
import ContactPerson from '@/components/distributor/FormSections/ContactPerson.vue'
import DistributionCapacity from '@/components/distributor/FormSections/DistributionCapacity.vue'
import ProductFocus from '@/components/distributor/FormSections/ProductFocus.vue'
import ReviewSubmit from '@/components/distributor/FormSections/ReviewSubmit.vue'

const router = useRouter()
const toast = useToast()

const currentStep = ref(1)
const submitting = ref(false)

const steps = [
    { number: 1, label: 'Business Info' },
    { number: 2, label: 'Contact Person' },
    { number: 3, label: 'Distribution' },
    { number: 4, label: 'Product Focus' },
    { number: 5, label: 'Review & Submit' }
]

const step1Ref = ref(null)
const step2Ref = ref(null)
const step3Ref = ref(null)
const step4Ref = ref(null)

const formData = reactive({
    businessInfo: {},
    contactPerson: {},
    distributionCapacity: {},
    productFocus: {}
})

const validateCurrentStep = () => {
    // Add validation logic for each step
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
        }

        if (currentStep.value < 5) {
            currentStep.value++
        }
    }
}

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const handleSubmit = async () => {
    submitting.value = true

    try {
        // Submit form data to API
        await new Promise(resolve => setTimeout(resolve, 2000)) // Simulate API call

        toast.success('Your distributor application has been submitted successfully!')
        router.push('/dashboard')
    } catch (error) {
        toast.error('Failed to submit application. Please try again.')
    } finally {
        submitting.value = false
    }
}
</script>