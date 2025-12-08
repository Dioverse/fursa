import { useCartStore } from '@/stores/cart';
import { useWishlistStore } from '@/stores/wishlist';
import { ref, computed } from 'vue';

export const loadingStates = ref({});
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();

// Always return computed properties for true reactivity
export function isInCart(productId) {
  return computed(() => {
    if (!cartStore.items || cartStore.items.length === 0) return false;
    return cartStore.items.some(item => {
      const itemId = item.product_id ?? item.id;
      return itemId && itemId == productId;
    });
  });
}

// Always return computed properties for true reactivity
export function getCartQuantity(productId) {
  return computed(() => {
    if (!cartStore.items || cartStore.items.length === 0) return 0;
    const item = cartStore.items.find(i => {
      const itemId = i.product_id ?? i.id;
      return itemId && itemId == productId;
    });
    return item?.quantity || 0;
  });
}

// Quantity input handlers
let isEnterPressed = false;

export const onQuantityEnter = (e, product) => {
  isEnterPressed = true;
  updateOrReturnQty(e, product);
  e.target.blur();
  setTimeout(() => {
    isEnterPressed = false;
  }, 100);
};

export const onQuantityBlur = (e, product) => {
  if (isEnterPressed) return;
  updateOrReturnQty(e, product);
};

function updateOrReturnQty(e, product) {
  const newQty = parseInt(e.target.innerText, 10);
  const currentQty =
    cartStore.items.find(i => {
      const itemId = i.product_id ?? i.id;
      return itemId && itemId == product.id;
    })?.quantity || 0;

  if (!isNaN(newQty) && newQty !== currentQty && newQty > 0) {
    updateQuantity(product, newQty, e.target);
  } else {
    e.target.innerText = currentQty;
  }
}

// Disable all buttons/links globally during update
let temporarilyDisabled = new WeakSet();

export function togglePageInteractivity(disable) {
  const elements = document.querySelectorAll('button, a');
  elements.forEach(el => {
    if (disable) {
      if (!el.hasAttribute('disabled')) {
        el.setAttribute('disabled', true);
        temporarilyDisabled.add(el);
      }
    } else {
      if (temporarilyDisabled.has(el)) {
        el.removeAttribute('disabled');
        temporarilyDisabled.delete(el);
      }
    }
  });
}

// Cart actions
export const addToCart = async (product) => {
  loadingStates.value[product.id] = true;
  togglePageInteractivity(true);
  try {
    await cartStore.addItem(product, 1);
  } catch (error) {
    console.error('Error adding to cart:', error);
  } finally {
    loadingStates.value[product.id] = false;
    togglePageInteractivity(false);
  }
};

export const updateQuantity = async (product, quantity, e = null) => {
  loadingStates.value[product.id] = true;
  togglePageInteractivity(true);
  try {
    if (quantity <= 0) {
      removeItem(product.id);
    } else {
      await cartStore.updateQuantity(product.id, quantity, e);
    }
  } catch (error) {
    console.error('Error updating quantity:', error);
  } finally {
    loadingStates.value[product.id] = false;
    togglePageInteractivity(false);
  }
};

export function removeItem(id) {
  cartStore.removeItem(id);
}

// Wishlist actions
export function toggleWishlist(product) {
  const exists = wishlistStore.items.find(p => p.id === product.id);
  if (exists) {
    wishlistStore.remove(product.id);
  } else {
    wishlistStore.add(product);
  }
}

export function clearCart() {
  localStorage.removeItem('cart')
  localStorage.removeItem('guestShipping')
  cartStore.items.values = []
}

export function getCartItemsList() {
  return cartStore.getCartItemsList()
}