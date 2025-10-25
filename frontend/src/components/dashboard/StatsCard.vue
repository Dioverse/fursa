<template>
    <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 hover:shadow-md transition">
        <div class="text-center">
            <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">
                {{ value }}
            </div>
            <div class="text-xs sm:text-sm text-gray-600 font-medium mb-3">
                {{ label }}
            </div>
            <font-awesome-icon :icon="icon" class="text-lg text-primary/30" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    value: Number,
    label: String,
    icon: String
})
</script>