import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

export const useCartStore = defineStore('cart', () => {
  const toast = useToast()
  const items = ref([])
  const couponCode = ref('')
  const discount = ref(0)

  // Load cart from localStorage on init
  const savedCart = localStorage.getItem('cart')
  if (savedCart) {
    items.value = JSON.parse(savedCart)
  }

  const itemCount = computed(() => items.value.reduce((total, item) => total + item.quantity, 0))

  const subtotal = computed(() =>
    items.value.reduce((total, item) => total + item.price * item.quantity, 0),
  )

  const shipping = computed(() => {
    // Free shipping above 50000
    return subtotal.value > 50000 ? 0 : 2500
  })

  const tax = computed(() => {
    // 7.5% VAT
    return subtotal.value * 0.075
  })

  const totalPrice = computed(() => subtotal.value + shipping.value + tax.value - discount.value)

  function addItem(product, quantity = 1) {
    const existingItem = items.value.find((item) => item.id === product.id)

    if (existingItem) {
      existingItem.quantity += quantity
      toast.success(`Updated quantity for ${product.name}`)
    } else {
      items.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        sku: product.sku,
        image: product.image,
        volume: product.volume || '5 Litres',
        quantity,
      })
      toast.success(`${product.name} added to cart`)
    }

    saveCart()
  }

  function removeItem(productId) {
    const index = items.value.findIndex((item) => item.id === productId)
    if (index > -1) {
      const item = items.value[index]
      items.value.splice(index, 1)
      toast.success(`${item.name} removed from cart`)
      saveCart()
    }
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find((item) => item.id === productId)
    if (item && quantity > 0) {
      item.quantity = parseInt(quantity)
      saveCart()
    }
  }

  function clearCart() {
    items.value = []
    discount.value = 0
    couponCode.value = ''
    saveCart()
  }

  function applyCoupon(code) {
    // Mock coupon validation
    if (code === 'SAVE10') {
      discount.value = subtotal.value * 0.1
      couponCode.value = code
      toast.success('Coupon applied! 10% discount added')
      return true
    } else if (code === 'SAVE20') {
      discount.value = subtotal.value * 0.2
      couponCode.value = code
      toast.success('Coupon applied! 20% discount added')
      return true
    } else {
      toast.error('Invalid coupon code')
      return false
    }
  }

  function removeCoupon() {
    discount.value = 0
    couponCode.value = ''
    toast.info('Coupon removed')
  }

  function saveCart() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  return {
    items,
    itemCount,
    subtotal,
    shipping,
    tax,
    totalPrice,
    discount,
    couponCode,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    applyCoupon,
    removeCoupon,
  }
})
