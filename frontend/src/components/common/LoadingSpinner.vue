<template>
    <div class="flex items-center justify-center" :class="containerClass">
        <font-awesome-icon icon="spinner" :size="size" spin :class="spinnerClass" />
        <span v-if="text" class="ml-2">{{ text }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    size: {
        type: String,
        default: '2x'
    },
    text: {
        type: String,
        default: ''
    },
    fullScreen: {
        type: Boolean,
        default: false
    },
    color: {
        type: String,
        default: 'primary'
    }
})

const containerClass = computed(() => ({
    'fixed inset-0 bg-white bg-opacity-75 z-50': props.fullScreen,
    'py-8': !props.fullScreen
}))

const spinnerClass = computed(() => {
    const colors = {
        primary: 'text-primary',
        white: 'text-white',
        gray: 'text-gray-500'
    }
    return colors[props.color] || 'text-primary'
})
</script>