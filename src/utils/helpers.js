/**
 * Format currency with Naira symbol
 */
export function formatCurrency(amount) {
  return `₦${amount.toLocaleString('en-NG')}`
}

const storageUrl = import.meta.env.VITE_STORAGE_URL;
export function getImageUrl(path) {
  if (!path) return '/images/oil-droplet.jpg';
  return `${storageUrl}${path.startsWith('/') ? path.slice(1) : path}`
}

export function handleImageError(e) {
  e.target.src = '/images/oil-droplet.jpg';
};

export const toUcwords = (text) => {
  if (!text) return ''
  return text
    .toLowerCase()
    .replace(/\b\w/g, char => char.toUpperCase())
}



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







/**
 * Format date to readable string
 */
export function formatDate(date, format = 'short') {
  const options =
    format === 'short'
      ? { year: 'numeric', month: 'short', day: 'numeric' }
      : { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }

  return new Date(date).toLocaleDateString('en-NG', options)
}

/**
 * Truncate text to specified length
 */
export function truncateText(text, length = 100) {
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

/**
 * Generate order number
 */
export function generateOrderNumber() {
  const prefix = 'ORD'
  const timestamp = Date.now().toString(36).toUpperCase()
  const random = Math.random().toString(36).substring(2, 5).toUpperCase()
  return `${prefix}-${timestamp}-${random}`
}

/**
 * Calculate discount percentage
 */
export function calculateDiscount(originalPrice, salePrice) {
  const discount = ((originalPrice - salePrice) / originalPrice) * 100
  return Math.round(discount)
}

/**
 * Validate email format
 */
export function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Validate Nigerian phone number
 */
export function isValidPhoneNumber(phone) {
  const phoneRegex = /^(\+234|0)[789][01]\d{8}$/
  return phoneRegex.test(phone.replace(/\s/g, ''))
}

/**
 * Format phone number
 */
export function formatPhoneNumber(phone) {
  const cleaned = phone.replace(/\D/g, '')
  if (cleaned.startsWith('234')) {
    return `+234 ${cleaned.slice(3, 6)} ${cleaned.slice(6, 10)} ${cleaned.slice(10)}`
  }
  if (cleaned.startsWith('0')) {
    return `${cleaned.slice(0, 4)} ${cleaned.slice(4, 7)} ${cleaned.slice(7)}`
  }
  return phone
}

/**
 * Get status color class
 */
export function getStatusColor(status) {
  const colors = {
    pending: 'yellow',
    processing: 'blue',
    shipped: 'indigo',
    delivered: 'purple',
    completed: 'green',
    cancelled: 'red',
    refunded: 'gray',
  }
  return colors[status.toLowerCase()] || 'gray'
}

/**
 * Debounce function
 */
export function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

/**
 * Deep clone object
 */
export function deepClone(obj) {
  return JSON.parse(JSON.stringify(obj))
}

/**
 * Check if object is empty
 */
export function isEmpty(obj) {
  return Object.keys(obj).length === 0
}

/**
 * Generate unique ID
 */
export function generateId() {
  return Date.now().toString(36) + Math.random().toString(36).substr(2)
}
