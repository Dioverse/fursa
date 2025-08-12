<template>
    <DefaultLayout>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-8">Checkout</h1>

            <!-- No Address Warning -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <font-awesome-icon icon="map-marker-alt" class="text-yellow-600 mt-1" />
                    <div class="flex-1">
                        <p class="font-semibold text-yellow-800">No Address Saved</p>
                        <p class="text-yellow-700 text-sm mt-1">
                            Add an address so we can get/deliver on the delivery
                        </p>
                        <button @click="showAddressModal = true"
                            class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
                            Add new Location
                        </button>
                    </div>
                </div>
            </div>

            <!-- Check Products Notice -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <font-awesome-icon icon="check-circle" class="text-green-600 mt-1" />
                    <div>
                        <p class="font-semibold text-green-800">Check your product before checkout</p>
                        <p class="text-green-700 text-sm mt-1">
                            Ensure every detail is perfect before completing your purchase
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">Choose how to pay</h2>

                        <div class="space-y-3">
                            <label
                                class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input v-model="paymentMethod" type="radio" value="card"
                                    class="text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">Pay with Card</span>
                                        <img src="/images/visa.png" alt="Visa" class="h-6">
                                        <img src="/images/mastercard.png" alt="Mastercard" class="h-6">
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    formData: {
        type: Object,
        required: true
    }
})

const agreed = ref(false)

defineExpose({ agreed })
</script>