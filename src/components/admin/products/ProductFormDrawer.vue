<template>
  <div class="fixed inset-0 overflow-hidden z-50" v-if="show">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeDrawer"></div>

    <!-- Drawer -->
    <div
      class="absolute inset-y-0 right-0 max-w-2xl w-full bg-white shadow-xl transform transition-transform duration-300"
      :class="{ 'translate-x-0': show, 'translate-x-full': !show }">

      <!-- Drawer Header -->
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h2>
        <button @click="closeDrawer" class="text-gray-400 hover:text-gray-500">
          <font-awesome-icon icon="times" class="h-5 w-5" />
        </button>
      </div>

      <!-- Drawer Content -->
      <div class="h-full overflow-y-auto px-6 py-4">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Basic Information -->
          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
              <input type="text" id="name" v-model="form.name" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div>
              <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
              <select id="category" v-model="form.category_id" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>

            <div>
              <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
              <textarea id="short_description" v-model="form.short_description" rows="2"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Full Description</label>
              <div class="mt-1 border border-gray-300 rounded-md">
                <editor-content :editor="editor" />
              </div>
            </div>

            <!-- Tags Field -->
            <div>
              <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
              <div class="mt-1">
                <div class="flex flex-wrap gap-2 mb-2" v-if="form.tags.length > 0">
                  <span v-for="(tag, index) in form.tags" :key="index"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                    {{ tag }}
                    <button type="button" @click="removeTag(index)"
                      class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-primary-400 hover:bg-primary-200 hover:text-primary-500">
                      <font-awesome-icon icon="times" class="h-3 w-3" />
                    </button>
                  </span>
                </div>
                <div class="flex">
                  <input type="text" v-model="newTag" @keydown.enter.prevent="addTag"
                    placeholder="Add tags (press Enter to add)"
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                  <button type="button" @click="addTag"
                    class="ml-2 px-3 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    Add
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pricing -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Pricing</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="base_price" class="block text-sm font-medium text-gray-700">Base Price</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">₦</span>
                  </div>
                  <input type="number" step="0.01" id="base_price" v-model.number="form.base_price" required
                    class="pl-7 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
              </div>
              <div>
                <label for="distributor_price" class="block text-sm font-medium text-gray-700">Distributor Price</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">₦</span>
                  </div>
                  <input type="number" step="0.01" id="distributor_price" v-model.number="form.distributor_price"
                    class="pl-7 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
              </div>
            </div>
          </div>

          <!-- Inventory -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Inventory</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                <input type="number" id="stock_quantity" v-model.number="form.stock_quantity" required
                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
              </div>
              <div>
                <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Alert
                  Threshold</label>
                <input type="number" id="low_stock_threshold" v-model.number="form.low_stock_threshold"
                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
              </div>
            </div>
          </div>

          <!-- Images -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Product Images</h3>
            <div
              class="flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
              <div class="space-y-1 text-center">
                <font-awesome-icon icon="cloud-upload" class="mx-auto h-12 w-12 text-gray-400" />
                <div class="flex text-sm text-gray-600">
                  <label for="images"
                    class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500">
                    <span>Upload images</span>
                    <input id="images" type="file" multiple accept="image/*" class="sr-only"
                      @change="handleImageUpload">
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
              </div>
            </div>

            <!-- Image Preview -->
            <div v-if="imagePreviewUrls.length" class="grid grid-cols-3 gap-4 mt-4">
              <div v-for="(url, index) in imagePreviewUrls" :key="index"
                class="relative rounded-lg overflow-hidden group">
                <img :src="`import.meta.env.FILE_BASE_PATH${url}`" alt="Preview" class="h-32 w-full object-cover">
                <button type="button" @click="removeImage(index)"
                  class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                  <font-awesome-icon icon="times" class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 -mx-6">
            <div class="flex justify-end space-x-3">
              <button type="button" class="btn-outline" @click="closeDrawer">Cancel</button>
              <button type="submit" class="btn-primary" :disabled="loading">
                <font-awesome-icon v-if="loading" icon="spinner" class="h-4 w-4 animate-spin mr-2" />
                {{ isEditing ? 'Update Product' : 'Create Product' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount, watch, nextTick } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

const props = defineProps({
  show: Boolean,
  categories: {
    type: Array,
    default: () => []
  },
  product: {
    type: Object,
    default: null
  },
  loading: Boolean
})

const emit = defineEmits(['close', 'submit'])

// Rich text editor setup
const editor = useEditor({
  extensions: [StarterKit],
  content: '',
  editorProps: {
    attributes: {
      class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none',
    },
  }
})

// Form state - Initialize with proper defaults
const form = ref({
  name: '',
  category_id: '',
  short_description: '',
  description: '',
  base_price: 0,
  distributor_price: 0,
  stock_quantity: 0,
  low_stock_threshold: 0,
  tags: [],
  images: []
})

const imagePreviewUrls = ref([])
const isEditing = ref(false)
const newTag = ref('')

// Methods
const addTag = () => {
  const tag = newTag.value.trim().replace(/,+$/, '') // Remove trailing commas
  if (tag && !form.value.tags.includes(tag)) {
    form.value.tags.push(tag)
    newTag.value = ''
  }
}

const removeTag = (index) => {
  form.value.tags.splice(index, 1)
}

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (file.type.startsWith('image/') && file.size <= 10 * 1024 * 1024) { // 10MB limit
      form.value.images.push(file)
      const reader = new FileReader()
      reader.onload = e => imagePreviewUrls.value.push(e.target.result)
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index) => {
  form.value.images.splice(index, 1)
  imagePreviewUrls.value.splice(index, 1)
}

const handleSubmit = () => {
  // Validate required fields
  if (!form.value.name || !form.value.category_id || !form.value.base_price || form.value.stock_quantity === null) {
    alert('Please fill in all required fields')
    return
  }

  console.log('=== Form Submission Debug ===')
  console.log('isEditing:', isEditing.value)
  console.log('props.product:', props.product)
  console.log('Form state:', form.value)
  console.log('Editor state:', editor.value?.getHTML())

  const formData = new FormData()

  try {

    // Basic fields
    const fieldsToProcess = {
      name: form.value.name,
      category_id: form.value.category_id,
      short_description: form.value.short_description || '',
      description: editor.value?.getHTML() || '',
      base_price: form.value.base_price,
      distributor_price: form.value.distributor_price || 0,
      stock_quantity: form.value.stock_quantity,
      low_stock_threshold: form.value.low_stock_threshold || 0
    }

    // Append basic fields
    Object.entries(fieldsToProcess).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        formData.append(key, typeof value === 'number' ? value.toString() : value)
      }
    })

    // Handle tags
    if (form.value.tags && form.value.tags.length > 0) {
      formData.append('tags', JSON.stringify(form.value.tags))
    }

    // Handle images - only add new images
    if (form.value.images && form.value.images.length > 0) {
      form.value.images.forEach((file, index) => {
        if (file instanceof File) {
          formData.append(`images[${index}]`, file)
        }
      })
    }

    console.log('Prepared FormData for submission.', formData)

    // Emit with more context for parent component
    emit('submit', {
      formData,
      isEdit: isEditing.value,
      productId: props.product?.id || null
    })

  } catch (error) {
    console.error('Error creating FormData:', error)
    alert('An error occurred while preparing the form data. Please try again.')
  }
}

