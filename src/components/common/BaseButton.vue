<template>
    <button :type="type" :disabled="disabled || loading" :class="buttonClasses" @click="$emit('click', $event)">
        <font-awesome-icon v-if="loading" icon="spinner" spin class="mr-2" />
        <font-awesome-icon v-else-if="icon" :icon="icon" class="mr-2" />
        <slot>{{ text }}</slot>
    </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    text: String,
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'success', 'outline'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    fullWidth: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    icon: String
})

defineEmits(['click'])

const buttonClasses = computed(() => {
    const base = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2'

    const variants = {
        primary: 'bg-primary text-white hover:bg-opacity-90 focus:ring-primary',
        secondary: 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        success: 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        outline: 'border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-primary'
    }

    const sizes = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-base',
        lg: 'px-6 py-3 text-lg'
    }

    const width = props.fullWidth ? 'w-full' : ''
    const disabled = props.disabled || props.loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'

    return [base, variants[props.variant], sizes[props.size], width, disabled].join(' ')
})
</script>