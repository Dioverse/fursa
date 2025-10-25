<template>
  <div class="space-y-6">
  <h3 class="text-xl font-semibold text-primary mb-4">{{ $t('distributor.sections.review_submit') }}</h3>

    <!-- Info Alert -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
      <div class="flex items-start gap-3">
        <font-awesome-icon icon="info-circle" class="text-blue-600 mt-1" />
        <div>
          <p class="font-semibold text-blue-800">{{ $t('distributor.review.info_title') }}</p>
          <p class="text-blue-700 text-sm mt-1">
            {{ $t('distributor.review.info_desc') }}
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
          {{ $t('distributor.review.business_info_title') }}
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.company_name') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.company_name || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.registered_name') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.registered_name || $t('distributor.common.same_as_company_name') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.rc_number') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.rc_number || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.company_type') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.company_type || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.email') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.email || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.office_phone') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.office_phone || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.website') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.website || $t('distributor.common.na') }}</dd>
          </div>

          <div class="md:col-span-2">
            <dt class="text-gray-600">{{ $t('distributor.review.labels.business_address') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.businessInfo?.business_address || $t('distributor.common.na') }}</dd>
          </div>
        </dl>
      </div>

      <!-- Contact Person Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="user-tie" class="text-primary" />
          {{ $t('distributor.review.contact_person_title') }}
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.full_name') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.contact_full_name || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.position_title') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.contact_position || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.mobile_number') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.contact_mobile || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.years_in_business') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.years_in_business || $t('distributor.common.na') }} {{ $t('distributor.common.years_suffix') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.id_type') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.means_of_id || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.id_number') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.contactPerson?.id_number || $t('distributor.common.na') }}</dd>
          </div>
        </dl>
      </div>

      <!-- Distribution Capacity Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="truck" class="text-primary" />
          {{ $t('distributor.review.distribution_capacity_title') }}
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.current_product_lines') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.current_product_lines || $t('distributor.common.none') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.monthly_capacity') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.monthly_capacity || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.regions_covered') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.regions_covered || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.sales_staff') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.number_of_sales_staff || '0' }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.preferred_region') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.preferred_region || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.has_warehouse') }}</dt>
            <dd class="font-medium text-gray-900">
              <span v-if="formData.distributionCapacity?.has_warehouse === 1" class="text-green-600">
                <font-awesome-icon icon="check-circle" /> {{ $t('distributor.common.yes') }}
              </span>
              <span v-else class="text-red-600">
                <font-awesome-icon icon="times-circle" /> {{ $t('distributor.common.no') }}
              </span>
            </dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.has_vehicles') }}</dt>
            <dd class="font-medium text-gray-900">
              <span v-if="formData.distributionCapacity?.has_vehicles === 1" class="text-green-600">
                <font-awesome-icon icon="check-circle" /> {{ $t('distributor.common.yes') }}
              </span>
              <span v-else class="text-red-600">
                <font-awesome-icon icon="times-circle" /> {{ $t('distributor.common.no') }}
              </span>
            </dd>
          </div>

          <div v-if="formData.distributionCapacity?.vehicle_details" class="md:col-span-2">
            <dt class="text-gray-600">{{ $t('distributor.review.labels.vehicle_details') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.distributionCapacity?.vehicle_details }}</dd>
          </div>

          <div class="md:col-span-2">
            <dt class="text-gray-600 mb-2">{{ $t('distributor.review.labels.preferred_states_to_cover') }}</dt>
            <dd class="font-medium text-gray-900">
              <div v-if="formData.distributionCapacity?.preferred_states?.length > 0" class="flex flex-wrap gap-2">
                <span v-for="state in formData.distributionCapacity.preferred_states" :key="state"
                  class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                  {{ state }}
                </span>
              </div>
              <span v-else>{{ $t('distributor.common.none_selected') }}</span>
            </dd>
          </div>
        </dl>
      </div>

      <!-- Product Focus Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="oil-can" class="text-primary" />
          {{ $t('distributor.review.product_focus_title') }}
        </h4>
        <dl class="space-y-3 text-sm">
          <div>
            <dt class="text-gray-600 mb-1">{{ $t('distributor.review.labels.product_categories_interested') }}</dt>
            <dd class="font-medium text-gray-900">
              <div v-if="formData.productFocus?.product_categories?.length > 0" class="flex flex-wrap gap-2">
                <span v-for="(category, i) in formData.productFocus.product_categories" :key="category"
                  class="px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-full text-xs">
                  {{ formData.productFocus?.product_categories_display?.[i] || $t('distributor.product_focus.categories.' + category) || category }}
                </span>
              </div>
              <span v-else>{{ $t('distributor.common.none_selected') }}</span>
            </dd>
          </div>

          <div v-if="formData.productFocus?.other_specify">
            <dt class="text-gray-600 mb-1">{{ $t('distributor.product_focus.other_specify_label') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.productFocus.other_specify }}</dd>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <dt class="text-gray-600">{{ $t('distributor.review.labels.technical_knowledge') }}</dt>
              <dd class="font-medium text-gray-900">
                <span v-if="formData.productFocus?.has_technical_knowledge === 1" class="text-green-600">
                  <font-awesome-icon icon="check-circle" /> {{ $t('distributor.common.yes') }}
                </span>
                <span v-else class="text-red-600">
                  <font-awesome-icon icon="times-circle" /> {{ $t('distributor.common.no') }}
                </span>
              </dd>
            </div>

            <div>
              <dt class="text-gray-600">{{ $t('distributor.review.labels.willing_to_train') }}</dt>
              <dd class="font-medium text-gray-900">
                <span v-if="formData.productFocus?.willing_to_train === 1" class="text-green-600">
                  <font-awesome-icon icon="check-circle" /> {{ $t('distributor.common.yes') }}
                </span>
                <span v-else-if="formData.productFocus?.willing_to_train === 0" class="text-red-600">
                  <font-awesome-icon icon="times-circle" /> {{ $t('distributor.common.no') }}
                </span>
                <span v-else class="text-yellow-600">
                  <font-awesome-icon icon="question-circle" /> {{ $t('distributor.common.depends') }}
                </span>
              </dd>
            </div>

            <div>
              <dt class="text-gray-600">{{ $t('distributor.review.labels.distribution_start_date') }}</dt>
              <dd class="font-medium text-gray-900">
                {{ formatDate(formData.productFocus?.distribution_start_time) }}
              </dd>
            </div>

            <div>
              <dt class="text-gray-600">{{ $t('distributor.review.labels.promo_participation') }}</dt>
              <dd class="font-medium text-gray-900">
                {{ formData.productFocus?.promo_participation || $t('distributor.common.na') }}
              </dd>
            </div>
          </div>

          <div v-if="formData.productFocus?.declarant_name" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <dt class="text-gray-600">{{ $t('distributor.review.labels.declarant_name') }}</dt>
              <dd class="font-medium text-gray-900">{{ formData.productFocus?.declarant_name }}</dd>
            </div>
            <div>
              <dt class="text-gray-600">{{ $t('distributor.review.labels.declaration_date') }}</dt>
              <dd class="font-medium text-gray-900">{{ formatDate(formData.productFocus?.declaration_date) }}</dd>
            </div>
          </div>
        </dl>
      </div>

      <!-- Banking & KYC Summary -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="university" class="text-primary" />
          {{ $t('distributor.review.banking_kyc_title') }}
        </h4>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.bank_name') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.bankingKYC?.bank_name || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.account_number') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.bankingKYC?.account_number || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.account_name') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.bankingKYC?.account_name || $t('distributor.common.na') }}</dd>
          </div>

          <div>
            <dt class="text-gray-600">{{ $t('distributor.review.labels.bvn') }}</dt>
            <dd class="font-medium text-gray-900">{{ maskBVN(formData.bankingKYC?.bvn) }}</dd>
          </div>

          <div v-if="formData.bankingKYC?.partnerships" class="md:col-span-2">
            <dt class="text-gray-600">{{ $t('distributor.review.labels.partnerships') }}</dt>
            <dd class="font-medium text-gray-900">{{ formData.bankingKYC?.partnerships }}</dd>
          </div>
        </dl>
      </div>

      <!-- Documents Uploaded -->
      <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="file-alt" class="text-primary" />
          {{ $t('distributor.review.documents_title') }}
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div v-for="doc in documentsList" :key="doc.key"
            class="flex items-center justify-between p-2 bg-gray-50 rounded">
            <span class="text-gray-700">{{ $t(doc.labelKey) }}:</span>
            <span v-if="formData.productFocus?.documents?.[doc.key]" class="text-green-600">
              <font-awesome-icon icon="check-circle" /> {{ $t('distributor.common.uploaded') }}
            </span>
            <span v-else class="text-yellow-600">
              <font-awesome-icon icon="exclamation-circle" /> {{ $t('distributor.common.pending') }}
            </span>
          </div>
        </div>
      </div>

      <!-- Choose a strong password -->
      <div class="border border-gray-200 rounded-lg p-4">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="lock" class="text-primary" />
          {{ $t('distributor.password.title') }} <span class="text-red-500">*</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('distributor.password.password') }}</label>
            <input v-model="password" type="password" 
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
              :placeholder="$t('distributor.password.password_placeholder')"
              required />
            <p class="text-xs text-gray-500 mt-1">{{ $t('distributor.password.password_hint') }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('distributor.password.confirm_password') }}</label>
            <input v-model="passwordConfirmation" type="password" 
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
              :placeholder="$t('distributor.password.confirm_password_placeholder')"
              required />
            <p v-if="password && passwordConfirmation && password !== passwordConfirmation" 
              class="text-xs text-red-500 mt-1">
              {{ $t('distributor.password.passwords_no_match') }}
            </p>
            <p v-else-if="password && passwordConfirmation && password === passwordConfirmation" 
              class="text-xs text-green-600 mt-1">
              <font-awesome-icon icon="check-circle" /> {{ $t('distributor.password.passwords_match') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Additional Notes -->
      <div class="border border-gray-200 rounded-lg p-4">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <font-awesome-icon icon="comment-alt" class="text-primary" />
          {{ $t('distributor.notes.title') }}
        </h4>
        <textarea v-model="additionalNotes" rows="3" :placeholder="$t('distributor.notes.placeholder')"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
      </div>

      <!-- Terms and Conditions -->
      <div class="border border-gray-200 rounded-lg p-4 bg-yellow-50">
        <label class="flex items-start gap-3 cursor-pointer">
          <input v-model="agreed" type="checkbox" class="mt-1 rounded text-primary focus:ring-primary" required>
          <div class="text-sm">
            <p class="font-semibold mb-2 text-gray-900">
              <font-awesome-icon icon="file-contract" class="text-primary mr-2" />
              {{ $t('distributor.terms.title') }} <span class="text-red-500">*</span>
            </p>
            <p class="text-gray-700 leading-relaxed">
              {{ $t('distributor.terms.p1') }}
            </p>
            <p class="text-gray-700 mt-2">
              {{ $t('distributor.terms.p2') }}
            </p>
          </div>
        </label>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="!isFormValid" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <font-awesome-icon icon="exclamation-triangle" class="text-red-600 mt-1" />
        <div>
          <p class="font-semibold text-red-800">{{ $t('distributor.error.missing_info_title') }}</p>
          <p class="text-red-700 text-sm mt-1">
            {{ $t('distributor.error.missing_info_desc') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  formData: {
    type: Object,
    required: true,
    default: () => ({
      businessInfo: {},
      contactPerson: {},
      distributionCapacity: {},
      productFocus: {},
      bankingKYC: {}
    })
  }
})

const agreed = ref(false)
const additionalNotes = ref('')
const password = ref('')
const passwordConfirmation = ref('')

// Document list for checking
const documentsList = [
  { key: 'cac', labelKey: 'distributor.docs.cac' },
  { key: 'form_c07', labelKey: 'distributor.docs.form_c07' },
  { key: 'memart', labelKey: 'distributor.docs.memart' },
  { key: 'tin', labelKey: 'distributor.docs.tin' },
  { key: 'referee', labelKey: 'distributor.docs.referee' }
]

// Computed property to check if form is valid
const isFormValid = computed(() => {
  return agreed.value &&
    props.formData.businessInfo?.company_name &&
    props.formData.contactPerson?.contact_full_name &&
    props.formData.bankingKYC?.bank_name &&
    password.value &&
    password.value.length >= 8 &&
    passwordConfirmation.value &&
    password.value === passwordConfirmation.value
})

// Helper function to mask BVN
const { t, locale } = useI18n()

const maskBVN = (bvn) => {
  if (!bvn) return t('distributor.common.na')
  return `*******${bvn.slice(-4)}`
}

const formatDate = (dateString) => {
  if (!dateString) return t('distributor.common.na')
  const date = new Date(dateString)
  return date.toLocaleDateString(locale.value || 'en', {
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