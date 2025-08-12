<template>
    <div class="flex items-center justify-between mb-8">
        <div v-for="step in steps" :key="step.number" class="flex-1 relative">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-all duration-300"
                    :class="getStepClass(step.number)">
                    <font-awesome-icon v-if="step.number < currentStep" icon="check" class="text-white" />
                    <span v-else>{{ step.number }}</span>
                </div>
                <div v-if="step.number < steps.length" class="flex-1 h-1 mx-2 transition-all duration-300"
                    :class="step.number < currentStep ? 'bg-green-500' : 'bg-gray-300'"></div>
            </div>
            <p class="text-xs mt-2" :class="step.number <= currentStep ? 'text-gray-800' : 'text-gray-400'">
                {{ step.label }}
            </p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    currentStep: {
        type: Number,
        required: true
    },
    steps: {
        type: Array,
        default: () => [
            { number: 1, label: 'Business Info' },
            { number: 2, label: 'Contact Person' },
            { number: 3, label: 'Distribution' },
            { number: 4, label: 'Product Focus' },
            { number: 5, label: 'Documents' }
        ]
    }
})

const getStepClass = (stepNumber) => {
    if (stepNumber < props.currentStep) {
        return 'bg-green-500 text-white'
    } else if (stepNumber === props.currentStep) {
        return 'bg-primary text-white'
    } else {
        return 'bg-gray-300 text-gray-600'
    }
}
</script>