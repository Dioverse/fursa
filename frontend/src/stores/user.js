import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUserStore = defineStore('user', () => {
  const wishlist = ref([])
  const addresses = ref([])
  const defaultAddressId = ref(null)

  // Load from localStorage
  const savedWishlist = localStorage.getItem('wishlist')
  if (savedWishlist) {
    wishlist.value = JSON.parse(savedWishlist)
  }

  const savedAddresses = localStorage.getItem('addresses')
  if (savedAddresses) {
    addresses.value = JSON.parse(savedAddresses)
  }

  const wishlistCount = computed(() => wishlist.value.length)

  const defaultAddress = computed(() => {
    return addresses.value.find((addr) => addr.id === defaultAddressId.value)
  })

  function addToWishlist(product) {
    const exists = wishlist.value.find((item) => item.id === product.id)
    if (!exists) {
      wishlist.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        sku: product.sku,
        image: product.image,
        volume: product.volume,
      })
      saveWishlist()
      return true
    }
    return false
  }

  function removeFromWishlist(productId) {
    const index = wishlist.value.findIndex((item) => item.id === productId)
    if (index > -1) {
      wishlist.value.splice(index, 1)
      saveWishlist()
      return true
    }
    return false
  }

  function isInWishlist(productId) {
    return wishlist.value.some((item) => item.id === productId)
  }

  function clearWishlist() {
    wishlist.value = []
    saveWishlist()
  }

  function addAddress(address) {
    const newAddress = {
      id: Date.now(),
      ...address,
      createdAt: new Date().toISOString(),
    }
    addresses.value.push(newAddress)

    // Set as default if it's the first address
    if (addresses.value.length === 1) {
      defaultAddressId.value = newAddress.id
    }

    saveAddresses()
    return newAddress
  }

  function updateAddress(id, updates) {
    const index = addresses.value.findIndex((addr) => addr.id === id)
    if (index > -1) {
      addresses.value[index] = { ...addresses.value[index], ...updates }
      saveAddresses()
      return true
    }
    return false
  }

  function deleteAddress(id) {
    const index = addresses.value.findIndex((addr) => addr.id === id)
    if (index > -1) {
      addresses.value.splice(index, 1)

      // Update default if deleted
      if (defaultAddressId.value === id) {
        defaultAddressId.value = addresses.value[0]?.id || null
      }

      saveAddresses()
      return true
    }
    return false
  }

  function setDefaultAddress(id) {
    if (addresses.value.find((addr) => addr.id === id)) {
      defaultAddressId.value = id
      saveAddresses()
      return true
    }
    return false
  }

  function saveWishlist() {
    localStorage.setItem('wishlist', JSON.stringify(wishlist.value))
  }

  function saveAddresses() {
    localStorage.setItem('addresses', JSON.stringify(addresses.value))
    localStorage.setItem('defaultAddressId', defaultAddressId.value)
  }

  return {
    wishlist,
    wishlistCount,
    addresses,
    defaultAddress,
    defaultAddressId,
    addToWishlist,
    removeFromWishlist,
    isInWishlist,
    clearWishlist,
    addAddress,
    updateAddress,
    deleteAddress,
    setDefaultAddress,
  }
})
