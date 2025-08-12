<template>
    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition">
        <div class="text-4xl font-bold text-gray-800 mb-2">{{ value }}</div>
        <div class="text-gray-600">{{ label }}</div>
        <div v-if="icon" class="mt-4">
            <font-awesome-icon :icon="icon" size="2x" class="text-primary opacity-50" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    value: {
        type: [String, Number],
        required: true
    },
    label: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        default: ''
    }
})
</script>