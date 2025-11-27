<template>
  <DashboardLayout>
    <div class="space-y-6">
  <h1 class="lg:text-2xl md:text-xl text-lg font-bold">{{ $t('addresses.title') }}</h1>

      <!-- Add Address Button -->
      <div class="flex justify-end">
        <button @click="openAdd"
          class="bg-primary text-xs text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
          <font-awesome-icon icon="plus" class="mr-2" />
          {{ $t('addresses.add_new') }}
        </button>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="loadingAddresses" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="n in 3" :key="n" class="bg-white rounded-lg shadow-md p-4 animate-pulse">
          <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-1/4 mb-4"></div>
          <div class="h-3 bg-gray-200 rounded w-3/4 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          <div class="mt-4 flex gap-3">
            <div class="h-6 w-6 bg-gray-200 rounded"></div>
            <div class="h-6 w-6 bg-gray-200 rounded"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="addresses.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
        <font-awesome-icon icon="map-marker-alt" size="3x" class="text-gray-400 mb-4" />
        <h2 class="text-xs md:text-md lg:text-xl font-semibold mb-2">{{ $t('addresses.empty.title') }}</h2>
        <p class="text-gray-600 mb-6 text-xs md:text-md lg:text-xl">
          {{ $t('addresses.empty.subtitle') }}
        </p>
        <button @click="openAdd"
          class="inline-flex items-center gap-2 bg-primary text-white text-xs px-2 md:px-4 lg:px-6 py-2 md:py-3 rounded hover:bg-opacity-90 transition">
          <font-awesome-icon icon="plus" />
          <span>{{ $t('addresses.empty.cta') }}</span>
        </button>
      </div>

      <!-- Address List -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="address in sortedAddresses" :key="address.id"
          class="bg-white rounded-lg text-xs md:text:sm lg:text:md shadow-md p-4 relative">
          <h3 class="font-semibold flex items-center gap-2">
            <span>{{ address.full_name }}</span>
            <span v-if="address.is_default == 1" class="inline-block bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full">
              {{ $t('checkout.default') }}
            </span>
          </h3>
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
          

          <div class="mt-4 flex items-center gap-3">
            <!-- Edit Icon Button -->
            <button
              @click="openEdit(address)"
              class="text-gray-600 hover:text-primary transition"
              :title="$t('checkout.edit')"
              :aria-label="$t('checkout.edit')"
            >
              <font-awesome-icon icon="edit" />
              <span class="sr-only">{{ $t('checkout.edit') }}</span>
            </button>

            <!-- Delete Icon Button -->
            <button
              @click="openDeleteConfirm(address.id)"
              class="text-red-500 hover:text-red-600 transition"
              :title="$t('addresses.actions.delete')"
              :aria-label="$t('addresses.actions.delete')"
            >
              <font-awesome-icon icon="trash" />
              <span class="sr-only">{{ $t('addresses.actions.delete') }}</span>
            </button>

            <!-- Set Default (text) -->
            <button
              v-if="address.is_default != 1"
              @click="setDefaultAddress(address.id)"
              class="ml-auto text-gray-600 hover:text-green-600 transition"
              :title="$t('addresses.actions.set_default')"
              :aria-label="$t('addresses.actions.set_default')"
            >
              <font-awesome-icon icon="check-circle" />
              <span class="sr-only">{{ $t('addresses.actions.set_default') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Address Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex px-4 lg:px-4 items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-3 sm:p-3 md:p-4 lg:p-6 relative max-h-[90vh] overflow-y-auto">
        <button @click="showForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
          <font-awesome-icon icon="times" />
        </button>

  <h2 class="lg:text-2xl md:text-xl text-lg font-bold mb-4">{{ isEditing ? $t('checkout.modal_edit_title') : $t('addresses.modal.title_add') }}</h2>

        <form @submit.prevent="isEditing ? updateAddress() : addAddress()" novalidate>
          <!-- Full Name & Phone -->
          <div class="grid grid-cols-1 xxs:grid-cols-2 sm:grid-cols-2 xs:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4">
            <div>
              <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">
                {{ $t('checkout.full_name') }} <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.full_name" 
                id="fullName"
                :placeholder="$t('checkout.full_name')" 
                required 
                class="input"
                :class="{ 'border-red-500': formErrors.full_name }"
              />
              <p v-if="formErrors.full_name" class="text-red-600 text-xs mt-1">{{ formErrors.full_name }}</p>
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                {{ $t('checkout.phone_number') }} <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.phone" 
                id="phone"
                type="tel"
                :placeholder="$t('checkout.phone_number')" 
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
              {{ $t('checkout.address_line_1') }} <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.address_line_one" 
              id="addressLine1"
              :placeholder="$t('checkout.address_line_1_ph')" 
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
                {{ $t('checkout.address_line_2') }}
              </label>
              <input 
                v-model="form.address_line_two" 
                id="addressLine2"
                :placeholder="$t('checkout.address_line_2_ph')" 
                class="input"
              />
            </div>

            <div>
              <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                {{ $t('checkout.country') }} <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.country" 
                id="country"
                @change="loadStatesProvinces"
                required 
                class="input"
                :class="{ 'border-red-500': formErrors.country }"
              >
                <option value="">{{ $t('checkout.select_country') }}</option>
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
                {{ $t('checkout.state_province') }} <span class="text-red-500">*</span>
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
                :placeholder="$t('checkout.select_state_province')"
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
                {{ $t('checkout.city') }} <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.city" 
                id="city"
                :placeholder="$t('checkout.city')" 
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
                {{ $t('checkout.postal_code') }}
              </label>
              <input 
                v-model="form.postal_code" 
                id="postalCode"
                :placeholder="$t('checkout.postal_code')" 
                class="input"
              />
            </div>

            <div class="flex items-end">
              <label class="flex items-center gap-2 mb-2">
                <input type="checkbox" v-model="form.is_default" class="w-4 h-4" />
                <span class="text-sm text-gray-700">{{ $t('addresses.form.set_as_default') }}</span>
              </label>
            </div>
          </div>

          <button type="submit"
            class="mt-4 w-full bg-primary text-white py-2 rounded hover:bg-opacity-90 transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="loading">
            <span v-if="loading" class="loader mr-2"></span>
            <span>{{ loading ? $t('addresses.submit.saving') : (isEditing ? $t('checkout.update_address') : $t('addresses.submit.save_address')) }}</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex px-4 items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-5 relative">
        <button @click="showDeleteConfirm = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
          <font-awesome-icon icon="times" />
        </button>
        <h3 class="text-lg font-semibold mb-3">{{ $t('addresses.actions.delete') }}</h3>
        <p class="text-sm text-gray-700 mb-5">{{ $t('addresses.confirm_delete') }}</p>
        <div class="flex justify-end gap-3">
          <button @click="showDeleteConfirm = false" class="px-4 py-2 rounded border text-gray-700 hover:bg-gray-50">
            {{ $t('checkout.cancel') }}
          </button>
          <button @click="deleteAddress(deleteTargetId)" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
            {{ $t('addresses.actions.delete') }}
          </button>
        </div>
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
import { useI18n } from 'vue-i18n'
import apiClient from '@/services/api'

const authStore = useAuthStore()
const toast = useToast()
const { t } = useI18n()

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
const loadingAddresses = ref(true)
const showForm = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
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
    formErrors.value.full_name = t('checkout.errors.full_name_required')
  }

  if (!form.value.phone?.trim()) {
    formErrors.value.phone = t('checkout.errors.phone_required')
  } else if (!PHONE_REGEX.test(form.value.phone)) {
    formErrors.value.phone = t('checkout.errors.phone_invalid')
  } else if (form.value.phone.replace(/\D/g, '').length < MIN_PHONE_LENGTH) {
    formErrors.value.phone = t('checkout.errors.phone_min_length')
  }

  if (!form.value.country) {
    formErrors.value.country = t('checkout.errors.country_required')
  }

  if (!form.value.selectedStateProvince) {
    formErrors.value.state = t('checkout.errors.state_required')
  }

  if (!form.value.city?.trim()) {
    formErrors.value.city = t('checkout.errors.city_required')
  }

  if (!form.value.address_line_one?.trim()) {
    formErrors.value.address_line_one = t('checkout.errors.address_line1_required')
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

/* ----------------------------------------
   FETCH ADDRESSES
---------------------------------------- */
const fetchAddresses = async () => {
  try {
    loadingAddresses.value = true
    const { data } = await apiClient.get('/shipping-address')
    addresses.value = data.data || data || []
  } catch (err) {
    console.error(err)
    toast.error(t('addresses.toasts.fetch_failed'))
  } finally {
    loadingAddresses.value = false
  }
}

/* ----------------------------------------
   LOAD COUNTRIES
---------------------------------------- */
const loadCountries = async () => {
  try {
    if (locationCache.hasCountries()) {
      countries.value = locationCache.getCountries()
      return
    }

    const { data } = await apiClient.get('/countries')
    countries.value = data.countries || []
    locationCache.setCountries(countries.value)
  } catch (err) {
    console.error('Failed to load countries:', err)
    toast.error(t('checkout.toasts.countries_failed'))
  }
}

/* ----------------------------------------
   LOAD STATES / PROVINCES
---------------------------------------- */
const loadStatesProvinces = async () => {
  if (!form.value.country) return

  loadingStates.value = true
  try {
    if (locationCache.hasStatesProvinces(form.value.country)) {
      statesList.value = locationCache.getStatesProvinces(form.value.country)
      loadingStates.value = false
      return
    }

    const { data } = await apiClient.get(`/states-provinces/${form.value.country}`)
    statesList.value = data.data || []
    locationCache.setStatesProvinces(form.value.country, statesList.value)
    form.value.selectedStateProvince = null
  } catch (err) {
    console.error('Failed to load states/provinces:', err)
    statesList.value = []
    toast.error(t('checkout.toasts.states_failed'))
  } finally {
    loadingStates.value = false
  }
}

/* ----------------------------------------
   ADD ADDRESS
---------------------------------------- */
const addAddress = async () => {
  if (!validateAddressForm()) return
  loading.value = true

  try {
    const payload = {
      ...form.value,
      is_default: form.value.is_default ? 1 : 0,
      selectedStateProvince: undefined,
    }

    const { data } = await apiClient.post('/shipping-address', payload)
    addresses.value.push(data.data || data)
    toast.success(t('checkout.toasts.address_added'))
    showForm.value = false
    resetForm()
  } catch (err) {
    console.error(err)
    if (err.response?.status === 422 && err.response?.data?.errors) {
      Object.entries(err.response.data.errors).forEach(([field, messages]) => {
        formErrors.value[field] = Array.isArray(messages) ? messages[0] : messages
      })
      toast.error(t('checkout.toasts.fix_errors_below'))
    } else {
      toast.error(err.response?.data?.message || t('checkout.toasts.save_address_failed'))
    }
  } finally {
    loading.value = false
  }
}

/* ----------------------------------------
   UPDATE ADDRESS
---------------------------------------- */
const updateAddress = async () => {
  if (!validateAddressForm()) return
  loading.value = true

  try {
    const payload = {
      ...form.value,
      is_default: form.value.is_default ? 1 : 0,
      selectedStateProvince: undefined,
    }

    const { data } = await apiClient.put(`/shipping-address/${editingId.value}`, payload)
    const updated = data.data || data
    addresses.value = addresses.value.map((a) => (a.id === editingId.value ? { ...a, ...updated } : a))
    toast.success(t('checkout.toasts.address_updated'))
    showForm.value = false
    resetForm()
  } catch (err) {
    console.error(err)
    if (err.response?.status === 422 && err.response?.data?.errors) {
      Object.entries(err.response.data.errors).forEach(([field, messages]) => {
        formErrors.value[field] = Array.isArray(messages) ? messages[0] : messages
      })
      toast.error(t('checkout.toasts.fix_errors_below'))
    } else {
      toast.error(err.response?.data?.message || t('checkout.toasts.save_address_failed'))
    }
  } finally {
    loading.value = false
  }
}

/* ----------------------------------------
   DELETE ADDRESS
---------------------------------------- */
const deleteAddress = async (id) => {
  try {
    await apiClient.delete(`/shipping-address/${id}`)
    addresses.value = addresses.value.filter((a) => a.id !== id)
    toast.success(t('addresses.toasts.delete_success'))
    showDeleteConfirm.value = false
  } catch (err) {
    console.error(err)
    toast.error(t('addresses.toasts.delete_failed'))
  }
}

/* ----------------------------------------
   SET DEFAULT ADDRESS
---------------------------------------- */
const setDefaultAddress = async (id) => {
  try {
    await apiClient.post(`/set-default-address/${id}`)
    addresses.value = addresses.value.map((a) => ({
      ...a,
      is_default: a.id === id ? 1 : 0,
    }))
    toast.success(t('checkout.toasts.default_address_updated'))
  } catch (err) {
    console.error(err)
    toast.error(t('checkout.toasts.default_address_update_failed'))
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

const openAdd = () => {
  isEditing.value = false
  editingId.value = null
  resetForm()
  showForm.value = true
}

const openEdit = async (address) => {
  isEditing.value = true
  editingId.value = address.id
  // Prefill form
  form.value.full_name = address.full_name || ''
  form.value.phone = address.phone || ''
  form.value.address_line_one = address.address_line_one || ''
  form.value.address_line_two = address.address_line_two || ''
  form.value.city = address.city || ''
  form.value.state = address.state || ''
  form.value.province = address.province || ''
  form.value.postal_code = address.postal_code || ''
  form.value.country = address.country || ''
  form.value.is_default = address.is_default == 1

  // Ensure countries/states loaded and set selected option
  if (!locationCache.hasCountries()) {
    await loadCountries()
  }
  if (form.value.country) {
    await loadStatesProvinces()
    const code = `${form.value.state}|${form.value.province || ''}`
    const groups = formattedOptions.value || []
    let selected = null
    for (const g of groups) {
      for (const item of g.items) {
        if (item.code === code) { selected = item; break }
      }
      if (selected) break
    }
    form.value.selectedStateProvince = selected
  }

  showForm.value = true
}

// Sorting: default addresses first
const sortedAddresses = computed(() => {
  const list = Array.isArray(addresses.value) ? [...addresses.value] : []
  return list.sort((a, b) => (b.is_default === 1) - (a.is_default === 1))
})

// Delete confirm helpers
const showDeleteConfirm = ref(false)
const deleteTargetId = ref(null)
const openDeleteConfirm = (id) => {
  deleteTargetId.value = id
  showDeleteConfirm.value = true
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