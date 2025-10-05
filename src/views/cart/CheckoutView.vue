<template>
  <DefaultLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Loader -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div>
        <span class="ml-3 text-gray-600">Initializing checkout...</span>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="text-center py-20">
        <p class="text-red-600 font-semibold mb-4">Checkout initialization failed</p>
        <button
          @click="initCheckout"
          class="px-5 py-2 bg-primary text-white rounded hover:bg-opacity-90 transition"
        >
          Retry
        </button>
      </div>

      <!-- Success Content -->
      <div v-else>
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        <!-- No Address Warning -->
        <div v-if="!hasAddress" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
          <div class="flex items-start gap-3">
            <font-awesome-icon icon="map-marker-alt" class="text-yellow-600 mt-1" />
            <div class="flex-1">
              <p class="font-semibold text-yellow-800">No Address Saved</p>
              <p class="text-yellow-700 text-sm mt-1">
                Add an address so we can deliver your order
              </p>
              <button
                @click="showAddressModal = true"
                class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition"
              >
                Add new Location
              </button>
            </div>
          </div>
        </div>

        <!-- Product Notice -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
          <div class="flex items-start gap-3">
            <font-awesome-icon icon="check-circle" class="text-green-600 mt-1" />
            <div>
              <p class="font-semibold text-green-800">Check your cart items before payment</p>
              <p class="text-green-700 text-sm mt-1">
                Ensure everything is correct before completing your purchase
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Payment Section -->
          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
              <h2 class="text-xl font-semibold mb-4">Select Payment Method</h2>

              <!-- Dynamic Payment Options -->
              <div class="space-y-3 relative">
                <!-- Loader Overlay when fetching gateway -->
                <div 
                  v-if="gatewayLoading" 
                  class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-70 rounded-lg z-10"
                >
                  <div class="animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
                  <span class="ml-2 text-gray-600">Loading gateway...</span>
                </div>

                <label
                  v-for="gateway in gateways"
                  :key="gateway.name"
                  class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                >
                  <input
                    v-model="paymentMethod"
                    type="radio"
                    :value="gateway.name"
                    class="text-primary focus:ring-primary"
                    @change="selectGateway(gateway.name)"
                  />
                  <div class="flex-1 flex items-center gap-2">
                    <span class="font-medium">Pay with {{ gateway.display }}</span>
                    <img v-if="gateway.logo" :src="gateway.logo" :alt="gateway.name" class="h-6" />
                  </div>
                </label>
              </div>

              <!-- Pay Now -->
              <button
                @click="handlePayment"
                class="mt-6 w-full bg-primary text-white py-2 rounded hover:bg-opacity-90 transition"
              >
                Pay ₦{{ cartTotal.toLocaleString() }}
              </button>
            </div>
          </div>

          <!-- Cart Summary -->
          <div class="lg:col-span-1">
            <CartSummary />
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom CTA Section -->
    <CTA />
    <Brochure />
  </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import CTA from '@/components/common/CTA.vue'
import Brochure from '@/components/common/Brochure.vue'
import CartSummary from '@/components/cart/CartSummary.vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'

const toast = useToast()
const cartStore = useCartStore()
const authStore = useAuthStore()
const user = computed(() => authStore.user)

const paymentMethod = ref(null)
const selectedGateway = ref(null)
const showAddressModal = ref(false)
const hasAddress = ref(false)
const gatewayLoading = ref(false)

const gateways = ref([]) // list of available gateways
const userCart = ref(null)

const loading = ref(true)
const error = ref(false)

// Cart values
const subtotal = computed(() => cartStore.subtotal)
const shipping = computed(() => cartStore.shipping)
const tax = computed(() => cartStore.tax)
const cartTotal = computed(() => cartStore.totalPrice)


