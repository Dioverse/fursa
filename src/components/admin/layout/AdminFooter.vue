<template>
    <footer class="bg-white border-t border-gray-200 px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
            <!-- Left side - Copyright -->
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>© {{ currentYear }} Fursa Energy. All rights reserved.</span>
                <span class="hidden sm:inline">•</span>
                <span class="hidden sm:inline">Version {{ appVersion }}</span>
            </div>

            <!-- Right side - Links and info -->
            <div class="flex items-center space-x-6 text-sm">
                <!-- System status indicator -->
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 rounded-full" :class="systemStatusClass"></div>
                    <span class="text-gray-500">{{ systemStatusText }}</span>
                </div>

                <!-- Last backup info -->
                <div class="hidden md:block text-gray-500">
                    <font-awesome-icon icon="database" class="h-3 w-3 mr-1" />
                    Last backup: {{ lastBackupTime }}
                </div>

                <!-- Support links -->
                <div class="flex items-center space-x-4">
                    <a href="/admin/help" class="text-gray-500 hover:text-gray-700 transition-colors" title="Help">
                        <font-awesome-icon icon="question-circle" class="h-4 w-4" />
                    </a>

                    <a href="mailto:support@fursaenergy.com" class="text-gray-500 hover:text-gray-700 transition-colors"
                        title="Support">
                        <font-awesome-icon icon="envelope" class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile version info -->
        <div class="sm:hidden mt-2 text-center">
            <span class="text-xs text-gray-400">Version {{ appVersion }}</span>
        </div>
    </footer>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { formatDistanceToNow } from 'date-fns'

// Reactive data
const systemStatus = ref('operational') // operational, maintenance, error
const lastBackup = ref(new Date(Date.now() - 1000 * 60 * 60 * 2)) // 2 hours ago

// Computed
const currentYear = computed(() => new Date().getFullYear())

const appVersion = computed(() => {
    return import.meta.env.VITE_APP_VERSION || '1.0.0'
})

const systemStatusClass = computed(() => {
    const classes = {
        operational: 'bg-green-400',
        maintenance: 'bg-yellow-400',
        error: 'bg-red-400'
    }
    return classes[systemStatus.value] || 'bg-gray-400'
})

const systemStatusText = computed(() => {
    const texts = {
        operational: 'All systems operational',
        maintenance: 'Under maintenance',
        error: 'System issues detected'
    }
    return texts[systemStatus.value] || 'Status unknown'
})

const lastBackupTime = computed(() => {
    return formatDistanceToNow(lastBackup.value, { addSuffix: true })
})

// Methods
const checkSystemStatus = async () => {
    try {
        // You can implement actual system status checking here
        // const response = await api.get('/system/status')
        // systemStatus.value = response.data.status

        // For now, we'll simulate a status check
        systemStatus.value = 'operational'
    } catch (error) {
        systemStatus.value = 'error'
    }
}

// Lifecycle
onMounted(() => {
    checkSystemStatus()

    // Check system status every 5 minutes
    setInterval(checkSystemStatus, 5 * 60 * 1000)
})
</script>