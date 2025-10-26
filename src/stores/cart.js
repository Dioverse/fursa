// /stores/cart.js
import { toNumber } from '@/utils/helpers'
import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'

export const useCartStore = defineStore('cart', () => {
  const toast = useToast()

  // --- STATE ---
  const items = ref([])
  const couponCode = ref('')
  const discount = ref(0)
  const shippingRate = ref(9.5)
  const taxRate = ref(0)
  const freeShippingThreshold = ref(5000)
  const loading = ref(false)

  const token = () => localStorage.getItem('token')

  // --- LOAD LOCAL CART IF NOT LOGGED IN ---
  if (!token()) {
    const savedCart = localStorage.getItem('cart')
    if (savedCart) {
      try {
        items.value = JSON.parse(savedCart)
      } catch (e) {
        items.value = []
      }
    }
  }

  watch(
    items,
    (newItems) => {
      localStorage.setItem('cart', JSON.stringify(newItems))
    },
    { deep: true },
  )

  // --- COMPUTED ---
  const itemCount = computed(() =>
    items.value.reduce((total, i) => total + (toNumber(i.quantity) || 0), 0),
  )

  const subtotal = computed(() =>
    items.value.reduce((sum, i) => {
      const product = i.product || i
      const price = product.discount ? toNumber(product.discounted_price) : toNumber(product.price)
      return sum + price * toNumber(i.quantity)
    }, 0),
  )

  const totalAtOriginalPrice = computed(() =>
    items.value.reduce((sum, i) => {
      const product = i.product || i
      const price = toNumber(product.price)
      return sum + price * toNumber(i.quantity)
    }, 0),
  )

  const totalSaved = computed(() => {
    return totalAtOriginalPrice.value - subtotal.value
  })

  const shipping = computed(() =>
    subtotal.value > freeShippingThreshold.value ? 0 : shippingRate.value,
  )

  const tax = computed(() => Number(((subtotal.value + shipping.value) * taxRate.value).toFixed(2)))

  const totalPrice = computed(() =>
    Number((subtotal.value + shipping.value + tax.value - toNumber(discount.value)).toFixed(2)),
  )

  // --- NORMALIZE CART ITEM ---
  // Ensures consistency whether data comes from API or local storage
  function normalizeItem(item) {
    if (item.product) {
      // API response format
      return {
        id: item.product_id || item.id,
        product_id: item.product_id,
        cart_id: item.cart_id,
        quantity: toNumber(item.quantity),
        product: item.product,
      }
    }
    // Local format - convert to API format
    return {
      id: item.id,
      product_id: item.id,
      quantity: toNumber(item.quantity),
      product: {
        id: item.id,
        name: item.name,
        sku: item.sku || '',
        images: item.images || null,
        price: toNumber(item.price),
        stock_quantity: item.stock_quantity,
        ...(item.discount
          ? {
              discount: item.discount,
              discounted_price: toNumber(item.discounted_price),
            }
          : { discount: null }),
      },
    }
  }

  // --- SAVE LOCAL CART ---
  function saveCart() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }
  watch(items, saveCart, { deep: true })

  // --- UPDATE CART FROM API RESPONSE ---
  // This function is called whenever cart data is returned from any API call
  function updateCartFromResponse(cartData) {
    if (!cartData) return

    // Check if cartData is an array or an object with cart property
    const cartArray = Array.isArray(cartData) ? cartData : cartData.cart || cartData

    if (Array.isArray(cartArray)) {
      items.value = cartArray.map(normalizeItem)
      saveCart()
      console.log("object cart updated ");
    }
  }

  // --- INIT CART FROM LOCAL STORAGE ---
  async function initFromLocal() {
    const savedCart = localStorage.getItem('cart')
    if (!savedCart) return

    try {
      const parsed = JSON.parse(savedCart)
      if (Array.isArray(parsed)) {
        items.value = parsed.map(normalizeItem)
        saveCart()
        toast.info('Cart loaded from local storage')
      }
    } catch (err) {
      console.error('Failed to load cart from local storage:', err)
      items.value = []
      saveCart()
    }
  }

  // Automatically load local cart on store initialization
  initFromLocal()

  // --- DETECT NETWORK ERROR ---
  function isNetworkError(error) {
    return (
      error instanceof TypeError &&
      (error.message.includes('Failed to fetch') ||
        error.message.includes('NetworkError') ||
        error.message.includes('timeout'))
    )
  }

  // --- API HELPER ---
  async function callApi(endpoint, payload = null, method = 'POST') {
    if (!token()) return null

    try {
      const controller = new AbortController()
      const timeoutId = setTimeout(() => controller.abort(), 10000) // 10s timeout

      const res = await fetch(`${API_BASE}${endpoint}`, {
        method,
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token()}`,
        },
        body: payload ? JSON.stringify(payload) : undefined,
        signal: controller.signal,
      })

      clearTimeout(timeoutId)

      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'API error')

      // AUTO-SYNC: Update cart if response contains cart data
      if (data && (data.cart || Array.isArray(data))) {
        updateCartFromResponse(data)
      }

      return data
    } catch (err) {
      if (err.name === 'AbortError') {
        console.error('❌ API request timeout')
        toast.error('Request timeout. Check your connection and try again.')
        return null
      }

      if (isNetworkError(err)) {
        console.error('❌ Network error:', err.message)
        toast.error('Network error. Check connection and try again.')
        return null
      }

      console.error('❌ API call failed:', err)
      toast.error(err.message || 'An error occurred. Try again.')
      return null
    }
  }

  // --- ADD ITEM ---
  async function addItem(product, quantity = 1) {
    try {
      const qty = Math.max(1, Number(quantity))

      // --- Sync with backend (remote first) ---
      if (token()) {
        const payload = { cart: [{ product_id: product.id, quantity: qty }] }
        const data = await callApi('/carts', payload, 'POST')
        
        if (data === null) {
          // Network error already handled in toast within callApi
          return
        }

        if (data?.cart) {
          // Cart is already updated in callApi via updateCartFromResponse
          toast.success(`Product added to cart`)
          return
        }
      }

      // --- Fallback to local cart ---
      const existing = items.value.find((i) => (i.product_id || i.id) == product.id)

      if (existing) {
        existing.quantity += qty
      } else {
        items.value.push(
          normalizeItem({
            id: product.id,
            product_id: product.id,
            quantity: qty,
            product,
          }),
        )
      }
      saveCart()
      toast.success(`Product added to cart`)
    } catch (err) {
      console.error(err)
      toast.error('Could not add item. Please try again.')
    }
  }

  // --- REMOVE ITEM ---
  async function removeItem(productId) {
    const item = items.value.find((i) => (i.product_id || i.id) == productId)
    const itemName = item?.product?.name || item?.name || 'Item'

    if (token()) {
      const data = await callApi('/carts/rm', { product_id: productId }, 'DELETE')
      
      if (data === null) {
        // Network error already handled in toast within callApi
        return
      }

      if (data?.cart) {
        // Cart is already updated in callApi via updateCartFromResponse
        toast.success(`${itemName} removed from cart`)
        return
      }
    }

    const index = items.value.findIndex((i) => (i.product_id || i.id) == productId)
    if (index === -1) return

    items.value.splice(index, 1)
    saveCart()
    toast.success(`${itemName} removed from cart`)
  }

  // --- UPDATE QUANTITY ---
  async function updateQuantity(productId, quantity, e = null) {
    const item = items.value.find((i) => (i.product_id || i.id) == productId)
    if (!item) return toast.error('Item not found')

    const stockQty = item.product?.stock_quantity || Infinity

    if (quantity > stockQty) {
      if (e?.innerText !== undefined) {
        e.innerText = item.quantity
      }
      return toast.info(`Only ${stockQty} units left in stock`)
    }

    if (quantity <= 0) return removeItem(productId)

    // --- Remote first ---
    if (token()) {
      const data = await callApi('/carts/update', { product_id: productId, quantity }, 'PATCH')
      
      if (data === null) {
        // Network error already handled in toast within callApi
        if (e?.innerText !== undefined) {
          e.innerText = item.quantity
        }
        return
      }

      if (data?.cart) {
        // Cart is already updated in callApi via updateCartFromResponse
        toast.success(`Updated product quantity`)
        return
      }
    }

    // --- Local fallback ---
    item.quantity = parseInt(quantity)
    saveCart()
    toast.success(`Updated ${item.product?.name || item.name} quantity`)
  }

  // --- CLEAR CART ---
  async function clearCart() {
    if (token()) {
      const data = await callApi('/carts/clear', {}, 'POST')
      
      if (data === null) {
        // Network error already handled in toast within callApi
        return
      }

      if (data?.cart) {
        discount.value = 0
        couponCode.value = ''
        toast.success(`Cart cleared successfully`)
        return
      }
    }

    items.value = []
    saveCart()
    discount.value = 0
    couponCode.value = ''
  }

  // --- APPLY/REMOVE COUPON ---
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

  // --- FETCH CART FROM SERVER ---
  async function fetchCartFromServer() {
    if (!token()) return
    loading.value = true
    try {
      const data = await callApi('/carts', null, 'GET')
      // Cart is already updated in callApi via updateCartFromResponse
    } catch (err) {
      console.error('Cart fetch failed:', err)
    } finally {
      loading.value = false
    }
  }

  // --- SYNC LOCAL CART AFTER LOGIN ---
  async function syncCart() {
    if (!token()) return

    try {
      const payload = {
        cart: items.value.map((i) => ({
          product_id: i.product_id || i.id,
          quantity: i.quantity,
        })),
      }
      const data = await callApi('/carts', payload, 'POST')
      
      if (data === null) {
        // Network error already handled in toast within callApi
        return
      }

      // Cart is already updated in callApi via updateCartFromResponse
      // Clear local storage after syncing
      localStorage.removeItem('cart')
    } catch (err) {
      console.error('Cart sync failed:', err)
    }
  }

  function getCartItemsList() {
    return items.value.map((item) => ({
      product_id: item.product_id || item.id,
      quantity: item.quantity,
    }))
  }

  return {
    items,
    itemCount,
    subtotal,
    totalAtOriginalPrice,
    totalSaved,
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
    fetchCartFromServer,
    syncCart,
    updateCartFromResponse,
    getCartItemsList
  }
})