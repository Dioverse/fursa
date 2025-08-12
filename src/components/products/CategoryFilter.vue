<template>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
            <span>Categories</span>
            <font-awesome-icon icon="filter" class="text-gray-400" />
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

        <div class="mt-6 pt-6 border-t">
            <h4 class="font-semibold mb-3">Price Range</h4>
            <div class="space-y-3">
                <input v-model="priceRange.min" type="number" placeholder="Min" class="w-full px-3 py-2 border rounded"
                    @input="updateFilter">
                <input v-model="priceRange.max" type="number" placeholder="Max" class="w-full px-3 py-2 border rounded"
                    @input="updateFilter">
            </div>
        </div>

        <button @click="clearFilters" class="mt-4 text-sm text-primary hover:underline">
            Clear all filters
        </button>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

const emit = defineEmits(['update'])

const categories = ref([
    { id: 1, name: 'Motor Oil', count: 24 },
    { id: 2, name: 'Heavy Duty Oil', count: 18 },
    { id: 3, name: 'Industrial', count: 12 },
    { id: 4, name: 'Agricultural', count: 8 },
    { id: 5, name: 'Marine', count: 6 },
    { id: 6, name: 'Gear Oil', count: 15 }
])

const selectedCategories = ref([])
const priceRange = reactive({
    min: '',
    max: ''
})

const updateFilter = () => {
    emit('update', {
        categories: selectedCategories.value,
        priceRange: priceRange
    })
}

const clearFilters = () => {
    selectedCategories.value = []
    priceRange.min = ''
    priceRange.max = ''
    updateFilter()
}
</script>