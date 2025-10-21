<template>
  <DashboardLayout>
    <div class="space-y-6">
      <h1 class="lg:text-2xl md:text-xl text-lg font-bold">My Addresses</h1>

      <!-- Add Address Button -->
      <div class="flex justify-end">
        <button @click="showAddForm = true"
          class="bg-primary text-xs text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
          <font-awesome-icon icon="plus" class="mr-2" />
          Add New Address
        </button>
      </div>

      <!-- Empty State -->
      <div v-if="addresses.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
        <font-awesome-icon icon="map-marker-alt" size="3x" class="text-gray-400 mb-4" />
        <h2 class="text-xs md:text-md lg:text-xl font-semibold mb-2">No address found</h2>
        <p class="text-gray-600 mb-6 text-xs md:text-md lg:text-xl">
          Add your shipping address to make checkout faster
        </p>
        <button @click="showAddForm = true"
          class="inline-flex items-center gap-2 bg-primary text-white text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 rounded hover:bg-opacity-90 transition">
          <font-awesome-icon icon="plus" />
          <span>Add Address</span>
        </button>
      </div>

      <!-- Address List -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="address in addresses" :key="address.id"
          class="bg-white rounded-lg text-xs md:text:sm lg:text:md shadow-md p-4 relative">
          <h3 class="font-semibold">{{ address.full_name }}</h3>
          <p class="text-gray-600">{{ address.phone }}</p>
          <p class="text-gray-600">
            {{ address.address_line_one }}, {{ address.address_line_two }}
          </p>
          <p class="text-gray-600">
            {{ address.city }}, {{ address.state }}
          </p>
          <p class="text-gray-600">
            {{ address.country }} - {{ address.postal_code }}
          </p>
          <span v-if="address.is_default == 1"
            class="absolute top-2 right-2 bg-green-100 text-green-700 text-xs px-2 py-1 rounded">
            Default
          </span>

          <div class="mt-4 flex gap-3">
            <button @click="deleteAddress(address.id)" class="text-red-500 hover:underline">
              Delete
            </button>
            <button v-if="address.is_default != 1" @click="setDefaultAddress(address.id)"
              class="text-primary hover:underline">
              Set as Default
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Address Modal -->
    <div v-if="showAddForm" class="fixed inset-0 bg-black bg-opacity-50 flex px-4 lg:px-4 items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-3 sm:p-3 md:p-4 lg:p-6 relative max-h-[90vh] overflow-y-auto">
        <button @click="showAddForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
          <font-awesome-icon icon="times" />
        </button>

        <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4">Add New Address</h2>

        <form @submit.prevent="addAddress" novalidate>
          <!-- Full Name & Phone -->
          <div class="grid grid-cols-1 xxs:grid-cols-2 sm:grid-cols-2 xs:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4">
            <div>
              <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">
                Full Name <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.full_name" 
                id="fullName"
                placeholder="Full Name" 
                required 
                class="input"
                :class="{ 'border-red-500': formErrors.full_name }"
              />
              <p v-if="formErrors.full_name" class="text-red-600 text-xs mt-1">{{ formErrors.full_name }}</p>
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.phone" 
                id="phone"
                type="tel"
                placeholder="Phone Number" 
                required 
                class="input"
                :class="{ 'border-red-500': formErrors.phone }"
              />
              <p v-if="formErrors.phone" class="text-red-600 text-xs mt-1">{{ formErrors.phone }}</p>
            </div>
          </div>

          <!-- Address Line 1 -->
          <div class="grid grid-cols-1 my-4">
            <label for="addressLine1" class="block text-sm font-medium text-gray-700 mb-1">
              Address Line 1 <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.address_line_one" 
              id="addressLine1"
              placeholder="Street address, building, house number, etc" 
              required 
              class="input"
              :class="{ 'border-red-500': formErrors.address_line_one }"
            />
            <p v-if="formErrors.address_line_one" class="text-red-600 text-xs mt-1">{{ formErrors.address_line_one }}</p>
          </div>

          <!-- Address Line 2 & Country -->
          <div class="grid grid-cols-1 xxs:grid-cols-2 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="addressLine2" class="block text-sm font-medium text-gray-700 mb-1">
                Address Line 2
              </label>
              <input 
                v-model="form.address_line_two" 
                id="addressLine2"
                placeholder="Apartment, suite, floor, etc." 
                class="input"
              />
            </div>

            <div>
              <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                Country <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.country" 
                id="country"
                @change="loadStatesProvinces"
                required 
                class="input"
                :class="{ 'border-red-500': formErrors.country }"
              >
                <option value="">Select Country</option>
                <option v-for="c in countries" :key="c.id" :value="c.country">
                  {{ c.country }}
                </option>
              </select>
              <p v-if="formErrors.country" class="text-red-600 text-xs mt-1">{{ formErrors.country }}</p>
            </div>
          </div>

          <!-- State & Province -->
          <div class="grid grid-cols-1 xxs:grid-cols-2 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                State/Province <span class="text-red-500">*</span>
              </label>
              <VueMultiselect 
                v-model="form.selectedStateProvince" 
                id="state"
                :options="formattedOptions"
                :multiple="false" 
                :group-values="'items'" 
                :loading="loadingStates" 
                :group-label="'label'"
                :searchable="true" 
                :allow-empty="false" 
                :custom-label="option => option.name || option.label"
                :filter="customFilter" 
                label="name" 
                track-by="code" 
                placeholder="Select State & Province"
                :disabled="!form.country || loadingStates"
                :class="{ 'multiselect-error': formErrors.state }"
              >
                <template #option="{ option }">
                  <div v-if="option.$groupLabel" class="multiselect__option--group">
                    <strong>{{ option.$groupLabel }}</strong>
                  </div>
                  <div v-else class="multiselect__option--item">
                    - {{ option.name }}
                  </div>
                </template>
              </VueMultiselect>
              <p v-if="formErrors.state" class="text-red-600 text-xs mt-1">{{ formErrors.state }}</p>
            </div>

            <div>
              <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                City <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.city" 
                id="city"
                placeholder="City" 
                required 
                class="input"
                :class="{ 'border-red-500': formErrors.city }"
              />
              <p v-if="formErrors.city" class="text-red-600 text-xs mt-1">{{ formErrors.city }}</p>
            </div>
          </div>

          <!-- Postal Code & Default -->
          <div class="grid grid-cols-1 xxs:grid-cols-2 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-1">
                Postal Code
              </label>
              <input 
                v-model="form.postal_code" 
                id="postalCode"
                placeholder="Postal Code" 
                class="input"
              />
            </div>

            <div class="flex items-end">
              <label class="flex items-center gap-2 mb-2">
                <input type="checkbox" v-model="form.is_default" class="w-4 h-4" />
                <span class="text-sm text-gray-700">Set as default</span>
              </label>
            </div>
          </div>

          <button type="submit"
            class="mt-4 w-full bg-primary text-white py-2 rounded hover:bg-opacity-90 transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="loading">
            <span v-if="loading" class="loader mr-2"></span>
            <span>{{ loading ? 'Saving...' : 'Save Address' }}</span>
          </button>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const toast = useToast()

