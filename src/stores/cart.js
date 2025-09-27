import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

// 🔑 API helper
const API_BASE = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'
// 'https://fursa.jarustraining.com.ng/api'

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

  const token = () => localStorage.getItem('token') // function so it’s always fresh

  const itemCount = computed(() => items.value.reduce((t, i) => t + i.quantity, 0))
  const subtotal = computed(() => items.value.reduce((t, i) => t + i.price * i.quantity, 0))
  const shipping = computed(() => (subtotal.value > 50000 ? 0 : 2500))
  const tax = computed(() => subtotal.value * 0.075)
  const totalPrice = computed(() => subtotal.value + shipping.value + tax.value - discount.value)

  // 🔹 Backend helper
  async function callApi(endpoint, payload, method = 'POST') {
    if (!token()) return

    try {
      const res = await fetch(`${API_BASE}${endpoint}`, {
        method,
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token()}`,
        },
        body: payload ? JSON.stringify(payload) : undefined,
      })

      if (!res.ok) throw new Error(`API error: ${res.status}`)
      return await res.json()
    } catch (err) {
      console.error(err)
      toast.error('Cart sync failed')
      return null
    }
  }

  // 🔹 Add item
  async function addItem(product, quantity = 1) {
    const existingItem = items.value.find((i) => i.id === product.id)

    if (existingItem) {
      existingItem.quantity += quantity
      toast.success(`Updated quantity for ${product.name}`)
      await callApi('/carts/update', { product_id: product.id, quantity: existingItem.quantity, _method: 'patch' })
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
      await callApi('/carts', { product_id: product.id, quantity })
    }

    saveCart()
  }

  // 🔹 Remove item
  async function removeItem(productId) {
    const index = items.value.findIndex((i) => i.id === productId)
    if (index > -1) {
      const item = items.value[index]
      items.value.splice(index, 1)
      toast.success(`${item.name} removed from cart`)
      await callApi('/carts/rm', { product_id: productId, _method: 'delete' })
      saveCart()
    }
  }

  // 🔹 Update quantity
  async function updateQuantity(productId, quantity) {
    const item = items.value.find((i) => i.id === productId)
    if (item && quantity > 0) {
      item.quantity = parseInt(quantity)
      await callApi('/carts/update', { product_id: productId, quantity, _method: 'patch' })
      saveCart()
    }
  }

  function clearCart() {
    items.value = []
    discount.value = 0
    couponCode.value = ''
    saveCart()
    // Optionally clear server too
  }

  function applyCoupon(code) {
    if (code === 'SAVE10') {
      discount.value = subtotal.value * 0.1
      couponCode.value = code
      toast.success('Coupon applied! 10% discount')
      return true
    } else if (code === 'SAVE20') {
      discount.value = subtotal.value * 0.2
      couponCode.value = code
      toast.success('Coupon applied! 20% discount')
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

  // 🔹 Merge carts on login
  async function syncCart() {
    if (!token()) return

    try {
      // 1. Push local items to backend
      for (const item of items.value) {
        await callApi('/carts', { product_id: item.id, quantity: item.quantity })
      }

      // 2. Pull fresh server cart
      const res = await callApi('/carts', null, 'GET')
      if (res && res.data) {
        const serverCart = res.data.map((item) => ({
          id: item.product_id,
          name: item.product?.name,
          price: item.product?.price,
          sku: item.product?.sku,
          image: item.product?.image,
          volume: item.product?.volume || '5 Litres',
          quantity: item.quantity,
        }))

        // Merge local + server
        for (const localItem of items.value) {
          const existing = serverCart.find((i) => i.id === localItem.id)
          if (!existing) serverCart.push(localItem)
        }

        items.value = serverCart
        saveCart()
        toast.success('Cart synchronized with server')
      }
    } catch (err) {
      console.error('Cart sync failed:', err)
    }
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
    syncCart,
  }
})
