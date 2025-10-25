<template>
    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
        <h3 class="text-xl font-semibold mb-4">{{ title }}</h3>

        <div class="space-y-3 mb-6">
            <div class="flex justify-between">
                <span class="text-gray-600">{{ $t('cart.total') }}</span>
                <span class="font-semibold">₦{{ formatAmount(totalAtOriginalPrice) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">{{ $t('cart.discount') }}</span>
                <span class="font-semibold text-[green]">₦{{ formatAmount(totalSaved) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">{{ $t('cart.subtotal') }}</span>
                <span class="font-semibold">₦{{ formatAmount(subtotal) }}</span>
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
                <span>{{ $t('cart.discount') }}</span>
                <span>-₦{{ formatAmount(discount) }}</span>
            </div>
        </div>

        <div class="border-t pt-4 mb-6">
            <div class="flex justify-between text-xl font-bold">
                <span>{{ $t('cart.total') }}</span>
                <span class="text-primary">₦{{ formatAmount(total) }}</span>
            </div>
        </div>

        <BaseButton @click="proceedToCheckout" variant="primary" size="lg" fullWidth icon="arrow-right">
            {{ $t('cart.proceed_to_checkout') }}
        </BaseButton>

        <div class="mt-4 text-center">
            <RouterLink to="/shop" class="text-primary hover:underline text-sm">
                {{ $t('cart.continue_shopping') }}
            </RouterLink>
        </div>

        <!-- Payment Methods -->
        <div class="mt-6 pt-6 border-t">
            <p class="text-sm text-gray-600 mb-3">{{ $t('cart.we_accept') }}</p>
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
import { formatAmount } from '@/utils/helpers'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
        toast.success(t('cart.toasts.coupon_applied'))
    } else {
        toast.error(t('cart.toasts.coupon_invalid'))
    }
}

const proceedToCheckout = () => {
    if (cartStore.itemCount === 0) {
        toast.warning(t('cart.toasts.cart_empty'))
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