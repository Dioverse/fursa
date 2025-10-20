import { useCartStore } from '@/stores/cart';
import { useWishlistStore } from '@/stores/wishlist';
import { ref } from 'vue';
export const loadingStates = ref({});
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
export function isInCart(productId) {
  return cartStore.items.some(item => (item.product_id || item.id) === productId);
};

export function getCartQuantity(productId) {
  const item = cartStore.items.find(i => (i.product_id || i.id) === productId);
  return item ? item.quantity : 0;
};

// Quantity input handlers
let isEnterPressed = false;

export const onQuantityEnter = (e, product) => {
    isEnterPressed = true;
    
    updateOrReturnQty(e, product)
    e.target.blur();

    setTimeout(() => {
        isEnterPressed = false;
    }, 100); 
};

export const onQuantityBlur = (e, product) => {
    // 1. Check the flag
    if (isEnterPressed) return;
    
    updateOrReturnQty(e, product)
    e.target.blur();
};

function updateOrReturnQty(e,product) {
  const newQty = parseInt(e.target.innerText, 10);
    if (!isNaN(newQty) && newQty !== getCartQuantity(product.id)) {
      updateQuantity(product, newQty, e.target);
    } else {
      e.target.innerText = getCartQuantity(product.id);
    }
}

// Disable all buttons/links globally during update
export function togglePageInteractivity(disable) {
  const elements = document.querySelectorAll('button, a')
  elements.forEach(el => {
    if (disable) el.setAttribute('disabled', true)
    else el.removeAttribute('disabled')
  })

}

// Cart actions
export const addToCart = async (product) => {
  loadingStates.value[product.id] = true;
  togglePageInteractivity(true)
  try {
    await cartStore.addItem(product, 1);
  } catch (error) {
    console.error('Error adding to cart:', error);
  } finally {
    loadingStates.value[product.id] = false;
  }
  togglePageInteractivity(false)
};

export const updateQuantity = async (product, quantity, e = null) => {
  loadingStates.value[product.id] = true;
  togglePageInteractivity(true)
  // await new Promise((resolve) => {setTimeout(() => {}, 3000)})
  try {
    if (quantity <= 0) {
      removeItem(product.id);
    } else {
      cartStore.updateQuantity(product.id, quantity, e);
    }
  } catch (error) {
    console.error('Error updating quantity:', error);
  } finally {
    loadingStates.value[product.id] = false;
  }
  togglePageInteractivity(false)
};

export function removeItem(id) {
    cartStore.removeItem(id)
}

// Wishlist actions
export function toggleWishlist(product) {
  const exists = wishlistStore.items.find(p => p.id === product.id);
  if (exists) {
    wishlistStore.remove(product.id);
  } else {
    wishlistStore.add(product);
  }
};

