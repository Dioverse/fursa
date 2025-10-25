<template>
  <ShopLayout :bgColor="`bg-white`">
    <div class="min-h-screen mx-auto bg-gray-50 sm:container md:container lg:container lg:px-20 px-3 bg-transparent">
      <h1 class="text-3xl font-bold my-8">{{ $t('cart.title') }}</h1>

      <!-- Loading -->
      <div v-if="cartStore.loading" class="text-center py-10">
        <font-awesome-icon icon="spinner" spin class="text-primary text-3xl" />
  <p class="mt-3 text-gray-600">{{ $t('cart.loading') }}</p>
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
            <h2 class="text-xl font-semibold mb-4">{{ $t('cart.products') }}</h2>

            <div class="divide-y">
              <CartItem
                v-for="item in cartStore.items"
                :key="item.id"
                :item="item"
              />
            </div>

            <!-- Coupon -->
            <!-- <div class="mt-6 p-4 bg-gray-50 rounded-lg">
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
            </div> -->

            <!-- Continue Shopping -->
            <div class="mt-6 flex justify-between">
              <RouterLink
                to="/shop"
                class="inline-flex items-center gap-2 text-primary hover:underline"
              >
                <font-awesome-icon icon="arrow-left" />
                <span>{{ $t('cart.continue_shopping') }}</span>
              </RouterLink>

              <button @click="clearCart" class="text-red-600 hover:underline">
                {{ $t('cart.clear_cart') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
          <CartSummary :title="$t('cart.summary')" />
        </div>
      </div>
    </div>

    <!-- Bottom CTA -->
    <CTA />
    <!-- Brochure -->
    <Brochure />
  </ShopLayout>
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
import ShopLayout from '@/layouts/ShopLayout.vue'
import { useI18n } from 'vue-i18n'

const cartStore = useCartStore()
const authStore = useAuthStore()
const toast = useToast()
const couponCode = ref('')
const { t } = useI18n()

// // --- Apply Coupon ---
// const applyCoupon = () => {
//   if (!couponCode.value.trim()) return toast.error('Enter a coupon code')
//   cartStore.applyCoupon(couponCode.value.trim().toUpperCase())
//   couponCode.value = ''
// }

// --- Clear Cart ---
const clearCart = () => {
  if (confirm(t('cart.toasts.clear_confirm'))) {
    cartStore.clearCart()
  }
}

// --- Load Cart on Mount ---
onMounted(async () => {
  if (authStore.isAuthenticated) {
    await cartStore.items
  }
})
</script>
