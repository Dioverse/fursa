<template>
  <div class="border rounded-md p-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <button @click="toggle" class="text-gray-600 hover:text-gray-900">
          <font-awesome-icon :icon="open ? 'chevron-down' : 'chevron-right'" />
        </button>
  <font-awesome-icon :icon="category.icon || 'tags'" class="text-gray-400" />
        <div>
          <div class="font-medium">{{ category.name }}</div>
          <div class="text-xs text-gray-500">{{ category.description }}</div>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button @click="$emit('edit', category)" class="text-sm text-primary-600">Edit</button>
        <button @click="$emit('delete', category)" :disabled="deletingId === category.id"
          :aria-disabled="deletingId === category.id"
          class="text-sm text-red-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
          <font-awesome-icon v-if="deletingId === category.id" icon="spinner" class="animate-spin h-4 w-4" />
          <span>Delete</span>
        </button>
      </div>
    </div>

    <div v-show="open" class="mt-4 pl-8">
      <div v-if="category.subcategories && category.subcategories.length">
        <div v-for="sub in category.subcategories" :key="sub.id" class="py-2 border-b">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <font-awesome-icon :icon="sub.icon || 'tag'" class="text-gray-400" />
              <div>
                <div class="font-medium">{{ sub.name }}</div>
                <div class="text-xs text-gray-500">{{ sub.description }}</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button @click="$emit('edit', sub)" class="text-sm text-primary-600">Edit</button>
              <button @click="$emit('delete', sub)" :disabled="deletingId === sub.id"
                :aria-disabled="deletingId === sub.id"
                class="text-sm text-red-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <font-awesome-icon v-if="deletingId === sub.id" icon="spinner" class="animate-spin h-4 w-4" />
                <span>Delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-sm text-gray-500">No subcategories</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const { category, deletingId } = defineProps({ category: Object, deletingId: [String, Number] })
const open = ref(false)
const toggle = () => { open.value = !open.value }
</script>
