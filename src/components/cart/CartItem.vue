<template>
    <div class="flex items-center gap-4 py-4 border-b">
        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
            <img v-if="item.product.image" :src="getProductImage(item.product)" :alt="item.product.name"
                class="w-full h-full object-cover rounded">
            <font-awesome-icon v-else icon="image" size="2x" class="text-gray-400" />
        </div>

        <div class="flex-1">
            <h4 class="font-semibold line-clamp-1">{{ item.product.name }}</h4>
            <small class="text-gray-600 text-xs line-clamp-1">#{{ item.product.sku }}</small>
        </div>

        <div class="flex flex-col text-lg font-semibold">
            <span class="text-sm">₦{{ Number((item.product?.discounted_price ?? item.product?.price) ||
                0).toLocaleString() }}</span>
            <small v-if="item.product?.discount"
                class="text-gray-400 text-right text-xs font-normal line-through m-0">₦{{ Number(item.product?.price ||
                    0).toLocaleString() }}</small>
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

        <div class="text-lg font-bold text-primary text-sm">
            ₦{{ ((item.product.discounted_price ?? item.product.price) * item.quantity).toLocaleString() }}
        </div>

        <button @click="removeItem" class="text-red-500 text-sm hover:text-red-700 transition">
            <font-awesome-icon icon="trash" />
        </button>
    </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const cartStore = useCartStore()
const toast = useToast()

// const updateQuantity = (newQuantity) => {
//     const quantity = parseInt(newQuantity)
//     if (quantity > 0) {
//         cartStore.updateQuantity(props.item.product.id, quantity)
//     }
// }
const loadingStates = ref({});

const updateQuantity = async (quantity) => {
    const qty = parseInt(quantity);
    if (isNaN(qty)) return;

    loadingStates.value = true;
    try {
        if (qty <= 0) {
            cartStore.removeItem(props.item.product.id);
        } else {
            cartStore.updateQuantity(props.item.product.id, qty);
        }
    } catch (err) {
        console.error(err);
    } finally {
        loadingStates.value = false;
    }
};

const removeItem = () => {
    cartStore.removeItem(props.item.product.id)
    // toast.success('Item removed from cart')
}

const getProductImage = (product) => {
    if (product.images && product.images.length > 0) {
        return storageUrl + product.images[0].path;
    }
    return '/placeholder.png';
};



</script>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>