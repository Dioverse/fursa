import { computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'

export function useCart() {
  const cartStore = useCartStore()
  const toast = useToast()

  const items = computed(() => cartStore.items)
  const itemCount = computed(() => cartStore.itemCount)
  const subtotal = computed(() => cartStore.subtotal)
  const total = computed(() => cartStore.totalPrice)
  const isEmpty = computed(() => cartStore.itemCount === 0)

  function addToCart(product, quantity = 1) {
    cartStore.addItem(product, quantity)
  }

  function removeFromCart(productId) {
    cartStore.removeItem(productId)
  }

  function updateQuantity(productId, quantity) {
    if (quantity < 1) {
      removeFromCart(productId)
    } else {
      cartStore.updateQuantity(productId, quantity)
    }
  }

  function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
      cartStore.clearCart()
      toast.info('Cart cleared')
    }
  }

  function applyCoupon(code) {
    return cartStore.applyCoupon(code)
  }

  return {
    items,
    itemCount,
    subtotal,
    total,
    isEmpty,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    applyCoupon,
  }
}