const closeDrawer = () => {
  emit('close')
}

const resetForm = () => {
  form.value = {
    name: '',
    category_id: '',
    short_description: '',
    description: '',
    base_price: 0,
    distributor_price: 0,
    stock_quantity: 0,
    low_stock_threshold: 0,
    tags: [],
    images: []
  }
  imagePreviewUrls.value = []
  newTag.value = ''
  isEditing.value = false

  if (editor.value) {
    editor.value.commands.setContent('')
  }
}

// Watch for show prop changes
watch(() => props.show, async (newVal) => {
  if (!newVal) {
    resetForm()
  } else if (props.product) {
    // Wait for drawer to be visible
    await nextTick()
    populateFormWithProduct(props.product)
  }
})

// Helper function to populate form with product data
const populateFormWithProduct = (product) => {
  console.log('Populating form with product:', product)

  isEditing.value = true

  form.value = {
    name: product.name || '',
    category_id: product.category_id || '',
    short_description: product.short_description || '',
    description: product.description || '',
    base_price: parseFloat(product.base_price) || 0,
    distributor_price: parseFloat(product.distributor_price) || 0,
    stock_quantity: parseInt(product.stock_quantity) || 0,
    low_stock_threshold: parseInt(product.low_stock_threshold) || 0,
    tags: Array.isArray(product.tags) ? [...product.tags] :
      typeof product.tags === 'string' ? product.tags.split(',').map(t => t.trim()).filter(t => t) : [],
    images: [] // Reset for new image uploads
  }

  // Set editor content
  if (editor.value) {
    editor.value.commands.setContent(product.description || '')
  }

  // Handle existing images for preview (URLs from server)
  if (product.images && Array.isArray(product.images)) {
    imagePreviewUrls.value = product.images.map(img => {
      if (typeof img === 'string') return img
      return img.url || img.path || img.src || ''
    }).filter(url => url)
  } else {
    imagePreviewUrls.value = []
  }

  console.log('Form populated:', form.value)
  console.log('Image previews:', imagePreviewUrls.value)
}

// Watch for product prop changes
watch(() => props.product, (newProduct) => {
  console.log('Product prop changed:', newProduct)

  if (newProduct && props.show) {
    populateFormWithProduct(newProduct)
  } else if (!newProduct) {
    isEditing.value = false
  }
}, { immediate: true, deep: true })

// Cleanup
onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
})
</script>

<style>
.ProseMirror {
  padding: 1rem;
  min-height: 150px;
  outline: none;
  border: none;
}

.ProseMirror p {
  margin-bottom: 0.5rem;
}

.ProseMirror:focus {
  outline: none;
  box-shadow: none;
}
</style>
