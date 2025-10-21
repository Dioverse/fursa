<template>
    <div class="">
        <div class="flex items-center gap-4 py-4 border-b">
            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                <img v-lazy="getImageUrl(item.product.images[0]?.path)" :alt="item.product.name"
                    class="w-full h-full object-cover rounded" @error="handleImageError">
            </div>

            <div class="flex-1">
                <h4 class="font-semibold line-clamp-1">{{ item.product.name }}</h4>
                <small class="text-gray-600 text-xs line-clamp-1">#{{ item.product.sku }}</small>
            </div>

            <div class="flex flex-col font-semibold">
                <span class="">₦{{ Number((item.product?.discounted_price ?? item.product?.price) ||
                    0).toFixed(2) }}</span>
                <small v-if="item.product?.discount"
                    class="text-gray-400 text-right text-sm font-normal line-through m-0">₦{{ Number(item.product?.price
                        ||
                        0).toFixed(2) }}</small>
            </div>
        </div>
        <div class="flex items-center gap-4 py-4 border-b justify-between">
            <button @click="removeItem(item.product.id)" class="p-2 text-red-500 rounded text-sm hover:text-red-700 hover:bg-red-300 transition">
                <font-awesome-icon icon="trash" /> Remove
            </button>
            <div class="flex items-center rounded overflow-hidden w-max">
                <!-- Decrement -->
                <button :disabled="getCartQuantity(item.product.id).value <= 1 || loadingStates[item.product.id]"
                    @click="updateQuantity(item.product, getCartQuantity(item.product.id).value - 1)"
                    class="px-[14px] py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                    -
                </button>

                <!-- Quantity Display / Loader -->
                <div
                    class="w-[50px] text-center border-x border-gold-300 text-sm flex justify-center items-center h-[38px]">
                    <div v-if="loadingStates[item.product.id]" class="flex justify-center items-center">
                        <svg class="animate-spin h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                    <div v-else contenteditable="true" class="w-full text-center outline-none"
                        :data-product-id="item.product.id" @blur="onQuantityBlur($event, item.product)"
                        @keydown.enter.prevent="onQuantityEnter($event, item.product)">
                        {{ getCartQuantity(item.product.id).value }}
                    </div>
                </div>

                <!-- Increment -->
                <button :disabled="loadingStates[item.product.id]"
                    @click="updateQuantity(item.product, getCartQuantity(item.product.id).value + 1)"
                    class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
                    +
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import { getCartQuantity, onQuantityBlur, onQuantityEnter, updateQuantity, loadingStates, removeItem } from '@/utils/neut';
import { getImageUrl, handleImageError } from '@/utils/helpers';

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})
</script>