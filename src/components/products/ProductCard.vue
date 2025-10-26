<template>
  <div
    class="bg-white rounded-lg shadow-md overflow-hidden max-w-64 m-auto hover:shadow-xl transition-shadow duration-300 group relative">
    <!-- 🔹 Loader Overlay -->
    <div v-if="loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-20">
      <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div class="relative">
      <img v-lazy="getImageUrl(product.image)" :alt="product.name"
        class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
        @error="handleImageError" />

      <!-- Quick View Button -->
      <button @click="quickView"
        class="absolute inset-0 bg-black bg-opacity-50 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <span class="bg-primary px-4 py-2 rounded">{{ $t('shop.quick_view') }}</span>
      </button>
      <span v-if="product.discount"
        class="absolute top-2 right-2 bg-mprimary-500 text-white px-2 py-1 text-xs rounded font-semibold">
        -{{ discountLabel(product) }}
      </span>
    </div>

    <div class="p-4">
      <h3 class="font-semibold text-xs sm:text-md md:text-[14px] lg:text-[14px] mb-1 line-clamp-1">
        <a :href="`/product/${product.slug}`" :title="product.name">{{ product.name }}</a>
      </h3>
      <p class="text-gray-500 text-xs mb-3">#{{ product.sku }}</p>

      <div class="flex items-center justify-between mb-3">
        <span class="text-lg font-bold text-primary">
          ₦ {{ getDisplayPrice(product) }}
          <span v-if="product.discount" class="line-through text-sm text-gray-400">₦ {{ priceToLocale(getBasePrice(product)) }}</span>
        </span>
      </div>

      <div class="flex w-full space-x-2">
        <!-- Add To Cart -->
        <button v-if="!isInCart(product.id).value" @click="addToCart(product)" :disabled="loadingStates[product.id]"
          class="flex-1 bg-gold-500 text-white py-2 disabled:opacity-50 disabled:cursor-not-allowed rounded hover:bg-gold-100 hover:text-black text-xs font-semibold">
          {{ loadingStates[product.id] ? $t('shop.adding') : $t('shop.add_to_cart') }}
        </button>

        <!-- Quantity Control -->
        <div v-else class="flex items-center rounded overflow-hidden w-max">
          <button :disabled="getCartQuantity(product.id) <= 1 || loadingStates[product.id]"
            @click="updateQuantity(product, getCartQuantity(product.id) - 1)"
            class="px-[14px] py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
            -
          </button>

          <div class="w-12 text-center border-x border-gold-300 text-sm flex justify-center items-center h-[38px]">
            <div v-if="loadingStates[product.id]" class="flex justify-center items-center">
              <svg class="animate-spin h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
            </div>
            <div v-else contenteditable="true" class="w-full text-center outline-none overflow-hidden"
              :data-product-id="product.id" @blur="onQuantityBlur($event, product)"
              @keydown.enter.prevent="onQuantityEnter($event, product)">
              {{ getCartQuantity(product.id) }}
            </div>
          </div>

          <button :disabled="loadingStates[product.id]"
            @click="updateQuantity(product, getCartQuantity(product.id) + 1)"
            class="px-3 py-2 bg-gold-500 text-white hover:bg-gold-100 hover:text-black disabled:opacity-50 disabled:cursor-not-allowed">
            +
          </button>
        </div>

        <!-- Wishlist -->
        <button @click="toggleWishlist(product)" :class="[
          'w-1/4 py-2 rounded text-sm font-semibold flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed transition-colors',
          wishlistStore.items.find(p => p.id === product.id)
            ? 'bg-red-100 text-red-500 hover:bg-red-500 hover:text-white'
            : 'bg-gold-100 text-mprimary hover:bg-gold-500 hover:text-white'
        ]">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
            :fill="wishlistStore.items.find(p => p.id === product.id) ? 'currentColor' : 'none'" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M21.8 8.6c0 5.4-9.2 11.1-9.8 11.4a1.5 1.5 0 0 1-1 0c-.6-.3-9.8-6-9.8-11.4A5 5 0 0 1 6.8 4.5c1.6 0 3.1.8 4 2a5.1 5.1 0 0 1 4-2 5 5 0 0 1 4.9 4.1z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useCartStore } from "@/stores/cart";
import { useToast } from "vue-toastification";
import { addToCart, getCartQuantity, isInCart, onQuantityBlur, onQuantityEnter, toggleWishlist, updateQuantity, loadingStates } from '@/utils/neut';
import { discountLabel, getBasePrice, getDisplayPrice, priceToLocale, getImageUrl, handleImageError } from "@/utils/helpers";
import { useRouter } from "vue-router";
import { useWishlistStore } from "@/stores/wishlist";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const toast = useToast();
const isInWishlist = ref(false);
const loading = ref(false);

// ✅ Load wishlist and check if product exists
const loadWishlist = () => {
  const storedWishlist = localStorage.getItem("wishlist");
  return storedWishlist ? JSON.parse(storedWishlist) : [];
};

// ✅ Save wishlist
const saveWishlist = (wishlist) => {
  localStorage.setItem("wishlist", JSON.stringify(wishlist));
};

// // ✅ Toggle wishlist add/remove
// const toggleWishlist = () => {
//   let wishlist = loadWishlist();

//   if (isInWishlist.value) {
//     wishlist = wishlist.filter((item) => item.id !== props.product.slug);
//     toast.success("Removed from wishlist");
//   } else {
//     wishlist.push(props.product);
//     toast.success("Added to wishlist");
//   }

//   saveWishlist(wishlist);
//   isInWishlist.value = !isInWishlist.value;
// };

// ✅ Check if product is already in wishlist on mount
onMounted(() => {
  const wishlist = loadWishlist();
  isInWishlist.value = wishlist.some(
    (item) => item.id === props.product.slug
  );
});

// ✅ Handle Add to Cart with Loader
const handleAddToCart = async () => {
  loading.value = true;
  try {
    await cartStore.addItem(props.product);
  } finally {
    loading.value = false;
  }
};

const router = useRouter();

const quickView = () => {
  // Check if the product and slug exist before navigating
  if (props.product && props.product.slug) {
    // 3. Use router.push() to navigate to the dynamic URL
    router.push(`/product/${props.product.slug}`);
  } else {
    console.error("Cannot navigate: Product or product slug is missing.");
  }
};
</script>
