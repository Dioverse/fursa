<template>
  <div class="relative">
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="loa          tooltip: {
            enabled: true,
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#1f2937',
            bodyColor: '#1f2937',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            cornerRadius: 8,
            padding: 12,
            boxPadding: 6,
            usePointStyle: true,
            displayColors: true,
            callbacks: {
              title: function(context) {
                return context[0].label;
              },
              label: function (context) {
                const value = context.parsed
                const percentage = getPercentage(value)
                return ` ${value} orders (${percentage}%)`
              }
            }
          }h-8"></div>
    </div>

    <div v-else-if="!data || data.length === 0" class="flex items-center justify-center h-64 text-gray-500">
      <div class="text-center">
        <font-awesome-icon icon="chart-pie" class="h-12 w-12 text-gray-300 mb-4" />
        <p>No data available</p>
      </div>
    </div>

    <div v-else class="flex flex-col items-center">
      <!-- Chart -->
      <div class="w-full" style="height: 300px;">
        <canvas ref="chartCanvas" id="ordersStatusChart"></canvas>
      </div>

      <!-- Legend -->
      <div class="w-full mt-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
          <div v-for="(item, index) in data" :key="index"
            class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: item.color }"></div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-900 truncate">
                {{ item.status }}
              </div>
              <div class="text-xs text-gray-500">
                {{ item.count }} ({{ getPercentage(item.count) }}%)
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  DoughnutController
} from 'chart.js'

// Register Chart.js components
ChartJS.register(DoughnutController, ArcElement, Tooltip, Legend)

// Props
const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Refs
const chartCanvas = ref(null)
let chartInstance = null

// Computed
const totalOrders = computed(() => {
  return props.data.reduce((sum, item) => sum + item.count, 0)
})

// Methods
const getPercentage = (count) => {
  if (totalOrders.value === 0) return 0
  return Math.round((count / totalOrders.value) * 100)
}

const createChart = () => {
  if (!chartCanvas.value) {
    return
  }
  if (!props.data.length) {
    return
  }

  const ctx = chartCanvas.value.getContext('2d')
  if (!ctx) {
    return
  }

  // Destroy existing chart
  if (chartInstance) {
    chartInstance.destroy()
  }

  const labels = props.data.map(item => item.status)
  const values = props.data.map(item => item.count)
  const colors = props.data.map(item => item.color)

  chartInstance = new ChartJS(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: colors,
        borderColor: '#ffffff',
        borderWidth: 2,
        hoverOffset: 12,
        borderRadius: 4,
        spacing: 3,
        cutout: '70%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: '#ffffff',
          bodyColor: '#ffffff',
          borderColor: '#3b82f6',
          borderWidth: 1,
          cornerRadius: 8,
          displayColors: false,
          callbacks: {
            label: function (context) {
              const label = context.label || ''
              const value = context.parsed || 0
              const percentage = getPercentage(value)
              return `${label}: ${value} orders (${percentage}%)`
            }
          }
        }
      },
      animation: {
        duration: 1000,
        easing: 'easeInOutQuart'
      },
      onHover: (event, elements) => {
        event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default'
      }
    }
  })
}

// Watchers
watch(() => props.data, (newData) => {
  console.log('Data changed:', newData)
  nextTick(() => {
    createChart()
  })
}, { immediate: true })

watch(() => props.loading, (newLoading) => {
  if (!newLoading) {
    nextTick(() => {
      createChart()
    })
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  // Try to create chart immediately if we have data
  if (props.data.length > 0) {
    createChart()
  }
  // Also try again after a short delay to ensure DOM is ready
  setTimeout(() => {
    if (props.data.length > 0) {
      createChart()
    }
  }, 100)
})

// Cleanup
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>
