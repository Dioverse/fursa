<template>
  <!-- Price Filter -->
  <div class="bg-white border rounded-lg shadow-sm p-4 mb-12">
    <h3 class="font-bold mb-4">🔎 Widget Price Filter</h3>
    <div class="flex gap-2 items-center">
      <input v-model.number="priceRange.min" type="number" placeholder="Min" class="w-20 px-2 py-1 border rounded">
      <span>-</span>
      <input v-model.number="priceRange.max" type="number" placeholder="Max" class="w-20 px-2 py-1 border rounded">
    </div>
    <button 
      @click="applyPriceFilter"
      class="mt-4 w-full bg-primary text-white py-2 rounded hover:bg-primary-dark"
    >
      Apply
    </button>
  </div>

  <!-- Dynamic Categories -->
  <div v-for="category in categories" :key="category.id" class="bg-white rounded-lg shadow-md p-6 text-center mb-12">
    <!-- Parent Category -->
    <h3 class="text-lg text-center font-semibold mb-4 flex items-center justify-center border-b pb-6">
      <span>{{ category.name }}</span>
    </h3>

    <!-- Subcategories -->
    <div class="space-y-3">
      <label 
        v-for="sub in category.subcategories" 
        :key="sub.id"
        class="flex items-center gap-3 cursor-pointer hover:text-primary transition"
      >
        <input 
          type="checkbox" 
          :value="sub.id" 
          v-model="selectedCategories" 
          @change="updateFilter"
          class="rounded border-gray-300 text-primary focus:ring-primary"
        >
        <span>{{ sub.name }}</span>
        <span class="text-gray-400 text-sm ml-auto">({{ sub.products_count }})</span>
      </label>
    </div>

    <button @click="clearFilters" class="mt-4 text-sm text-primary hover:underline">
      Clear all filters
    </button>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const emit = defineEmits(['update'])
const categories = ref([])  // will be loaded from API

const selectedCategories = ref([])
const priceRange = reactive({
  min: null,
  max: null
})

// --- Fetch categories from API ---
onMounted(async () => {
  try {
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/cats?sub=true`)
    const json = await res.json()
    categories.value = json.data || []
  } catch (error) {
    console.error("Error fetching categories:", error)
  }
})

// --- Methods ---
const updateFilter = () => {
  emit('update', {
    categories: selectedCategories.value,
    priceRange: { ...priceRange }
  })
}

const applyPriceFilter = () => {
  updateFilter()
}

const clearFilters = () => {
  selectedCategories.value = []
  priceRange.min = null
  priceRange.max = null
  updateFilter()
}
</script>
