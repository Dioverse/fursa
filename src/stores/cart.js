import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'
const toNumber = v => (v === null || v === undefined ? 0 : Number(v))

export const useCartStore = defineStore('cart', () => {
  const toast = useToast()
  const items = ref([])
  const couponCode = ref('')
  const discount = ref(0)
  const shippingRate = ref(9.50)
  const taxRate = ref(0) //0.075
  const freeShippingThreshold = ref(5000)
  const loading = ref(false)

  const token = () => localStorage.getItem('token')

  // ✅ Load from localStorage
  const savedCart = localStorage.getItem('cart')
  if (savedCart) {
    try { items.value = JSON.parse(savedCart) } catch (e) { items.value = [] }
  }

  // ✅ Computed properties
  const itemCount = computed(() =>
    items.value.reduce((total, i) => total + (toNumber(i.quantity) || 0), 0)
  )

  const subtotal = computed(() =>
    items.value.reduce((sum, i) => sum + (toNumber(i.price) * toNumber(i.quantity)), 0)
  )

  const shipping = computed(() => (shippingRate.value))
  const tax = computed(() => Number(((subtotal.value + shipping.value) * taxRate.value).toFixed(2)))
  const totalPrice = computed(() => Number((subtotal.value + shipping.value + tax.value - toNumber(discount.value)).toFixed(2)))

  // ✅ API helper
  async function callApi(endpoint, payload = null, method = 'POST') {
    if (!token()) return null

    try {
      const res = await fetch(`${API_BASE}${endpoint}`, {
        method,
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token()}`,
        },
        body: payload ? JSON.stringify(payload) : undefined,
      })

      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'API error')

      return data
    } catch (err) {
      console.error('❌ API call failed:', err)
      toast.error('Cart sync failed. Please try again.')
      return null
    }
  }

  // ✅ Add item
  async function addItem(product, quantity = 1) {
    try {
      const qty = Math.max(1, Number(quantity))
      const existing = items.value.find(i => i.id === product.id)

      if (existing) {
        existing.quantity += qty
      } else {
        items.value.push({
          id: product.id,
          name: product.name,
          price: Number(product.discountedPrice || product.price),
          sku: product.sku || '',
          image: product.image || null,
          volume: product.volume || '5 Litres',
          quantity: qty,
        })
      }

      // ✅ sync with backend
      if (token()) {
        await callApi('/carts', { cart: [{ product_id: product.id, quantity: existing ? existing.quantity : qty }] })
      }

      saveCart()
      toast.success(`${product.name} ${existing ? 'updated' : 'added'} to cart`)
    } catch (err) {
      console.error(err)
      toast.error('Could not update cart. Please try again.')
    }
  }

  // ✅ Remove item
  async function removeItem(productId) {
    const index = items.value.findIndex(i => i.id === productId)
    if (index === -1) return

    const removed = items.value.splice(index, 1)[0]
    saveCart()

    if (token()) {
      await callApi('/carts/rm', { cart: [{ product_id: productId, quantity: 0 }] })
    }

    toast.success(`${removed.name} removed from cart`)
  }

  // ✅ Update quantity
  async function updateQuantity(productId, quantity) {
    const item = items.value.find(i => i.id === productId)
    if (!item) return toast.error('Item not found')
    if (quantity <= 0) return removeItem(productId)

    item.quantity = parseInt(quantity)
    saveCart()

    if (token()) {
      await callApi('/carts', { cart: [{ product_id: productId, quantity: item.quantity }] })
    }

    toast.success(`Updated ${item.name} quantity`)
  }

  // ✅ Clear cart
  async function clearCart() {
    items.value = []
    discount.value = 0
    couponCode.value = ''
    saveCart()

    if (token()) {
      await callApi('/carts/clear', { cart: [] })
    }

    toast.success('Cart cleared')
  }

  // ✅ Apply coupon
  function applyCoupon(code) {
    if (code === 'SAVE10') {
      discount.value = subtotal.value * 0.1
      couponCode.value = code
      toast.success('10% discount applied!')
    } else {
      toast.error('Invalid coupon')
    }
  }

  function removeCoupon() {
    discount.value = 0
    couponCode.value = ''
    toast.info('Coupon removed')
  }

  // ✅ Save to localStorage
  function saveCart() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }
  watch(items, saveCart, { deep: true })

  // ✅ Fetch cart from backend (used in dashboard)
  async function fetchCartFromServer() {
    if (!token()) return
    loading.value = true
    try {
      const data = await callApi('/checkout/init', null, 'GET')
      if (data?.cart) {
        items.value = data.cart
        saveCart()
      }
    } catch (err) {
      console.error('Cart fetch failed:', err)
    } finally {
      loading.value = false
    }
  }

  // ✅ Sync cart after login (called from dashboard)
  async function syncCart() {
    if (!token()) return

    try {
      const payload = {
        cart: items.value.map(i => ({
          product_id: i.id,
          quantity: i.quantity
        }))
      }
      await callApi('/carts', payload, 'POST')
      await fetchCartFromServer()
      toast.success('Cart synchronized successfully')
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
    loadCart: () => { items.value = JSON.parse(localStorage.getItem('cart') || '[]') },
    fetchCartFromServer,
    applyCoupon,
    removeCoupon,
    syncCart,
  }
})