// Constants
const PHONE_REGEX = /^[+]?[\d\s\-()]+$/
const MIN_PHONE_LENGTH = 10

// Location Cache
class LocationCache {
  constructor() {
    this.countries = null
    this.statesProvinces = {}
  }

  setCountries(data) {
    this.countries = data
  }

  getCountries() {
    return this.countries
  }

  setStatesProvinces(country, data) {
    this.statesProvinces[country] = data
  }

  getStatesProvinces(country) {
    return this.statesProvinces[country]
  }

  hasCountries() {
    return this.countries !== null
  }

  hasStatesProvinces(country) {
    return this.statesProvinces.hasOwnProperty(country)
  }
}

const locationCache = new LocationCache()
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

// State
const addresses = ref([])
const showAddForm = ref(false)
const loading = ref(false)
const loadingStates = ref(false)
const countries = ref([])
const statesList = ref([])
const formErrors = ref({})

const form = ref({
  full_name: '',
  phone: '',
  address_line_one: '',
  address_line_two: '',
  city: '',
  state: '',
  province: '',
  postal_code: '',
  country: '',
  is_default: false,
  selectedStateProvince: null
})

// Computed
const formattedOptions = computed(() => {
  if (!Array.isArray(statesList.value) || statesList.value.length === 0) return []

  return statesList.value.map(stateData => {
    const stateLabel = stateData.state
    const provinceItems = (stateData.provinces || []).map(prov => ({
      name: prov.province,
      code: `${stateLabel}|${prov.province}`
    }))

    return {
      label: stateLabel,
      items: provinceItems.length > 0
        ? provinceItems
        : [{ name: stateLabel, code: `${stateLabel}|` }]
    }
  })
})

// Watchers
watch(() => form.value.selectedStateProvince, (val) => {
  if (val?.code) {
    const parts = val.code.split('|')
    form.value.state = parts[0] || ''
    form.value.province = parts[1] || ''
  } else {
    form.value.state = ''
    form.value.province = ''
  }
})

// Validation
const validateAddressForm = () => {
  formErrors.value = {}

  if (!form.value.full_name?.trim()) {
    formErrors.value.full_name = 'Full name is required'
  }

  if (!form.value.phone?.trim()) {
    formErrors.value.phone = 'Phone number is required'
  } else if (!PHONE_REGEX.test(form.value.phone)) {
    formErrors.value.phone = 'Please enter a valid phone number'
  } else if (form.value.phone.replace(/\D/g, '').length < MIN_PHONE_LENGTH) {
    formErrors.value.phone = 'Phone number must be at least 10 digits'
  }

  if (!form.value.country) {
    formErrors.value.country = 'Country is required'
  }

  if (!form.value.selectedStateProvince) {
    formErrors.value.state = 'State/Province is required'
  }

  if (!form.value.city?.trim()) {
    formErrors.value.city = 'City is required'
  }

  if (!form.value.address_line_one?.trim()) {
    formErrors.value.address_line_one = 'Address line 1 is required'
  }

  return Object.keys(formErrors.value).length === 0
}

