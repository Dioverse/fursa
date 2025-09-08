<template>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">Categories</h1>
            <button @click="openCreate" class="btn-primary">Create Category</button>
        </div>

        <div v-if="loading" class="py-6">
            <div class="animate-pulse space-y-3">
                <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                <div class="h-4 bg-gray-200 rounded w-full"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-4">
            <CategoryAccordion v-for="cat in categories" :key="cat.id" :category="cat" :deleting-id="deletingId"
                @edit="openEdit" @delete="confirmDelete" />
        </div>

        <CategoryModal v-if="showModal" :category="editingCategory" @close="closeModal" @saved="reload" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCategoriesStore } from '@/stores/categories'
import CategoryAccordion from '@/components/admin/categories/CategoryAccordion.vue'
import CategoryModal from '@/components/admin/categories/CategoryModal.vue'

const categoriesStore = useCategoriesStore()
const categories = ref([])
const showModal = ref(false)
const editingCategory = ref(null)
const loading = ref(true)
const deletingId = ref(null)

const load = async () => {
    await categoriesStore.fetchCategories()
    categories.value = categoriesStore.categories
}

const openCreate = () => {
    editingCategory.value = null
    showModal.value = true
}

const openEdit = (category) => {
    editingCategory.value = category
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const reload = async () => {
    await load()
    closeModal()
}

const confirmDelete = async (category) => {
    if (confirm(`Delete category "${category.name}"?`)) {
        try {
            deletingId.value = category.id
            await categoriesStore.deleteCategory(category.id)
            await load()
        } finally {
            deletingId.value = null
        }
    }
}

onMounted(async () => {
    // simulate short loading delay for dummy loading UI
    await load()
    setTimeout(() => {
        loading.value = false
    }, 400)
})
</script>
