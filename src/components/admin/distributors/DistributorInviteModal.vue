<!-- DistributorInviteModal.vue (now a right-side drawer supporting full create payload) -->
<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-50" @close="$emit('update:show', false)">
      <!-- Overlay -->
      <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in-out duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-300"
              enter-from="translate-x-full" enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-200" leave-from="translate-x-0"
              leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                <div class="flex h-full flex-col bg-white shadow-xl">
                  <!-- Header -->
                  <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                      <DialogTitle class="text-lg font-medium text-gray-900">Create Distributor</DialogTitle>
                      <p class="text-sm text-gray-500">Create a distributor account and upload required documents</p>
                    </div>
                    <button @click="$emit('update:show', false)" class="text-gray-400 hover:text-gray-600">
                      <XMarkIcon class="h-5 w-5" />
                    </button>
                  </div>

                  <!-- Body -->
                  <form @submit.prevent="sendInvitation" class="relative flex-1 overflow-y-auto px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">First name *</label>
                        <input v-model="form.first_name" type="text" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                          :class="{ 'border-red-300': errors.first_name }" />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Last name *</label>
                        <input v-model="form.last_name" type="text" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                          :class="{ 'border-red-300': errors.last_name }" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                        <input v-model="form.email" type="email" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                          :class="{ 'border-red-300': errors.email }" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input v-model="form.phone" type="tel"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Password *</label>
                        <input v-model="form.password" type="password" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm password *</label>
                        <input v-model="form.password_confirmation" type="password" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Company name</label>
                        <input v-model="form.company_name" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Registered name</label>
                        <input v-model="form.registered_name" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">RC number</label>
                        <input v-model="form.rc_number" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Business address</label>
                        <input v-model="form.business_address" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Office phone</label>
                        <input v-model="form.office_phone" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Website</label>
                        <input v-model="form.website" type="url"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Company type</label>
                        <input v-model="form.company_type" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Contact full name</label>
                        <input v-model="form.contact_full_name" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Contact position</label>
                        <input v-model="form.contact_position" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Contact mobile</label>
                        <input v-model="form.contact_mobile" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">ID number</label>
                        <input v-model="form.id_number" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Means of ID</label>
                        <input v-model="form.means_of_id" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Years in business</label>
                        <input v-model="form.years_in_business" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Current product lines</label>
                        <input v-model="form.current_product_lines" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Monthly capacity</label>
                        <input v-model="form.monthly_capacity" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Regions covered</label>
                        <input v-model="form.regions_covered" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Number of sales staff</label>
                        <input v-model="form.number_of_sales_staff" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Has warehouse</label>
                        <select v-model="form.has_warehouse"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Preferred region</label>
                        <input v-model="form.preferred_region" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Has vehicles</label>
                        <select v-model="form.has_vehicles"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Vehicle details</label>
                        <input v-model="form.vehicle_details" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Product categories (comma
                          separated)</label>
                        <input v-model="product_categories_input" type="text" placeholder="Electronics, Home Appliances"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Willing to train</label>
                        <select v-model="form.willing_to_train"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Has technical knowledge</label>
                        <select v-model="form.has_technical_knowledge"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Distribution start time</label>
                        <input v-model="form.distribution_start_time" type="date"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Preferred states (comma
                          separated)</label>
                        <input v-model="preferred_states_input" type="text" placeholder="Lagos, Abuja"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Promo participation</label>
                        <input v-model="form.promo_participation" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Bank name</label>
                        <input v-model="form.bank_name" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Account name</label>
                        <input v-model="form.account_name" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Account number</label>
                        <input v-model="form.account_number" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">BVN</label>
                        <input v-model="form.bvn" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Partnerships</label>
                        <input v-model="form.partnerships" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Declarant name</label>
                        <input v-model="form.declarant_name" type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700">Declaration date</label>
                        <input v-model="form.declaration_date" type="date"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
                      </div>
                    </div>

                    <!-- Files -->
                    <div class="grid grid-cols-1 gap-4">
                      <template
                        v-for="field in ['cac_certificate', 'form_co7', 'memart', 'utility_bill', 'tin_certificate', 'id_of_contact', 'referee_letter', 'signature']"
                        :key="field">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">{{ humanizeField(field) }}</label>
                          <div class="mt-1 flex items-center gap-3">
                            <label
                              class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                              <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                              </svg>
                              <span>Upload</span>
                              <input class="sr-only" type="file"
                                :accept="field === 'signature' ? 'image/*' : 'application/pdf,image/*'"
                                @change="onFileChange($event, field)" />
                            </label>
                            <div class="text-sm text-gray-600 truncate max-w-[60%]">
                              <span v-if="fileNames[field]">{{ fileNames[field] }}</span>
                              <span v-else class="italic text-gray-400">No file selected</span>
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>

                    <!-- Error -->
                    <div v-if="generalError" class="rounded-md bg-red-50 p-4">
                      <div class="flex">
                        <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                        <div class="ml-3">
                          <h3 class="text-sm font-medium text-red-800">Error</h3>
                          <p class="text-sm text-red-700 mt-1">{{ generalError }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                      <button type="button" @click="$emit('update:show', false)" :disabled="loading"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">
                        Cancel
                      </button>
                      <button type="submit" :disabled="loading || !isFormValid"
                        class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white">
                        <span v-if="loading">Creating...</span>
                        <span v-else>Create Distributor</span>
                      </button>
                    </div>
                  </form>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useDistributorStore } from '@/stores/distributorStore'
import { XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: { type: Boolean, required: true },
  territories: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:show', 'created'])

const distributorStore = useDistributorStore()

const loading = ref(false)
const generalError = ref('')

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'distributor',
  company_name: '',
  registered_name: '',
  rc_number: '',
  business_address: '',
  office_phone: '',
  website: '',
  company_type: '',
  contact_full_name: '',
  contact_position: '',
  contact_mobile: '',
  id_number: '',
  means_of_id: '',
  years_in_business: '',
  current_product_lines: '',
  monthly_capacity: '',
  regions_covered: '',
  number_of_sales_staff: '',
  has_warehouse: '1',
  preferred_region: '',
  has_vehicles: '1',
  vehicle_details: '',
  product_categories: [],
  willing_to_train: '1',
  has_technical_knowledge: '1',
  distribution_start_time: '',
  preferred_states: [],
  promo_participation: '',
  bank_name: '',
  account_name: '',
  account_number: '',
  bvn: '',
  partnerships: '',
  declarant_name: '',
  declaration_date: '',
  // files
  cac_certificate: null,
  form_co7: null,
  memart: null,
  utility_bill: null,
  tin_certificate: null,
  id_of_contact: null,
  referee_letter: null,
  signature: null
})

// Helper inputs for comma-separated lists
const product_categories_input = ref('')
const preferred_states_input = ref('')

// Track selected file names for display
const fileNames = reactive({
  cac_certificate: '',
  form_co7: '',
  memart: '',
  utility_bill: '',
  tin_certificate: '',
  id_of_contact: '',
  referee_letter: '',
  signature: ''
})

const errors = reactive({ first_name: '', last_name: '', email: '' })

const onFileChange = (event, field) => {
  const file = event.target.files?.[0] || null
  form[field] = file
  fileNames[field] = file ? file.name : ''
}

const humanizeField = (key) => {
  const map = {
    cac_certificate: 'CAC certificate',
    form_co7: 'Form CO7',
    memart: 'Memart',
    utility_bill: 'Utility bill',
    tin_certificate: 'TIN certificate',
    id_of_contact: 'ID of contact',
    referee_letter: 'Referee letter',
    signature: 'Signature'
  }
  return map[key] || key.replace(/_/g, ' ')
}

const isFormValid = computed(() => {
  return form.first_name && form.last_name && form.email && form.password && form.password_confirmation && !Object.values(errors).some(Boolean)
})

const validateForm = () => {
  errors.first_name = ''
  errors.last_name = ''
  errors.email = ''
  generalError.value = ''

  if (!form.first_name.trim()) errors.first_name = 'First name is required'
  if (!form.last_name.trim()) errors.last_name = 'Last name is required'
  if (!form.email.trim()) errors.email = 'Email is required'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.email = 'Please enter a valid email address'

  if (!form.password) {
    generalError.value = 'Password is required'
  } else if (form.password !== form.password_confirmation) {
    generalError.value = 'Passwords do not match'
  }

  return !Object.values(errors).some(Boolean) && !generalError.value
}

const resetForm = () => {
  Object.keys(form).forEach(key => {
    if (Array.isArray(form[key])) form[key] = []
    else form[key] = ''
  })
  form.role = 'distributor'
  // reset files explicitly
  form.cac_certificate = null
  form.form_co7 = null
  form.memart = null
  form.utility_bill = null
  form.tin_certificate = null
  form.id_of_contact = null
  form.referee_letter = null
  form.signature = null
  product_categories_input.value = ''
  preferred_states_input.value = ''
  Object.keys(errors).forEach(k => errors[k] = '')
  generalError.value = ''
}

const sendInvitation = async () => {
  if (!validateForm()) return

  loading.value = true
  generalError.value = ''

  try {
    // Convert comma-separated inputs into arrays if provided
    if (product_categories_input.value) {
      form.product_categories = product_categories_input.value.split(',').map(s => s.trim()).filter(Boolean)
    }
    if (preferred_states_input.value) {
      form.preferred_states = preferred_states_input.value.split(',').map(s => s.trim()).filter(Boolean)
    }

    const formData = new FormData()
    Object.keys(form).forEach((key) => {
      const value = form[key]
      if (value === null || value === undefined || value === '') return

      if (Array.isArray(value)) {
        value.forEach(v => formData.append(`${key}[]`, v))
        return
      }

      if (value instanceof File) {
        formData.append(key, value)
        return
      }

      formData.append(key, value)
    })

    await distributorStore.sendInvitation(formData)
    emit('created')
    resetForm()
  } catch (error) {
    generalError.value = error.response?.data?.message || 'Failed to create distributor. Please try again.'
  } finally {
    loading.value = false
  }
}

watch(() => props.show, (s) => { if (s) resetForm() })
</script>
