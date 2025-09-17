<template>
  <DefaultLayout>
    <div class="container mx-auto px-4 py-8">
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
            <h2 class="text-xl font-semibold mb-4">Payment Method</h2>

            <!-- Payment Option -->
            <div class="space-y-3">
              <label
                class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="card"
                  class="text-primary focus:ring-primary"
                />
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <span class="font-medium">Pay with Card</span>
                    <img src="/images/visa.png" alt="Visa" class="h-6" />
                    <img src="/images/mastercard.png" alt="Mastercard" class="h-6" />
                  </div>
                </div>
              </label>
            </div>

            <!-- Pay Now -->
            <button
              @click="payWithPaystack"
              class="mt-6 w-full bg-primary text-white py-2 rounded hover:bg-opacity-90 transition"
            >
              Pay ₦{{ cartTotal.toLocaleString() }}
            
            </button>
          </div>
        </div>

        <!-- Cart Summary (re-use same component as in CartView) -->
        <div class="lg:col-span-1">
          <CartSummary />
        </div>
      </div>
    </div>

    <!-- Bottom CTA Section -->
    <CTA />
    <Brochure />
  </DefaultLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
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

// State
const paymentMethod = ref('card')
const showAddressModal = ref(false)
const hasAddress = ref(false) // later connect to user profile/address store

// Cart values
const cart = cartStore
const discount = ref(0)

const subtotal = computed(() => cartStore.totalPrice)
const shipping = computed(() => subtotal.value > 50000 ? 0 : 2500)
const tax = computed(() => subtotal.value * 0.075) // 7.5% VAT
const cartTotal = computed(() => subtotal.value + shipping.value + tax.value - discount.value)


const handlePaystackSuccess = async (response) => {
  try {
    const token = localStorage.getItem('token')
    const orderPayload = {
      reference: response.reference,
      items: cart.items,
      total_amount: cartTotal.value
    }

    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/orders`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`
      },
      body: JSON.stringify(orderPayload)
    })

    if (!res.ok) throw new Error('Failed to save order')

    await res.json()
    toast.success('Payment successful & order saved!')
    cart.clearCart()
  } catch (err) {
    console.error(err)
    toast.error('Payment successful but failed to save order')
  }
}


// Paystack Payment
const payWithPaystack = () => {
  if (!cart.items.length) {
    toast.error('Cart is empty!')
    return
  }

  const handler = PaystackPop.setup({
    key: import.meta.env.VITE_PAYSTACK_PUBLIC_KEY,
    email: user.value.email || 'customer@example.com',
    amount: parseInt(cartTotal.value) * 100, // Paystack expects kobo
    currency: 'NGN',
    ref: '' + Math.floor(Math.random() * 1000000000 + 1),

    callback: function (response) {
      handlePaystackSuccess(response)
    },

    onClose: function () {
      toast.info('Transaction cancelled')
    }
  })

  handler.openIframe()
}
</script>