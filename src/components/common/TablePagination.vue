<template>
  <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
    <!-- Results Info -->
    <div class="flex-1 flex justify-center sm:justify-start">
      <p class="text-sm text-gray-700">
        Showing
        <span class="font-medium">{{ from.toLocaleString() }}</span>
        to
        <span class="font-medium">{{ to.toLocaleString() }}</span>
        of
        <span class="font-medium">{{ total.toLocaleString() }}</span>
        results
      </p>
    </div>

    <!-- Pagination Controls -->
    <div class="flex items-center space-x-2">
      <!-- Previous Button -->
      <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
        class="inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
        <font-awesome-icon icon="chevron-left" class="h-4 w-4" />
        <span class="sr-only">Previous</span>
      </button>

      <!-- Page Numbers -->
      <div class="hidden sm:flex items-center space-x-1">
        <!-- First Page -->
        <button v-if="showFirstPage" @click="goToPage(1)"
          class="inline-flex items-center px-3 py-2 border text-sm font-medium rounded-md transition-colors"
          :class="currentPage === 1 ? pageActiveClass : pageInactiveClass">
          1
        </button>

        <!-- First Ellipsis -->
        <span v-if="showFirstEllipsis" class="inline-flex items-center px-3 py-2 text-sm text-gray-500">
          ...
        </span>

        <!-- Visible Page Numbers -->
        <button v-for="page in visiblePages" :key="page" @click="goToPage(page)"
          class="inline-flex items-center px-3 py-2 border text-sm font-medium rounded-md transition-colors"
          :class="currentPage === page ? pageActiveClass : pageInactiveClass">
          {{ page }}
        </button>

        <!-- Last Ellipsis -->
        <span v-if="showLastEllipsis" class="inline-flex items-center px-3 py-2 text-sm text-gray-500">
          ...
        </span>

        <!-- Last Page -->
        <button v-if="showLastPage" @click="goToPage(lastPage)"
          class="inline-flex items-center px-3 py-2 border text-sm font-medium rounded-md transition-colors"
          :class="currentPage === lastPage ? pageActiveClass : pageInactiveClass">
          {{ lastPage }}
        </button>
      </div>

      <!-- Mobile Page Info -->
      <div class="sm:hidden flex items-center">
        <span class="text-sm text-gray-700">
          Page {{ currentPage }} of {{ lastPage }}
        </span>
      </div>

      <!-- Next Button -->
      <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage"
        class="inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
        <font-awesome-icon icon="chevron-right" class="h-4 w-4" />
        <span class="sr-only">Next</span>
      </button>
    </div>

    <!-- Jump to Page -->
    <div class="hidden lg:flex items-center space-x-2">
      <label for="jump-to-page" class="text-sm text-gray-700">
        Go to page:
      </label>
      <input id="jump-to-page" v-model.number="jumpToPageInput" @keydown.enter="handleJumpToPage"
        @blur="handleJumpToPage" type="number" :min="1" :max="lastPage"
        class="w-16 px-2 py-1 border border-gray-300 rounded-md text-sm text-center focus:ring-primary-500 focus:border-primary-500">
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props (accept both camelCase and legacy snake_case fields)
const props = defineProps({
  // current
  currentPage: { type: Number, default: undefined },
  current_page: { type: Number, default: undefined },

  // last/total pages
  lastPage: { type: Number, default: undefined },
  last_page: { type: Number, default: undefined },
  totalPages: { type: Number, default: undefined },
  total_pages: { type: Number, default: undefined },

  // per page
  perPage: { type: Number, default: undefined },
  per_page: { type: Number, default: undefined },

  // totals and ranges
  total: { type: Number, default: undefined },
  totalItems: { type: Number, default: undefined },
  from: { type: Number, default: undefined },
  to: { type: Number, default: undefined }
})

// Normalized computed values (template uses these)
const currentPage = computed(() => {
  return props.currentPage ?? props.current_page ?? 1
})

const lastPage = computed(() => {
  return (
    props.lastPage ?? props.last_page ?? props.totalPages ?? props.total_pages ?? 1
  )
})

const total = computed(() => props.total ?? props.totalItems ?? 0)

const from = computed(() => props.from ?? 0)

const to = computed(() => props.to ?? 0)

// Emits
const emit = defineEmits(['pageChange'])

// Reactive data
const jumpToPageInput = ref(currentPage.value)

// Computed
const pageActiveClass = computed(() =>
  'bg-primary-600 border-primary-600 text-white hover:bg-primary-700'
)

const pageInactiveClass = computed(() =>
  'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
)

const visiblePages = computed(() => {
  const delta = 2 // Number of pages to show on each side of current page
  const pages = []
  const start = Math.max(1, currentPage.value - delta)
  const end = Math.min(lastPage.value, currentPage.value + delta)

  for (let i = start; i <= end; i++) {
    if (i !== 1 && i !== lastPage.value) {
      pages.push(i)
    }
  }

  return pages
})

const showFirstPage = computed(() => {
  return lastPage.value > 1 && (currentPage.value > 3 || lastPage.value <= 7)
})

const showLastPage = computed(() => {
  return lastPage.value > 1 && (currentPage.value < lastPage.value - 2 || lastPage.value <= 7)
})

const showFirstEllipsis = computed(() => {
  return currentPage.value > 4 && lastPage.value > 7
})

const showLastEllipsis = computed(() => {
  return currentPage.value < lastPage.value - 3 && lastPage.value > 7
})

// Methods
const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value && page !== currentPage.value) {
    emit('pageChange', page)
  }
}

const handleJumpToPage = () => {
  const page = parseInt(jumpToPageInput.value)
  if (page && page >= 1 && page <= lastPage.value) {
    goToPage(page)
  } else {
    jumpToPageInput.value = currentPage.value
  }
}

// Watchers
watch(currentPage, (newPage) => {
  jumpToPageInput.value = newPage
})
</script>

<style scoped>
/* Custom number input styling */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

/* Smooth transitions */
button {
  transition: all 0.2s ease-in-out;
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
}

button:active:not(:disabled) {
  transform: translateY(0);
}
</style>
