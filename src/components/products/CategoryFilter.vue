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

  <!-- Product Categories -->
  <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
    <span>Product Categories</span>
  </h3>

  <!-- Automotive Lubricants -->
  <div class="bg-white rounded-lg shadow-md p-6 text-center">
    <h3 class="text-lg text-center font-semibold mb-4 flex items-center justify-center border-b pb-6">
      <span>Automotive Lubricants</span>
    </h3>

    <div class="space-y-3">
      <label v-for="category in categories" :key="category.id"
        class="flex items-center gap-3 cursor-pointer hover:text-primary transition">
        <input type="checkbox" :value="category.id" v-model="selectedCategories" @change="updateFilter"
          class="rounded border-gray-300 text-primary focus:ring-primary">
        <span>{{ category.name }}</span>
        <span class="text-gray-400 text-sm ml-auto">({{ category.count }})</span>
      </label>
    </div>

    <button @click="clearFilters" class="mt-4 text-sm text-primary hover:underline">
      Clear all filters
    </button>
  </div>

  <!-- Industrial Lubricants -->
  <div class="bg-white rounded-lg shadow-md p-6 text-center mt-12">
    <h3 class="text-lg text-center font-semibold mb-4 flex items-center justify-center border-b pb-6">
      <span>Industrial Lubricants</span>
    </h3>

    <div class="space-y-3">
      <label v-for="icategory in icategories" :key="icategory.id"
        class="flex items-center gap-3 cursor-pointer hover:text-primary transition">
        <input type="checkbox" :value="icategory.id" v-model="selectedCategories" @change="updateFilter"
          class="rounded border-gray-300 text-primary focus:ring-primary">
        <span>{{ icategory.name }}</span>
        <span class="text-gray-400 text-sm ml-auto">({{ icategory.count }})</span>
      </label>
    </div>

    <button @click="clearFilters" class="mt-4 text-sm text-primary hover:underline">
      Clear all filters
    </button>
  </div>

  <!-- Greases -->
  <div class="bg-white rounded-lg shadow-md p-6 text-center mt-12">
    <h3 class="text-lg text-center font-semibold mb-4 flex items-center justify-center border-b pb-6">
      <span>Greases</span>
    </h3>

    <div class="space-y-3">
      <label v-for="grease in greases" :key="grease.id"
        class="flex items-center gap-3 cursor-pointer hover:text-primary transition">
        <input type="checkbox" :value="grease.id" v-model="selectedCategories" @change="updateFilter"
          class="rounded border-gray-300 text-primary focus:ring-primary">
        <span>{{ grease.name }}</span>
        <span class="text-gray-400 text-sm ml-auto">({{ grease.count }})</span>
      </label>
    </div>

    <button @click="clearFilters" class="mt-4 text-sm text-primary hover:underline">
      Clear all filters
    </button>
  </div>

  <!-- Marine Heavy Oil -->
  <div class="bg-white rounded-lg shadow-md p-6 text-center mt-12">
    <h3 class="text-lg text-center font-semibold mb-4 flex items-center justify-center border-b pb-6">
      <span>Marine and Heavy Equipment Oil</span>
    </h3>

    <div class="space-y-3">
      <label v-for="marine in marines" :key="marine.id"
        class="flex items-center gap-3 cursor-pointer hover:text-primary transition">
        <input type="checkbox" :value="marine.id" v-model="selectedCategories" @change="updateFilter"
          class="rounded border-gray-300 text-primary focus:ring-primary">
        <span>{{ marine.name }}</span>
        <span class="text-gray-400 text-sm ml-auto">({{ marine.count }})</span>
      </label>
    </div>

    <button @click="clearFilters" class="mt-4 text-sm text-primary hover:underline">
      Clear all filters
    </button>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

const emit = defineEmits(['update'])

// --- Categories Data ---
const categories = ref([
  { id: 1, name: 'Engine Oil', count: 24 },
  { id: 2, name: 'Brake Fluid', count: 18 },
  { id: 3, name: 'Transmission Fluid', count: 12 },
  { id: 4, name: 'Gear Oil', count: 15 }
])

const icategories = ref([
  { id: 5, name: 'Hydraulic Oils', count: 24 },
  { id: 6, name: 'Compressor Oils', count: 18 },
  { id: 7, name: 'Turbine Oils', count: 12 },
  { id: 8, name: 'Specialty Grease', count: 15 }
])

const greases = ref([
  { id: 9, name: 'Lithium Complex Greases', count: 24 },
  { id: 10, name: 'Multipurpose and heavy duty Greases', count: 18 }
])

const marines = ref([
  { id: 11, name: 'Marine Engine Oils', count: 24 },
  { id: 12, name: 'Heavy Equipment Oils', count: 18 }
])

// --- State ---
const selectedCategories = ref([])
const priceRange = reactive({
  min: null,
  max: null
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
