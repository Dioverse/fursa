<template>
  <div
    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 group relative"
  >
    <!-- 🔹 Loader Overlay -->
    <div
      v-if="loading"
      class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-20"
    >
      <div
        class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"
      ></div>
    </div>

    <div class="relative">
      <img
        v-if="product.image"
        :src="product.image"
        :alt="product.name"
        class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div
        v-else
        class="w-full h-48 bg-gray-200 flex items-center justify-center"
      >
        <font-awesome-icon icon="image" size="3x" class="text-gray-400" />
      </div>

      <!-- Wishlist Button -->
      <button
        @click="toggleWishlist"
        class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:shadow-lg transition"
      >
        <font-awesome-icon
          icon="heart"
          :class="isInWishlist ? 'text-red-500' : 'text-gray-400'"
        />
      </button>

      <!-- Quick View Button -->
      <button
        @click="quickView"
        class="absolute inset-0 bg-black bg-opacity-50 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"
      >
        <span class="bg-primary px-4 py-2 rounded">Quick View</span>
      </button>
    </div>

    <div class="p-4">
      <h3 class="font-semibold text-lg mb-1 line-clamp-2">
        <a :href="`/product/${product.slug}`">{{ product.name }}</a>
      </h3>
      <p class="text-gray-600 text-sm mb-2">
        {{ product.volume || '5 Litres' }}
      </p>
      <p class="text-gray-500 text-xs mb-3">SKU: {{ product.sku }}</p>

      <div class="flex items-center justify-between mb-3">
        <span class="text-2xl font-bold text-primary">
          ₦{{ product.price.toLocaleString() }}
        </span>
        <div class="flex items-center gap-1">
          <font-awesome-icon
            v-for="i in 5"
            :key="i"
            icon="star"
            :class="i <= product.rating ? 'text-yellow-400' : 'text-gray-300'"
            size="sm"
          />
        </div>
      </div>

      <BaseButton
        @click="handleAddToCart"
        variant="primary"
        fullWidth
        icon="shopping-cart"
        :disabled="loading"
      >
        {{ loading ? "Adding..." : "Add to Cart" }}
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useCartStore } from "@/stores/cart";
import { useToast } from "vue-toastification";
import BaseButton from "@/components/common/BaseButton.vue";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const cartStore = useCartStore();
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

// ✅ Toggle wishlist add/remove
const toggleWishlist = () => {
  let wishlist = loadWishlist();

  if (isInWishlist.value) {
    wishlist = wishlist.filter((item) => item.id !== props.product.slug);
    toast.success("Removed from wishlist");
  } else {
    wishlist.push(props.product);
    toast.success("Added to wishlist");
  }

  saveWishlist(wishlist);
  isInWishlist.value = !isInWishlist.value;
};

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

const quickView = () => {
  console.log("Quick view:", props.product);
};
</script>