// --- Init Checkout ---
const initCheckout = async () => {
  loading.value = true
  error.value = false
  try {
    const token = localStorage.getItem('token')
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/checkout/init`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      }
    })

    if (!res.ok) throw new Error('Failed to initialize checkout')
    const data = await res.json()

    // Address
    hasAddress.value = data.data.user_cart?.shippingAddress

    // Build gateways list
    gateways.value = Object.entries(data.data.gateways || {})
      .filter(([_, g]) => g.status === 'active')
      .map(([name, g]) => ({
        name,
        display: name.charAt(0).toUpperCase() + name.slice(1),
        public_key: g.public_key,
        logo: g.image || null,
      }))

    if (gateways.value.length > 0) {
      //paymentMethod.value = gateways.value[0].name // default first
    }

    // User cart snapshot
    userCart.value = data.data.user_cart
  } catch (err) {
    console.error(err)
    error.value = true
    toast.error('Checkout initialization failed')
  } finally {
    loading.value = false
  }
}

// --- Select Gateway ---
const selectGateway = async (gatewayName) => {
  try {
    gatewayLoading.value = true
    const token = localStorage.getItem('token')
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/place-order/${gatewayName}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      }
    })

    if (!res.ok) throw new Error('Failed to fetch gateway details')
    const data = await res.json()

    selectedGateway.value = {
      ...data.data,
      name: gatewayName,
    }
  } catch (err) {
    console.error(err)
    toast.error('Failed to load gateway configuration')
  } finally {
    gatewayLoading.value = false
  }
}

onMounted(initCheckout)
const handlePaystackSuccess = async (response) => {
  try {
    const token = localStorage.getItem('token')
    const gateway = selectedGateway.value
    const orderPayload = {
      reference: response.reference,
      items: cartStore.items,
      total_amount: cartTotal.value,
    }
    console.log(response)

    const res = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/checkout/paystack/${response.reference}/${gateway.orderId}`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(orderPayload),
      }
    )

    if (!res.ok) throw new Error('Failed to save order')
    await res.json()

    toast.success('Payment successful & order saved!')
    cartStore.clearCart()
  } catch (err) {
    console.error(err)
    toast.error('Payment successful but failed to save order')
  }
}


const handleFlutterSuccess = async (response) => {
  try {
    const token = localStorage.getItem('token')
    const orderPayload = {
      reference: response.reference,
      items: cartStore.items,
      total_amount: cartTotal.value,
    }

    const res = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/checkout/paystack/${response.reference}`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(orderPayload),
      }
    )

    if (!res.ok) throw new Error('Failed to save order')
    await res.json()

    toast.success('Payment successful & order saved!')
    cartStore.clearCart()
  } catch (err) {
    console.error(err)
    toast.error('Payment successful but failed to save order')
  }
}


// --- Handle Payment ---
const handlePayment = () => {
  if (!cartStore.items.length) {
    toast.error('Cart is empty!')
    return
  }
  if (!paymentMethod.value) {
    toast.error('Please select a payment method')
    return
  }

  if (paymentMethod.value === 'paystack') {
    payWithPaystack()
  } else if (paymentMethod.value === 'flutterwave') {
    payWithFlutterwave()
  } else {
    toast.error('Unsupported payment method')
  }
}

// --- Paystack ---
const payWithPaystack = () => {

  const gateway = selectedGateway.value
  if (!cartStore.items.length) {
    toast.error('Cart is empty!')
    return
  }
  if (gateways.value[0].name !== 'paystack') {
    toast.error('Paystack is not available')
    return
  }
  console.log(gateway)

  const handler = PaystackPop.setup({
    key: gateway.gws.public_key,
    email: user.value.email || 'customer@fursaenergy.com',
    amount: parseInt(cartTotal.value) * 100,
    currency: 'NGN',
    ref: gateway.trans_ref,//'' + Math.floor(Math.random() * 1000000000 + 1),

    callback: (response) => handlePaystackSuccess(response),
    onClose: () => toast.info('Transaction cancelled'),
  })

  handler.openIframe()
}

// --- Flutterwave ---
const payWithFlutterwave = () => {
  const gateway = gateways.value.find(g => g.name === 'flutterwave')
  if (!gateway) {
    toast.error('Flutterwave not available')
    return
  }

  FlutterwaveCheckout({
    public_key: gateway.public_key,
    tx_ref: Date.now(),
    amount: cartTotal.value,
    currency: 'NGN',
    payment_options: 'card,ussd,banktransfer',
    customer: {
      email: user.value.email,
      name: user.value.name,
    },
    callback: (response) => handleFlutterSuccess(response),
    onclose: () => toast.info('Transaction cancelled'),
  })
}
</script>
