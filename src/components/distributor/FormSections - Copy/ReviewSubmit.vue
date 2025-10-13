<template>
  <div class="space-y-6">
    <h3 class="text-xl font-semibold text-primary mb-4">Section 5: Review & Submit</h3>

    <!-- Info Alert -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
      <div class="flex items-start gap-3">
        <font-awesome-icon icon="info-circle" class="text-blue-600 mt-1" />
        <div>
          <p class="font-semibold text-blue-800">Please Review Your Information</p>
          <p class="text-blue-700 text-sm mt-1">
            Ensure all information provided is accurate before submitting your application.
          </p>
        </div>
      </div>
    </div>

    <!-- Summary of all form data -->
    <div class="space-y-6">
      <!-- Business Information Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="building" class="text-primary" />
          Business Information
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">Company Name:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.companyName || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Registered Business Name:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.registeredName || 'Same as company name' }}
            </dd>
          </div>

          <div>
            <dt class="text-gray-600">RC Number:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.rcNumber || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Company Type:</dt>
            <dd class="font-medium text-gray-900">{{ getCompanyTypeLabel(formData.businessInfo?.companyType) || 'N/A' }}
            </dd>
          </div>

          <div>
            <dt class="text-gray-600">Email:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.email || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Phone:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.phone || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Website:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.website || 'N/A' }}</dd>
          </div>

          <div class="md:col-span-2">
            <dt class="text-gray-600">Business Address:</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.address || 'N/A' }}</dd>
          </div>
        </dl>
      </div>

      <!-- Contact Person Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="user-tie" class="text-primary" />
          Contact Person
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">Full Name:</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.fullName || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Position/Title:</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.position || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Mobile Number:</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.mobile || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">ID Type:</dt>
            <dd class="font-medium text-gray-900">{{ getIdTypeLabel(formData.contactPerson?.idType) || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">ID Number:</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.idNumber || 'N/A' }}</dd>
          </div>
        </dl>
      </div>

      <!-- Distribution Capacity Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="truck" class="text-primary" />
          Distribution Capacity
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">Current Products Distributed:</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.currentProducts || 'None' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Monthly Capacity:</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.monthlyCapacity || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Regions Covered:</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.regions || 'N/A' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Number of Sales Staff:</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.salesStaff || '0' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">Has Warehouse:</dt>
            <dd class="font-medium text-gray-900">
              <span v-if="formData.distributionCapacity?.hasWarehouse === 'yes'" class="text-green-600">
                <font-awesome-icon icon="check-circle" /> Yes
              </span>
              <span v-else class="text-red-600">
                <font-awesome-icon icon="times-circle" /> No
              </span>
            </dd>
          </div>

          <div>
            <dt class="text-gray-600">Has Distribution Vehicles:</dt>
            <dd class="font-medium text-gray-900">
              <span v-if="formData.distributionCapacity?.hasVehicles === 'yes'" class="text-green-600">
                <font-awesome-icon icon="check-circle" /> Yes
              </span>
              <span v-else class="text-red-600">
                <font-awesome-icon icon="times-circle" /> No
              </span>
            </dd>
          </div>

          <div v-if="formData.distributionCapacity?.vehicleDetails" class="md:col-span-2">
            <dt class="text-gray-600">Vehicle Details:</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity.vehicleDetails }}</dd>
          </div>
        </dl>
      </div>

      <!-- Product Focus Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="oil-can" class="text-primary" />
          Distribution Strategy & Product Focus
        </h4>
        <dl class="space-y-3 text-sm">
          <div>
            <dt class="text-gray-600 mb-1">Product Categories Interested In:</dt>
            <dd class="font-medium text-gray-900">
              <div v-if="formData.productFocus?.categories?.length > 0" class="flex flex-wrap gap-2">
                <span v-for="category in formData.productFocus.categories" :key="category"
                  class="px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-full text-xs">
                  {{ category }}
                </span>
              </div>
              <span v-else>None selected</span>
            </dd>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <dt class="text-gray-600">Technical Knowledge:</dt>
              <dd class="font-medium text-gray-900">
                <span v-if="formData.productFocus?.technicalKnowledge === 'yes'" class="text-green-600">
                  <font-awesome-icon icon="check-circle" /> Yes
                </span>
                <span v-else class="text-red-600">
                  <font-awesome-icon icon="times-circle" /> No
                </span>
              </dd>
            </div>

            <div>
              <dt class="text-gray-600">Willing to Take Training:</dt>
              <dd class="font-medium text-gray-900">
                <span v-if="formData.productFocus?.training === 'yes'" class="text-green-600">
                  <font-awesome-icon icon="check-circle" /> Yes
                </span>
                <span v-else-if="formData.productFocus?.training === 'no'" class="text-red-600">
                  <font-awesome-icon icon="times-circle" /> No
                </span>
                <span v-else class="text-yellow-600">
                  <font-awesome-icon icon="question-circle" /> Depends on Arrangement
                </span>
              </dd>
            </div>

            <div>
              <dt class="text-gray-600">Can Commence Distribution:</dt>
              <dd class="font-medium text-gray-900">
                {{ getCommenceTimeLabel(formData.productFocus?.commenceTime) || 'N/A' }}
              </dd>
            </div>
          </div>

          <div>
            <dt class="text-gray-600">Preferred States to Cover:</dt>
            <dd class="font-medium text-gray-900">{{ formData.productFocus?.preferredStates || 'N/A' }}</dd>
          </div>
        </dl>
      </div>

      <!-- Documents Uploaded -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="file-alt" class="text-primary" />
          Documents Uploaded
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div v-for="doc in documentsList" :key="doc.key"
            class="flex items-center justify-between p-2 bg-gray-50 rounded">
            <span class="text-gray-700">{{ doc.label }}:</span>
            <span v-if="formData.productFocus?.documents?.[doc.key]" class="text-green-600">
              <font-awesome-icon icon="check-circle" /> Uploaded
            </span>
            <span v-else class="text-yellow-600">
              <font-awesome-icon icon="exclamation-circle" /> Pending
            </span>
          </div>
        </div>

        <div v-if="formData.productFocus?.signature" class="mt-3 flex items-center gap-2 text-sm">
          <font-awesome-icon icon="signature" class="text-primary" />
          <span class="text-gray-700">Digital Signature:</span>
          <span class="text-green-600">
            <font-awesome-icon icon="check-circle" /> Provided
          </span>
        </div>

        <div v-if="formData.productFocus?.date" class="mt-2 flex items-center gap-2 text-sm">
          <font-awesome-icon icon="calendar" class="text-primary" />
          <span class="text-gray-700">Date:</span>
          <span class="font-medium">{{ formatDate(formData.productFocus.date) }}</span>
        </div>
      </div>

      <!-- Terms and Conditions -->
      <div class="border border-gray-200 rounded-lg p-4 bg-yellow-50">
        <label class="flex items-start gap-3 cursor-pointer">
          <input v-model="agreed" type="checkbox" class="mt-1 rounded text-primary focus:ring-primary" required>
          <div class="text-sm">
            <p class="font-semibold mb-2 text-gray-900">
              <font-awesome-icon icon="file-contract" class="text-primary mr-2" />
              Terms and Conditions Agreement
            </p>
            <p class="text-gray-700 leading-relaxed">
              I hereby confirm that all information provided in this application is true and accurate
              to the best of my knowledge. I understand that Fursa Energy may carry out verification
              and reserve the right to reject or revoke distributorship if any information is found
              to be false or misleading.
            </p>
            <p class="text-gray-700 mt-2">
              I also agree to comply with all Fursa Energy distributor policies, maintain the required
              standards of service, and uphold the company's reputation in all business dealings.
            </p>
          </div>
        </label>
      </div>

      <!-- CHoose a strong password -->
      

      <!-- CHoose a strong password -->
    <div class="border border-gray-200 rounded-lg p-4">
      <h4 class="font-semibold mb-3 flex items-center gap-2">
        <font-awesome-icon icon="lock" class="text-primary" />
        Choose a Strong Password
      </h4>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input v-model="password" type="password" class="border p-2 rounded w-full" required />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input v-model="passwordConfirmation" type="password" class="border p-2 rounded w-full" required />
      </div>
    </div>
    


      <!-- Additional Notes -->
      <div class="border border-gray-200 rounded-lg p-4">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="comment-alt" class="text-primary" />
          Additional Notes (Optional)
        </h4>
        <textarea v-model="additionalNotes" rows="3" placeholder="Any additional information you'd like to provide..."
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="!isFormValid" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <font-awesome-icon icon="exclamation-triangle" class="text-red-600 mt-1" />
        <div>
          <p class="font-semibold text-red-800">Missing Information</p>
          <p class="text-red-700 text-sm mt-1">
            Please ensure all required fields are filled and you have agreed to the terms and conditions.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  formData: {
    type: Object,
    required: true,
    default: () => ({
      businessInfo: {},
      contactPerson: {},
      distributionCapacity: {},
      productFocus: {}
    })
  }
})

