import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'

// 🔑 API helper
const API_BASE = import.meta.env.VITE_API_BASE_URL || 'https://back.fursaenergy.com/public/api'
// 'https://fursa.jarustraining.com.ng/api'

const toNumber = v => (v === null || v === undefined ? 0 : Number(v))

export const useCartStore = defineStore('cart', () => {
  const toast = useToast()
  const items = ref([])
  const couponCode = ref('')
  const discount = ref(0)

  const shippingRate = ref(0)
  const taxRate = ref(0.075)
  const freeShippingThreshold = ref(100)

  // Load cart from localStorage on init
  const savedCart = localStorage.getItem('cart')
  if (savedCart) {
    try { items.value = JSON.parse(saved) } catch (e) { items.value = [] }
  }

  const token = () => localStorage.getItem('token') // function so it’s always fresh

   const itemCount = computed(() => {
    return items.value.reduce((count, item) => count + item.quantity, 0)
  })

  const subtotal = computed(() => {
    const total = items.value.reduce((sum, item) => {
      return sum + (item.price * item.quantity)
    }, 0)
    return Math.round(total * 100) / 100
  })

  const shipping = computed(() => {
    if (items.value.length === 0) return 0
    if (subtotal.value >= freeShippingThreshold.value) return 0
    return Math.round(shippingRate.value * 100) / 100
  })

  const tax = computed(() => {
    const taxableAmount = subtotal.value + shipping.value
    const taxAmount = taxableAmount * taxRate.value
    return 0//Math.round(taxAmount * 100) / 100
  })

  const totalPrice = computed(() => {
    const total = subtotal.value + shipping.value + tax.value
    return Math.round(total * 100) / 100  
  })
  // 🔹 Backend helper
//   async function callApi(endpoint, payload, method = 'POST') {
//     if (!token()) return

//     try {
//       const res = await fetch(`${API_BASE}${endpoint}`, {
//         method,
//         headers: {
//           'Content-Type': 'application/json',
//           Authorization: `Bearer ${token()}`,
//         },
//         body: payload ? JSON.stringify(payload) : undefined,
//       })

//       if (!res.ok) throw new Error(`API error: ${res.status}`)
//       return await res.json()
//     } catch (err) {
//       console.error(err)
//       toast.error('Cart sync failed')
//       return null
//     }
//   }

//   // 🔹 Add item
// async function addItem(product, quantity = 1) {
//   try {
//     // Normalize quantity
//     const qty = Math.max(1, Number(quantity))

//     // Check if item already exists
//     const existingItem = items.value.find((i) => i.id === product.id)

//     if (existingItem) {
//       // Update local quantity
//       existingItem.quantity += qty

//       // Sync with backend
//       await callApi('/carts/update', {
//         product_id: product.id,
//         quantity: existingItem.quantity,
//         _method: 'patch',
//       })

//       toast.success(`Updated quantity for ${product.name}`)
//     } else {
//       // Add new item locally
//       const newItem = {
//         id: product.id,
//         name: product.name,
//         price: Number(product.price),
//         sku: product.sku ?? '',
//         image: product.image ?? null,
//         volume: product.volume || '5 Litres',
//         quantity: qty,
//       }
//       items.value.push(newItem)

//       // Sync with backend
//       await callApi('/carts', {
//         cart: {
//           product_id: product.id,
//           quantity: qty,
//         },
//       })

//       toast.success(`${product.name} added to cart`)
//     }

//     // Persist locally
//     saveCart()
//   } catch (error) {
//     console.error('Failed to add item to cart:', error)
//     toast.error('Could not add item to cart. Please try again.')
//   }
// }


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

      if (!res.ok) {
        const errMsg = await res.text()
        throw new Error(`API error ${res.status}: ${errMsg}`)
      }

      return await res.json()
    } catch (err) {
      console.error('❌ API call failed:', err)
      toast.error('Cart sync failed. Please try again.')
      return null
    }
  }


  async function addItem(product, quantity = 1) {
    try {
      const qty = Math.max(1, Number(quantity)) // ensure at least 1
      const existingItem = items.value.find((i) => i.id === product.id)

      if (existingItem) {
        // update local
        existingItem.quantity += qty

        // backend expects array format
        await callApi('/carts', {
          cart: [
            {
              product_id: product.id,
              quantity: existingItem.quantity,
            },
          ],
        }, 'POST')

        toast.success(`Updated quantity for ${product.name}`)
      } else {
        // create new local item
        const newItem = {
          id: product.id,
          name: product.name,
          price: Number(product.discountedPrice || product.price),
          sku: product.sku ?? '',
          image: product.image ?? null,
          volume: product.volume || '5 Litres',
          quantity: qty,
        }
        items.value.push(newItem)

        // backend expects array format
        await callApi('/carts', {
          cart: [
            {
              product_id: product.id,
              quantity: qty,
            },
          ],
        })

        toast.success(`${product.name} added to cart`)
      }

      saveCart()
    } catch (error) {
      console.error('❌ Failed to add item to cart:', error)
      toast.error('Could not add item to cart. Please try again.')
    }
  }



  // 🔹 Remove item from cart
  async function removeItem(productId) {
    const index = items.value.findIndex((i) => i.id === productId)
    if (index > -1) {
      const item = items.value[index]
      items.value.splice(index, 1)

      try {
        await callApi('/carts/rm', {
          cart: [
            { product_id: productId, quantity: 0 }
          ]
        })
        toast.success(`${item.name} removed from cart`)
      } catch (err) {
        console.error('Remove failed:', err)
        toast.error('Could not remove item. Please try again.')
      }

      saveCart()
    }
  }

  // 🔹 Update quantity
  async function updateQuantity(productId, quantity) {
    const item = items.value.find((i) => i.id === productId)

    if (!item) {
      toast.error('Item not found in cart')
      return
    }

    if (quantity <= 0) {
      // auto-remove if qty <= 0
      return removeItem(productId)
    }

    item.quantity = parseInt(quantity)

    try {
      await callApi('/carts', {
        cart: [
          { product_id: productId, quantity: item.quantity }
        ]
      })
      toast.success(`Updated ${item.name} quantity`)
    } catch (err) {
      console.error('Update failed:', err)
      toast.error('Could not update item. Please try again.')
    }

    saveCart()
  }

  // 🔹 Clear cart
  async function clearCart() {
    items.value = []
    discount.value = 0
    couponCode.value = ''

    try {
      await callApi('/carts/clear', { cart: [] }) // ✅ if backend supports clearing
    } catch (err) {
      console.error('Clear failed:', err)
    }

    saveCart()
    toast.success('Cart cleared')
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
  watch(items, saveCart, { deep: true })

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
