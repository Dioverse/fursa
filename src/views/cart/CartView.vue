<template>
  <DefaultLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Cart</h1>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-10">
        <font-awesome-icon icon="spinner" spin class="text-primary text-3xl" />
        <p class="mt-3 text-gray-600">Loading your cart...</p>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartStore.itemCount === 0">
        <EmptyCart />
      </div>

      <!-- Cart Content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Products</h2>

            <div class="divide-y">
              <CartItem
                v-for="item in cartStore.items"
                :key="item.id"
                :item="item"
              />
            </div>

            <!-- Coupon -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-2 mb-3">
                <font-awesome-icon icon="tag" class="text-primary" />
                <span class="font-semibold">Apply Coupon</span>
              </div>
              <div class="flex gap-2">
                <input
                  v-model="couponCode"
                  type="text"
                  placeholder="Enter coupon code"
                  class="flex-1 px-4 py-2 border rounded"
                />
                <button
                  @click="applyCoupon"
                  class="px-6 py-2 bg-primary text-white rounded hover:bg-opacity-90 transition"
                >
                  Apply
                </button>
              </div>
            </div>

            <!-- Continue Shopping -->
            <div class="mt-6 flex justify-between">
              <RouterLink
                to="/shop"
                class="inline-flex items-center gap-2 text-primary hover:underline"
              >
                <font-awesome-icon icon="arrow-left" />
                <span>Continue Shopping</span>
              </RouterLink>

              <button @click="clearCart" class="text-red-600 hover:underline">
                Clear Cart
              </button>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
          <CartSummary />
        </div>
      </div>
    </div>

    <!-- Bottom CTA -->
    <CTA />

    <!-- Brochure -->
    <Brochure />
  </DefaultLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import CartItem from '@/components/cart/CartItem.vue'
import CartSummary from '@/components/cart/CartSummary.vue'
import EmptyCart from '@/components/cart/EmptyCart.vue'
import Brochure from '@/components/common/Brochure.vue'
import CTA from '@/components/common/CTA.vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const cartStore = useCartStore()
const authStore = useAuthStore()
const toast = useToast()

const couponCode = ref('')
const loading = ref(false)

const loadCart = async () => {
  loading.value = true
  try {
    if (authStore.isAuthenticated) {
      // Fetch cart from server
      const { data } = await api.get('/carts')

      if (Array.isArray(data) && data.length > 0) {
        cartStore.items = data.map((item) => {
          const product = item.product || {}
          const image =
            product.images && product.images.length > 0
            //   ? `${import.meta.env.VITE_API_BASE_URL}/storage/${product.images[0].path}`
              ? `${product.images[0].path}`
              : '/images/oil-droplet.jpg'

          return {
            id: Number(product.id),
            name: product.name || 'Unnamed Product',
            price: Number(product.discounted_price ?? product.price ?? 0),
            quantity: Number(item.quantity ?? 1),
            sku: product.slug || '',
            image,
            volume: product.volume || '',
          }
        })
        cartStore.saveCart()
        toast.success('Cart loaded from server')
      } else {
        cartStore.clearCart()
        toast.info('Your cart is empty')
      }
    } else {
      // Load from localStorage
      cartStore.loadCart()
      if (cartStore.items.length > 0) {
        toast.info('Loaded cart from local storage')
      }
    }
  } catch (error) {
    console.error('Error loading cart:', error)
    toast.error('Unable to load cart. Please try again.')
  } finally {
    loading.value = false
  }
}

const applyCoupon = () => {
  if (couponCode.value.trim().toUpperCase() === 'SAVE10') {
    toast.success('Coupon applied successfully! 10% discount added.')
  } else {
    toast.error('Invalid coupon code')
  }
  couponCode.value = ''
}

const clearCart = () => {
  if (confirm('Are you sure you want to clear your cart?')) {
    cartStore.clearCart()
    toast.success('Cart cleared')
  }
}

onMounted(() => {
  loadCart()
})
</script>