const customFilter = (option, search, label) => {
  const term = search.toLowerCase()
  if (option.$code && option.$code.toLowerCase().includes(term)) return true
  if (option.name && option.name.toLowerCase().includes(term)) return true
  return false
}

// Methods
const fetchAddresses = async () => {
  try {
    const { data } = await axios.get(`${apiBaseUrl}/shipping-address`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    addresses.value = data.data || data || []
  } catch (err) {
    console.error(err)
    toast.error('Failed to fetch addresses!')
  }
}

const loadCountries = async () => {
  try {
    if (locationCache.hasCountries()) {
      countries.value = locationCache.getCountries()
      return
    }

    const { data } = await axios.get(`${apiBaseUrl}/countries`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    countries.value = data.countries || []
    locationCache.setCountries(countries.value)
  } catch (err) {
    console.error('Failed to load countries:', err)
    toast.error('Failed to load countries')
  }
}

const loadStatesProvinces = async () => {
  if (!form.value.country) return

  loadingStates.value = true
  try {
    if (locationCache.hasStatesProvinces(form.value.country)) {
      statesList.value = locationCache.getStatesProvinces(form.value.country)
      loadingStates.value = false
      return
    }

    const { data } = await axios.get(`${apiBaseUrl}/states-provinces/${form.value.country}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    statesList.value = data.data || []
    locationCache.setStatesProvinces(form.value.country, statesList.value)
    form.value.selectedStateProvince = null
  } catch (err) {
    console.error('Failed to load states/provinces:', err)
    statesList.value = []
    toast.error('Failed to load states/provinces')
  } finally {
    loadingStates.value = false
  }
}

const addAddress = async () => {
  if (!validateAddressForm()) return

  loading.value = true
  try {
    const payload = {
      ...form.value,
      is_default: form.value.is_default ? 1 : 0,
      selectedStateProvince: undefined
    }

    const { data } = await axios.post(`${apiBaseUrl}/shipping-address`, payload, {
      headers: { 
        Authorization: `Bearer ${authStore.token}`, 
        'Content-Type': 'application/json' 
      }
    })

    addresses.value.push(data.data || data)
    toast.success('Address added successfully!')
    showAddForm.value = false
    resetForm()
  } catch (err) {
    console.error(err)
    if (err.response?.status === 422 && err.response?.data?.errors) {
      Object.entries(err.response.data.errors).forEach(([field, messages]) => {
        formErrors.value[field] = Array.isArray(messages) ? messages[0] : messages
      })
      toast.error('Please fix the errors below')
    } else {
      toast.error(err.response?.data?.message || 'Failed to save address!')
    }
  } finally {
    loading.value = false
  }
}

const deleteAddress = async (id) => {
  if (!confirm('Are you sure you want to delete this address?')) return

  try {
    await axios.delete(`${apiBaseUrl}/shipping-address/${id}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    addresses.value = addresses.value.filter((a) => a.id !== id)
    toast.success('Address deleted successfully!')
  } catch (err) {
    console.error(err)
    toast.error('Failed to delete address!')
  }
}

const setDefaultAddress = async (id) => {
  try {
    await axios.post(`${apiBaseUrl}/set-default-address/${id}`, {}, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    addresses.value = addresses.value.map((a) => ({
      ...a,
      is_default: a.id === id ? 1 : 0
    }))
    toast.success('Default address updated!')
  } catch (err) {
    console.error(err)
    toast.error('Failed to update default address!')
  }
}

const resetForm = () => {
  form.value = {
    full_name: '',
    phone: '',
    address_line_one: '',
    address_line_two: '',
    city: '',
    state: '',
    province: '',
    postal_code: '',
    country: '',
    is_default: false,
    selectedStateProvince: null
  }
  formErrors.value = {}
}

// Lifecycle
onMounted(() => {
  fetchAddresses()
  loadCountries()
})
</script>

<style scoped>
.input {
  @apply w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none;
}

.loader {
  border: 2px solid #f3f3f3;
  border-top: 2px solid white;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

:deep(.multiselect__tags) {
  min-height: 42px;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 8px 40px 0 8px;
}

:deep(.multiselect--active .multiselect__tags) {
  border-color: transparent !important;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 1);
}

:deep(.multiselect-error .multiselect__tags) {
  border-color: #ef4444 !important;
}

:deep(.multiselect__content-wrapper) {
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
  margin-top: 4px;
}

:deep(.multiselect__option--highlight) {
  background: #eff6ff;
  color: #1d4ed8;
}

:deep(.multiselect__option--highlight:after) {
  content: none !important;
}

:deep(.multiselect__option--group) {
  color: #6b7280;
  padding: 3px 10px;
  font-size: 14px;
  font-weight: 600;
  background: #f3f4f6;
}

:deep(.multiselect__option--item) {
  padding: 3px 10px;
  font-size: 14px;
}

:deep(.multiselect__option--selected) {
  background-color: #dbeafe;
  color: #1e40af;
}

:deep(.multiselect__input::placeholder),
:deep(.multiselect__single) {
  font-size: 14px;
}

:deep(.multiselect__single) {
  padding: 4px 0;
}
</style>