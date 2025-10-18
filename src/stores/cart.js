// /stores/cart.js
import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'
const toNumber = v => (v === null || v === undefined ? 0 : Number(v))

export const useCartStore = defineStore('cart', () => {
  const toast = useToast()

  // --- STATE ---
  const items = ref([])
  const couponCode = ref('')
  const discount = ref(0)
  const shippingRate = ref(9.50)
  const taxRate = ref(0)
  const freeShippingThreshold = ref(5000)
  const loading = ref(false)

  const token = () => localStorage.getItem('token')

  // --- LOAD LOCAL CART IF NOT LOGGED IN ---
  if (!token()) {
    const savedCart = localStorage.getItem('cart')
    if (savedCart) {
      try { items.value = JSON.parse(savedCart) } catch (e) { items.value = [] }
    }
  }

  // --- COMPUTED ---
  const itemCount = computed(() =>
    items.value.reduce((total, i) => total + (toNumber(i.quantity) || 0), 0)
  )

  const subtotal = computed(() =>
    items.value.reduce((sum, i) => {
      const product = i.product || i
      const price = toNumber(product.discounted_price || product.price)
      return sum + (price * toNumber(i.quantity))
    }, 0)
  )

  const shipping = computed(() =>
    subtotal.value > freeShippingThreshold.value ? 0 : shippingRate.value
  )

  const tax = computed(() =>
    Number(((subtotal.value + shipping.value) * taxRate.value).toFixed(2))
  )

  const totalPrice = computed(() =>
    Number((subtotal.value + shipping.value + tax.value - toNumber(discount.value)).toFixed(2))
  )

  // --- API HELPER ---
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
      return null
    }
  }

  // --- SAVE LOCAL CART ---
  function saveCart() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }
  watch(items, saveCart, { deep: true })

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
        price: item.price,
        discounted_price: item.discounted_price || item.price,
        sku: item.sku || '',
        image: item.image || null,
      },
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
        if (data?.cart) {
          console.log(data.cart);
          items.value = data.cart.map(normalizeItem)
          console.log(items.value);
          saveCart()
          toast.success(`Product added to cart`)
          return
        }
      }

      // --- Fallback to local cart ---
      const existing = items.value.find(i => (i.product_id || i.id) === product.id)

      if (existing) {
        existing.quantity += qty
      } else {
        items.value.push(normalizeItem({
          id: product.id,
          product_id: product.id,
          quantity: qty,
          product: {
            id: product.id,
            name: product.name,
            price: Number(product.price),
            discounted_price: Number(product.discountedPrice || product.price),
            sku: product.sku || '',
            image: product.image || null,
          },
        }))
      }
      saveCart()
      toast.success(`Product added to cart`)
    } catch (err) {
      console.error(err)
      toast.error('Could not add item to cart. Please try again.')
    }
  }

  // --- REMOVE ITEM ---
  async function removeItem(productId) {
    const item = items.value.find(i => (i.product_id || i.id) === productId)
    const itemName = item?.product?.name || item?.name || 'Item'

    if (token()) {
      const data = await callApi('/carts/rm', { product_id: productId }, 'DELETE')
      if (data?.cart) {
        items.value = data.cart.map(normalizeItem)
        saveCart()
        toast.success(`${itemName} removed from cart`)
        return
      }
    }

    const index = items.value.findIndex(i => (i.product_id || i.id) === productId)
    if (index === -1) return

    items.value.splice(index, 1)
    saveCart()
    toast.success(`${itemName} removed from cart`)
  }

  // --- UPDATE QUANTITY ---
  async function updateQuantity(productId, quantity) {
    
    if (token()) {
      const data = await callApi('/carts/update', { product_id: productId, quantity }, 'PATCH')
      if (data?.cart) {
        items.value = data.cart.map(normalizeItem)
        saveCart()
        toast.success(`Updated product quantity`)
        return
      }
    }

    const item = items.value.find(i => (i.product_id || i.id) === productId)
    if (!item) return toast.error('Item not found')

    if (quantity <= 0) return removeItem(productId)

    item.quantity = parseInt(quantity)
    saveCart()
    toast.success(`Updated ${item.product?.name || item.name} quantity`)
  }

  // --- CLEAR CART ---
  async function clearCart() {
    if (token()) await callApi('/carts/clear', {}, 'POST')
    items.value = []
    discount.value = 0
    couponCode.value = ''
    saveCart()
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
      if (data?.cart) {
        items.value = data.cart.map(normalizeItem)
        saveCart()
      }
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
        cart: items.value.map(i => ({
          product_id: i.product_id || i.id,
          quantity: i.quantity,
        })),
      }
      await callApi('/carts', payload, 'POST')
      // Clear local storage after syncing
      localStorage.removeItem('cart')
      // Fetch server cart and replace local
      await fetchCartFromServer()
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
    fetchCartFromServer,
    syncCart,
  }
})