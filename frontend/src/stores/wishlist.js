import { defineStore } from 'pinia'

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    items: JSON.parse(localStorage.getItem('wishlist')) || []
  }),

  actions: {
    add(item) {
      if (!this.items.find(p => p.id === item.id)) {
        this.items.push(item)
        this.saveToLocalStorage()
      }
    },

    remove(id) {
      this.items = this.items.filter(p => p.id !== id)
      this.saveToLocalStorage()
    },

    saveToLocalStorage() {
      localStorage.setItem('wishlist', JSON.stringify(this.items))
    }
  },

  getters: {
    count: (state) => state.items.length
  }
})
