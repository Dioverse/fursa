<template>
  <div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
      <h3 class="text-lg font-medium mb-4">{{ category ? 'Edit Category' : 'Create Category' }}</h3>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input v-model="local.name" class="mt-1 block w-full border rounded p-2" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="local.description" class="mt-1 block w-full border rounded p-2"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Icon</label>
          <div class="flex items-center gap-3 mt-1">
            <select v-model="local.icon" class="block w-full border rounded p-2">
              <option value="">None</option>
              <option v-for="i in ICONS" :key="i" :value="i">{{ i }}</option>
            </select>
            <div class="w-10 h-10 flex items-center justify-center rounded border">
              <font-awesome-icon v-if="local.icon" :icon="local.icon" class="text-gray-600" />
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-1">Pick from the preset icon names. We can expand this list later.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Parent Category</label>
          <select v-model="local.parent_id" class="mt-1 block w-full border rounded p-2">
            <option value="">None</option>
            <option v-for="cat in flatCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
      </div>
      <div class="mt-4 flex justify-end gap-2">
        <button @click="$emit('close')" class="btn-outline">Cancel</button>
        <button @click="save" class="btn-primary flex items-center gap-2" :disabled="isSaving">
          <font-awesome-icon v-if="isSaving" icon="spinner" class="animate-spin h-4 w-4" />
          <span>{{ isSaving ? 'Saving...' : 'Save' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCategoriesStore } from '@/stores/categories'

const props = defineProps({ category: Object })
const emit = defineEmits(['close', 'saved'])

const categoriesStore = useCategoriesStore()

import { CATEGORY_ICON_OPTIONS as ICONS } from '@/constants/categoryIcons'

const local = ref({ name: '', description: '', parent_id: '', icon: '' })
const isSaving = ref(false)

if (props.category) {
  local.value = { ...props.category, parent_id: props.category.parent_id || '', icon: props.category.icon || '' }
}

const flatCategories = computed(() => categoriesStore.categories)

const save = async () => {
  if (!local.value.name) return alert('Name required')
  try {
    isSaving.value = true
    if (props.category) {
      await categoriesStore.updateCategory(props.category.id, local.value)
    } else {
      await categoriesStore.createCategory(local.value)
    }
    emit('saved')
  } catch (err) {
    // error toasts are handled by store; keep modal open for retry
    console.error(err)
  } finally {
    isSaving.value = false
  }
}
</script>
