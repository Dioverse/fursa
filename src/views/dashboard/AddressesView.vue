<template>
  <DashboardLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold">My Addresses</h1>

      <!-- Add Address Button -->
      <div class="flex justify-end">
        <button
          @click="showAddForm = true"
          class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition"
        >
          <font-awesome-icon icon="plus" class="mr-2" />
          Add New Address
        </button>
      </div>

      <!-- Empty State -->
      <div
        v-if="addresses.length === 0"
        class="bg-white rounded-lg shadow-md p-12 text-center"
      >
        <font-awesome-icon
          icon="map-marker-alt"
          size="3x"
          class="text-gray-400 mb-4"
        />
        <h2 class="text-xl font-semibold mb-2">No address found</h2>
        <p class="text-gray-600 mb-6">
          Add your shipping address to make checkout faster
        </p>
        <button
          @click="showAddForm = true"
          class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded hover:bg-opacity-90 transition"
        >
          <font-awesome-icon icon="plus" />
          <span>Add Address</span>
        </button>
      </div>

      <!-- Address List -->
      <div
        v-else
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="address in addresses"
          :key="address.id"
          class="bg-white rounded-lg shadow-md p-4 relative"
        >
          <h3 class="font-semibold">{{ address.full_name }}</h3>
          <p class="text-gray-600 text-sm">{{ address.phone }}</p>
          <p class="text-gray-600 text-sm">
            {{ address.address_line_one }}, {{ address.address_line_two }}
          </p>
          <p class="text-gray-600 text-sm">
            {{ address.city }}, {{ address.state }}
          </p>
          <p class="text-gray-600 text-sm">
            {{ address.country }} - {{ address.postal_code }}
          </p>
          <span
            v-if="address.is_default == 1"
            class="absolute top-2 right-2 bg-green-100 text-green-700 text-xs px-2 py-1 rounded"
          >
            Default
          </span>

          <div class="mt-4 flex gap-3">
            <button
              @click="deleteAddress(address.id)"
              class="text-red-500 hover:underline"
            >
              Delete
            </button>
            <button
              v-if="address.is_default != 1"
              @click="setDefaultAddress(address.id)"
              class="text-primary hover:underline"
            >
              Set as Default
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Address Modal -->
    <div
      v-if="showAddForm"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <button
          @click="showAddForm = false"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-800"
        >
          <font-awesome-icon icon="times" />
        </button>

        <h2 class="text-xl font-bold mb-4">Add New Address</h2>

        <form @submit.prevent="addAddress">
          <div class="grid grid-cols-1 gap-4">
            <input v-model="form.full_name" placeholder="Full Name" required class="input" />
            <input v-model="form.phone" placeholder="Phone Number" required class="input" />
            <input v-model="form.address_line_one" placeholder="Address Line 1" required class="input" />
            <input v-model="form.address_line_two" placeholder="Address Line 2" class="input" />
            <input v-model="form.city" placeholder="City" required class="input" />
            <input v-model="form.province" placeholder="Province" required class="input" />
            <input v-model="form.state" placeholder="State" required class="input" />
            <input v-model="form.postal_code" placeholder="Postal Code" required class="input" />
            <input v-model="form.country" placeholder="Country" required class="input" />

            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="form.is_default" />
              Set as default
            </label>
          </div>

          <button
            type="submit"
            class="mt-4 w-full bg-primary text-white py-2 rounded hover:bg-opacity-90 transition flex items-center justify-center"
            :disabled="loading"
          >
            <span v-if="loading" class="loader mr-2"></span>
            <span>{{ loading ? 'Saving...' : 'Save Address' }}</span>
          </button>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const toast = useToast()

const addresses = ref([])
const showAddForm = ref(false)
const loading = ref(false)

const form = ref({
  full_name: '',
  phone: '',
  address_line_one: '',
  address_line_two: '',
  city: '',
  province: '',
  state: '',
  postal_code: '',
  country: '',
  is_default: false
})

const resetForm = () => {
  form.value = {
    full_name: '',
    phone: '',
    address_line_one: '',
    address_line_two: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
    is_default: false
  }
}

const fetchAddresses = async () => {
  try {
    const { data } = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/shipping-address`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    addresses.value = data.data || data || []
  } catch (err) {
    console.error(err)
    toast.error('Failed to fetch addresses!')
  }
}

const addAddress = async () => {
  loading.value = true
  try {
    const { data } = await axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/shipping-address`,
      { ...form.value, is_default: form.value.is_default ? 1 : 0 },
      { headers: { Authorization: `Bearer ${authStore.token}`, 'Content-Type': 'application/json' } }
    )
    console.log(form.value)

    addresses.value.push(data)
    toast.success('Address added successfully!')
    showAddForm.value = false
    resetForm()
  } catch (err) {
    console.error(err)
    toast.error(err.response?.data?.message || 'Failed to save address!')
  } finally {
    loading.value = false
  }
}

const deleteAddress = async (id) => {
  if (!confirm('Are you sure you want to delete this address?')) return

  try {
    await axios.delete(`${import.meta.env.VITE_API_BASE_URL}/shipping-address/${id}`, {
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
    await axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/set-default-address/${id}`,
      {},
      { headers: { Authorization: `Bearer ${authStore.token}` } }
    )

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

onMounted(fetchAddresses)
</script>

<style scoped>
.input {
  @apply w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent;
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
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