const agreed = ref(false)
const additionalNotes = ref('')
const password = ref('')
const passwordConfirmation = ref('')

// Document list for checking
const documentsList = [
  { key: 'cac', label: 'CAC Certificate' },
  { key: 'form_c07', label: 'Form C07' },
  { key: 'memart', label: 'MEMART' },
  { key: 'tin', label: 'Business TIN' },
  { key: 'referee', label: 'Letter of Introduction' }
]

// Computed property to check if form is valid
const isFormValid = computed(() => {
  return agreed.value &&
    props.formData.businessInfo?.companyName &&
    props.formData.contactPerson?.fullName &&
    password.value &&
    passwordConfirmation.value &&
    password.value === passwordConfirmation.value
})

// Helper functions for labels
const getCompanyTypeLabel = (type) => {
  const types = {
    'limited': 'Limited Liability Company',
    'plc': 'Public Limited Company',
    'partnership': 'Partnership',
    'sole': 'Sole Proprietorship'
  }
  return types[type] || type
}

const getIdTypeLabel = (type) => {
  const types = {
    'nin': 'National ID (NIN)',
    'drivers': "Driver's License",
    'passport': 'International Passport',
    'voters': "Voter's Card"
  }
  return types[type] || type
}

const getCommenceTimeLabel = (time) => {
  const times = {
    'immediately': 'Immediately',
    '2weeks': 'Within 2 Weeks',
    'month': 'Within a Month'
  }
  return times[time] || time
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-NG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Expose data to parent
defineExpose({
  agreed,
  additionalNotes,
  password,
  passwordConfirmation,
  isFormValid
})
</script>

<style scoped>
dl dt {
  font-weight: 500;
  margin-bottom: 0.25rem;
}

dl dd {
  margin-bottom: 0.5rem;
}

.border:hover {
  border-color: #b8974f20;
}
</style>
