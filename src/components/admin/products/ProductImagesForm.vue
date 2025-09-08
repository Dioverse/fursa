<template>
  <div class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900">Product Images</h3>

    <!-- Upload Area -->
    <div
      class="flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-primary-400 transition-colors"
      @drop="handleDrop" @dragover.prevent @dragenter.prevent>
      <div class="space-y-1 text-center">
        <font-awesome-icon icon="cloud-upload" class="mx-auto h-12 w-12 text-gray-400" />
        <div class="flex text-sm text-gray-600">
          <label for="images"
            class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500">
            <span>Upload images</span>
            <input id="images" type="file" multiple accept="image/*" class="sr-only" @change="handleImageUpload"
              ref="fileInput">
          </label>
          <p class="pl-1">or drag and drop</p>
        </div>
        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each</p>
      </div>
    </div>

    <!-- Image Preview Grid -->
    <div v-if="imagePreviewUrls.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
      <div v-for="(preview, index) in imagePreviewUrls" :key="`preview-${index}`"
        class="relative rounded-lg overflow-hidden group border border-gray-200">
        <img :src="preview.url" :alt="`Preview ${index + 1}`" class="h-32 w-full object-cover"
          @error="handleImageError(index)">
        <button type="button" @click="removeImage(index)"
          class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
          :title="`Remove image ${index + 1}`">
          <font-awesome-icon icon="times" class="h-4 w-4" />
        </button>
        <!-- Image type indicator -->
        <div class="absolute bottom-2 left-2 px-2 py-1 bg-black bg-opacity-50 text-white text-xs rounded">
          {{ preview.type }}
        </div>
      </div>
    </div>

    <!-- Error Messages -->
    <div v-if="errors.length" class="bg-red-50 border border-red-200 rounded-md p-3">
      <div class="flex">
        <font-awesome-icon icon="exclamation-triangle" class="h-5 w-5 text-red-400" />
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Upload Errors</h3>
          <div class="mt-2 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
              <li v-for="error in errors" :key="error">{{ error }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 mt-6">
      <button type="button" class="btn-outline" @click="resetForm" :disabled="loading">
        Cancel
      </button>
      <button type="button" class="btn-primary" :disabled="loading || !hasChanges" @click="handleSubmit">
        <font-awesome-icon v-if="loading" icon="spinner" class="h-4 w-4 animate-spin mr-2" />
        {{ loading ? 'Updating...' : 'Update Images' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  productId: {
    type: [Number, String],
    required: true
  },
  existingImages: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  maxFileSize: {
    type: Number,
    default: 10 * 1024 * 1024 // 10MB in bytes
  },
  maxFiles: {
    type: Number,
    default: 10
  },
  allowedTypes: {
    type: Array,
    default: () => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']
  }
})

const emit = defineEmits(['submit', 'reset', 'error'])

// Reactive state
const images = ref([])
const imagePreviewUrls = ref([])
const errors = ref([])
const fileInput = ref(null)

// Computed properties
const hasChanges = computed(() => images.value.length > 0)
const totalFiles = computed(() => images.value.length + (props.existingImages?.length || 0))

// Initialize with existing images
const initializeExistingImages = () => {
  if (props.existingImages?.length > 0) {
    imagePreviewUrls.value = props.existingImages.map((img, index) => {
      let url = ''
      if (typeof img === 'string') {
        url = img
      } else if (img && typeof img === 'object') {
        url = img.url || img.path || img.src || ''
      }

      return {
        url,
        type: 'existing',
        isExisting: true,
        id: img.id || index
      }
    }).filter(preview => preview.url)
  }
}

// Watch for changes in existing images
watch(() => props.existingImages, initializeExistingImages, { immediate: true })

// File validation
const validateFile = (file) => {
  const errors = []

  if (!props.allowedTypes.includes(file.type)) {
    errors.push(`${file.name}: Unsupported file type. Please use JPG, PNG, GIF, or WebP.`)
  }

  if (file.size > props.maxFileSize) {
    const sizeMB = (props.maxFileSize / 1024 / 1024).toFixed(1)
    errors.push(`${file.name}: File too large. Maximum size is ${sizeMB}MB.`)
  }

  if (totalFiles.value >= props.maxFiles) {
    errors.push(`Maximum ${props.maxFiles} images allowed.`)
  }

  return errors
}

// Handle file upload
const processFiles = (files) => {
  errors.value = []
  const validFiles = []

  Array.from(files).forEach(file => {
    const fileErrors = validateFile(file)
    if (fileErrors.length === 0) {
      validFiles.push(file)
    } else {
      errors.value.push(...fileErrors)
    }
  })

  // Add valid files
  validFiles.forEach(file => {
    if (totalFiles.value < props.maxFiles) {
      images.value.push(file)

      // Create preview
      const reader = new FileReader()
      reader.onload = e => {
        imagePreviewUrls.value.push({
          url: e.target.result,
          type: 'new',
          isExisting: false,
          file: file
        })
      }
      reader.readAsDataURL(file)
    }
  })

  // Emit errors if any
  if (errors.value.length > 0) {
    emit('error', errors.value)
  }
}

const handleImageUpload = (event) => {
  const files = event.target.files
  if (files?.length > 0) {
    processFiles(files)
  }
}

// Drag and drop handlers
const handleDrop = (event) => {
  event.preventDefault()
  const files = event.dataTransfer.files
  if (files?.length > 0) {
    processFiles(files)
  }
}

// Remove image
const removeImage = (index) => {
  const preview = imagePreviewUrls.value[index]

  if (preview.isExisting) {
    // Remove existing image from preview only
    imagePreviewUrls.value.splice(index, 1)
  } else {
    // Remove new image from both arrays
    const imageIndex = images.value.findIndex(img => img === preview.file)
    if (imageIndex > -1) {
      images.value.splice(imageIndex, 1)
    }
    imagePreviewUrls.value.splice(index, 1)
  }

  // Clear errors when user removes problematic files
  if (errors.value.length > 0) {
    errors.value = []
  }
}

// Handle image load errors
const handleImageError = (index) => {
  console.warn(`Failed to load image at index ${index}`)
  // Optionally remove broken image
  // removeImage(index)
}

// Submit handler
const handleSubmit = () => {
  if (!hasChanges.value) return

  const formData = new FormData()

  // Append new images
  images.value.forEach((file, index) => {
    if (file instanceof File) {
      formData.append(`images[${index}]`, file)
    }
  })

  // Also send info about which existing images to keep
  const remainingExistingImages = imagePreviewUrls.value
    .filter(preview => preview.isExisting)
    .map(preview => preview.id)

  if (remainingExistingImages.length > 0) {
    formData.append('existing_images', JSON.stringify(remainingExistingImages))
  }

  emit('submit', {
    formData,
    productId: props.productId,
    newImagesCount: images.value.length,
    existingImagesCount: remainingExistingImages.length
  })
}

// Reset form
const resetForm = () => {
  images.value = []
  errors.value = []
  initializeExistingImages()

  // Clear file input
  if (fileInput.value) {
    fileInput.value.value = ''
  }

  emit('reset')
}

// Expose methods for parent component
defineExpose({
  resetForm,
  clearErrors: () => { errors.value = [] },
  addFiles: processFiles
})
</script>

<style scoped>
.btn-outline {
  @apply px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed;
}

.btn-primary {
  @apply px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed;
}
</style>
