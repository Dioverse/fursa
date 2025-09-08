<template>
  <div class="relative">
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="loading-spinner w-8 h-8"></div>
    </div>

    <div v-else-if="!data || data.length === 0" class="flex items-center justify-center h-64 text-gray-500">
      <div class="text-center">
        <font-awesome-icon icon="chart-line" class="h-12 w-12 text-gray-300 mb-4" />
        <p>No data available</p>
      </div>
    </div>

    <canvas v-else ref="chartCanvas" id="revenueChart" class="w-full h-64"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
  LineController
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  LineController,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

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

// Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    minimumFractionDigits: 0
  }).format(value)
}

const formatMonthLabel = (monthName) => {
  try {
    // Try parsing with a dummy day & year
    const date = new Date(`${monthName} 1, 2000`)
    if (!isNaN(date)) {
      return date.toLocaleDateString('en-US', { month: 'short' })
    }
    return monthName // fallback if invalid
  } catch (error) {
    console.log('Error formatting month label:', error)
    return monthName
  }
}

const createChart = () => {
  if (!chartCanvas.value || !props.data.length) return

  const ctx = chartCanvas.value.getContext('2d')

  // Destroy existing chart
  if (chartInstance) {
    chartInstance.destroy()
  }

  // Extract labels and values from data
  const labels = props.data.map(item => {
    // Handle both 'date' and 'month' property names
    const labelValue = item.date || item.month || item.label || ''
    return formatMonthLabel(labelValue)
  })

  const values = props.data.map(item => item.revenue || 0)

  chartInstance = new ChartJS(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Revenue',
        data: values,
        borderColor: '#10b981', // Green color to match Naira theme
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#10b981',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 8,
        pointHoverBackgroundColor: '#10b981',
        pointHoverBorderColor: '#ffffff',
        pointHoverBorderWidth: 3
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
          borderColor: '#10b981',
          borderWidth: 1,
          cornerRadius: 8,
          displayColors: false,
          callbacks: {
            label: function (context) {
              return `Revenue: ${formatCurrency(context.parsed.y)}`
            }
          }
        }
      },
      scales: {
        x: {
          display: true,
          grid: {
            display: false
          },
          ticks: {
            color: '#6b7280',
            font: {
              size: 12
            },
            maxRotation: 45,
            minRotation: 0
          }
        },
        y: {
          display: true,
          grid: {
            color: '#f3f4f6',
            borderDash: [5, 5]
          },
          ticks: {
            color: '#6b7280',
            font: {
              size: 12
            },
            callback: function (value) {
              return formatCurrency(value)
            }
          },
          beginAtZero: true
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      },
      elements: {
        point: {
          hoverRadius: 8
        }
      },
      animation: {
        duration: 1000,
        easing: 'easeInOutQuart',
        onComplete: function () {
          if (this.data.datasets.length === 0) {
            this.update('none');
          }
        }
      }
    }
  })
}

// Watchers
watch(() => props.data, () => {
  nextTick(() => {
    createChart()
  })
}, { deep: true })

watch(() => props.loading, (newLoading) => {
  if (!newLoading) {
    nextTick(() => {
      createChart()
    })
  }
})

// Lifecycle
onMounted(() => {
  if (!props.loading && props.data.length > 0) {
    createChart()
  }
})

// Cleanup
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>
