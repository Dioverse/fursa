<template>
    <div class="flex items-center gap-4 py-4 border-b">
        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
            <img v-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover rounded">
            <font-awesome-icon v-else icon="image" size="2x" class="text-gray-400" />
        </div>

        <div class="flex-1">
            <h3 class="font-semibold">{{ item.name }}</h3>
            <p class="text-sm text-gray-600">SKU: {{ item.sku }}</p>
            <p class="text-sm text-gray-600">{{ item.volume || '5 Litres' }}</p>
        </div>

        <div class="text-lg font-semibold">
            ₦{{ item.price.toLocaleString() }}
             <!-- ₦{{ (item.price)}}  -->
        </div>

        <div class="flex items-center gap-2">
            <button @click="updateQuantity(item.quantity - 1)"
                class="w-8 h-8 rounded border hover:bg-gray-100 transition flex items-center justify-center"
                :disabled="item.quantity <= 1">
                <font-awesome-icon icon="minus" size="sm" />
            </button>
            <input :value="item.quantity" @change="updateQuantity($event.target.value)" type="number" min="1"
                class="w-16 text-center border rounded px-2 py-1">
            <button @click="updateQuantity(item.quantity + 1)"
                class="w-8 h-8 rounded border hover:bg-gray-100 transition flex items-center justify-center">
                <font-awesome-icon icon="plus" size="sm" />
            </button>
        </div>

        <div class="text-lg font-bold text-primary">
            ₦{{ (item.price * item.quantity).toLocaleString() }}
        </div>

        <button @click="removeItem" class="text-red-500 hover:text-red-700 transition">
            <font-awesome-icon icon="trash" />
        </button>
    </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const cartStore = useCartStore()
const toast = useToast()

const updateQuantity = (newQuantity) => {
    const quantity = parseInt(newQuantity)
    if (quantity > 0) {
        cartStore.updateQuantity(props.item.id, quantity)
    }
}

const removeItem = () => {
    cartStore.removeItem(props.item.id)
    // toast.success('Item removed from cart')
}




</script>