<template>
    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
        <h3 class="text-xl font-semibold mb-4">{{ title }}</h3>

        <div class="space-y-3 mb-6">
            <div class="flex justify-between">
                <span class="text-gray-600">Total</span>
                <span class="font-semibold">₦{{ totalAtOriginalPrice.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Discount</span>
                <span class="font-semibold text-[green]">₦{{ totalSaved.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold">₦{{ subtotal.toFixed(2) }}</span>
            </div>

            <!-- <div class="pt-3 border-t">
                <div class="flex items-center gap-2 mb-3">
                    <font-awesome-icon icon="tag" class="text-primary" />
                    <span class="text-sm">Have a coupon?</span>
                </div>
                <div class="flex gap-2">
                    <input v-model="couponCode" type="text" placeholder="Enter code"
                        class="flex-1 px-3 py-2 border rounded">
                    <button @click="applyCoupon" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
                        Apply
                    </button>
                </div>
            </div> -->

            <div v-if="discount > 0" class="flex justify-between text-green-600">
                <span>Discount</span>
                <span>-₦{{ discount.toFixed(2) }}</span>
            </div>
        </div>

        <div class="border-t pt-4 mb-6">
            <div class="flex justify-between text-xl font-bold">
                <span>Total</span>
                <span class="text-primary">₦{{ total.toFixed(2) }}</span>
            </div>
        </div>

        <BaseButton @click="proceedToCheckout" variant="primary" size="lg" fullWidth icon="arrow-right">
            Proceed to Checkout
        </BaseButton>

        <div class="mt-4 text-center">
            <RouterLink to="/shop" class="text-primary hover:underline text-sm">
                Continue Shopping
            </RouterLink>
        </div>

        <!-- Payment Methods -->
        <div class="mt-6 pt-6 border-t">
            <p class="text-sm text-gray-600 mb-3">We Accept:</p>
            <div class="flex gap-2">
                <img src="/images/visa.png" alt="Visa" class="h-8">
                <img src="/images/mastercard.png" alt="Mastercard" class="h-8">
                <img src="/images/paypal.png" alt="PayPal" class="h-8">
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'
import BaseButton from '@/components/common/BaseButton.vue'

const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const couponCode = ref('')
const discount = ref(0)

const subtotal = computed(() => cartStore.subtotal)
const totalSaved = computed(() => cartStore.totalSaved)
const totalAtOriginalPrice = computed(() => cartStore.totalAtOriginalPrice)
const shipping = computed(() => cartStore.shipping)
const tax = computed(() => cartStore.tax)
const total = computed(() => cartStore.totalPrice)

const applyCoupon = () => {
    if (couponCode.value === 'SAVE10') {
        discount.value = subtotal.value * 0.1
        toast.success('Coupon applied successfully!')
    } else {
        toast.error('Invalid coupon code')
    }
}

const proceedToCheckout = () => {
    if (cartStore.itemCount === 0) {
        toast.warning('Your cart is empty')
        return
    }

    router.push('/checkout')
}

const props = defineProps({
    title: {
        type: String,
        required: true
    }
})
</script>