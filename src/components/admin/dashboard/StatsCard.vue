<template>
    <div class="card hover:shadow-medium transition-shadow duration-200">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ title }}
                    </p>

                    <div v-if="loading" class="space-y-2">
                        <div class="loading-skeleton h-8 w-24 rounded"></div>
                        <div class="loading-skeleton h-4 w-16 rounded"></div>
                    </div>

                    <div v-else>
                        <p class="text-2xl font-bold text-gray-900 mb-1">
                            {{ value }}
                        </p>

                        <div v-if="change !== null" class="flex items-center text-sm">
                            <font-awesome-icon :icon="changeIcon" class="h-3 w-3 mr-1" :class="changeClass" />
                            <span :class="changeClass">
                                {{ Math.abs(change) }}%
                            </span>
                            <span class="text-gray-500 ml-1">
                                {{ changeText }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" :class="iconBackgroundClass">
                        <font-awesome-icon :icon="icon" class="h-6 w-6" :class="iconClass" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    change: {
        type: Number,
        default: null
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger'].includes(value)
    },
    loading: {
        type: Boolean,
        default: false
    }
})

// Computed
const changeIcon = computed(() => {
    if (props.change === null) return null
    return props.change >= 0 ? 'arrow-up' : 'arrow-down'
})

const changeClass = computed(() => {
    if (props.change === null) return ''
    return props.change >= 0 ? 'text-success-600' : 'text-danger-600'
})

const changeText = computed(() => {
    if (props.change === null) return ''
    return props.change >= 0 ? 'from last month' : 'from last month'
})

const iconBackgroundClass = computed(() => {
    const classes = {
        primary: 'bg-primary-100',
        secondary: 'bg-secondary-100',
        success: 'bg-success-100',
        warning: 'bg-warning-100',
        danger: 'bg-danger-100'
    }
    return classes[props.color] || classes.primary
})

const iconClass = computed(() => {
    const classes = {
        primary: 'text-primary-600',
        secondary: 'text-secondary-600',
        success: 'text-success-600',
        warning: 'text-warning-600',
        danger: 'text-danger-600'
    }
    return classes[props.color] || classes.primary
})
</script>