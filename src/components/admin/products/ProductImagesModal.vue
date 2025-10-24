<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/50"
      @click.self="close">
      <div class="bg-white rounded-xl shadow-strong max-w-4xl w-full max-h-[90vh] overflow-y-auto animate-fade-in"
        @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
          <h3 class="text-lg font-semibold text-gray-900">Update Product Images</h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors" :disabled="loading">
            <font-awesome-icon icon="times" class="h-5 w-5" />
          </button>
        </div>

        <!-- Body -->
        <div class="p-6">
          <ProductImagesForm :product-id="productId" :existing-images="existingImages" :loading="loading"
            @submit="handleSubmit" @reset="close" @error="handleError" ref="formRef" />
        </div>
      </div>
    </div>
  </teleport>

</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useProductsStore } from '@/stores/products'
import ProductImagesForm from '@/components/admin/products/ProductImagesForm.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  productId: { type: [String, Number], required: true },
  existingImages: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:show', 'updated'])

const productsStore = useProductsStore()
const loading = computed(() => productsStore.isUpdatingImages)
const formRef = ref(null)

const unlockScroll = () => { try { document.body.style.overflow = '' } catch { /* no-op */ } }
const lockScroll = () => { try { document.body.style.overflow = 'hidden' } catch { /* no-op */ } }

const close = () => {
  unlockScroll()
  emit('update:show', false)
}
// const submit = () => formRef.value?.handleSubmit?.()

const handleSubmit = async ({ formData, productId, removedExistingIds = [] }) => {
  try {
    // First remove any existing images the user deleted
    if (Array.isArray(removedExistingIds) && removedExistingIds.length > 0) {
      await productsStore.deleteProductImages(productId, removedExistingIds)
    }

    // Then upload new images if provided
    if (formData && [...formData.keys()].some(k => k.startsWith('images'))) {
      await productsStore.updateProductImages(productId, formData)
    }
    emit('updated')
    close()
  } catch {
    // errors are toasted in store
  }
}

const handleError = (errors) => {
  // could toast or log
  console.warn('Image upload errors:', errors)
}

// Close on Escape
let onKey
onMounted(() => {
  if (typeof window !== 'undefined') {
    onKey = (e) => { if (e.key === 'Escape' && props.show && !loading.value) close() }
    window.addEventListener('keydown', onKey)
  }
  if (props.show) lockScroll()
})
onUnmounted(() => {
  if (typeof window !== 'undefined' && onKey) {
    window.removeEventListener('keydown', onKey)
  }
  unlockScroll()
})

// When modal opens, reset the form previews and lock scroll
watch(() => props.show, (val) => {
  if (val) {
    lockScroll()
    // Reset form to re-initialize previews from latest existingImages
    formRef.value?.resetForm?.()
  } else {
    unlockScroll()
  }
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.98); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fade-in { animation: fadeIn 0.15s ease-out; }
</style>
